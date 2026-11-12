<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const DISPLAY_TZ = 'Asia/Jakarta';

    public function index(): View
    {
        $payload = $this->buildDashboardData(Auth::user());

        return view('dashboard', $payload);
    }

    public function liveData(Request $request): JsonResponse
    {
        User::touchPresence(Auth::user(), $request);
        $payload = $this->buildDashboardData(Auth::user());

        return response()
            ->json([
                'success' => true,
                'server_time' => now()->timezone(self::DISPLAY_TZ)->format('H:i:s') . ' WIB',
                'data' => $payload,
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    /**
     * Lightweight endpoint for global tab presence heartbeats.
     */
    public function heartbeat(Request $request): JsonResponse
    {
        User::touchPresence(Auth::user(), $request);

        return response()
            ->json([
                'ok' => true,
                'server_time' => now()->timezone(self::DISPLAY_TZ)->format('H:i:s') . ' WIB',
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    /**
     * Build shared dashboard chart metrics and role-scoped activity payload.
     *
     * @return array<string, mixed>
     */
    private function buildDashboardData(?User $user): array
    {
        $isAdmin = $user ? $user->isAdmin() : false;

        try {
            $totalReports = Report::nonDraft()->count();
            $acceptCount = Report::nonDraft()->where('has_rejects', false)->count();
            $waitingCount = Report::where('review_status', Report::STATUS_WAITING_REVIEW)->count();
            $reviewedCount = Report::where('review_status', Report::STATUS_REVIEWED)->count();
            $draftCount = Report::where('review_status', Report::STATUS_DRAFT)->count();
            $preCount = Report::nonDraft()->where('inspection_type', 'Pre')->count();
            $postCount = Report::nonDraft()->where('inspection_type', 'Post')->count();
            $myDraftCount = $user ? Report::ownedOrCheckedBy($user)->where('review_status', Report::STATUS_DRAFT)->count() : 0;
            $myPendingApprovalCount = $user ? Report::pendingApprovalFor($user)->count() : 0;
        } catch (\Throwable $e) {
            $totalReports = 0;
            $acceptCount = 0;
            $waitingCount = 0;
            $reviewedCount = 0;
            $draftCount = 0;
            $preCount = 0;
            $postCount = 0;
            $myDraftCount = 0;
            $myPendingApprovalCount = 0;
        }

        $yieldRate = $totalReports > 0 ? round(($acceptCount / $totalReports) * 100, 1) : 100;
        $presence = User::getOnlinePresenceSummary();

        $charts = [
            'totalReports' => $totalReports,
            'acceptCount' => $acceptCount,
            'yieldRate' => $yieldRate,
            'waitingCount' => $waitingCount,
            'reviewedCount' => $reviewedCount,
            'draftCount' => $draftCount,
            'preCount' => $preCount,
            'postCount' => $postCount,
            'myDraftCount' => $myDraftCount,
            'totalUsers' => $presence['totalUsers'],
            'onlineCount' => $presence['onlineCount'],
            'offlineCount' => $presence['offlineCount'],
            'onlineRate' => $presence['onlineRate'],
            'onlineByRole' => $presence['onlineByRole'],
            'onlineUsers' => $presence['onlineUsers'],
        ];

        $adminLogs = [];
        $myReportTimeline = [];
        $myActivityLogs = [];

        if ($isAdmin) {
            $adminLogs = $this->getAdminActivityFeed();
        } elseif ($user) {
            $myReportTimeline = $this->getUserReportTimeline($user);
            $myActivityLogs = $this->getUserOwnActivityLogs($user);
        }

        return [
            'isAdmin' => $isAdmin,
            'charts' => $charts,
            'totalReports' => $totalReports,
            'acceptCount' => $acceptCount,
            'yieldRate' => $yieldRate,
            'waitingCount' => $waitingCount,
            'reviewedCount' => $reviewedCount,
            'draftCount' => $draftCount,
            'preCount' => $preCount,
            'postCount' => $postCount,
            'myDraftCount' => $myDraftCount,
            'myPendingApprovalCount' => $myPendingApprovalCount,
            'totalUsers' => $presence['totalUsers'],
            'onlineCount' => $presence['onlineCount'],
            'offlineCount' => $presence['offlineCount'],
            'onlineRate' => $presence['onlineRate'],
            'onlineByRole' => $presence['onlineByRole'],
            'onlineUsers' => $presence['onlineUsers'],
            'adminLogs' => $adminLogs,
            'myReportTimeline' => $myReportTimeline,
            'myActivityLogs' => $myActivityLogs,
        ];
    }

    /**
     * Global real-time activity feed strictly for Administrators.
     *
     * @return array<int, array<string, mixed>>
     */
    private function getAdminActivityFeed(): array
    {
        if (!Schema::hasTable('activity_logs')) {
            return [];
        }

        $logs = ActivityLog::with([
            'user:id,name,role',
            'report:id,ssc_id,part_name,review_status',
        ])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->take(25)
            ->get();

        return $logs->map(fn (ActivityLog $log) => $this->formatActivityLog($log))->all();
    }

    /**
     * Concise report lifecycle timeline strictly for non-admin account.
     *
     * @return array<int, array<string, mixed>>
     */
    private function getUserReportTimeline(User $user): array
    {
        $userName = mb_strtolower(trim($user->name));

        $reports = Report::with(['creator:id,name', 'checker:id,name', 'reviewer:id,name', 'reviewerUser:id,name'])
            ->where(function ($q) use ($user, $userName) {
                $q->where('created_by', $user->id)
                    ->orWhere('checker_id', $user->id)
                    ->orWhere('reviewer_id', $user->id)
                    ->orWhere(function ($sub) use ($userName) {
                        $sub->whereNull('checker_id')
                            ->whereRaw("LOWER(TRIM(JSON_UNQUOTE(JSON_EXTRACT(signatures, '$.checkedBy.name')))) = ?", [$userName]);
                    })
                    ->orWhere(function ($sub) use ($userName) {
                        $sub->whereNull('reviewer_id')
                            ->whereRaw("LOWER(TRIM(JSON_UNQUOTE(JSON_EXTRACT(signatures, '$.reviewedBy.name')))) = ?", [$userName]);
                    });
            })
            ->orderByDesc('updated_at')
            ->take(10)
            ->get();

        return $reports->map(function (Report $report) use ($user): array {
            $roleInReport = __('qc.dashboard.role_related');
            if ((int) $report->created_by === (int) $user->id) {
                $roleInReport = __('qc.dashboard.role_creator');
            } elseif ((int) $report->checker_id === (int) $user->id) {
                $roleInReport = __('qc.dashboard.role_checker');
            } elseif ($report->canBeReviewedBy($user)) {
                $roleInReport = __('qc.dashboard.role_reviewer');
            }

            $submittedAt = $report->submitted_at
                ? $report->submitted_at->copy()->timezone(self::DISPLAY_TZ)->format('d M Y, H:i') . ' WIB'
                : null;
            $reviewedAt = $report->reviewed_at
                ? $report->reviewed_at->copy()->timezone(self::DISPLAY_TZ)->format('d M Y, H:i') . ' WIB'
                : null;
            $createdAt = $report->created_at
                ? $report->created_at->copy()->timezone(self::DISPLAY_TZ)->format('d M Y, H:i') . ' WIB'
                : '-';

            $reviewerName = $report->reviewerUser->name
                ?? ($report->reviewer->name
                ?? ($report->signatures['reviewedBy']['name'] ?? '-'));

            $actionUrl = route('reports.pdf', $report->id);
            if ($report->isWaitingReview() && $report->canBeReviewedBy($user)) {
                $actionUrl = route('reports.review', $report->id);
            } elseif ($report->canBeModifiedBy($user)) {
                $actionUrl = route('reports.edit', $report->id);
            }

            return [
                'id' => $report->id,
                'ssc_id' => $report->ssc_id,
                'part_name' => $report->part_name,
                'inspection_type' => $report->inspection_type,
                'review_status' => $report->review_status,
                'role_in_report' => $roleInReport,
                'created_at_str' => $createdAt,
                'submitted_at_str' => $submittedAt,
                'reviewed_at_str' => $reviewedAt,
                'reviewer_name' => $reviewerName ?: '-',
                'action_url' => $actionUrl,
            ];
        })->all();
    }

    /**
     * Personal recent actions strictly for the logged-in non-admin user (and review completions on their own reports).
     *
     * @return array<int, array<string, mixed>>
     */
    private function getUserOwnActivityLogs(User $user): array
    {
        if (!Schema::hasTable('activity_logs')) {
            return [];
        }

        $ownReportIds = Report::ownedOrCheckedBy($user)->pluck('id')->all();

        $logs = ActivityLog::with(['user:id,name,role', 'report:id,ssc_id,part_name,review_status'])
            ->where(function ($q) use ($user, $ownReportIds) {
                $q->where('user_id', $user->id);
                if (!empty($ownReportIds)) {
                    $q->orWhere(function ($sub) use ($ownReportIds) {
                        $sub->whereIn('report_id', $ownReportIds)
                            ->where('action', 'report_reviewed');
                    });
                }
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->take(12)
            ->get();

        return $logs->map(fn (ActivityLog $log) => $this->formatActivityLog($log))->all();
    }

    /**
     * Format single ActivityLog model into UI-ready array.
     *
     * @return array<string, mixed>
     */
    private function formatActivityLog(ActivityLog $log): array
    {
        $userName = $log->user->name ?? 'Sistem';
        $roleLabel = $log->user ? $log->user->roleLabel() : 'Sistem';
        $roleKey = $log->user->role ?? 'system';

        $dt = $log->created_at ? Carbon::parse($log->created_at)->timezone(self::DISPLAY_TZ) : null;
        if ($dt) {
            $dt->locale(app()->getLocale());
        }

        return [
            'id' => $log->id,
            'user_id' => $log->user_id,
            'user_name' => $userName,
            'user_initials' => $log->user ? $log->user->initials() : strtoupper(substr($userName, 0, 2)),
            'avatar_url' => $log->user?->avatar_url,
            'user_role' => $roleLabel,
            'role_key' => $roleKey,
            'action' => $log->action,
            'badge' => $log->actionBadge(),
            'subject_label' => $log->subject_label,
            'description' => $log->localizedDescription(),
            'part_name' => $log->meta['part_name'] ?? ($log->report->part_name ?? null),
            'time_formatted' => $dt ? $dt->format('d M Y, H:i:s') . ' WIB' : '-',
            'time_short' => $dt ? $dt->format('d M, H:i') : '-',
            'time_relative' => $dt ? $dt->diffForHumans() : '',
            'report_url' => $log->report_id ? route('reports.pdf', $log->report_id) : null,
        ];
    }
}
