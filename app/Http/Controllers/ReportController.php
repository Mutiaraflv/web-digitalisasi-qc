<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Report;
use App\Models\User;
use App\Services\ExcelReportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ReportController extends Controller
{
    /** Folder foto laporan yang PRIVAT (hanya bisa dibuka lewat route berotorisasi). */
    public const PRIVATE_PHOTO_DIR = 'app/private/report-photos';

    /** Awalan path foto yang diizinkan tersimpan di data laporan. */
    private const ALLOWED_PHOTO_PREFIXES = [
        '/reports/photo/',
        '/uploads/reports/photos/',
        '/images/doc_assets/',
    ];

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
        $user = Auth::user();
        $activeTab = $request->input('tab', 'all');
        if (!in_array($activeTab, ['all', 'mine', 'approval'], true)) {
            $activeTab = 'all';
        }

        // Kolom JSON besar (sections, tir, photos, recommendations) tidak dimuat untuk daftar
        $query = Report::select([
                'id', 'created_by', 'checker_id', 'reviewer_id', 'ssc_id', 'report_no', 'doc_no', 'date',
                'customer', 'part_name', 'model_unit', 'inspection_type', 'signatures', 'has_rejects',
                'review_status', 'submitted_at', 'reviewed_at', 'reviewed_by', 'created_at', 'updated_at',
            ])
            ->with(['creator:id,name', 'checker:id,name', 'reviewer:id,name', 'reviewerUser:id,name'])
            ->orderBy('created_at', 'desc');

        if ($activeTab === 'mine' && $user) {
            $query->ownedOrCheckedBy($user);
            if ($request->filled('status') && $request->status !== 'All') {
                $statusFilter = strtolower($request->status);
                if ($statusFilter === 'draft') {
                    $query->where('review_status', Report::STATUS_DRAFT);
                } elseif (in_array($statusFilter, ['dikirim', 'submitted'], true)) {
                    $query->where('review_status', '!=', Report::STATUS_DRAFT);
                }
            }
        } elseif ($activeTab === 'approval' && $user) {
            $query->pendingApprovalFor($user);
        } else {
            $query->nonDraft();
        }

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

        $totalCount = Report::nonDraft()->count();
        $waitingCount = Report::where('review_status', Report::STATUS_WAITING_REVIEW)->count();
        $reviewedCount = Report::where('review_status', Report::STATUS_REVIEWED)->count();
        $mySubmissionsCount = $user ? Report::ownedOrCheckedBy($user)->count() : 0;
        $waitingApprovalCount = $user ? Report::pendingApprovalFor($user)->count() : 0;

        return view('reports.index', compact(
            'reports',
            'activeTab',
            'totalCount',
            'waitingCount',
            'reviewedCount',
            'mySubmissionsCount',
            'waitingApprovalCount'
        ));
    }

    /**
     * Show the Inspection Studio workspace for creating a new QC report
     */
    public function create()
    {
        $identifiers = $this->generateNextIdentifiers();
        $users = User::orderBy('name')->get(['id', 'name', 'role']);

        return view('reports.create', [
            'report' => null,
            'isEdit' => false,
            'nextReportNo' => $identifiers['nextReportNo'],
            'nextSscId' => $identifiers['nextSscId'],
            'users' => $users,
        ]);
    }

    /**
     * Show the Inspection Studio workspace for editing an existing QC report
     */
    public function edit(Report $report)
    {
        if (!$report->canBeModifiedBy(Auth::user())) {
            return redirect()->route('reports.index')
                ->with('error', 'Anda hanya dapat mengedit laporan yang Anda buat atau periksa.');
        }

        $users = User::orderBy('name')->get(['id', 'name', 'role']);

        return view('reports.create', [
            'report' => $report,
            'isEdit' => true,
            'nextReportNo' => $report->report_no,
            'nextSscId' => $report->ssc_id,
            'users' => $users,
        ]);
    }

    /**
     * Generate next sequential report_no and ssc_id from previous records
     */
    private function generateNextIdentifiers(): array
    {
        $nextReportNo = '0743';
        $nextSscId = 'SSC SBY-5779';

        try {
            $latest = Report::orderBy('id', 'desc')->first();
            if ($latest) {
                // Next report_no (e.g. 0742 -> 0743)
                if (!empty($latest->report_no) && preg_match('/(\d+)/', $latest->report_no, $m)) {
                    $nextNum = intval($m[1]) + 1;
                    $len = max(strlen($m[1]), 4);
                    $nextReportNo = str_pad($nextNum, $len, '0', STR_PAD_LEFT);
                } elseif (!empty($latest->id)) {
                    $nextReportNo = str_pad(742 + $latest->id, 4, '0', STR_PAD_LEFT);
                }

                // Next ssc_id (e.g. SSC SBY-5778 -> SSC SBY-5779)
                if (!empty($latest->ssc_id) && preg_match('/^(.*?)(\d+)$/', $latest->ssc_id, $m)) {
                    $prefix = $m[1];
                    $nextNum = intval($m[2]) + 1;
                    $nextSscId = $prefix . $nextNum;
                } elseif (!empty($latest->id)) {
                    $nextSscId = 'SSC SBY-' . (5778 + $latest->id);
                }
            }
        } catch (\Throwable $e) {
            // Keep default fallback if table unreadable
        }

        return [
            'nextReportNo' => $nextReportNo,
            'nextSscId' => $nextSscId
        ];
    }

    /**
     * Draf hanya boleh dilihat oleh pembuat/pemeriksa laporan dan admin.
     */
    private function authorizeView(Report $report): void
    {
        if (!$report->isDraft()) {
            return;
        }
        $user = Auth::user();
        if (!$user || (!$report->canBeModifiedBy($user) && !$user->isAdmin())) {
            abort(403, 'Draf laporan hanya dapat dilihat oleh pembuat atau pemeriksanya.');
        }
    }

    /**
     * Return single report JSON
     */
    public function show(Report $report)
    {
        $this->authorizeView($report);

        return response()->json($report);
    }

    /**
     * Store a newly created report
     */
    public function store(Request $request)
    {
        $submitAction = strtolower(trim((string) ($request->input('submit_action') ?? $request->input('action') ?? 'submit')));
        $isDraft = ($submitAction === 'draft');

        $data = $this->extractReportData($request);
        $data['has_rejects'] = $this->checkHasRejects($data);
        $data['created_by'] = Auth::id();
        $data['review_status'] = $isDraft ? Report::STATUS_DRAFT : Report::STATUS_WAITING_REVIEW;
        $data['submitted_at'] = $isDraft ? null : now();

        $identifiersChanged = false;
        try {
            $report = Report::create($data);
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            // Permintaan ganda dari pengguna yang sama (draf sudah terbentuk): jadikan pembaruan, bukan laporan baru
            $dup = Report::where('ssc_id', $data['ssc_id'] ?? '')->first();
            if ($dup && (int) $dup->created_by === (int) Auth::id() && $dup->isDraft()) {
                return $this->update($request, $dup);
            }

            // SSC ID dipakai orang lain (mis. dua orang membuka form baru bersamaan): beri nomor berikutnya
            $report = null;
            for ($attempt = 0; $attempt < 5 && !$report; $attempt++) {
                $ids = $this->generateNextIdentifiers();
                $data['ssc_id'] = $ids['nextSscId'];
                $data['report_no'] = $ids['nextReportNo'];
                try {
                    $report = Report::create($data);
                    $identifiersChanged = true;
                } catch (\Illuminate\Database\UniqueConstraintViolationException $retry) {
                    $report = null;
                }
            }
            if (!$report) {
                throw $e;
            }
        }
        $message = $isDraft
            ? "Draft laporan {$report->ssc_id} berhasil disimpan!"
            : "Laporan {$report->ssc_id} berhasil disubmit (Waiting Review)!";
        if ($identifiersChanged) {
            $message .= " (SSC ID diubah otomatis karena nomor sebelumnya sudah dipakai)";
        }

        ActivityLog::record(
            $isDraft ? 'report_draft_created' : 'report_submitted',
            $isDraft
                ? "Menyimpan draft laporan {$report->ssc_id}"
                : "Mensubmit laporan {$report->ssc_id} (Waiting Review)",
            $report,
            ['part_name' => $report->part_name, 'status' => $report->review_status]
        );

        if ($request->wantsJson() || $request->is('api/*')) {
            if ($request->hasSession()) {
                $request->session()->flash('success', $message);
            }
            return response()->json([
                'success' => true,
                'message' => $message,
                'report' => $report,
                'identifiers_changed' => $identifiersChanged,
            ], 201);
        }

        return redirect()->route('reports.index', ['tab' => 'mine'])->with('success', $message);
    }

    /**
     * Update the specified report
     */
    public function update(Request $request, Report $report)
    {
        if (!$report->canBeModifiedBy(Auth::user())) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda hanya dapat mengubah laporan yang Anda buat atau periksa.'
                ], 403);
            }
            return redirect()->route('reports.index')->with('error', 'Anda hanya dapat mengubah laporan yang Anda buat atau periksa.');
        }

        $submitAction = strtolower(trim((string) ($request->input('submit_action') ?? $request->input('action') ?? '')));
        $isAutosave = $request->boolean('is_autosave');

        // Deteksi konflik: laporan sudah diubah (tab/perangkat/orang lain) sejak versi yang dilihat klien
        $baseUpdatedAt = $request->input('base_updated_at');
        if ($baseUpdatedAt && $report->updated_at) {
            try {
                $baseTs = \Illuminate\Support\Carbon::parse($baseUpdatedAt)->timestamp;
            } catch (\Throwable $e) {
                $baseTs = null;
            }
            if ($baseTs !== null && $report->updated_at->timestamp > $baseTs) {
                return response()->json([
                    'success' => false,
                    'conflict' => true,
                    'message' => 'Laporan ini sudah diubah di tab/perangkat lain sejak Anda membukanya.',
                    'server_updated_at' => $report->updated_at->toISOString(),
                ], 409);
            }
        }

        // Autosave tidak boleh menimpa laporan yang sudah dikirim / direview
        if ($isAutosave && !$report->isDraft()) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan sudah dikirim; autosave diabaikan.',
            ], 409);
        }

        $data = $this->extractReportData($request, $report);
        $data['has_rejects'] = $this->checkHasRejects($data);

        $wasDraft = $report->isDraft();
        if ($wasDraft) {
            if ($submitAction === 'submit') {
                $data['review_status'] = Report::STATUS_WAITING_REVIEW;
                $data['submitted_at'] = now();
                $message = "Laporan {$report->ssc_id} berhasil disubmit (Waiting Review)!";
            } else {
                $data['review_status'] = Report::STATUS_DRAFT;
                $message = "Draft laporan {$report->ssc_id} berhasil disimpan!";
            }
        } else {
            $message = "Laporan {$report->ssc_id} berhasil diperbarui!";
        }

        $report->update($data);

        // Autosave berjalan tiap beberapa detik: jangan membanjiri log aktivitas
        if ($isAutosave) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'report' => $report->fresh(),
            ]);
        }

        if ($wasDraft && $submitAction === 'submit') {
            ActivityLog::record('report_submitted', "Mensubmit laporan {$report->ssc_id} dari Draft ke Waiting Review", $report, [
                'part_name' => $report->part_name,
                'status' => Report::STATUS_WAITING_REVIEW,
            ]);
        } elseif ($wasDraft) {
            ActivityLog::record('report_draft_updated', "Memperbarui draft laporan {$report->ssc_id}", $report, [
                'part_name' => $report->part_name,
                'status' => Report::STATUS_DRAFT,
            ]);
        } else {
            ActivityLog::record('report_updated', "Memperbarui data laporan {$report->ssc_id}", $report, [
                'part_name' => $report->part_name,
                'status' => $report->review_status,
            ]);
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            if ($request->hasSession()) {
                $request->session()->flash('success', $message);
            }
            return response()->json([
                'success' => true,
                'message' => $message,
                'report' => $report->fresh(),
            ]);
        }

        return redirect()->route('reports.index', ['tab' => 'mine'])->with('success', $message);
    }

    /**
     * Remove the specified report
     */
    public function destroy(Report $report)
    {
        if (!$report->canBeModifiedBy(Auth::user())) {
            if (request()->wantsJson() || request()->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda hanya dapat menghapus laporan yang Anda buat atau periksa.'
                ], 403);
            }
            return redirect()->route('reports.index')->with('error', 'Anda hanya dapat menghapus laporan yang Anda buat atau periksa.');
        }

        $sscId = $report->ssc_id;
        $partName = $report->part_name;
        $photoPaths = $this->collectOwnedPhotoPaths($report->photos);
        $report->delete();
        $this->deletePhotoFiles($photoPaths);

        ActivityLog::record('report_deleted', "Menghapus laporan {$sscId}", null, [
            'subject' => $sscId,
            'part_name' => $partName,
        ]);

        if (request()->wantsJson() || request()->is('api/*')) {
            return response()->json(['success' => true, 'message' => "Laporan {$sscId} berhasil dihapus."]);
        }

        return redirect()->route('reports.index')->with('success', "Laporan {$sscId} berhasil dihapus!");
    }

    /**
     * Upload photo for an inspection section.
     * Foto disimpan di folder PRIVAT (storage/app/private/report-photos) dan hanya
     * dapat dibuka lewat route /reports/photo/{filename} yang mewajibkan login.
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240', // 10MB max
            'section' => 'nullable|string|max:80'
        ]);

        $file = $request->file('photo');

        // Cegah gambar raksasa yang menghabiskan memori saat diproses GD
        $dim = @getimagesize($file->getRealPath());
        if (!$dim || ($dim[0] * $dim[1]) > 50_000_000) {
            return response()->json(['success' => false, 'message' => 'Resolusi foto terlalu besar atau file tidak valid.'], 422);
        }

        $uploadPath = storage_path(self::PRIVATE_PHOTO_DIR);
        if (!File::isDirectory($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true, true);
        }

        // Ekstensi ditentukan dari ISI file (bukan nama dari klien)
        $ext = match ($dim[2] ?? null) {
            IMAGETYPE_PNG => 'png',
            IMAGETYPE_WEBP => 'webp',
            default => 'jpg',
        };

        $section = preg_replace('/[^a-zA-Z0-9_-]/', '_', $request->input('section', 'sec'));
        $filename = 'qc_' . $section . '_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $file->move($uploadPath, $filename);

        $targetFilePath = $uploadPath . DIRECTORY_SEPARATOR . $filename;

        // Kompres ulang di server hanya bila benar-benar perlu (hindari kompresi ganda)
        if (file_exists($targetFilePath) && (max($dim[0], $dim[1]) > 1600 || filesize($targetFilePath) > 1024 * 1024)) {
            $this->optimizeInspectionPhoto($targetFilePath);
        }

        $path = '/reports/photo/' . $filename;

        return response()->json([
            'success' => true,
            'url' => $path,
            'path' => $path,
            'filename' => $filename,
            'section' => $request->input('section')
        ]);
    }

    /**
     * Sajikan foto privat (wajib login).
     */
    public function photo(string $filename)
    {
        if (!preg_match('/^[A-Za-z0-9_.-]+\.(jpg|jpeg|png|webp)$/i', $filename)) {
            abort(404);
        }

        $path = storage_path(self::PRIVATE_PHOTO_DIR . DIRECTORY_SEPARATOR . $filename);
        if (!is_file($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Cache-Control' => 'private, max-age=86400',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Hanya izinkan path foto yang berasal dari sistem ini (blokir path traversal,
     * file lokal sembarang, dan base64 baru). URL absolut diubah menjadi path relatif
     * supaya tetap valid ketika domain/host berubah.
     */
    private function sanitizePhotoPath($value, $existingValue = null): ?string
    {
        if (!is_string($value) || trim($value) === '') {
            return null;
        }
        $v = trim($value);

        // Data lama berupa base64 hanya dipertahankan bila memang sudah ada di laporan ini
        if (str_starts_with($v, 'data:')) {
            return ($existingValue !== null && $existingValue === $v) ? $v : null;
        }

        if (preg_match('#^https?://#i', $v)) {
            $v = (string) (parse_url($v, PHP_URL_PATH) ?: '');
        }
        if ($v === '' || str_contains($v, '..') || str_contains($v, '\\') || str_contains($v, "\0")) {
            return null;
        }
        if ($v[0] !== '/') {
            $v = '/' . $v;
        }
        foreach (self::ALLOWED_PHOTO_PREFIXES as $prefix) {
            if (str_starts_with($v, $prefix)) {
                return $v;
            }
        }
        return null;
    }

    private function sanitizePhotos($photos, $existing = null): array
    {
        if (!is_array($photos)) {
            return [];
        }
        $existing = is_array($existing) ? $existing : [];
        $out = [];
        foreach ($photos as $key => $value) {
            if ($key === '_markers') {
                $out[$key] = is_array($value) ? $value : [];
            } elseif ($key === '_originals') {
                $out[$key] = [];
                if (is_array($value)) {
                    foreach ($value as $slot => $orig) {
                        $out[$key][$slot] = $this->sanitizePhotoPath($orig, $existing['_originals'][$slot] ?? null);
                    }
                }
            } else {
                $out[$key] = $this->sanitizePhotoPath($value, $existing[$key] ?? null);
            }
        }
        return $out;
    }

    /**
     * Kumpulkan path file foto milik laporan (selain aset bawaan /images/doc_assets).
     */
    private function collectOwnedPhotoPaths($photos): array
    {
        if (!is_array($photos)) {
            return [];
        }
        $paths = [];
        $consider = function ($value) use (&$paths) {
            if (is_string($value) && preg_match('#^(?:https?://[^/]+)?(/(?:reports/photo|uploads/reports/photos)/[A-Za-z0-9_.-]+)$#i', $value, $m)) {
                $paths[$m[1]] = true;
            }
        };
        foreach ($photos as $key => $value) {
            if ($key === '_markers') {
                continue;
            }
            if ($key === '_originals' && is_array($value)) {
                foreach ($value as $orig) {
                    $consider($orig);
                }
            } else {
                $consider($value);
            }
        }
        return array_keys($paths);
    }

    private function deletePhotoFiles(array $paths): void
    {
        foreach ($paths as $path) {
            $name = basename($path);
            $file = str_starts_with($path, '/reports/photo/')
                ? storage_path(self::PRIVATE_PHOTO_DIR . DIRECTORY_SEPARATOR . $name)
                : public_path('uploads/reports/photos/' . $name);
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }

    /**
     * Ensure uploaded inspection photo is proportionally scaled (max 1600px)
     * and optimized to keep storage consumption minimal.
     */
    private function optimizeInspectionPhoto(string $filePath): void
    {
        if (!file_exists($filePath)) return;

        $imgType = @exif_imagetype($filePath);
        $img = match ($imgType) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($filePath),
            IMAGETYPE_PNG => @imagecreatefrompng($filePath),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($filePath) : null,
            default => null,
        };

        if (!$img) return;

        // Fix orientation for JPEG
        if ($imgType === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $exif = @exif_read_data($filePath);
            $ort = $exif['Orientation'] ?? 1;
            $rot = match ($ort) {
                3 => imagerotate($img, 180, 0),
                6 => imagerotate($img, -90, 0),
                8 => imagerotate($img, 90, 0),
                default => null,
            };
            if ($rot) {
                imagedestroy($img);
                $img = $rot;
            }
        }

        $w = imagesx($img);
        $h = imagesy($img);
        $maxPx = 1600;

        if ($w > $maxPx || $h > $maxPx) {
            $scale = min($maxPx / $w, $maxPx / $h);
            $nw = max(1, (int) round($w * $scale));
            $nh = max(1, (int) round($h * $scale));

            $dst = imagecreatetruecolor($nw, $nh);
            $white = imagecolorallocate($dst, 255, 255, 255);
            imagefilledrectangle($dst, 0, 0, $nw, $nh, $white);
            imagecopyresampled($dst, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
            imagedestroy($img);
            $img = $dst;
        }

        if ($imgType === IMAGETYPE_WEBP && function_exists('imagewebp')) {
            imagewebp($img, $filePath, 82);
        } elseif ($imgType === IMAGETYPE_PNG) {
            imagepng($img, $filePath, 6);
        } else {
            imagejpeg($img, $filePath, 82);
        }
        imagedestroy($img);
    }

    /**
     * Build sanitized download filename reflecting SSC ID, inspection type, and part name.
     */
    private function buildExportFilename(Report $report): string
    {
        $sscSafe = preg_replace('/[^A-Za-z0-9_-]/', '_', $report->ssc_id ?? 'REPORT');
        $typeSafe = strtoupper(preg_replace('/[^A-Za-z0-9_-]/', '', $report->inspection_type ?? 'PRE'));
        $partSafe = strtoupper(trim(preg_replace('/[^A-Za-z0-9 _-]/', '', $report->part_name ?? 'OUTPUT SHAFT')));
        return "{$sscSafe} CCR {$typeSafe} {$partSafe}.xlsx";
    }

    /**
     * Export report to Excel
     */
    public function export(Report $report)
    {
        $this->authorizeView($report);

        try {
            $filePath = $this->excelService->generateExcelFile($report);
            $filename = $this->buildExportFilename($report);

            ActivityLog::record('report_exported_excel', "Mengunduh Excel laporan {$report->ssc_id}", $report, [
                'part_name' => $report->part_name,
            ]);

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
        $submitAction = strtolower(trim((string) ($request->input('submit_action') ?? $request->input('action') ?? 'submit')));
        $isDraft = ($submitAction === 'draft');

        $data = $this->extractReportData($request, Report::where('ssc_id', trim((string) ($request->input('ssc_id') ?? ($request->input('general.sscId') ?? ''))))->first());
        $data['has_rejects'] = $this->checkHasRejects($data);

        $existing = Report::where('ssc_id', $data['ssc_id'])->first();
        if ($existing && !$existing->canBeModifiedBy(Auth::user())) {
            return back()->with('error', 'Anda hanya dapat mengubah laporan yang Anda buat atau periksa.');
        }

        if ($existing) {
            if ($existing->isDraft() && $submitAction === 'submit') {
                $data['review_status'] = Report::STATUS_WAITING_REVIEW;
                $data['submitted_at'] = now();
            }
            $existing->update($data);
            $report = $existing;
        } else {
            $data['created_by'] = Auth::id();
            $data['review_status'] = $isDraft ? Report::STATUS_DRAFT : Report::STATUS_WAITING_REVIEW;
            $data['submitted_at'] = $isDraft ? null : now();
            $report = Report::create($data);
        }

        $filePath = $this->excelService->generateExcelFile($report);
        $filename = $this->buildExportFilename($report);

        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ])->deleteFileAfterSend(true);
    }

    /**
     * Printable view of official FORM-QC-002 document (for direct printing or Save as PDF)
     */
    public function pdf(Report $report)
    {
        $this->authorizeView($report);

        ActivityLog::record('report_exported_pdf', "Membuka cetak / PDF laporan {$report->ssc_id}", $report, [
            'part_name' => $report->part_name,
        ]);

        return view('reports.print', [
            'report' => $report,
            'isReviewMode' => false,
        ]);
    }

    /**
     * Reviewer preview mode for approving/finishing review of a QC report
     */
    public function review(Report $report)
    {
        $this->authorizeView($report);

        $report->loadMissing(['creator:id,name', 'reviewer:id,name', 'reviewerUser:id,name']);

        return view('reports.print', [
            'report' => $report,
            'isReviewMode' => true,
        ]);
    }

    /**
     * Mark report as Reviewed by the assigned reviewer
     */
    public function finishReview(Request $request, Report $report)
    {
        $user = Auth::user();
        if ($report->isDraft()) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Laporan masih berstatus Draft dan belum disubmit.'
                ], 422);
            }
            return redirect()->route('reports.index', ['tab' => 'approval'])
                ->with('error', 'Laporan masih berstatus Draft dan belum disubmit.');
        }

        if (!$report->canBeReviewedBy($user)) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak terdaftar sebagai peninjau (Reviewed by) untuk laporan ini.'
                ], 403);
            }
            return redirect()->route('reports.index', ['tab' => 'approval'])
                ->with('error', 'Anda tidak terdaftar sebagai peninjau (Reviewed by) untuk laporan ini.');
        }

        if ($report->isReviewed()) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => "Laporan {$report->ssc_id} sudah berstatus Reviewed.",
                    'report' => $report
                ]);
            }
            return redirect()->route('reports.index', ['tab' => 'approval'])
                ->with('success', "Laporan {$report->ssc_id} sudah berstatus Reviewed.");
        }

        $signatures = is_array($report->signatures) ? $report->signatures : [];
        $signatures['reviewedBy'] = array_merge($signatures['reviewedBy'] ?? [], [
            'name' => !empty($signatures['reviewedBy']['name']) ? $signatures['reviewedBy']['name'] : $user->name,
            'date' => !empty($signatures['reviewedBy']['date']) ? $signatures['reviewedBy']['date'] : date('Y-m-d'),
        ]);

        $report->update([
            'review_status' => Report::STATUS_REVIEWED,
            'reviewed_at' => now(),
            'reviewed_by' => $user->id,
            'reviewer_id' => $report->reviewer_id ?: $user->id,
            'signatures' => $signatures,
        ]);

        ActivityLog::record('report_reviewed', "Menyelesaikan review (Finish Review) laporan {$report->ssc_id}", $report, [
            'part_name' => $report->part_name,
            'status' => Report::STATUS_REVIEWED,
            'creator' => $report->creator?->name,
        ]);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => "Laporan {$report->ssc_id} berhasil diselesaikan (Reviewed)!",
                'report' => $report->fresh(),
            ]);
        }

        return redirect()->route('reports.index', ['tab' => 'approval'])
            ->with('success', "Laporan {$report->ssc_id} berhasil diselesaikan (Reviewed)!");
    }

    /**
     * Extract report payload safely
     */
    private function extractReportData(Request $request, ?Report $existing = null): array
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
        $photos = $this->sanitizePhotos($request->input('photos', []), $existing?->photos);

        $rawCheckerId = $request->input('checker_id') ?? ($g['checkerId'] ?? ($g['checker_id'] ?? null));
        $resolvedCheckerId = null;
        if (is_numeric($rawCheckerId)) {
            $resolvedCheckerId = User::where('id', (int) $rawCheckerId)->value('id');
        }
        if (!$resolvedCheckerId && !empty($signatures['checkedBy']['name'])) {
            $checkerName = mb_strtolower(trim((string) $signatures['checkedBy']['name']));
            if ($checkerName !== '') {
                $resolvedCheckerId = User::whereRaw('LOWER(TRIM(name)) = ?', [$checkerName])->value('id');
            }
        }

        $rawReviewerId = $request->input('reviewer_id') ?? ($g['reviewerId'] ?? ($g['reviewer_id'] ?? null));
        $resolvedReviewerId = null;
        if (is_numeric($rawReviewerId)) {
            $resolvedReviewerId = User::where('id', (int) $rawReviewerId)->value('id');
        }
        if (!$resolvedReviewerId && !empty($signatures['reviewedBy']['name'])) {
            $reviewerName = mb_strtolower(trim((string) $signatures['reviewedBy']['name']));
            if ($reviewerName !== '') {
                $resolvedReviewerId = User::whereRaw('LOWER(TRIM(name)) = ?', [$reviewerName])->value('id');
            }
        }

        return [
            'checker_id' => $resolvedCheckerId,
            'reviewer_id' => $resolvedReviewerId,
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
