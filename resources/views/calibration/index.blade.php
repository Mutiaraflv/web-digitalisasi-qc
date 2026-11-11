@extends('layouts.app')

@section('title', 'Manajemen Alat Ukur & Kalibrasi')
@section('page_title', 'Alat Ukur & Kalibrasi (ISO 9001)')

@section('top_actions')
  <button class="btn btn-primary btn-sm" onclick="window.qcAlert({ title: 'Informasi Fitur', message: 'Fitur registrasi alat ukur baru akan aktif pada rilis berikutnya.', variant: 'info' })">
    <i data-lucide="plus-circle"></i> Registrasi Alat Ukur
  </button>
@endsection

@section('content')
  <div class="page-header">
    <div class="page-title">
      <h1>Inventaris Alat Ukur & Jadwal Kalibrasi</h1>
      <p>Ketertelusuran masa berlaku sertifikat kalibrasi KAN/Badan Metrologi Resmi untuk seluruh instrumen QC di workshop.</p>
    </div>
  </div>

  <div class="table-card">
    <div class="table-responsive">
      <table class="qc-table">
        <thead>
          <tr>
            <th>Kode Alat Ukur</th>
            <th>Nama Instrumen & Rentang Ukur</th>
            <th>Merk / Pabrikan</th>
            <th>Resolusi Alat</th>
            <th>Tanggal Kalibrasi Terakhir</th>
            <th>Berlaku Hingga</th>
            <th style="text-align: right;">Status Kelayakan</th>
          </tr>
        </thead>
        <tbody>
          @foreach($tools as $t)
            <tr>
              <td class="mono" style="font-weight: 700; color: var(--primary);">
                {{ $t['tool_code'] }}
              </td>
              <td>
                <strong style="color: var(--gray-900);">{{ $t['name'] }}</strong>
                <span style="font-size: 0.74rem; color: var(--gray-500); display: block;" class="mono">Sertifikat: {{ $t['cert_no'] }}</span>
              </td>
              <td>{{ $t['brand'] }}</td>
              <td class="mono" style="font-size: 0.8rem; font-weight: 600;">{{ $t['resolution'] }}</td>
              <td class="mono">{{ $t['last_calibrated'] }}</td>
              <td class="mono" style="font-weight: 600; color: #047857;">{{ $t['valid_until'] }}</td>
              <td style="text-align: right;">
                <span class="badge badge-green">
                  <i data-lucide="check-circle-2" style="width: 12px; height: 12px;"></i> {{ $t['status'] }}
                </span>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection

