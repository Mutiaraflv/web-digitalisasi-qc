<?php

namespace App\Console\Commands;

use App\Http\Controllers\ReportController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Memindahkan foto laporan LAMA ke penyimpanan privat dan mengubah data base64
 * (yang pernah tersimpan di kolom photos) menjadi file.
 *
 * Default: simulasi (dry-run). Tambahkan --apply untuk benar-benar memproses.
 * Lakukan BACKUP database + folder public/uploads/reports/photos sebelum --apply.
 */
class PhotosMigratePrivate extends Command
{
    protected $signature = 'photos:migrate-private {--apply : Jalankan perubahan sesungguhnya (default: simulasi)}';

    protected $description = 'Pindahkan foto laporan lama ke storage privat & ubah foto base64 menjadi file';

    /** @var array<string,string> path lama => path baru (agar file yang dipakai 2x hanya dipindah sekali) */
    private array $moved = [];

    /** @var string[] file publik yang dihapus setelah DB berhasil diperbarui */
    private array $toUnlink = [];

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');
        $privateDir = storage_path(ReportController::PRIVATE_PHOTO_DIR);
        if ($apply && !File::isDirectory($privateDir)) {
            File::makeDirectory($privateDir, 0755, true, true);
        }

        $reportsChanged = 0;
        $filesMoved = 0;
        $base64Converted = 0;

        DB::table('reports')->select('id', 'photos')->orderBy('id')->chunkById(50, function ($rows) use (&$reportsChanged, &$filesMoved, &$base64Converted, $apply, $privateDir) {
            foreach ($rows as $row) {
                $photos = is_string($row->photos) ? json_decode($row->photos, true) : $row->photos;
                if (!is_array($photos)) {
                    continue;
                }

                $changed = false;
                $this->toUnlink = [];

                $convert = function ($value) use (&$changed, &$filesMoved, &$base64Converted, $apply, $privateDir, $row) {
                    if (!is_string($value) || $value === '') {
                        return $value;
                    }

                    // 1) base64 -> file
                    if (preg_match('/^data:image\/(png|jpe?g|webp);base64,(.+)$/is', $value, $m)) {
                        $bin = base64_decode($m[2], true);
                        if ($bin === false) {
                            return $value;
                        }
                        $ext = strtolower($m[1]) === 'png' ? 'png' : (strtolower($m[1]) === 'webp' ? 'webp' : 'jpg');
                        $name = 'qc_legacy_' . $row->id . '_' . substr(sha1($bin), 0, 16) . '.' . $ext;
                        $new = '/reports/photo/' . $name;
                        if ($apply && !is_file($privateDir . DIRECTORY_SEPARATOR . $name)) {
                            file_put_contents($privateDir . DIRECTORY_SEPARATOR . $name, $bin);
                        }
                        $base64Converted++;
                        $changed = true;
                        return $new;
                    }

                    // 2) file publik lama -> privat
                    if (preg_match('#^(?:https?://[^/]+)?/uploads/reports/photos/([A-Za-z0-9_.-]+\.(?:jpe?g|png|webp))$#i', $value, $m)) {
                        $name = $m[1];
                        $old = '/uploads/reports/photos/' . $name;
                        $src = public_path('uploads/reports/photos/' . $name);
                        $new = '/reports/photo/' . $name;

                        if (isset($this->moved[$old])) {
                            $changed = true;
                            return $this->moved[$old];
                        }
                        if (!is_file($src)) {
                            return $value; // file sudah hilang: biarkan apa adanya
                        }
                        if ($apply) {
                            copy($src, $privateDir . DIRECTORY_SEPARATOR . $name);
                            $this->toUnlink[] = $src;
                        }
                        $this->moved[$old] = $new;
                        $filesMoved++;
                        $changed = true;
                        return $new;
                    }

                    return $value;
                };

                foreach ($photos as $key => $value) {
                    if ($key === '_markers') {
                        continue;
                    }
                    if ($key === '_originals' && is_array($value)) {
                        foreach ($value as $slot => $orig) {
                            $photos[$key][$slot] = $convert($orig);
                        }
                    } else {
                        $photos[$key] = $convert($value);
                    }
                }

                if ($changed) {
                    $reportsChanged++;
                    if ($apply) {
                        DB::table('reports')->where('id', $row->id)->update([
                            'photos' => json_encode($photos, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                        ]);
                        // File publik hanya dihapus setelah DB sukses diperbarui
                        foreach (array_unique($this->toUnlink) as $f) {
                            @unlink($f);
                        }
                    }
                }
            }
        });

        $mode = $apply ? 'DITERAPKAN' : 'SIMULASI (tidak ada yang diubah, gunakan --apply)';
        $this->info("[{$mode}] laporan terdampak: {$reportsChanged}, file dipindah: {$filesMoved}, base64 jadi file: {$base64Converted}");

        return self::SUCCESS;
    }
}
