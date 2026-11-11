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
            'report_submitted' => ['label' => __('qc.activity.report_submitted'), 'tone' => 'warning', 'icon' => 'send'],
            'report_reviewed' => ['label' => __('qc.activity.report_reviewed'), 'tone' => 'success', 'icon' => 'check-circle-2'],
            'report_draft_created', 'report_draft_updated' => ['label' => __('qc.activity.report_draft'), 'tone' => 'slate', 'icon' => 'file-clock'],
            'report_updated' => ['label' => __('qc.activity.report_updated'), 'tone' => 'primary', 'icon' => 'edit-3'],
            'report_deleted' => ['label' => __('qc.activity.report_deleted'), 'tone' => 'danger', 'icon' => 'trash-2'],
            'report_exported_excel' => ['label' => __('qc.activity.report_exported_excel'), 'tone' => 'success', 'icon' => 'file-spreadsheet'],
            'report_exported_pdf' => ['label' => __('qc.activity.report_exported_pdf'), 'tone' => 'primary', 'icon' => 'printer'],
            'user_created', 'user_updated', 'user_deleted' => ['label' => __('qc.activity.user_manage'), 'tone' => 'primary', 'icon' => 'user-cog'],
            'preferences_updated' => ['label' => __('qc.activity.preferences_updated'), 'tone' => 'primary', 'icon' => 'sliders-horizontal'],
            'login' => ['label' => __('qc.activity.login'), 'tone' => 'primary', 'icon' => 'log-in'],
            'logout' => ['label' => __('qc.activity.logout'), 'tone' => 'slate', 'icon' => 'log-out'],
            default => ['label' => ucfirst(str_replace('_', ' ', $this->action)), 'tone' => 'slate', 'icon' => 'activity'],
        };
    }

    /**
     * Localized description matching active application locale.
     */
    public function localizedDescription(): string
    {
        $ssc = $this->subject_label ?? ($this->report?->ssc_id ?? '-');
        $roleKey = $this->meta['role'] ?? null;
        $roleName = $roleKey ? (User::roleLabels()[$roleKey] ?? $roleKey) : '';

        return match ($this->action) {
            'report_draft_created' => __('qc.activity.desc_draft_created', ['ssc' => $ssc]),
            'report_draft_updated' => __('qc.activity.desc_draft_updated', ['ssc' => $ssc]),
            'report_submitted' => __('qc.activity.desc_submitted', ['ssc' => $ssc]),
            'report_updated' => __('qc.activity.desc_updated', ['ssc' => $ssc]),
            'report_deleted' => __('qc.activity.desc_deleted', ['ssc' => $ssc]),
            'report_reviewed' => __('qc.activity.desc_reviewed', ['ssc' => $ssc]),
            'report_exported_excel' => __('qc.activity.desc_exported_excel', ['ssc' => $ssc]),
            'report_exported_pdf' => __('qc.activity.desc_exported_pdf', ['ssc' => $ssc]),
            'login' => __('qc.activity.desc_login'),
            'logout' => __('qc.activity.desc_logout'),
            'user_created' => __('qc.activity.desc_user_created', ['name' => $ssc, 'role' => $roleName]),
            'user_updated' => __('qc.activity.desc_user_updated', ['name' => $ssc, 'role' => $roleName]),
            'user_deleted' => __('qc.activity.desc_user_deleted', ['name' => $ssc]),
            'preferences_updated' => __('qc.activity.desc_preferences_updated'),
            default => $this->description,
        };
    }
}

