@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('top_actions')
  <a href="{{ route('inspection.create') }}" class="btn btn-primary btn-sm">
    <i data-lucide="plus"></i> Input Inspeksi Baru
  </a>
@endsection

@section('content')
  <div class="page-header">
    <div class="page-title">
      <h1>Ringkasan Mutu & Inspeksi</h1>
      <p>Data aktivitas inspeksi komponen dan kepatuhan standar toleransi workshop PT SSC Works.</p>
    </div>
  </div>

  <!-- Corporate KPI Grid -->
  <div class="stats-grid">
    <div class="stat-card">
      <p>Total Inspeksi</p>
      <h3>{{ $totalReports }}</h3>
      <div class="stat-sub">Semua jenis komponen</div>
    </div>
    
    <div class="stat-card">
      <p>Lolos (Accept)</p>
      <h3 style="color: var(--success);">{{ $acceptCount }}</h3>
      <div class="stat-sub">Memenuhi toleransi</div>
    </div>

    <div class="stat-card">
      <p>Temuan (Reject)</p>
      <h3 style="color: var(--danger);">{{ $rejectCount }}</h3>
      <div class="stat-sub">Perlu perbaikan / NCR</div>
    </div>

    <div class="stat-card">
      <p>First Pass Yield</p>
      <h3>{{ $yieldRate }}%</h3>
      <div class="stat-sub">Tingkat kelulusan mutu</div>
    </div>
  </div>

  <!-- 2-Column Corporate Layout -->
  <div class="dashboard-grid-2">
    <!-- Inspections Table -->
    <div class="card">
      <div class="card-header">
        <h3>Inspeksi Terbaru</h3>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
      </div>
      <div class="table-responsive">
        <table class="qc-table">
          <thead>
            <tr>
              <th>SSC ID</th>
              <th>Komponen</th>
              <th>Pelanggan</th>
              <th>Tipe</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentReports as $report)
              <tr>
                <td class="mono" style="font-weight: 600; color: var(--primary);">
                  {{ $report->ssc_id }}
                </td>
                <td>
                  <strong>{{ $report->part_name }}</strong>
                  <span style="font-size: 0.75rem; color: var(--text-subtle); display: block;">{{ $report->model_unit }}</span>
                </td>
                <td>{{ $report->customer ?? '-' }}</td>
                <td>
                  <span class="badge {{ $report->inspection_type === 'Pre' ? 'badge-blue' : 'badge-purple' }}">
                    {{ $report->inspection_type }}
                  </span>
                </td>
                <td>
                  <span class="badge {{ $report->has_rejects ? 'badge-red' : 'badge-green' }}">
                    {{ $report->has_rejects ? 'REJECT' : 'ACCEPT' }}
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-subtle);">
                  Belum ada data inspeksi tercatat.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Quick Links -->
    <div class="card">
      <div class="card-header">
        <h3>Menu Cepat Operasional</h3>
      </div>
      <div class="card-body" style="display: flex; flex-direction: column; gap: 0.5rem;">
        <a href="{{ route('inspection.create') }}" class="btn btn-primary btn-sm" style="justify-content: flex-start;">
          <i data-lucide="file-plus-2"></i> Input Inspeksi Komponen Baru
        </a>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm" style="justify-content: flex-start;">
          <i data-lucide="archive"></i> Riwayat & Arsip Laporan QC
        </a>
        <a href="{{ route('templates.index') }}" class="btn btn-secondary btn-sm" style="justify-content: flex-start;">
          <i data-lucide="file-spreadsheet"></i> Status Template Komponen Antar Divisi
        </a>
        <a href="{{ route('ncrs.index') }}" class="btn btn-secondary btn-sm" style="justify-content: flex-start;">
          <i data-lucide="alert-octagon"></i> Log Temuan Non-Conformance (NCR)
        </a>
      </div>
    </div>
  </div>
@endsection
