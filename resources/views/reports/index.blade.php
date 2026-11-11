@extends('layouts.app')

@section('title', 'Riwayat Laporan QC')
@section('page_title', 'Riwayat Laporan QC')

@section('top_actions')
  <a href="{{ route('inspection.create') }}" class="btn btn-primary btn-sm">
    <i data-lucide="plus"></i> Input Inspeksi Baru
  </a>
@endsection

@section('content')
  <div class="page-header">
    <div class="page-title">
      <h1>Riwayat & Arsip Laporan QC</h1>
      <p>Basis data rekaman pemeriksaan komponen yang telah diinput teknisi PT SSC Works.</p>
    </div>
  </div>

  <!-- Corporate KPI Grid -->
  <div class="stats-grid">
    <div class="stat-card">
      <p>Total Laporan</p>
      <h3>{{ $totalCount }}</h3>
      <div class="stat-sub">Semua pengajuan QC tercatat</div>
    </div>
    <div class="stat-card">
      <p>Waiting Review</p>
      <h3 style="color: var(--warning, #d97706);">{{ $waitingCount }}</h3>
      <div class="stat-sub">Menunggu ditinjau reviewer</div>
    </div>
    <div class="stat-card">
      <p>Reviewed</p>
      <h3 style="color: var(--success);">{{ $reviewedCount }}</h3>
      <div class="stat-sub">Sudah selesai direview</div>
    </div>
    <a href="{{ route('reports.index', ['tab' => 'approval']) }}" class="stat-card" style="text-decoration: none; color: inherit;">
      <p>Waiting My Approval</p>
      <h3 style="color: {{ $waitingApprovalCount > 0 ? '#ef4444' : 'var(--text-main)' }};">{{ $waitingApprovalCount }}</h3>
      <div class="stat-sub">Perlu review dari akun Anda</div>
    </a>
  </div>

  <!-- History Sub-Tabs -->
  <div class="report-tabs" role="tablist" aria-label="Filter Riwayat Laporan">
    <a href="{{ route('reports.index', ['tab' => 'all']) }}" class="report-tab {{ ($activeTab ?? 'all') === 'all' ? 'active' : '' }}">
      <i data-lucide="layers"></i>
      <span>Semua Laporan</span>
      <span class="report-tab-badge">{{ $totalCount }}</span>
    </a>
    <a href="{{ route('reports.index', ['tab' => 'mine']) }}" class="report-tab {{ ($activeTab ?? 'all') === 'mine' ? 'active' : '' }}">
      <i data-lucide="user-check"></i>
      <span>Pengajuan Saya</span>
      <span class="report-tab-badge">{{ $mySubmissionsCount }}</span>
    </a>
    <a href="{{ route('reports.index', ['tab' => 'approval']) }}" class="report-tab {{ ($activeTab ?? 'all') === 'approval' ? 'active' : '' }}">
      <i data-lucide="clipboard-check"></i>
      <span>Waiting My Approval</span>
      <span class="report-tab-badge {{ $waitingApprovalCount > 0 ? 'alert' : '' }}">{{ $waitingApprovalCount }}</span>
    </a>
  </div>

  <!-- Control Bar -->
  <div class="control-bar">
    <div class="search-box">
      <i data-lucide="search" class="search-icon"></i>
      <input type="text" id="searchInput" placeholder="Cari nomor SSC ID, customer, part, atau model unit...">
    </div>

    <div class="filter-group">
      <select id="typeFilter" class="select-filter">
        <option value="All">Semua Tipe</option>
        <option value="Pre">Pre-Inspection</option>
        <option value="Post">Post-Inspection</option>
      </select>
      <a href="{{ route('inspection.create') }}" class="btn btn-primary btn-sm">
        <i data-lucide="file-plus"></i> Form Inspeksi Baru
      </a>
    </div>
  </div>

  <!-- Table Card -->
  <div class="table-card">
    <div class="table-responsive">
      <table class="qc-table">
        <thead>
          <tr>
            <th>SSC ID</th>
            <th>No. Laporan</th>
            <th>Tanggal</th>
            <th>Customer</th>
            <th>Komponen</th>
            <th>Tipe</th>
            <th>Status</th>
            <th style="text-align: right;">Aksi & Dokumen</th>
          </tr>
        </thead>
        <tbody id="reportTableBody">
          @forelse($reports as $item)
            @php
              $creatorLabel = $item->creator->name ?? ($item->signatures['createdBy']['name'] ?? '-');
              $checkerLabel = $item->checker->name ?? ($item->signatures['checkedBy']['name'] ?? '-');
              $reviewerLabel = $item->reviewer->name ?? ($item->signatures['reviewedBy']['name'] ?? '-');
            @endphp
            <tr data-type="{{ $item->inspection_type }}">
              <td class="mono" style="font-weight: 700; color: var(--primary);">
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
                  Pengaju: {{ $creatorLabel }} &bull; Pemeriksa: {{ $checkerLabel }} &bull; Reviewer: {{ $reviewerLabel }}
                </span>
              </td>
              <td>
                <span class="badge {{ $item->inspection_type === 'Pre' ? 'badge-blue' : 'badge-purple' }}">
                  {{ $item->inspection_type }}
                </span>
              </td>
              <td>
                @if($item->isReviewed())
                  <span class="badge badge-green">Reviewed</span>
                @elseif($item->isDraft())
                  <span class="badge badge-slate">Draft</span>
                @else
                  <span class="badge badge-orange">Waiting Review</span>
                @endif
              </td>
              <td style="text-align: right; white-space: nowrap;">
                @if(($activeTab ?? 'all') === 'approval')
                  <a href="{{ route('reports.review', $item->id) }}" class="btn btn-primary btn-sm" title="Buka Preview & Mulai Review Laporan">
                    <i data-lucide="clipboard-check"></i> Mulai Review
                  </a>
                @else
                  <a href="{{ route('reports.export', $item->id) }}" class="btn btn-secondary btn-sm" title="Unduh File Excel">
                    <i data-lucide="file-spreadsheet"></i> Excel
                  </a>
                  <a href="{{ route('reports.pdf', $item->id) }}" target="_blank" class="btn btn-secondary btn-sm" title="Cetak / PDF">
                    <i data-lucide="printer"></i> PDF
                  </a>
                  @if($item->canBeModifiedBy(auth()->user()))
                    <a href="{{ route('reports.edit', $item->id) }}" class="btn btn-secondary btn-sm" title="Edit Data & Foto">
                      <i data-lucide="edit"></i> Edit
                    </a>
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteReport({{ $item->id }}, '{{ $item->ssc_id }}')" title="Hapus Laporan">
                      <i data-lucide="trash-2"></i>
                    </button>
                  @endif
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" style="text-align: center; padding: 3rem; color: var(--text-subtle);">
                <i data-lucide="clipboard-x" style="width: 38px; height: 38px; margin: 0 auto 0.5rem auto; display: block; opacity: 0.5;"></i>
                @if(($activeTab ?? 'all') === 'approval')
                  <p style="margin-bottom: 0.75rem; font-size: 0.95rem;">Tidak ada laporan yang menunggu review dari akun Anda.</p>
                @elseif(($activeTab ?? 'all') === 'mine')
                  <p style="margin-bottom: 0.75rem; font-size: 0.95rem;">Anda belum mengajukan laporan inspeksi.</p>
                  <a href="{{ route('inspection.create') }}" class="btn btn-primary btn-sm">
                    <i data-lucide="plus"></i> Mulai Input Inspeksi Baru
                  </a>
                @else
                  <p style="margin-bottom: 0.75rem; font-size: 0.95rem;">Belum ada rekaman laporan inspeksi.</p>
                  <a href="{{ route('inspection.create') }}" class="btn btn-primary btn-sm">
                    <i data-lucide="plus"></i> Mulai Input Inspeksi Baru
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
  function deleteReport(id, sscId) {
    if (!confirm(`Apakah Anda yakin ingin menghapus laporan ${sscId}? Data yang dihapus tidak dapat dipulihkan.`)) {
      return;
    }

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
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
        alert(data.message || 'Gagal menghapus laporan.');
        return;
      }
      window.location.reload();
    })
    .catch(err => {
      alert('Gagal menghapus laporan: ' + err.message);
    });
  }

  // Search filter
  document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    const tableBody = document.getElementById('reportTableBody');

    function applyFilter() {
      const q = (searchInput?.value || '').toLowerCase().trim();
      const t = typeFilter?.value || 'All';

      const rows = tableBody.querySelectorAll('tr');
      rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        const type = row.getAttribute('data-type');

        const matchQuery = !q || text.includes(q);
        const matchType = (t === 'All') || (type === t);

        if (matchQuery && matchType) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    if (searchInput) searchInput.addEventListener('input', applyFilter);
    if (typeFilter) typeFilter.addEventListener('change', applyFilter);
  });
</script>
@endsection
