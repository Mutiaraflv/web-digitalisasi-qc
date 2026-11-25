<?php

namespace App\Console\Commands;

use App\Http\Controllers\ReportController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Menghapus file foto yatim (tidak dipakai laporan manapun) yang lebih tua dari
 * batas jam tertentu. Batas jam melindungi foto yang baru diunggah untuk draf
 * yang belum sempat tersimpan.
 *
 * Default: simulasi. Tambahkan --apply untuk menghapus.
 */
class PhotosCleanup extends Command
{
    protected $signature = 'photos:cleanup {--apply : Hapus file yatim (default: simulasi)} {--hours=48 : Hanya file yang lebih tua dari N jam}';

    protected $description = 'Hapus foto laporan yatim yang tidak lagi dirujuk oleh laporan manapun';

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');
        $cutoff = time() - max(1, (int) $this->option('hours')) * 3600;

        // Kumpulkan semua nama file yang masih dirujuk oleh laporan (foto & tanda tangan)
        $referenced = [];
        DB::table('reports')->select('id', 'photos', 'signatures')->orderBy('id')->chunkById(100, function ($rows) use (&$referenced) {
            foreach ($rows as $row) {
                $payload = ($row->photos ?? '') . ' ' . ($row->signatures ?? '');
                if (trim($payload) === '') {
                    continue;
                }
                if (preg_match_all('/[A-Za-z0-9_.\-]+\.(?:jpe?g|png|webp)/i', $payload, $m)) {
                    foreach ($m[0] as $name) {
                        $referenced[strtolower($name)] = true;
                    }
                }
            }
        });

        $dirs = [
            storage_path(ReportController::PRIVATE_PHOTO_DIR),
            public_path('uploads/reports/photos'),
        ];

        $orphans = 0;
        $bytes = 0;
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                continue;
            }
            foreach (new \DirectoryIterator($dir) as $file) {
                if (!$file->isFile() || !preg_match('/\.(jpe?g|png|webp)$/i', $file->getFilename())) {
                    continue;
                }
                if (isset($referenced[strtolower($file->getFilename())]) || $file->getMTime() > $cutoff) {
                    continue;
                }
                $orphans++;
                $bytes += $file->getSize();
                if ($apply) {
                    @unlink($file->getPathname());
                } else {
                    $this->line('yatim: ' . $file->getPathname());
                }
            }
        }

        $mb = number_format($bytes / 1048576, 2);
        $this->info(($apply ? 'Dihapus' : 'Simulasi') . ": {$orphans} file yatim ({$mb} MB)");

        return self::SUCCESS;
    }
}
