<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Services\ExcelReportService;
use Illuminate\Support\Facades\File;

class ReportController extends Controller
{
    protected ExcelReportService $excelService;

    public function __construct(ExcelReportService $excelService)
    {
        $this->excelService = $excelService;
    }

    /**
     * Display a listing of reports (Blade view or JSON API)
     */
    public function index(Request $request)
    {
        $query = Report::query()->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('ssc_id', 'like', "%{$s}%")
                  ->orWhere('report_no', 'like', "%{$s}%")
                  ->orWhere('customer', 'like', "%{$s}%")
                  ->orWhere('part_name', 'like', "%{$s}%")
                  ->orWhere('model_unit', 'like', "%{$s}%");
            });
        }

        if ($request->filled('type') && $request->type !== 'All') {
            $query->where('inspection_type', $request->type);
        }

        $reports = $query->get();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($reports);
        }

        $totalCount = Report::count();
        $acceptCount = Report::where('has_rejects', false)->count();
        $rejectCount = Report::where('has_rejects', true)->count();

        return view('reports.index', compact('reports', 'totalCount', 'acceptCount', 'rejectCount'));
    }

    /**
     * Show the Inspection Studio workspace for creating a new QC report
     */
    public function create()
    {
        return view('reports.create', [
            'report' => null,
            'isEdit' => false
        ]);
    }

    /**
     * Show the Inspection Studio workspace for editing an existing QC report
     */
    public function edit(Report $report)
    {
        return view('reports.create', [
            'report' => $report,
            'isEdit' => true
        ]);
    }

    /**
     * Return single report JSON
     */
    public function show(Report $report)
    {
        return response()->json($report);
    }

    /**
     * Store a newly created report
     */
    public function store(Request $request)
    {
        $data = $this->extractReportData($request);
        $data['has_rejects'] = $this->checkHasRejects($data);

        $report = Report::create($data);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => "Laporan {$report->ssc_id} berhasil disimpan!",
                'report' => $report
            ], 201);
        }

        return redirect()->route('reports.index')->with('success', "Laporan {$report->ssc_id} berhasil dibuat!");
    }

    /**
     * Update the specified report
     */
    public function update(Request $request, Report $report)
    {
        $data = $this->extractReportData($request);
        $data['has_rejects'] = $this->checkHasRejects($data);

        $report->update($data);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => "Laporan {$report->ssc_id} berhasil diperbarui!",
                'report' => $report
            ]);
        }

        return redirect()->route('reports.index')->with('success', "Laporan {$report->ssc_id} berhasil diperbarui!");
    }

    /**
     * Remove the specified report
     */
    public function destroy(Report $report)
    {
        $sscId = $report->ssc_id;
        $report->delete();

        if (request()->wantsJson() || request()->is('api/*')) {
            return response()->json(['success' => true, 'message' => "Laporan {$sscId} berhasil dihapus."]);
        }

        return redirect()->route('reports.index')->with('success', "Laporan {$sscId} berhasil dihapus!");
    }

    /**
     * Duplicate existing report
     */
    public function duplicate(Report $report)
    {
        $replica = $report->replicate();
        $replica->ssc_id = $report->ssc_id . ' (COPY-' . rand(100, 999) . ')';
        $replica->report_no = ($report->report_no ?? '') . '-CPY';
        $replica->date = date('Y-m-d');
        $replica->save();

        if (request()->wantsJson() || request()->is('api/*')) {
            return response()->json($replica);
        }

        return redirect()->route('reports.index')->with('success', "Laporan berhasil diduplikat menjadi {$replica->ssc_id}!");
    }

    /**
     * Upload photo for an inspection section
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240', // 10MB max
            'section' => 'nullable|string'
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $uploadPath = public_path('uploads/reports/photos');
            
            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true, true);
            }

            $section = preg_replace('/[^a-zA-Z0-9_-]/', '_', $request->input('section', 'sec'));
            $filename = 'qc_' . $section . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);

            $url = asset('uploads/reports/photos/' . $filename);

            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => '/uploads/reports/photos/' . $filename,
                'filename' => $filename,
                'section' => $request->input('section')
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada file foto yang diunggah'], 400);
    }

    /**
     * Export report to Excel
     */
    public function export(Report $report)
    {
        try {
            $filePath = $this->excelService->generateExcelFile($report);
            $filename = preg_replace('/[^A-Za-z0-9_-]/', '_', $report->ssc_id ?? 'REPORT') . ' CCR PRE OUTPUT SHAFT.xlsx';

            return response()->download($filePath, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat file Excel: ' . $e->getMessage());
        }
    }

    /**
     * Save/Update and immediately download Excel file in one action
     */
    public function submitAndDownload(Request $request)
    {
        $data = $this->extractReportData($request);
        $data['has_rejects'] = $this->checkHasRejects($data);

        $report = Report::updateOrCreate(
            ['ssc_id' => $data['ssc_id']],
            $data
        );

        $filePath = $this->excelService->generateExcelFile($report);
        $filename = preg_replace('/[^A-Za-z0-9_-]/', '_', $report->ssc_id) . ' CCR PRE OUTPUT SHAFT.xlsx';

        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ])->deleteFileAfterSend(true);
    }

    /**
     * Printable view of official FORM-QC-002 document (for direct printing or Save as PDF)
     */
    public function pdf(Report $report)
    {
        return view('reports.print', compact('report'));
    }

    /**
     * Extract report payload safely
     */
    private function extractReportData(Request $request): array
    {
        $g = $request->input('general', []);

        $sscId = $request->input('ssc_id') ?? ($g['sscId'] ?? ($g['ssc_id'] ?? 'SSC-' . time()));
        $reportNo = $request->input('report_no') ?? ($g['reportNo'] ?? ($g['report_no'] ?? null));
        $date = $request->input('date') ?? ($g['date'] ?? null);
        $customer = $request->input('customer') ?? ($g['customer'] ?? null);
        $partName = $request->input('part_name') ?? ($g['partName'] ?? ($g['part_name'] ?? 'OUTPUT SHAFT'));
        $inspectionType = $request->input('inspection_type') ?? ($g['inspectionType'] ?? ($g['inspection_type'] ?? 'Pre'));
        $jobNo = $request->input('job_no') ?? ($g['jobNo'] ?? ($g['job_no'] ?? '-'));
        $modelUnit = $request->input('model_unit') ?? ($g['modelUnit'] ?? ($g['model_unit'] ?? 'HT051'));
        $quoteNo = $request->input('quote_no') ?? ($g['quoteNo'] ?? ($g['quote_no'] ?? '-'));
        $partNo = $request->input('part_no') ?? ($g['partNo'] ?? ($g['part_no'] ?? '-'));
        $docNo = $request->input('doc_no') ?? ($g['docNo'] ?? 'FORM-QC-002');
        $rev = $request->input('rev') ?? ($g['rev'] ?? '04');
        $responsibility = $request->input('responsibility') ?? ($g['responsibility'] ?? 'QC Inspector');
        $effectiveDate = $request->input('effective_date') ?? ($g['effectiveDate'] ?? ($g['effective_date'] ?? null));
        $page = $request->input('page') ?? ($g['page'] ?? '1');

        $sections = $request->input('sections', []);
        $tir = $request->input('tir', []);
        $recommendations = $request->input('recommendations', []);
        $signatures = $request->input('signatures', []);
        $photos = $request->input('photos', []);

        return [
            'ssc_id' => trim($sscId),
            'report_no' => $reportNo ? trim($reportNo) : null,
            'date' => $date ?: null,
            'customer' => $customer ? trim($customer) : null,
            'part_name' => trim($partName),
            'inspection_type' => $inspectionType,
            'job_no' => trim($jobNo),
            'model_unit' => trim($modelUnit),
            'quote_no' => trim($quoteNo),
            'part_no' => trim($partNo),
            'doc_no' => trim($docNo),
            'rev' => trim($rev),
            'responsibility' => trim($responsibility),
            'effective_date' => $effectiveDate ?: null,
            'page' => trim($page),
            'sections' => $sections,
            'tir' => $tir,
            'recommendations' => $recommendations,
            'signatures' => $signatures,
            'photos' => $photos,
        ];
    }

    /**
     * Check if report has any reject status
     */
    private function checkHasRejects(array $data): bool
    {
        $sec = $data['sections'] ?? [];
        if (!empty($sec['item01']['ar']) && $sec['item01']['ar'] === 'R') return true;
        if (!empty($sec['item02']['itemB']['row1']['ar']) && $sec['item02']['itemB']['row1']['ar'] === 'R') return true;
        if (!empty($sec['item02']['itemB']['row2']['ar']) && $sec['item02']['itemB']['row2']['ar'] === 'R') return true;
        if (!empty($sec['item02']['itemB']['row3']['ar']) && $sec['item02']['itemB']['row3']['ar'] === 'R') return true;
        if (!empty($sec['item02']['itemC']['row1']['ar']) && $sec['item02']['itemC']['row1']['ar'] === 'R') return true;
        if (!empty($sec['item02']['itemC']['row2']['ar']) && $sec['item02']['itemC']['row2']['ar'] === 'R') return true;
        if (!empty($sec['item02']['itemC']['row3']['ar']) && $sec['item02']['itemC']['row3']['ar'] === 'R') return true;
        if (!empty($sec['item03']['itemD']['ar']) && $sec['item03']['itemD']['ar'] === 'R') return true;
        if (!empty($sec['item04']['itemF']['row1']['ar']) && $sec['item04']['itemF']['row1']['ar'] === 'R') return true;
        if (!empty($sec['item04']['itemF']['row2']['ar']) && $sec['item04']['itemF']['row2']['ar'] === 'R') return true;
        if (!empty($sec['item04']['itemF']['row3']['ar']) && $sec['item04']['itemF']['row3']['ar'] === 'R') return true;
        if (!empty($sec['item05']['itemH']['row1']['ar']) && $sec['item05']['itemH']['row1']['ar'] === 'R') return true;
        if (!empty($sec['item05']['itemH']['row2']['ar']) && $sec['item05']['itemH']['row2']['ar'] === 'R') return true;
        if (!empty($sec['item05']['itemH']['row3']['ar']) && $sec['item05']['itemH']['row3']['ar'] === 'R') return true;
        if (!empty($sec['item06']['itemG']['ar']) && $sec['item06']['itemG']['ar'] === 'R') return true;

        $tirItems = $data['tir']['items'] ?? [];
        foreach ($tirItems as $item) {
            if (!empty($item['ar']) && $item['ar'] === 'R') return true;
        }

        return false;
    }
}
