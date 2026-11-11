@extends('layouts.app')

@section('title', 'Manajemen Non-Conformance Report (NCR)')
@section('page_title', 'Non-Conformance Reports (NCR)')

@section('top_actions')
  <button class="btn btn-danger btn-sm" onclick="window.qcAlert({ title: 'Penerbitan NCR', message: 'Form penerbitan NCR baru dapat dibuka langsung dari hasil temuan inspeksi.', variant: 'info' })">
    <i data-lucide="alert-octagon"></i> Buat Laporan NCR
  </button>
@endsection

@section('content')
  <div class="page-header">
    <div class="page-title">
      <h1>Daftar Temuan Ketidaksesuaian (NCR)</h1>
      <p>Pelacakan komponen dengan penyimpangan toleransi, rencana tindakan perbaikan (Corrective Action), dan disposisi rekayasa.</p>
    </div>
  </div>

  <div class="table-card">
    <div class="table-responsive">
      <table class="qc-table">
        <thead>
          <tr>
            <th>Nomor NCR</th>
            <th>Ref. SSC ID / Part</th>
            <th>Pelanggan</th>
            <th>Uraian Ketidaksesuaian (Problem Description)</th>
            <th>Disposisi Tindakan Teknis</th>
            <th>Tingkat Keparahan</th>
            <th style="text-align: right;">Status NCR</th>
          </tr>
        </thead>
        <tbody>
          @foreach($ncrs as $ncr)
            <tr>
              <td class="mono" style="font-weight: 700; color: var(--danger);">
                {{ $ncr['ncr_no'] }}
              </td>
              <td>
                <span class="mono" style="font-weight: 600; color: var(--primary);">{{ $ncr['ssc_id'] }}</span>
                <span style="font-size: 0.76rem; color: var(--gray-600); display: block;">{{ $ncr['part_name'] }}</span>
              </td>
              <td style="font-weight: 500;">{{ $ncr['customer'] }}</td>
              <td style="max-width: 280px; font-size: 0.82rem; color: var(--gray-800);">
                {{ $ncr['issue_description'] }}
              </td>
              <td style="max-width: 250px; font-size: 0.82rem; color: var(--gray-700);">
                <strong>{{ $ncr['disposition'] }}</strong>
                <span style="font-size: 0.74rem; color: var(--gray-500); display: block;">PIC: {{ $ncr['pic_engineer'] }}</span>
              </td>
              <td>
                <span class="badge {{ $ncr['severity'] === 'Kritis' ? 'badge-red' : 'badge-orange' }}">
                  {{ $ncr['severity'] }}
                </span>
              </td>
              <td style="text-align: right;">
                <span class="badge {{ $ncr['status'] === 'Resolved / Closed' ? 'badge-green' : 'badge-purple' }}">
                  {{ $ncr['status'] }}
                </span>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection

