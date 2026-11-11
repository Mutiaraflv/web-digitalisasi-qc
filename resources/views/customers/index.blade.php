@extends('layouts.app')

@section('title', 'Master Pelanggan & Mitra Workshop')
@section('page_title', 'Master Pelanggan')

@section('top_actions')
  <button class="btn btn-primary btn-sm" onclick="window.qcAlert({ title: 'Informasi Fitur', message: 'Fitur tambah mitra pelanggan akan aktif pada rilis berikutnya.', variant: 'info' })">
    <i data-lucide="plus-circle"></i> Tambah Pelanggan Baru
  </button>
@endsection

@section('content')
  <div class="page-header">
    <div class="page-title">
      <h1>Direktori Pelanggan & Mitra Perusahaan</h1>
      <p>Pencatatan data perusahaan pemesan, PIC Quality Engineer, dan riwayat volume job inspeksi komponen.</p>
    </div>
  </div>

  <div class="table-card">
    <div class="table-responsive">
      <table class="qc-table">
        <thead>
          <tr>
            <th>Kode Akun</th>
            <th>Nama Perusahaan Pelanggan</th>
            <th>Sektor Industri / Lokasi Site</th>
            <th>Kontak PIC Quality / Workshop</th>
            <th>Job Aktif</th>
            <th style="text-align: right;">Klasifikasi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($customers as $c)
            <tr>
              <td class="mono" style="font-weight: 700; color: var(--primary);">
                {{ $c['code'] }}
              </td>
              <td>
                <strong style="color: var(--gray-900);">{{ $c['name'] }}</strong>
              </td>
              <td>
                {{ $c['industry'] }}
                <span style="font-size: 0.76rem; color: var(--gray-500); display: block;">Lokasi: {{ $c['location'] }}</span>
              </td>
              <td>
                <span style="font-weight: 600;">{{ $c['pic_name'] }}</span>
                <span style="font-size: 0.76rem; color: var(--gray-500); display: block;" class="mono">{{ $c['pic_phone'] }}</span>
              </td>
              <td>
                <span class="badge badge-blue">{{ $c['active_jobs'] }} Job Sedang Berjalan</span>
              </td>
              <td style="text-align: right;">
                <span class="badge {{ $c['status'] === 'Akun Prioritas' ? 'badge-purple' : 'badge-green' }}">
                  {{ $c['status'] }}
                </span>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection

