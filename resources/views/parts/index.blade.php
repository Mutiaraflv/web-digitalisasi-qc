@extends('layouts.app')

@section('title', 'Katalog Komponen & Standar Spesifikasi')
@section('page_title', 'Master Data Komponen Mekanikal')

@section('top_actions')
  <button class="btn btn-primary btn-sm" onclick="alert('Fitur tambah master komponen baru akan aktif pada rilis berikutnya.')">
    <i data-lucide="plus-circle"></i> Tambah Komponen Baru
  </button>
@endsection

@section('content')
  <div class="page-header">
    <div class="page-title">
      <h1>Katalog Master Komponen & Standar Gambar</h1>
      <p>Basis data acuan standar toleransi dimensi, nomor gambar rekayasa, dan format form inspeksi untuk tiap komponen.</p>
    </div>
  </div>

  <div class="table-card">
    <div class="table-responsive">
      <table class="qc-table">
        <thead>
          <tr>
            <th>Nomor Gambar (Drawing)</th>
            <th>Nama Komponen</th>
            <th>Kategori / Aplikasi Unit</th>
            <th>Material Standar</th>
            <th>Ringkasan Kunci Toleransi</th>
            <th>Format Form QC</th>
            <th style="text-align: right;">Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach($parts as $part)
            <tr>
              <td class="mono" style="font-weight: 700; color: var(--primary);">
                {{ $part['part_no'] }}
              </td>
              <td>
                <strong style="color: var(--gray-900); font-size: 0.92rem;">{{ $part['name'] }}</strong>
              </td>
              <td>
                {{ $part['category'] }}
                <span style="font-size: 0.76rem; color: var(--gray-500); display: block;">Unit: {{ $part['model_unit'] }}</span>
              </td>
              <td style="font-size: 0.82rem; color: var(--gray-700);">
                {{ $part['material'] }}
              </td>
              <td class="mono" style="font-size: 0.78rem; color: var(--gray-800); max-width: 260px;">
                {{ $part['key_specs'] }}
              </td>
              <td>
                <span class="doc-tag" style="display: inline-block;">{{ $part['form_doc'] }}</span>
              </td>
              <td style="text-align: right;">
                <span class="badge badge-green">{{ $part['status'] }}</span>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection

