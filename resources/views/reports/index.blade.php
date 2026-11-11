@extends('layouts.app')

@section('title', __('qc.reports.title'))
@section('page_title', __('qc.reports.page_title'))

@section('content')
  <div class="page-header">
    <div class="page-title">
      <h1>{{ __('qc.reports.heading') }}</h1>
      <p>{{ __('qc.reports.subtitle') }}</p>
    </div>
  </div>

  <!-- Corporate KPI Grid -->
  <div class="stats-grid">
    <div class="stat-card">
      <p>{{ __('qc.reports.kpi_total') }}</p>
      <h3>{{ $totalCount }}</h3>
      <div class="stat-sub">{{ __('qc.reports.kpi_total_sub') }}</div>
    </div>
    <div class="stat-card">
      <p>{{ __('qc.reports.kpi_waiting') }}</p>
      <h3 style="color: var(--warning, #d97706);">{{ $waitingCount }}</h3>
      <div class="stat-sub">{{ __('qc.reports.kpi_waiting_sub') }}</div>
    </div>
    <div class="stat-card">
      <p>{{ __('qc.reports.kpi_reviewed') }}</p>
      <h3 style="color: var(--success);">{{ $reviewedCount }}</h3>
      <div class="stat-sub">{{ __('qc.reports.kpi_reviewed_sub') }}</div>
    </div>
    <a href="{{ route('reports.index', ['tab' => 'approval']) }}" class="stat-card" style="text-decoration: none; color: inherit;">
      <p>{{ __('qc.reports.kpi_my_approval') }}</p>
      <h3 style="color: {{ $waitingApprovalCount > 0 ? '#ef4444' : 'var(--text-main)' }};">{{ $waitingApprovalCount }}</h3>
      <div class="stat-sub">{{ __('qc.reports.kpi_my_approval_sub') }}</div>
    </a>
  </div>

  <!-- History Sub-Tabs -->
  <div class="report-tabs" role="tablist" aria-label="{{ __('qc.reports.title') }}">
    <a href="{{ route('reports.index', ['tab' => 'all']) }}" class="report-tab {{ ($activeTab ?? 'all') === 'all' ? 'active' : '' }}">
      <i data-lucide="layers"></i>
      <span>{{ __('qc.reports.tab_all') }}</span>
      <span class="report-tab-badge">{{ $totalCount }}</span>
    </a>
    <a href="{{ route('reports.index', ['tab' => 'mine']) }}" class="report-tab {{ ($activeTab ?? 'all') === 'mine' ? 'active' : '' }}">
      <i data-lucide="user-check"></i>
      <span>{{ __('qc.reports.tab_mine') }}</span>
      <span class="report-tab-badge">{{ $mySubmissionsCount }}</span>
    </a>
    <a href="{{ route('reports.index', ['tab' => 'approval']) }}" class="report-tab {{ ($activeTab ?? 'all') === 'approval' ? 'active' : '' }}">
      <i data-lucide="clipboard-check"></i>
      <span>{{ __('qc.reports.tab_approval') }}</span>
      <span class="report-tab-badge {{ $waitingApprovalCount > 0 ? 'alert' : '' }}">{{ $waitingApprovalCount }}</span>
    </a>
  </div>

  <!-- Control Bar -->
  <div class="control-bar">
    <div class="search-box">
      <i data-lucide="search" class="search-icon"></i>
      <input type="text" id="searchInput" placeholder="{{ __('qc.reports.search_placeholder') }}">
    </div>

    <div class="filter-group">
      @if(($activeTab ?? 'all') === 'mine')
        <select id="statusFilter" class="select-filter" aria-label="{{ __('qc.common.status') }}">
          <option value="All" {{ request('status') === 'All' || !request('status') ? 'selected' : '' }}>{{ __('qc.reports.all_statuses') }}</option>
          <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>{{ __('qc.reports.status_draft') }}</option>
          <option value="dikirim" {{ in_array(request('status'), ['dikirim', 'submitted'], true) ? 'selected' : '' }}>{{ __('qc.reports.status_sent') }}</option>
        </select>
      @endif

      <select id="typeFilter" class="select-filter">
        <option value="All">{{ __('qc.reports.all_types') }}</option>
        <option value="Pre">{{ __('qc.reports.pre_type') }}</option>
        <option value="Post">{{ __('qc.reports.post_type') }}</option>
      </select>
      <a href="{{ route('inspection.create') }}" class="btn btn-primary btn-sm">
        <i data-lucide="file-plus"></i> {{ __('qc.reports.new_form_btn') }}
      </a>
    </div>
  </div>

  <!-- Table Card -->
  <div class="table-card">
    <div class="table-responsive">
      <table class="qc-table">
        <thead>
          <tr>
            <th>{{ __('qc.reports.col_ssc_id') }}</th>
            <th>{{ __('qc.reports.col_report_no') }}</th>
            <th>{{ __('qc.reports.col_date') }}</th>
            <th>{{ __('qc.reports.col_customer') }}</th>
            <th>{{ __('qc.reports.col_component') }}</th>
            <th>{{ __('qc.reports.col_type') }}</th>
            <th>{{ __('qc.reports.col_status') }}</th>
            <th style="text-align: right;">{{ __('qc.reports.col_actions') }}</th>
          </tr>
        </thead>
        <tbody id="reportTableBody">
          @forelse($reports as $item)
            @php
              $creatorLabel = $item->creator->name ?? ($item->signatures['createdBy']['name'] ?? '-');
              $checkerLabel = $item->checker->name ?? ($item->signatures['checkedBy']['name'] ?? '-');
              $reviewerLabel = $item->reviewer->name ?? ($item->signatures['reviewedBy']['name'] ?? '-');
              $statusCategory = $item->isDraft() ? 'draft' : 'dikirim';
            @endphp
            <tr data-type="{{ $item->inspection_type }}" data-status="{{ $statusCategory }}">
              <td class="mono" style="font-weight: 700; color: var(--primary-text);">
                {{ $item->ssc_id }}
              </td>
              <td class="mono">{{ $item->report_no ?? '-' }}</td>
              <td>{{ $item->date ? \Carbon\Carbon::parse($item->date)->format('Y-m-d') : '-' }}</td>
              <td>{{ $item->customer ?? '-' }}</td>
              <td>
                <strong>{{ $item->part_name }}</strong>
                <span style="color: var(--text-subtle); font-size: 0.75rem; display: block;">
                  {{ $item->model_unit }} &bull; {{ $item->doc_no }}
                </span>
                <span style="color: var(--text-subtle); font-size: 0.72rem; display: block;">
                  {{ __('qc.reports.creator_label') }}: {{ $creatorLabel }} &bull; {{ __('qc.reports.checker_label') }}: {{ $checkerLabel }} &bull; {{ __('qc.reports.reviewer_label') }}: {{ $reviewerLabel }}
                </span>
              </td>
              <td>
                <span class="badge {{ $item->inspection_type === 'Pre' ? 'badge-blue' : 'badge-purple' }}">
                  {{ $item->inspection_type }}
                </span>
              </td>
              <td>
                @if($item->isReviewed())
                  <span class="badge badge-green">{{ __('qc.reports.status_reviewed') }}</span>
                @elseif($item->isDraft())
                  <span class="badge badge-slate">{{ __('qc.reports.status_draft') }}</span>
                @else
                  <span class="badge badge-orange">{{ __('qc.reports.status_waiting') }}</span>
                @endif
              </td>
              <td style="text-align: right; white-space: nowrap;">
                @if(($activeTab ?? 'all') === 'approval')
                  <a href="{{ route('reports.review', $item->id) }}" class="btn btn-primary btn-sm" title="{{ __('qc.reports.start_review') }}">
                    <i data-lucide="clipboard-check"></i> {{ __('qc.reports.start_review') }}
                  </a>
                @else
                  <div class="table-actions-stack">
                    @if($item->canBeModifiedBy(auth()->user()))
                      <div class="table-actions-row">
                        <a href="{{ route('reports.edit', $item->id) }}" class="btn btn-secondary btn-sm" title="{{ __('qc.reports.edit_btn') }}">
                          <i data-lucide="edit"></i> {{ __('qc.reports.edit_btn') }}
                        </a>
                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteReport({{ $item->id }}, '{{ $item->ssc_id }}')" title="{{ __('qc.common.delete') }}">
                          <i data-lucide="trash-2"></i>
                        </button>
                      </div>
                    @endif

                    <div class="row-dropdown">
                      <button type="button" class="btn btn-secondary btn-sm row-dropdown-btn" onclick="toggleRowDropdown(event, this)" title="{{ __('qc.reports.download_title') }}">
                        <i data-lucide="download"></i> <span>{{ __('qc.reports.download_btn') }}</span> <i data-lucide="chevron-down" style="width: 12px; height: 12px; margin-left: 2px;"></i>
                      </button>
                      <div class="row-dropdown-menu" role="menu">
                        <a href="{{ route('reports.export', $item->id) }}" class="row-dropdown-item" role="menuitem">
                          <i data-lucide="file-spreadsheet"></i> <span>{{ __('qc.reports.download_excel_full') }}</span>
                        </a>
                        <a href="{{ route('reports.pdf', $item->id) }}" target="_blank" class="row-dropdown-item" role="menuitem">
                          <i data-lucide="printer"></i> <span>{{ __('qc.reports.view_print_pdf') }}</span>
                        </a>
                      </div>
                    </div>
                  </div>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" style="text-align: center; padding: 3rem; color: var(--text-subtle);">
                <i data-lucide="clipboard-x" style="width: 38px; height: 38px; margin: 0 auto 0.5rem auto; display: block; opacity: 0.5;"></i>
                @if(($activeTab ?? 'all') === 'approval')
                  <p style="margin-bottom: 0.75rem; font-size: 0.95rem;">{{ __('qc.reports.empty_approval') }}</p>
                @elseif(($activeTab ?? 'all') === 'mine')
                  <p style="margin-bottom: 0.75rem; font-size: 0.95rem;">{{ __('qc.reports.empty_mine') }}</p>
                  <a href="{{ route('inspection.create') }}" class="btn btn-primary btn-sm">
                    <i data-lucide="plus"></i> {{ __('qc.reports.start_new_input') }}
                  </a>
                @else
                  <p style="margin-bottom: 0.75rem; font-size: 0.95rem;">{{ __('qc.reports.empty_all') }}</p>
                  <a href="{{ route('inspection.create') }}" class="btn btn-primary btn-sm">
                    <i data-lucide="plus"></i> {{ __('qc.reports.start_new_input') }}
                  </a>
                @endif
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection

@section('scripts')
<script>
  const R = window.qcI18n?.reports || {};
  const C = window.qcI18n?.common || {};

  async function deleteReport(id, sscId) {
    const confirmMsg = (R.delete_confirm || 'Are you sure you want to delete report :ssc?').replace(':ssc', sscId);
    const confirmed = await window.qcConfirm({
      title: R.delete_title || 'Delete QC Report',
      message: confirmMsg,
      confirmText: C.yes_delete || 'Yes, Delete',
      cancelText: C.cancel || 'Cancel',
      variant: 'danger'
    });
    if (!confirmed) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    window.qcNotify((R.deleting || 'Deleting report :ssc...').replace(':ssc', sscId), 'loading');

    fetch(`/reports/${id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': csrf,
        'Accept': 'application/json'
      }
    })
    .then(async r => {
      const data = await r.json().catch(() => ({}));
      if (!r.ok || data.success === false) {
        window.qcNotify(data.message || R.delete_failed || 'Failed to delete report.', 'error');
        return;
      }
      window.qcNotify(data.message || `Report ${sscId} deleted.`, 'success');
      setTimeout(() => window.location.reload(), 650);
    })
    .catch(err => {
      window.qcNotify((R.delete_failed || 'Failed to delete report:') + ' ' + err.message, 'error');
    });
  }

  // Search and filter
  document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    const statusFilter = document.getElementById('statusFilter');
    const tableBody = document.getElementById('reportTableBody');

    function applyFilter() {
      const q = (searchInput?.value || '').toLowerCase().trim();
      const t = typeFilter?.value || 'All';
      const s = statusFilter?.value || 'All';

      const rows = tableBody ? tableBody.querySelectorAll('tr[data-type]') : [];
      rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        const type = row.getAttribute('data-type');
        const status = row.getAttribute('data-status');

        const matchQuery = !q || text.includes(q);
        const matchType = (t === 'All') || (type === t);
        const matchStatus = (s === 'All') || (status === s);

        if (matchQuery && matchType && matchStatus) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    if (searchInput) searchInput.addEventListener('input', applyFilter);
    if (typeFilter) typeFilter.addEventListener('change', applyFilter);
    if (statusFilter) statusFilter.addEventListener('change', applyFilter);
  });

  // Table row dropdown handling
  function toggleRowDropdown(event, btn) {
    event.stopPropagation();
    const dropdown = btn.closest('.row-dropdown');
    if (!dropdown) return;

    const isOpen = dropdown.classList.contains('is-open');

    // Close any other open row dropdowns
    document.querySelectorAll('.row-dropdown.is-open').forEach(d => {
      if (d !== dropdown) {
        d.classList.remove('is-open');
        d.classList.remove('dropup');
      }
    });

    if (!isOpen) {
      const rect = btn.getBoundingClientRect();
      const tableResp = btn.closest('.table-responsive');
      const tableRespRect = tableResp ? tableResp.getBoundingClientRect() : null;

      const spaceBelowViewport = window.innerHeight - rect.bottom;
      const spaceBelowContainer = tableRespRect ? (tableRespRect.bottom - rect.bottom) : 999;

      if (spaceBelowViewport < 125 || spaceBelowContainer < 115) {
        dropdown.classList.add('dropup');
      } else {
        dropdown.classList.remove('dropup');
      }
      dropdown.classList.add('is-open');
      if (window.lucide) lucide.createIcons({ root: dropdown });
    } else {
      dropdown.classList.remove('is-open');
      dropdown.classList.remove('dropup');
    }
  }

  // Close row dropdown when clicking outside or pressing Escape
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.row-dropdown')) {
      document.querySelectorAll('.row-dropdown.is-open').forEach(d => {
        d.classList.remove('is-open');
        d.classList.remove('dropup');
      });
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      document.querySelectorAll('.row-dropdown.is-open').forEach(d => {
        d.classList.remove('is-open');
        d.classList.remove('dropup');
      });
    }
  });
</script>
@endsection
