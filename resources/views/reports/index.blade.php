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
      <div class="stat-sub">Semua inspeksi tercatat</div>
    </div>
    <div class="stat-card">
      <p>Lolos (Accept)</p>
      <h3 style="color: var(--success);">{{ $acceptCount }}</h3>
      <div class="stat-sub">Sesuai toleransi</div>
    </div>
    <div class="stat-card">
      <p>Temuan (Reject)</p>
      <h3 style="color: var(--danger);">{{ $rejectCount }}</h3>
      <div class="stat-sub">Di luar toleransi</div>
    </div>
    <div class="stat-card">
      <p>Tingkat Kelulusan</p>
      <h3>{{ $totalCount > 0 ? round(($acceptCount / $totalCount) * 100, 1) : 100 }}%</h3>
      <div class="stat-sub">Rasio lolos mutu</div>
    </div>
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
            <tr data-type="{{ $item->inspection_type }}">
              <td class="mono" style="font-weight: 700; color: var(--primary);">
                {{ $item->ssc_id }}
              </td>
              <td class="mono">{{ $item->report_no ?? '-' }}</td>
              <td>{{ $item->date ? \Carbon\Carbon::parse($item->date)->format('Y-m-d') : '-' }}</td>
              <td>{{ $item->customer ?? '-' }}</td>
              <td>
                <strong>{{ $item->part_name }}</strong>
                <span style="color: var(--text-subtle); font-size: 0.75rem; display: block;">{{ $item->model_unit }} &bull; {{ $item->doc_no }}</span>
              </td>
              <td>
                <span class="badge {{ $item->inspection_type === 'Pre' ? 'badge-blue' : 'badge-purple' }}">
                  {{ $item->inspection_type }}
                </span>
              </td>
              <td>
                <span class="badge {{ $item->has_rejects ? 'badge-red' : 'badge-green' }}">
                  {{ $item->has_rejects ? 'REJECT' : 'ACCEPT' }}
                </span>
              </td>
              <td style="text-align: right; white-space: nowrap;">
                <a href="{{ route('reports.export', $item->id) }}" class="btn btn-secondary btn-sm" title="Unduh File Excel">
                  <i data-lucide="file-spreadsheet"></i> Excel
                </a>
                <a href="{{ route('reports.pdf', $item->id) }}" target="_blank" class="btn btn-secondary btn-sm" title="Cetak / PDF">
                  <i data-lucide="printer"></i> PDF
                </a>
                <a href="{{ route('reports.edit', $item->id) }}" class="btn btn-secondary btn-sm" title="Edit Data & Foto">
                  <i data-lucide="edit"></i> Edit
                </a>
                <button type="button" class="btn btn-secondary btn-sm" onclick="duplicateReport({{ $item->id }})" title="Duplikat Laporan">
                  <i data-lucide="copy"></i>
                </button>
                <button type="button" class="btn btn-danger btn-sm" onclick="deleteReport({{ $item->id }}, '{{ $item->ssc_id }}')" title="Hapus Laporan">
                  <i data-lucide="trash-2"></i>
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" style="text-align: center; padding: 3rem; color: var(--text-subtle);">
                <i data-lucide="clipboard-x" style="width: 38px; height: 38px; margin: 0 auto 0.5rem auto; display: block; opacity: 0.5;"></i>
                <p style="margin-bottom: 0.75rem; font-size: 0.95rem;">Belum ada rekaman laporan inspeksi.</p>
                <a href="{{ route('inspection.create') }}" class="btn btn-primary btn-sm">
                  <i data-lucide="plus"></i> Mulai Input Inspeksi Baru
                </a>
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
    .then(r => r.json())
    .then(data => {
      window.location.reload();
    })
    .catch(err => {
      alert('Gagal menghapus laporan: ' + err.message);
    });
  }

  function duplicateReport(id) {
    if (!confirm('Duplikat laporan inspeksi ini?')) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    fetch(`/reports/${id}/duplicate`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrf,
        'Accept': 'application/json'
      }
    })
    .then(r => r.json())
    .then(data => {
      window.location.reload();
    })
    .catch(err => {
      alert('Gagal menduplikat laporan: ' + err.message);
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
