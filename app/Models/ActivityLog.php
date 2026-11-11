<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ActivityLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'report_id',
        'action',
        'subject_label',
        'description',
        'meta',
        'created_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'report_id');
    }

    /**
     * Record a user/system activity safely without interrupting primary workflow.
     */
    public static function record(
        string $action,
        string $description,
        ?Report $report = null,
        array $meta = [],
        ?int $userId = null
    ): ?self {
        try {
            if (!Schema::hasTable('activity_logs')) {
                return null;
            }

            return self::create([
                'user_id' => $userId ?? Auth::id(),
                'report_id' => $report?->id,
                'action' => $action,
                'subject_label' => $meta['subject'] ?? $report?->ssc_id,
                'description' => $description,
                'meta' => empty($meta) ? null : $meta,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Action display badge metadata.
     *
     * @return array{label: string, tone: string, icon: string}
     */
    public function actionBadge(): array
    {
        return match ($this->action) {
            'report_submitted' => ['label' => 'Submit Pengajuan', 'tone' => 'warning', 'icon' => 'send'],
            'report_reviewed' => ['label' => 'Finish Review', 'tone' => 'success', 'icon' => 'check-circle-2'],
            'report_draft_created', 'report_draft_updated' => ['label' => 'Simpan Draft', 'tone' => 'slate', 'icon' => 'file-clock'],
            'report_updated' => ['label' => 'Edit Laporan', 'tone' => 'primary', 'icon' => 'edit-3'],
            'report_deleted' => ['label' => 'Hapus Laporan', 'tone' => 'danger', 'icon' => 'trash-2'],
            'report_exported_excel' => ['label' => 'Unduh Excel', 'tone' => 'success', 'icon' => 'file-spreadsheet'],
            'report_exported_pdf' => ['label' => 'Cetak / PDF', 'tone' => 'primary', 'icon' => 'printer'],
            'user_created', 'user_updated', 'user_deleted' => ['label' => 'Kelola Akun', 'tone' => 'primary', 'icon' => 'user-cog'],
            'login' => ['label' => 'Login', 'tone' => 'primary', 'icon' => 'log-in'],
            'logout' => ['label' => 'Logout', 'tone' => 'slate', 'icon' => 'log-out'],
            default => ['label' => ucfirst(str_replace('_', ' ', $this->action)), 'tone' => 'slate', 'icon' => 'activity'],
        };
    }
}

