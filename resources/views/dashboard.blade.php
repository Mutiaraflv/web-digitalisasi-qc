@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
  <div class="page-header dash-page-header">
    <div class="page-title">
      <h1>Dashboard</h1>
      <p>Ringkasan inspeksi komponen &amp; aktivitas QC PT SSC Works.</p>
    </div>
    <div class="dash-live-pill" id="dashLiveIndicator" title="Sinkronisasi otomatis setiap 5 detik">
      <span class="live-dot"></span>
      <span>LIVE</span>
      <span class="live-time" id="dashLiveTime">{{ now()->timezone('Asia/Jakarta')->format('H:i:s') }} WIB</span>
    </div>
  </div>

  <!-- 3 Interactive Visual Charts (Clean & Focused) -->
  <div class="dashboard-charts-grid">
    <!-- Chart 1: Total Inspeksi (Bar Chart) -->
    <div class="chart-card">
      <div class="chart-card-head">
        <div>
          <span class="chart-kicker">Total Inspeksi</span>
          <h2 class="chart-metric-val" id="chartValTotal">{{ $totalReports }}</h2>
        </div>
      </div>

      <div class="bar-chart-wrap" id="barChartArea" aria-label="Grafik Total Inspeksi">
        @php
          $barMax = max(1, $preCount, $postCount, $waitingCount, $reviewedCount);
          $bars = [
            ['key' => 'pre', 'label' => 'Pre', 'value' => $preCount, 'color' => '#1312b6'],
            ['key' => 'post', 'label' => 'Post', 'value' => $postCount, 'color' => '#6366f1'],
            ['key' => 'waiting', 'label' => 'Waiting', 'value' => $waitingCount, 'color' => '#f59e0b'],
            ['key' => 'reviewed', 'label' => 'Reviewed', 'value' => $reviewedCount, 'color' => '#10b981'],
          ];
        @endphp
        @foreach($bars as $bar)
          @php $pct = round(($bar['value'] / $barMax) * 100, 1); @endphp
          <div class="bar-col" data-bar-key="{{ $bar['key'] }}" title="{{ $bar['label'] }}: {{ $bar['value'] }} laporan">
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
          <span class="chart-kicker">Lolos (Accept)</span>
          <h2 class="chart-metric-val" style="color: var(--success);" id="chartValAccept">{{ $acceptCount }}</h2>
        </div>
      </div>

      <div class="donut-chart-layout">
        <div class="donut-svg-box">
          <svg viewBox="0 0 120 120" class="donut-svg" id="donutChartSvg" role="img" aria-label="Pie Chart Status Inspeksi">
            <circle cx="60" cy="60" r="44" fill="none" stroke="#f1f5f9" stroke-width="14"></circle>
            <g id="donutSegments"></g>
          </svg>
          <div class="donut-center">
            <strong id="donutCenterVal">{{ $acceptCount }}</strong>
          </div>
        </div>
        <div class="donut-legend" id="donutLegend"></div>
      </div>
    </div>

    <!-- Chart 3: First Pass Yield (Radial Gauge Chart) -->
    <div class="chart-card">
      <div class="chart-card-head">
        <div>
          <span class="chart-kicker">First Pass Yield</span>
          <h2 class="chart-metric-val" style="color: var(--primary);" id="chartValYield">{{ $yieldRate }}%</h2>
        </div>
      </div>

      <div class="gauge-chart-layout">
        <div class="gauge-svg-box">
          @php
            $r = 46;
            $circ = 2 * pi() * $r;
            $offset = $circ - ($circ * min(100, max(0, $yieldRate)) / 100);
          @endphp
          <svg viewBox="0 0 120 120" class="gauge-svg" id="yieldGaugeSvg" role="img" aria-label="First Pass Yield Chart">
            <defs>
              <linearGradient id="yieldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#1312b6" />
                <stop offset="100%" stop-color="#10b981" />
              </linearGradient>
            </defs>
            <circle cx="60" cy="60" r="46" fill="none" stroke="#eef2ff" stroke-width="12"></circle>
            <circle id="yieldGaugeArc"
                    cx="60" cy="60" r="46"
                    fill="none"
                    stroke="url(#yieldGrad)"
                    stroke-width="12"
                    stroke-linecap="round"
                    stroke-dasharray="{{ round($circ, 2) }}"
                    stroke-dashoffset="{{ round($offset, 2) }}"
                    transform="rotate(-90 60 60)"></circle>
          </svg>
          <div class="gauge-center">
            <strong id="yieldGaugeText">{{ $yieldRate }}%</strong>
          </div>
        </div>

        <div class="gauge-meta-list" id="yieldMetaList">
          <div class="gauge-meta-item">
            <span>Lolos</span>
            <strong><span id="yieldMetaAccept">{{ $acceptCount }}</span> / <span id="yieldMetaTotal">{{ $totalReports }}</span></strong>
          </div>
          <div class="gauge-meta-item">
            <span>Reviewed</span>
            <strong style="color: #059669;" id="yieldMetaReviewed">{{ $reviewedCount }}</strong>
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
            <h3>Log Activity Real-Time</h3>
            <span class="badge badge-blue">Semua Akun (Admin)</span>
          </div>
          <div class="activity-filter-group" id="adminLogFilters">
            <button type="button" class="activity-filter-btn active" data-filter="all">Semua</button>
            <button type="button" class="activity-filter-btn" data-filter="submit">Submit / Draft</button>
            <button type="button" class="activity-filter-btn" data-filter="review">Finish Review</button>
            <button type="button" class="activity-filter-btn" data-filter="auth">Akun &amp; Login</button>
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
              <div class="activity-avatar role-{{ $log['role_key'] }}">{{ $log['user_initials'] }}</div>
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
              <p>Belum ada aktivitas tercatat di sistem.</p>
            </div>
          @endforelse
        </div>
      </div>
    @else
      <!-- NON-ADMIN VIEW: Personal Submission & Review Activity Timeline -->
      <div class="card activity-panel-card">
        <div class="card-header activity-card-header">
          <div class="activity-header-title">
            <h3>Aktivitas Laporan Saya</h3>
            <span class="badge badge-purple">Riwayat Pribadi</span>
          </div>
          <a href="{{ route('reports.index', ['tab' => 'mine']) }}" class="btn btn-secondary btn-sm">Buka Pengajuan Saya</a>
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
                    <span class="badge badge-green">Reviewed</span>
                  @elseif($item['review_status'] === 'draft')
                    <span class="badge badge-slate">Draft</span>
                  @else
                    <span class="badge badge-orange">Waiting Review</span>
                  @endif
                </div>
              </div>
              <div class="pt-steps">
                <div class="pt-step">
                  <i data-lucide="send"></i>
                  <span><strong>Mengajukan:</strong> {{ $item['submitted_at_str'] ?? 'Masih Draft (belum diajukan)' }}</span>
                </div>
                <div class="pt-step">
                  <i data-lucide="check-circle-2"></i>
                  <span>
                    <strong>Ter-Reviewed:</strong>
                    @if($item['reviewed_at_str'])
                      {{ $item['reviewed_at_str'] }} ({{ $item['reviewer_name'] }})
                    @elseif($item['review_status'] === 'draft')
                      Menunggu Submit
                    @else
                      Menunggu Review ({{ $item['reviewer_name'] }})
                    @endif
                  </span>
                </div>
              </div>
            </div>
          @empty
            <div class="activity-empty">
              <i data-lucide="clipboard-list"></i>
              <p>Anda belum memiliki aktivitas laporan. Mulai dengan membuat inspeksi baru.</p>
            </div>
          @endforelse
        </div>

        <div class="personal-sublog-header">
          <h4>Jejak Aktivitas Akun Anda</h4>
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
              <p>Belum ada catatan aktivitas terbaru.</p>
            </div>
          @endforelse
        </div>
      </div>
    @endif

    <!-- Quick Operational Links -->
    <div class="card">
      <div class="card-header">
        <h3>Menu Cepat Operasional</h3>
      </div>
      <div class="card-body" style="display: flex; flex-direction: column; gap: 0.6rem;">
        <a href="{{ route('inspection.create') }}" class="btn btn-primary btn-sm" style="justify-content: flex-start;">
          <i data-lucide="file-plus-2"></i> Buat Laporan Inspeksi Baru
        </a>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm" style="justify-content: flex-start;">
          <i data-lucide="archive"></i> Riwayat &amp; Arsip Laporan QC
        </a>
        <a href="{{ route('reports.index', ['tab' => 'approval']) }}" class="btn btn-secondary btn-sm" style="justify-content: space-between;">
          <span style="display: inline-flex; align-items: center; gap: 0.45rem;">
            <i data-lucide="clipboard-check"></i> Waiting My Approval
          </span>
          <span class="badge {{ ($myPendingApprovalCount ?? 0) > 0 ? 'badge-red' : 'badge-slate' }}" id="quickApprovalBadge">{{ $myPendingApprovalCount ?? 0 }}</span>
        </a>
        <a href="{{ route('templates.index') }}" class="btn btn-secondary btn-sm" style="justify-content: flex-start;">
          <i data-lucide="file-spreadsheet"></i> Template Komponen
        </a>
        @if($isAdmin)
          <a href="{{ route('settings.users.index') }}" class="btn btn-secondary btn-sm" style="justify-content: flex-start;">
            <i data-lucide="shield-check"></i> Kelola Akun Web
          </a>
        @endif
      </div>
    </div>
  </div>
@endsection

@section('scripts')
<script>
  (function () {
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
        { key: 'pre', label: 'Pre', value: Number(c.preCount || 0), color: '#1312b6' },
        { key: 'post', label: 'Post', value: Number(c.postCount || 0), color: '#6366f1' },
        { key: 'waiting', label: 'Waiting', value: Number(c.waitingCount || 0), color: '#f59e0b' },
        { key: 'reviewed', label: 'Reviewed', value: Number(c.reviewedCount || 0), color: '#10b981' },
      ];
      const maxVal = Math.max(1, ...items.map(i => i.value));

      area.innerHTML = items.map(item => {
        const pct = item.value > 0 ? Math.max(12, Math.round((item.value / maxVal) * 1000) / 10) : 4;
        return `
          <div class="bar-col" data-bar-key="${item.key}" title="${item.label}: ${item.value} laporan">
            <span class="bar-val">${item.value}</span>
            <div class="bar-track">
              <div class="bar-fill" style="height: ${pct}%; background: ${item.color};"></div>
            </div>
            <span class="bar-lbl">${item.label}</span>
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
        { label: 'Reviewed', value: reviewed, color: '#10b981' },
        { label: 'Waiting Review', value: waiting, color: '#f59e0b' },
      ];
      const total = segments.reduce((acc, s) => acc + s.value, 0);
      const r = 44;
      const circ = 2 * Math.PI * r;
      let accumulated = 0;

      if (total === 0) {
        segGroup.innerHTML = `<circle cx="60" cy="60" r="${r}" fill="none" stroke="#e2e8f0" stroke-width="14"></circle>`;
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
              <title>${seg.label}: ${seg.value} (${Math.round(frac * 100)}%)</title>
            </circle>
          `;
        }).join('');
      }

      legend.innerHTML = segments.map(seg => `
        <div class="donut-legend-row">
          <span class="donut-dot" style="background: ${seg.color};"></span>
          <span class="donut-lbl">${seg.label}</span>
          <strong class="donut-num">${seg.value}</strong>
        </div>
      `).join('');
    }

    function renderYieldGauge(c) {
      const rate = Math.min(100, Math.max(0, Number(c.yieldRate ?? 100)));
      const arc = document.getElementById('yieldGaugeArc');
      const text = document.getElementById('yieldGaugeText');
      const r = 46;
      const circ = 2 * Math.PI * r;
      if (arc) {
        arc.setAttribute('stroke-dasharray', circ.toFixed(2));
        arc.setAttribute('stroke-dashoffset', (circ - (circ * rate / 100)).toFixed(2));
      }
      if (text) text.textContent = `${rate}%`;

      const elTotal = document.getElementById('chartValTotal');
      const elAccept = document.getElementById('chartValAccept');
      const elYield = document.getElementById('chartValYield');
      if (elTotal) elTotal.textContent = String(c.totalReports ?? 0);
      if (elAccept) elAccept.textContent = String(c.acceptCount ?? 0);
      if (elYield) elYield.textContent = `${rate}%`;

      const metaAccept = document.getElementById('yieldMetaAccept');
      const metaTotal = document.getElementById('yieldMetaTotal');
      const metaReviewed = document.getElementById('yieldMetaReviewed');
      if (metaAccept) metaAccept.textContent = String(c.acceptCount ?? 0);
      if (metaTotal) metaTotal.textContent = String(c.totalReports ?? 0);
      if (metaReviewed) metaReviewed.textContent = String(c.reviewedCount ?? 0);
    }

    function matchesAdminFilter(action, filter) {
      if (filter === 'all') return true;
      if (filter === 'submit') return ['report_submitted', 'report_draft_created', 'report_draft_updated', 'report_updated', 'report_deleted'].includes(action);
      if (filter === 'review') return action === 'report_reviewed';
      if (filter === 'auth') return ['login', 'logout', 'user_created', 'user_updated', 'user_deleted'].includes(action);
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
            <p>Belum ada aktivitas pada kategori ini.</p>
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
        return `
          <div class="activity-item ${isFresh ? 'is-fresh' : ''}" data-log-id="${esc(log.id)}" data-action="${esc(log.action)}">
            <div class="activity-avatar role-${esc(log.role_key)}">${esc(log.user_initials)}</div>
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
              <p>Anda belum memiliki aktivitas laporan. Mulai dengan membuat inspeksi baru.</p>
            </div>
          `;
        } else {
          tlWrap.innerHTML = timeline.map(item => {
            const typeBadge = item.inspection_type === 'Pre' ? 'badge-blue' : 'badge-purple';
            const statusBadge = item.review_status === 'reviewed'
              ? '<span class="badge badge-green">Reviewed</span>'
              : (item.review_status === 'draft'
                ? '<span class="badge badge-slate">Draft</span>'
                : '<span class="badge badge-orange">Waiting Review</span>');
            const submitText = item.submitted_at_str ? esc(item.submitted_at_str) : 'Masih Draft (belum diajukan)';
            const reviewText = item.reviewed_at_str
              ? `${esc(item.reviewed_at_str)} (${esc(item.reviewer_name)})`
              : (item.review_status === 'draft' ? 'Menunggu Submit' : `Menunggu Review (${esc(item.reviewer_name)})`);

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
                    <span><strong>Mengajukan:</strong> ${submitText}</span>
                  </div>
                  <div class="pt-step">
                    <i data-lucide="check-circle-2"></i>
                    <span><strong>Ter-Reviewed:</strong> ${reviewText}</span>
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
          logWrap.innerHTML = `<div class="activity-empty compact"><p>Belum ada catatan aktivitas terbaru.</p></div>`;
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

    function pollDashboardLive() {
      fetch(@json(route('dashboard.liveData')), {
        headers: { 'Accept': 'application/json' }
      })
        .then(r => r.ok ? r.json() : null)
        .then(res => {
          if (!res || !res.success || !res.data) return;
          const d = res.data;

          const timeEl = document.getElementById('dashLiveTime');
          if (timeEl && res.server_time) timeEl.textContent = res.server_time;

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
            renderYieldGauge(d.charts);
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
      renderYieldGauge(currentCharts);

      document.querySelectorAll('#adminLogFilters .activity-filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          document.querySelectorAll('#adminLogFilters .activity-filter-btn').forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
          activeAdminFilter = btn.dataset.filter || 'all';
          renderAdminLogs(lastAdminLogs, false);
        });
      });

      setInterval(() => {
        if (!document.hidden) pollDashboardLive();
      }, 5000);
    });
  })();
</script>
@endsection
