<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Services\ExcelReportService;

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

        $hasRejects = $this->checkHasRejects($data);
        $data['has_rejects'] = $hasRejects;

        $report = Report::create($data);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($report, 201);
        }

        return redirect()->route('reports.index')->with('success', "Laporan {$report->ssc_id} berhasil dibuat!");
    }

    /**
     * Update the specified report
     */
    public function update(Request $request, Report $report)
    {
        $data = $this->extractReportData($request);

        $hasRejects = $this->checkHasRejects($data);
        $data['has_rejects'] = $hasRejects;

        $report->update($data);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($report);
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

        $sections = $request->input('sections', []);
        $tir = $request->input('tir', []);
        $recommendations = $request->input('recommendations', []);
        $signatures = $request->input('signatures', []);

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
            'sections' => $sections,
            'tir' => $tir,
            'recommendations' => $recommendations,
            'signatures' => $signatures,
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

