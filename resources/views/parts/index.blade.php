@extends('layouts.app')

@section('title', 'Template Dokumen QC & Komponen')
@section('page_title', 'Template Komponen QC')

@section('top_actions')
  <a href="{{ route('inspection.create') }}" class="btn btn-primary btn-sm">
    <i data-lucide="file-plus"></i> Buka Form Inspeksi
  </a>
@endsection

@section('content')
  <div class="page-header">
    <div class="page-title">
      <h1>Manajemen Template Dokumen QC Komponen</h1>
      <p>Daftar format form inspeksi dimensi dan status integrasi template Excel antar divisi PT SSC Works.</p>
    </div>
  </div>

  <div class="notice-box" style="margin-bottom: 1.5rem;">
    <i data-lucide="info" style="color: var(--primary); flex-shrink: 0;"></i>
    <div>
      <strong>Status Integrasi Multi-Komponen:</strong>
      <p style="margin: 0.25rem 0 0 0; color: var(--text-default); font-size: 0.88rem;">
        Saat ini sistem telah mengintegrasikan penuh template Excel untuk <strong>Output Shaft (FORM-QC-002 REV 04)</strong>. Untuk komponen lainnya (Hydraulic Cylinder, Pinion Gear, Bearing Journal, dsb.), input inspeksi akan otomatis diaktifkan setelah file template Excel resmi dari divisi terkait diintegrasikan.
      </p>
    </div>
  </div>

  <div class="table-card">
    <div class="table-responsive">
      <table class="qc-table">
        <thead>
          <tr>
            <th>No. Dokumen QC</th>
            <th>Nama Komponen</th>
            <th>Divisi / Kategori</th>
            <th>Drawing Standar</th>
            <th>Kunci Toleransi</th>
            <th>Status Integrasi</th>
            <th style="text-align: right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($templates as $tpl)
            <tr>
              <td>
                <span class="mono" style="font-weight: 700; color: var(--primary);">
                  {{ $tpl['form_doc'] }}
                </span>
              </td>
              <td>
                <strong style="color: var(--text-heading); font-size: 0.95rem;">{{ $tpl['name'] }}</strong>
                <span style="font-size: 0.75rem; color: var(--text-subtle); display: block;">Unit: {{ $tpl['model_unit'] }}</span>
              </td>
              <td>
                <span style="font-weight: 500;">{{ $tpl['division'] }}</span>
                <span style="font-size: 0.75rem; color: var(--text-subtle); display: block;">{{ $tpl['category'] }}</span>
              </td>
              <td class="mono" style="font-size: 0.8rem;">
                {{ $tpl['part_no'] }}
              </td>
              <td class="mono" style="font-size: 0.78rem; color: var(--text-subtle); max-width: 240px;">
                {{ $tpl['key_specs'] }}
              </td>
              <td>
                @if($tpl['is_active'])
                  <span class="badge badge-green">
                    <i data-lucide="check-circle" style="width: 12px; height: 12px; display: inline;"></i> Aktif Digunakan
                  </span>
                @else
                  <span class="badge badge-blue" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;">
                    <i data-lucide="clock" style="width: 12px; height: 12px; display: inline;"></i> Menunggu Template Divisi
                  </span>
                @endif
              </td>
              <td style="text-align: right; white-space: nowrap;">
                @if($tpl['is_active'])
                  <a href="{{ route('inspection.create') }}" class="btn btn-primary btn-sm">
                    <i data-lucide="edit"></i> Gunakan
                  </a>
                @else
                  <button type="button" class="btn btn-secondary btn-sm" onclick="alert('Template Excel untuk komponen ini sedang dipersiapkan oleh divisi terkait. Silakan hubungi koordinator QC jika template sudah siap diintegrasikan.')">
                    <i data-lucide="upload"></i> Upload Excel
                  </button>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection
