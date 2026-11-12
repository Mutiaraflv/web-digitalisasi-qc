@extends('layouts.app')

@section('title', __('qc.dashboard.title'))
@section('page_title', __('qc.dashboard.heading'))

@section('content')
  @php
    $hourWib = now()->timezone('Asia/Jakarta')->hour;
    $fallbackGreeting = match (true) {
      $hourWib >= 4 && $hourWib < 11 => __('qc.dashboard.greeting_morning'),
      $hourWib >= 11 && $hourWib < 15 => __('qc.dashboard.greeting_afternoon'),
      $hourWib >= 15 && $hourWib < 18 => __('qc.dashboard.greeting_evening'),
      default => __('qc.dashboard.greeting_night'),
    };
    $accountName = auth()->user()?->name ?? '';
  @endphp

  <!-- Greeting Bar (Syncs immediately with device local clock) -->
  <section class="dash-welcome-bar" id="dashWelcomeBar" aria-label="{{ __('qc.dashboard.title') }}">
    <div class="dash-welcome-main">
      <h1 class="dash-welcome-title">
        <span id="dashGreetingWord">{{ $fallbackGreeting }}</span>@if($accountName !== ''), <span class="dash-welcome-name">{{ $accountName }}</span>@endif
      </h1>
      <p class="dash-welcome-sub">{{ __('qc.dashboard.welcome_subtitle') }}</p>
    </div>
    <a href="{{ route('inspection.create') }}" class="btn btn-primary btn-sm dash-welcome-cta">
      <i data-lucide="file-plus-2"></i> {{ __('qc.dashboard.quick_create') }}
    </a>
  </section>
  @php
    $greetI18n = [
      'morning' => __('qc.dashboard.greeting_morning'),
      'afternoon' => __('qc.dashboard.greeting_afternoon'),
      'evening' => __('qc.dashboard.greeting_evening'),
      'night' => __('qc.dashboard.greeting_night'),
    ];
  @endphp
  <script>
    (function () {
      const i18n = @json($greetI18n);

      function updateDeviceGreeting() {
        const hour = new Date().getHours();
        let word = i18n.night;
        if (hour >= 4 && hour < 11) {
          word = i18n.morning;
        } else if (hour >= 11 && hour < 15) {
          word = i18n.afternoon;
        } else if (hour >= 15 && hour < 18) {
          word = i18n.evening;
        }

        const greetEl = document.getElementById('dashGreetingWord');
        if (greetEl && greetEl.textContent !== word) {
          greetEl.textContent = word;
        }
      }

      updateDeviceGreeting();
      setInterval(updateDeviceGreeting, 30000);
    })();
  </script>

  <!-- 3 Interactive Visual Charts (Clean & Focused) -->
  <div class="dashboard-charts-grid">
    <!-- Chart 1: Total Inspeksi (Bar Chart) -->
    <div class="chart-card">
      <div class="chart-card-head">
        <div>
          <span class="chart-kicker">{{ __('qc.dashboard.total_inspections') }}</span>
          <h2 class="chart-metric-val" id="chartValTotal">{{ $totalReports }}</h2>
        </div>
      </div>

      <div class="bar-chart-wrap" id="barChartArea" aria-label="{{ __('qc.dashboard.total_inspections') }}">
        @php
          $barMax = max(1, $preCount, $postCount, $draftCount);
          $bars = [
            ['key' => 'pre', 'label' => __('qc.dashboard.bar_pre'), 'value' => $preCount, 'color' => '#4f46e5'],
            ['key' => 'post', 'label' => __('qc.dashboard.bar_post'), 'value' => $postCount, 'color' => '#6366f1'],
            ['key' => 'draft', 'label' => __('qc.dashboard.bar_draft'), 'value' => $draftCount, 'color' => '#f59e0b'],
          ];
        @endphp
        @foreach($bars as $bar)
          @php $pct = round(($bar['value'] / $barMax) * 100, 1); @endphp
          <div class="bar-col" data-bar-key="{{ $bar['key'] }}" title="{{ $bar['label'] }}: {{ $bar['value'] }}">
            <span class="bar-val">{{ $bar['value'] }}</span>
            <div class="bar-track">
              <div class="bar-fill" style="height: {{ $bar['value'] > 0 ? max(10, $pct) : 4 }}%; background: {{ $bar['color'] }};"></div>
            </div>
            <span class="bar-lbl">{{ $bar['label'] }}</span>
          </div>
        @endforeach
      </div>
    </div>

    <!-- Chart 2: Lolos (Accept) & Status Pie / Donut Chart -->
    <div class="chart-card">
      <div class="chart-card-head">
        <div>
          <span class="chart-kicker">{{ __('qc.dashboard.passed_accept') }}</span>
          <h2 class="chart-metric-val" style="color: var(--success);" id="chartValAccept">{{ $acceptCount }}</h2>
        </div>
      </div>

      <div class="donut-chart-layout">
        <div class="donut-svg-box">
          <svg viewBox="0 0 120 120" class="donut-svg" id="donutChartSvg" role="img" aria-label="{{ __('qc.dashboard.passed_accept') }}">
            <circle cx="60" cy="60" r="44" fill="none" class="chart-ring-track" stroke-width="14"></circle>
            <g id="donutSegments"></g>
          </svg>
          <div class="donut-center">
            <strong id="donutCenterVal">{{ $acceptCount }}</strong>
          </div>
        </div>
        <div class="donut-legend" id="donutLegend"></div>
      </div>
    </div>

    <!-- Chart 3: Akun Aktif / Online (Real-time Presence Ring) -->
    <div class="chart-card">
      <div class="chart-card-head">
        <div>
          <span class="chart-kicker">{{ __('qc.dashboard.active_accounts') }}</span>
          <div class="chart-metric-row">
            <h2 class="chart-metric-val" style="color: var(--success);" id="chartValOnline">{{ $onlineCount }}</h2>
            <span class="chart-metric-sub" id="chartSubTotalUsers">/ {{ $totalUsers }}</span>
          </div>
        </div>
        <div class="chart-presence-head-right">
          <span class="live-status-pill" id="dashPresenceLivePill" title="{{ __('qc.dashboard.live_badge') }}">
            <span class="live-pulse-dot" aria-hidden="true"></span>
            <span>{{ __('qc.dashboard.live_badge') }}</span>
          </span>
          <div class="live-avatar-stack" id="onlineAvatarStack" aria-label="{{ __('qc.dashboard.active_accounts') }}">
            @forelse (array_slice($onlineUsers, 0, 4) as $uChip)
              @php $chipRoleClass = 'role-' . ($uChip['role'] ?? 'user'); @endphp
              <span class="live-avatar-chip {{ $chipRoleClass }}" title="{{ $uChip['name'] }} ({{ $uChip['role_label'] }}) • {{ __('qc.dashboard.online_status') }}">
                @if (! empty($uChip['avatar_url']))
                  <img src="{{ $uChip['avatar_url'] }}" alt="{{ $uChip['initials'] }}">
                @else
                  {{ $uChip['initials'] }}
                @endif
              </span>
            @empty
              <span class="chart-metric-sub">{{ __('qc.dashboard.no_other_online') }}</span>
            @endforelse
            @if ($onlineCount > 4)
              <span class="live-avatar-chip live-avatar-overflow" title="+{{ $onlineCount - 4 }} {{ __('qc.dashboard.online_status') }}">+{{ $onlineCount - 4 }}</span>
            @endif
          </div>
        </div>
      </div>

      <div class="donut-chart-layout">
        <div class="donut-svg-box">
          @php
            $prR = 44;
            $prCirc = 2 * pi() * $prR;
            $prDenom = max(1, $totalUsers, $onlineCount);
            $adminOnline = (int) ($onlineByRole[\App\Models\User::ROLE_ADMIN] ?? 0);
            $leaderOnline = (int) ($onlineByRole[\App\Models\User::ROLE_QC_LEADER] ?? 0);
            $staffOnline = (int) ($onlineByRole[\App\Models\User::ROLE_USER] ?? 0);
            $adminLen = ($adminOnline / $prDenom) * $prCirc;
            $leaderLen = ($leaderOnline / $prDenom) * $prCirc;
            $staffLen = ($staffOnline / $prDenom) * $prCirc;
          @endphp
          <svg viewBox="0 0 120 120" class="donut-svg" id="presenceDonutSvg" role="img" aria-label="{{ __('qc.dashboard.active_accounts') }}">
            <circle cx="60" cy="60" r="44" fill="none" class="chart-ring-track" stroke-width="14"></circle>
            <circle id="presenceSegAdmin"
                    cx="60" cy="60" r="44"
                    fill="none"
                    stroke="#6366f1"
                    stroke-width="14"
                    stroke-linecap="butt"
                    stroke-dasharray="{{ round($adminLen, 2) }} {{ round($prCirc, 2) }}"
                    stroke-dashoffset="0"
                    style="opacity: {{ $adminOnline > 0 ? 1 : 0 }};"
                    transform="rotate(-90 60 60)"></circle>
            <circle id="presenceSegLeader"
                    cx="60" cy="60" r="44"
                    fill="none"
                    stroke="#f59e0b"
                    stroke-width="14"
                    stroke-linecap="butt"
                    stroke-dasharray="{{ round($leaderLen, 2) }} {{ round($prCirc, 2) }}"
                    stroke-dashoffset="{{ round(-$adminLen, 2) }}"
                    style="opacity: {{ $leaderOnline > 0 ? 1 : 0 }};"
                    transform="rotate(-90 60 60)"></circle>
            <circle id="presenceSegStaff"
                    cx="60" cy="60" r="44"
                    fill="none"
                    stroke="#10b981"
                    stroke-width="14"
                    stroke-linecap="butt"
                    stroke-dasharray="{{ round($staffLen, 2) }} {{ round($prCirc, 2) }}"
                    stroke-dashoffset="{{ round(-($adminLen + $leaderLen), 2) }}"
                    style="opacity: {{ $staffOnline > 0 ? 1 : 0 }};"
                    transform="rotate(-90 60 60)"></circle>
          </svg>
          <div class="donut-center" style="display:flex; flex-direction:column; align-items:center; justify-content:center; line-height:1.05;">
            <strong id="presenceCenterCount">{{ $onlineCount }}</strong>
            <span style="font-size:0.56rem; font-weight:700; color:var(--text-muted, #64748b); text-transform:uppercase; letter-spacing:0.04em; margin-top:2px;">{{ __('qc.dashboard.online_status') }}</span>
          </div>
        </div>

        <div class="donut-legend presence-legend-list" id="presenceRoleList">
          <div class="donut-legend-row">
            <span class="donut-dot" style="background: #6366f1;"></span>
            <span class="donut-lbl">{{ __('qc.dashboard.role_admin') }}</span>
            <strong class="donut-num" id="presenceRoleAdmin">{{ $adminOnline }}</strong>
          </div>
          <div class="donut-legend-row">
            <span class="donut-dot" style="background: #f59e0b;"></span>
            <span class="donut-lbl">{{ __('qc.dashboard.role_leader') }}</span>
            <strong class="donut-num" id="presenceRoleLeader">{{ $leaderOnline }}</strong>
          </div>
          <div class="donut-legend-row">
            <span class="donut-dot" style="background: #10b981;"></span>
            <span class="donut-lbl">{{ __('qc.dashboard.role_staff') }}</span>
            <strong class="donut-num" id="presenceRoleStaff">{{ $staffOnline }}</strong>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 2-Column Activity & Quick Operational Menu -->
  <div class="dashboard-grid-2">
    @if($isAdmin)
      <!-- ADMIN VIEW: Real-Time All-Account Activity Log -->
      <div class="card activity-panel-card">
        <div class="card-header activity-card-header">
          <div class="activity-header-title">
            <h3>{{ __('qc.dashboard.realtime_log_title') }}</h3>
          </div>
          <div class="activity-filter-group" id="adminLogFilters">
            <button type="button" class="activity-filter-btn active" data-filter="all">{{ __('qc.dashboard.filter_all') }}</button>
            <button type="button" class="activity-filter-btn" data-filter="submit">{{ __('qc.dashboard.filter_submit') }}</button>
            <button type="button" class="activity-filter-btn" data-filter="review">{{ __('qc.dashboard.filter_review') }}</button>
            <button type="button" class="activity-filter-btn" data-filter="auth">{{ __('qc.dashboard.filter_auth') }}</button>
          </div>
        </div>

        <div class="activity-feed" id="adminActivityFeed">
          @forelse($adminLogs as $log)
            @php
              $toneClass = match($log['badge']['tone']) {
                'success' => 'badge-green',
                'warning' => 'badge-orange',
                'danger' => 'badge-red',
                'primary' => 'badge-blue',
                default => 'badge-slate',
              };
            @endphp
            <div class="activity-item" data-log-id="{{ $log['id'] }}" data-action="{{ $log['action'] }}">
              <div class="activity-avatar role-{{ $log['role_key'] }}" style="overflow: hidden;">
                @if(!empty($log['avatar_url']))
                  <img src="{{ $log['avatar_url'] }}" alt="{{ $log['user_name'] }}" class="user-avatar-img">
                @else
                  {{ $log['user_initials'] }}
                @endif
              </div>
              <div class="activity-content">
                <div class="activity-top">
                  <span class="activity-user">{{ $log['user_name'] }}</span>
                  <span class="activity-user-role">{{ $log['user_role'] }}</span>
                  <span class="badge {{ $toneClass }}">{{ $log['badge']['label'] }}</span>
                </div>
                <p class="activity-desc">
                  {{ $log['description'] }}
                  @if(!empty($log['part_name']))
                    <span class="activity-part">&bull; {{ $log['part_name'] }}</span>
                  @endif
                </p>
                <div class="activity-time">
                  <i data-lucide="clock"></i>
                  <span>{{ $log['time_formatted'] }}</span>
                  @if(!empty($log['time_relative']))
                    <span class="activity-rel">({{ $log['time_relative'] }})</span>
                  @endif
                </div>
              </div>
            </div>
          @empty
            <div class="activity-empty">
              <i data-lucide="activity"></i>
              <p>{{ __('qc.dashboard.empty_admin_logs') }}</p>
            </div>
          @endforelse
        </div>
      </div>
    @else
      <!-- NON-ADMIN VIEW: Personal Submission & Review Activity Timeline -->
      <div class="card activity-panel-card">
        <div class="card-header activity-card-header">
          <div class="activity-header-title">
            <h3>{{ __('qc.dashboard.my_activity_title') }}</h3>
            <span class="badge badge-purple">{{ __('qc.dashboard.personal_history') }}</span>
          </div>
          <a href="{{ route('reports.index', ['tab' => 'mine']) }}" class="btn btn-secondary btn-sm">{{ __('qc.dashboard.open_my_submissions') }}</a>
        </div>

        <div class="personal-timeline-wrap" id="userTimelineList">
          @forelse($myReportTimeline as $item)
            <div class="personal-timeline-item">
              <div class="pt-head">
                <div class="pt-id-group">
                  <a href="{{ $item['action_url'] }}" class="pt-ssc-link mono">{{ $item['ssc_id'] }}</a>
                  <span class="pt-part">{{ $item['part_name'] }}</span>
                  <span class="badge {{ $item['inspection_type'] === 'Pre' ? 'badge-blue' : 'badge-purple' }}">{{ $item['inspection_type'] }}</span>
                </div>
                <div class="pt-status-group">
                  <span class="pt-role-tag">{{ $item['role_in_report'] }}</span>
                  @if($item['review_status'] === 'reviewed')
                    <span class="badge badge-green">{{ __('qc.reports.status_reviewed') }}</span>
                  @elseif($item['review_status'] === 'draft')
                    <span class="badge badge-slate">{{ __('qc.reports.status_draft') }}</span>
                  @else
                    <span class="badge badge-orange">{{ __('qc.reports.status_waiting') }}</span>
                  @endif
                </div>
              </div>
              <div class="pt-steps">
                <div class="pt-step">
                  <i data-lucide="send"></i>
                  <span><strong>{{ __('qc.dashboard.submitting_label') }}</strong> {{ $item['submitted_at_str'] ?? __('qc.dashboard.still_draft') }}</span>
                </div>
                <div class="pt-step">
                  <i data-lucide="check-circle-2"></i>
                  <span>
                    <strong>{{ __('qc.dashboard.reviewed_label') }}</strong>
                    @if($item['reviewed_at_str'])
                      {{ $item['reviewed_at_str'] }} ({{ $item['reviewer_name'] }})
                    @elseif($item['review_status'] === 'draft')
                      {{ __('qc.dashboard.awaiting_submit') }}
                    @else
                      {{ __('qc.dashboard.awaiting_review') }} ({{ $item['reviewer_name'] }})
                    @endif
                  </span>
                </div>
              </div>
            </div>
          @empty
            <div class="activity-empty">
              <i data-lucide="clipboard-list"></i>
              <p>{{ __('qc.dashboard.empty_my_timeline') }}</p>
            </div>
          @endforelse
        </div>

        <div class="personal-sublog-header">
          <h4>{{ __('qc.dashboard.account_trail_title') }}</h4>
        </div>
        <div class="activity-feed compact" id="userOwnLogList">
          @forelse($myActivityLogs as $log)
            @php
              $toneClass = match($log['badge']['tone']) {
                'success' => 'badge-green',
                'warning' => 'badge-orange',
                'danger' => 'badge-red',
                'primary' => 'badge-blue',
                default => 'badge-slate',
              };
            @endphp
            <div class="activity-item compact" data-log-id="{{ $log['id'] }}">
              <div class="activity-content">
                <div class="activity-top">
                  <span class="badge {{ $toneClass }}">{{ $log['badge']['label'] }}</span>
                  <span class="activity-time-inline">{{ $log['time_formatted'] }}</span>
                </div>
                <p class="activity-desc">{{ $log['description'] }}</p>
              </div>
            </div>
          @empty
            <div class="activity-empty compact">
              <p>{{ __('qc.dashboard.empty_account_trail') }}</p>
            </div>
          @endforelse
        </div>
      </div>
    @endif

    <!-- Quick Operational Links -->
    <div class="card">
      <div class="card-header">
        <h3>{{ __('qc.dashboard.quick_menu_title') }}</h3>
      </div>
      <div class="card-body" style="display: flex; flex-direction: column; gap: 0.6rem;">
        <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm" style="justify-content: flex-start;">
          <i data-lucide="archive"></i> {{ __('qc.dashboard.quick_history') }}
        </a>
        <a href="{{ route('reports.index', ['tab' => 'approval']) }}" class="btn btn-secondary btn-sm" style="justify-content: space-between;">
          <span style="display: inline-flex; align-items: center; gap: 0.45rem;">
            <i data-lucide="clipboard-check"></i> {{ __('qc.dashboard.quick_approval') }}
          </span>
          <span class="badge {{ ($myPendingApprovalCount ?? 0) > 0 ? 'badge-red' : 'badge-slate' }}" id="quickApprovalBadge">{{ $myPendingApprovalCount ?? 0 }}</span>
        </a>
        <a href="{{ route('templates.index') }}" class="btn btn-secondary btn-sm" style="justify-content: flex-start;">
          <i data-lucide="file-spreadsheet"></i> {{ __('qc.dashboard.quick_templates') }}
        </a>
        @if($isAdmin)
          <a href="{{ route('settings.users.index') }}" class="btn btn-secondary btn-sm" style="justify-content: flex-start;">
            <i data-lucide="shield-check"></i> {{ __('qc.dashboard.quick_users') }}
          </a>
        @endif
      </div>
    </div>
  </div>
@endsection

@section('scripts')
<script>
  (function () {
    window.__qcHasOwnLivePoll = true;
    const D = @json(__('qc.dashboard'));
    const R = @json(__('qc.reports'));
    const isAdmin = @json($isAdmin);
    let currentCharts = @json($charts);
    let activeAdminFilter = 'all';
    let lastAdminLogs = @json($adminLogs);
    const seenLogIds = new Set((lastAdminLogs || []).map(l => Number(l.id)));

    function esc(val) {
      return String(val ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
    }

    function toneToBadgeClass(tone) {
      if (tone === 'success') return 'badge-green';
      if (tone === 'warning') return 'badge-orange';
      if (tone === 'danger') return 'badge-red';
      if (tone === 'primary') return 'badge-blue';
      return 'badge-slate';
    }

    function renderBarChart(c) {
      const area = document.getElementById('barChartArea');
      if (!area) return;
      const items = [
        { key: 'pre', label: D.bar_pre || 'Pre', value: Number(c.preCount || 0), color: '#4f46e5' },
        { key: 'post', label: D.bar_post || 'Post', value: Number(c.postCount || 0), color: '#6366f1' },
        { key: 'draft', label: D.bar_draft || 'Draft', value: Number(c.draftCount || 0), color: '#f59e0b' },
      ];
      const maxVal = Math.max(1, ...items.map(i => i.value));

      area.innerHTML = items.map(item => {
        const pct = item.value > 0 ? Math.max(12, Math.round((item.value / maxVal) * 1000) / 10) : 4;
        return `
          <div class="bar-col" data-bar-key="${item.key}" title="${esc(item.label)}: ${item.value}">
            <span class="bar-val">${item.value}</span>
            <div class="bar-track">
              <div class="bar-fill" style="height: ${pct}%; background: ${item.color};"></div>
            </div>
            <span class="bar-lbl">${esc(item.label)}</span>
          </div>
        `;
      }).join('');
    }

    function renderDonutChart(c) {
      const segGroup = document.getElementById('donutSegments');
      const legend = document.getElementById('donutLegend');
      const centerVal = document.getElementById('donutCenterVal');
      if (!segGroup || !legend) return;

      const reviewed = Number(c.reviewedCount || 0);
      const waiting = Number(c.waitingCount || 0);
      const accept = Number(c.acceptCount || 0);
      if (centerVal) centerVal.textContent = String(accept);

      const segments = [
        { label: D.reviewed || 'Reviewed', value: reviewed, color: '#10b981' },
        { label: D.waiting_review || 'Waiting Review', value: waiting, color: '#f59e0b' },
      ];
      const total = segments.reduce((acc, s) => acc + s.value, 0);
      const r = 44;
      const circ = 2 * Math.PI * r;
      let accumulated = 0;

      if (total === 0) {
        segGroup.innerHTML = `<circle cx="60" cy="60" r="${r}" fill="none" class="chart-ring-track" stroke-width="14"></circle>`;
      } else {
        segGroup.innerHTML = segments.map(seg => {
          if (seg.value <= 0) return '';
          const frac = seg.value / total;
          const dashLen = frac * circ;
          const gap = Math.max(0, circ - dashLen);
          const rotateDeg = -90 + (accumulated / total) * 360;
          accumulated += seg.value;
          return `
            <circle cx="60" cy="60" r="${r}"
                    fill="none"
                    stroke="${seg.color}"
                    stroke-width="14"
                    stroke-dasharray="${dashLen.toFixed(2)} ${gap.toFixed(2)}"
                    transform="rotate(${rotateDeg.toFixed(2)} 60 60)"
                    class="donut-slice">
              <title>${esc(seg.label)}: ${seg.value} (${Math.round(frac * 100)}%)</title>
            </circle>
          `;
        }).join('');
      }

      legend.innerHTML = segments.map(seg => `
        <div class="donut-legend-row">
          <span class="donut-dot" style="background: ${seg.color};"></span>
          <span class="donut-lbl">${esc(seg.label)}</span>
          <strong class="donut-num">${seg.value}</strong>
        </div>
      `).join('');
    }

    function renderOnlinePresenceChart(c) {
      const elTotal = document.getElementById('chartValTotal');
      const elAccept = document.getElementById('chartValAccept');
      if (elTotal) elTotal.textContent = String(c.totalReports ?? 0);
      if (elAccept) elAccept.textContent = String(c.acceptCount ?? 0);

      const onlineCount = Math.max(0, Number(c.onlineCount ?? 0));
      const totalUsers = Math.max(0, Number(c.totalUsers ?? 0), onlineCount);
      const byRole = c.onlineByRole || {};
      const adminOnline = Math.max(0, Number(byRole.admin ?? 0));
      const leaderOnline = Math.max(0, Number(byRole.qc_leader ?? 0));
      const staffOnline = Math.max(0, Number(byRole.user ?? 0));

      const elOnline = document.getElementById('chartValOnline');
      const elSubTotal = document.getElementById('chartSubTotalUsers');
      const elCenter = document.getElementById('presenceCenterCount');
      if (elOnline) elOnline.textContent = String(onlineCount);
      if (elSubTotal) elSubTotal.textContent = `/ ${totalUsers}`;
      if (elCenter) elCenter.textContent = String(onlineCount);

      const elRoleAdmin = document.getElementById('presenceRoleAdmin');
      const elRoleLeader = document.getElementById('presenceRoleLeader');
      const elRoleStaff = document.getElementById('presenceRoleStaff');
      if (elRoleAdmin) elRoleAdmin.textContent = String(adminOnline);
      if (elRoleLeader) elRoleLeader.textContent = String(leaderOnline);
      if (elRoleStaff) elRoleStaff.textContent = String(staffOnline);

      const r = 44;
      const circ = 2 * Math.PI * r;
      const denom = Math.max(1, totalUsers);
      const adminLen = (adminOnline / denom) * circ;
      const leaderLen = (leaderOnline / denom) * circ;
      const staffLen = (staffOnline / denom) * circ;

      const segments = [
        { id: 'presenceSegAdmin', count: adminOnline, len: adminLen, offset: 0 },
        { id: 'presenceSegLeader', count: leaderOnline, len: leaderLen, offset: -adminLen },
        { id: 'presenceSegStaff', count: staffOnline, len: staffLen, offset: -(adminLen + leaderLen) },
      ];

      segments.forEach(s => {
        const el = document.getElementById(s.id);
        if (!el) return;
        el.setAttribute('stroke-dasharray', `${s.len.toFixed(2)} ${circ.toFixed(2)}`);
        el.setAttribute('stroke-dashoffset', s.offset.toFixed(2));
        el.style.opacity = s.count > 0 ? '1' : '0';
      });

      const stackEl = document.getElementById('onlineAvatarStack');
      if (stackEl) {
        const users = Array.isArray(c.onlineUsers) ? c.onlineUsers : [];
        stackEl.replaceChildren();

        if (users.length === 0) {
          const emptyEl = document.createElement('span');
          emptyEl.className = 'chart-metric-sub';
          emptyEl.textContent = D.no_other_online || 'Tidak ada sesi aktif';
          stackEl.appendChild(emptyEl);
        } else {
          users.slice(0, 4).forEach(u => {
            const roleKey = ['admin', 'qc_leader', 'user'].includes(u.role) ? u.role : 'user';
            const chip = document.createElement('span');
            chip.className = `live-avatar-chip role-${roleKey}`;
            chip.title = `${u.name || ''} (${u.role_label || roleKey}) • ${D.online_status || 'Online'}`;

            if (u.avatar_url) {
              const img = document.createElement('img');
              img.src = u.avatar_url;
              img.alt = u.initials || '';
              chip.appendChild(img);
            } else {
              chip.textContent = u.initials || '?';
            }
            stackEl.appendChild(chip);
          });

          if (onlineCount > 4) {
            const overflow = document.createElement('span');
            overflow.className = 'live-avatar-chip live-avatar-overflow';
            overflow.title = `+${onlineCount - 4} ${D.online_status || 'Online'}`;
            overflow.textContent = `+${onlineCount - 4}`;
            stackEl.appendChild(overflow);
          }
        }
      }
    }

    function matchesAdminFilter(action, filter) {
      if (filter === 'all') return true;
      if (filter === 'submit') return ['report_submitted', 'report_draft_created', 'report_draft_updated', 'report_updated', 'report_deleted'].includes(action);
      if (filter === 'review') return action === 'report_reviewed';
      if (filter === 'auth') return ['login', 'logout', 'user_created', 'user_updated', 'user_deleted', 'preferences_updated'].includes(action);
      return true;
    }

    function renderAdminLogs(logs, highlightNew = false) {
      const container = document.getElementById('adminActivityFeed');
      if (!container) return;

      const filtered = (logs || []).filter(l => matchesAdminFilter(l.action, activeAdminFilter));
      if (!filtered.length) {
        container.innerHTML = `
          <div class="activity-empty">
            <i data-lucide="activity"></i>
            <p>${esc(D.empty_filtered_logs || 'No activity in this category yet.')}</p>
          </div>
        `;
        if (window.lucide) lucide.createIcons({ root: container });
        return;
      }

      container.innerHTML = filtered.map(log => {
        const isFresh = highlightNew && !seenLogIds.has(Number(log.id));
        const badgeCls = toneToBadgeClass(log.badge?.tone);
        const partHtml = log.part_name ? `<span class="activity-part">&bull; ${esc(log.part_name)}</span>` : '';
        const relHtml = log.time_relative ? `<span class="activity-rel">(${esc(log.time_relative)})</span>` : '';
        const avatarContent = log.avatar_url
          ? `<img src="${esc(log.avatar_url)}" alt="${esc(log.user_name)}" class="user-avatar-img">`
          : esc(log.user_initials);
        return `
          <div class="activity-item ${isFresh ? 'is-fresh' : ''}" data-log-id="${esc(log.id)}" data-action="${esc(log.action)}">
            <div class="activity-avatar role-${esc(log.role_key)}" style="overflow: hidden;">${avatarContent}</div>
            <div class="activity-content">
              <div class="activity-top">
                <span class="activity-user">${esc(log.user_name)}</span>
                <span class="activity-user-role">${esc(log.user_role)}</span>
                <span class="badge ${badgeCls}">${esc(log.badge?.label)}</span>
              </div>
              <p class="activity-desc">${esc(log.description)} ${partHtml}</p>
              <div class="activity-time">
                <i data-lucide="clock"></i>
                <span>${esc(log.time_formatted)}</span>
                ${relHtml}
              </div>
            </div>
          </div>
        `;
      }).join('');

      (logs || []).forEach(l => seenLogIds.add(Number(l.id)));
      if (window.lucide) lucide.createIcons({ root: container });
    }

    function renderNonAdminTimeline(timeline, ownLogs) {
      const tlWrap = document.getElementById('userTimelineList');
      if (tlWrap) {
        if (!timeline || !timeline.length) {
          tlWrap.innerHTML = `
            <div class="activity-empty">
              <i data-lucide="clipboard-list"></i>
              <p>${esc(D.empty_my_timeline || 'You have no report activity yet.')}</p>
            </div>
          `;
        } else {
          tlWrap.innerHTML = timeline.map(item => {
            const typeBadge = item.inspection_type === 'Pre' ? 'badge-blue' : 'badge-purple';
            const statusBadge = item.review_status === 'reviewed'
              ? `<span class="badge badge-green">${esc(R.status_reviewed || 'Reviewed')}</span>`
              : (item.review_status === 'draft'
                ? `<span class="badge badge-slate">${esc(R.status_draft || 'Draft')}</span>`
                : `<span class="badge badge-orange">${esc(R.status_waiting || 'Waiting Review')}</span>`);
            const submitText = item.submitted_at_str ? esc(item.submitted_at_str) : esc(D.still_draft || 'Still Draft');
            const reviewText = item.reviewed_at_str
              ? `${esc(item.reviewed_at_str)} (${esc(item.reviewer_name)})`
              : (item.review_status === 'draft' ? esc(D.awaiting_submit || 'Awaiting Submit') : `${esc(D.awaiting_review || 'Awaiting Review')} (${esc(item.reviewer_name)})`);

            return `
              <div class="personal-timeline-item">
                <div class="pt-head">
                  <div class="pt-id-group">
                    <a href="${esc(item.action_url)}" class="pt-ssc-link mono">${esc(item.ssc_id)}</a>
                    <span class="pt-part">${esc(item.part_name)}</span>
                    <span class="badge ${typeBadge}">${esc(item.inspection_type)}</span>
                  </div>
                  <div class="pt-status-group">
                    <span class="pt-role-tag">${esc(item.role_in_report)}</span>
                    ${statusBadge}
                  </div>
                </div>
                <div class="pt-steps">
                  <div class="pt-step">
                    <i data-lucide="send"></i>
                    <span><strong>${esc(D.submitting_label || 'Submitted:')}</strong> ${submitText}</span>
                  </div>
                  <div class="pt-step">
                    <i data-lucide="check-circle-2"></i>
                    <span><strong>${esc(D.reviewed_label || 'Reviewed:')}</strong> ${reviewText}</span>
                  </div>
                </div>
              </div>
            `;
          }).join('');
        }
        if (window.lucide) lucide.createIcons({ root: tlWrap });
      }

      const logWrap = document.getElementById('userOwnLogList');
      if (logWrap) {
        if (!ownLogs || !ownLogs.length) {
          logWrap.innerHTML = `<div class="activity-empty compact"><p>${esc(D.empty_account_trail || 'No recent activity records yet.')}</p></div>`;
        } else {
          logWrap.innerHTML = ownLogs.map(log => `
            <div class="activity-item compact" data-log-id="${esc(log.id)}">
              <div class="activity-content">
                <div class="activity-top">
                  <span class="badge ${toneToBadgeClass(log.badge?.tone)}">${esc(log.badge?.label)}</span>
                  <span class="activity-time-inline">${esc(log.time_formatted)}</span>
                </div>
                <p class="activity-desc">${esc(log.description)}</p>
              </div>
            </div>
          `).join('');
        }
      }
    }

    let lastPollAt = 0;

    function pollDashboardLive(force = false) {
      const nowMs = Date.now();
      if (!force && (nowMs - lastPollAt) < 1800) return;
      lastPollAt = nowMs;

      fetch(@json(route('dashboard.liveData')), {
        cache: 'no-store',
        credentials: 'same-origin',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        }
      })
        .then(r => r.ok ? r.json() : null)
        .then(res => {
          if (!res || !res.success || !res.data) return;
          const d = res.data;

          const livePill = document.getElementById('dashPresenceLivePill');
          if (livePill && res.server_time) {
            livePill.title = `${D.live_badge || 'Live'} • ${res.server_time}`;
          }

          const approvalBadge = document.getElementById('quickApprovalBadge');
          if (approvalBadge) {
            const cnt = Number(d.myPendingApprovalCount || 0);
            approvalBadge.textContent = String(cnt);
            approvalBadge.className = `badge ${cnt > 0 ? 'badge-red' : 'badge-slate'}`;
          }

          if (d.charts) {
            currentCharts = d.charts;
            renderBarChart(d.charts);
            renderDonutChart(d.charts);
            renderOnlinePresenceChart(d.charts);
          }

          if (isAdmin) {
            lastAdminLogs = d.adminLogs || [];
            renderAdminLogs(lastAdminLogs, true);
          } else {
            renderNonAdminTimeline(d.myReportTimeline || [], d.myActivityLogs || []);
          }
        })
        .catch(() => {});
    }

    document.addEventListener('DOMContentLoaded', () => {
      renderDonutChart(currentCharts);
      renderBarChart(currentCharts);
      renderOnlinePresenceChart(currentCharts);

      document.querySelectorAll('#adminLogFilters .activity-filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          document.querySelectorAll('#adminLogFilters .activity-filter-btn').forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
          activeAdminFilter = btn.dataset.filter || 'all';
          renderAdminLogs(lastAdminLogs, false);
        });
      });

      setInterval(() => {
        if (!document.hidden) pollDashboardLive(false);
      }, 5000);

      document.addEventListener('visibilitychange', () => {
        if (!document.hidden) pollDashboardLive(false);
      });

      window.addEventListener('focus', () => {
        if (!document.hidden) pollDashboardLive(false);
      });
    });
  })();
</script>
@endsection
