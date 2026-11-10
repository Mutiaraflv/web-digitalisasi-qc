<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>FORM-QC-002 - {{ $report->ssc_id }} - PT SSC Works</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap');

    @page {
      size: A4 portrait;
      margin: 8mm 8mm;
    }
    * {
      box-sizing: border-box;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }
    body {
      font-family: 'Inter', Arial, sans-serif;
      font-size: 7.2pt;
      color: #1e293b;
      margin: 0;
      padding: 0;
      background: #e2e8f0;
    }
    .print-actions {
      padding: 10px 20px;
      background: #1e293b;
      color: #fff;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 100;
    }
    @media print {
      body { background: #fff; }
      .print-actions { display: none !important; }
      .a4-page {
        margin: 0 0 0 0 !important;
        box-shadow: none !important;
        page-break-after: always;
        break-after: page;
        min-height: 98vh;
      }
    }
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 7px 14px;
      font-size: 12px;
      font-weight: 600;
      border-radius: 4px;
      cursor: pointer;
      border: 1px solid #475569;
      background: #fff;
      color: #0f172a;
      text-decoration: none;
    }
    .btn-primary { background: #2563eb; color: #fff; border-color: #2563eb; }

    /* Page Container */
    .a4-page {
      width: 100%;
      max-width: 790px;
      margin: 15px auto;
      background: #fff;
      border: 1.5px solid #475569;
      padding: 0;
      box-shadow: 0 4px 18px rgba(15, 23, 42, 0.14);
      position: relative;
    }

    /* Common Header for Pages */
    .header-table {
      width: 100%;
      border-collapse: collapse;
      table-layout: fixed;
      border-bottom: 2px solid #1e40af;
    }
    .header-table td {
      border: 1px solid #cbd5e1;
      padding: 0;
      vertical-align: middle;
    }
    .header-logo-cell {
      width: 37%;
      padding: 3px 6px 3px 8px !important;
      text-align: left;
      background: #fff;
    }
    .header-logo-cell img {
      max-width: 100%;
      height: 52px;
      object-fit: contain;
      display: block;
      margin: 0 auto 0 2px;
    }
    .header-title-cell {
      text-align: center;
      font-family: 'Inter', Arial, sans-serif;
      font-weight: 900;
      font-size: 11pt;
      letter-spacing: 0.8px;
      padding: 6px 4px !important;
      background: #1e40af;
      color: #ffffff;
      border-bottom: 1px solid #1d4ed8;
    }
    .header-ctrl-cell {
      padding: 0 !important;
      background: #f8fafc;
    }
    .ctrl-table {
      width: 100%;
      border-collapse: collapse;
      table-layout: fixed;
      font-size: 6.8pt;
    }
    .ctrl-table td {
      border: 1px solid #cbd5e1;
      padding: 2px 4px;
      vertical-align: middle;
    }
    .ctrl-table .ctrl-lbl {
      font-weight: 700;
      background: #f8fafc;
      white-space: nowrap;
      color: #334155;
    }
    .ctrl-table .ctrl-colon {
      width: 1.6%;
      text-align: center;
      padding: 0;
      font-weight: 700;
    }
    .ctrl-table .ctrl-val {
      background: #fff;
    }
    .ctrl-split-val {
      display: flex;
      width: 100%;
      align-items: center;
    }
    .ctrl-date-part {
      flex: 1;
      text-align: center;
      border-right: 1px solid #cbd5e1;
      padding: 1px 4px;
    }
    .ctrl-rev-part {
      padding: 1px 8px;
      text-align: center;
      white-space: nowrap;
    }

    /* Metadata Table (Page 1) */
    .meta-table {
      width: 100%;
      border-collapse: collapse;
      table-layout: fixed;
      font-size: 6.8pt;
      border-bottom: 1.5px solid #1e40af;
    }
    .meta-table td {
      border: 1px solid #cbd5e1;
      padding: 2.5px 4px;
      vertical-align: middle;
    }
    .meta-table .lbl {
      font-weight: 700;
      background: #eff6ff;
      color: #1e3a8a;
      white-space: nowrap;
    }
    .meta-table .colon {
      width: 1%;
      text-align: center;
      padding: 0;
      background: #eff6ff;
      font-weight: 700;
      color: #1e3a8a;
    }
    .meta-table .val {
      background: #ffffff;
      color: #1e293b;
      font-family: 'Inter', Arial, sans-serif;
    }
    .meta-table .mono {
      font-family: 'JetBrains Mono', monospace;
    }

    /* Section Bars */
    .bar-grey {
      background: #eff6ff;
      color: #1e3a8a;
      font-weight: 700;
      font-size: 7.5pt;
      padding: 4px 6px 4px 10px;
      border-bottom: 1px solid #bfdbfe;
      border-top: 1px solid #bfdbfe;
      border-left: 4px solid #1d4ed8;
      letter-spacing: 0.3px;
      text-align: left;
    }
    .bar-yellow {
      background: #fffbeb;
      color: #92400e;
      font-weight: 700;
      font-size: 7.5pt;
      padding: 4px 6px 4px 10px;
      border-bottom: 1px solid #fcd34d;
      border-top: 1px solid #fcd34d;
      border-left: 4px solid #f59e0b;
      letter-spacing: 0.3px;
      text-align: left;
    }

    /* Blueprint Diagram */
    .blueprint-box {
      text-align: center;
      padding: 4px 6px;
      border-bottom: 1.5px solid #cbd5e1;
      background: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }
    .blueprint-box img {
      width: 100%;
      max-width: 100%;
      height: auto;
      max-height: 220px;
      object-fit: contain;
      display: block;
      margin: 0 auto;
    }

    /* Finding Table */
    .finding-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 7pt;
      border-bottom: 1.5px solid #cbd5e1;
    }
    .finding-table th {
      background: #eff6ff;
      border: 1px solid #cbd5e1;
      padding: 2px 4px;
      font-weight: 700;
      text-align: center;
      color: #1e3a8a;
    }
    .finding-table td {
      border: 1px solid #cbd5e1;
      padding: 2px 4px;
      vertical-align: middle;
      overflow: hidden;
      box-sizing: border-box;
    }
    .finding-img-wrap {
      width: 100%;
      height: 75px;
      max-height: 75px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f8fafc;
      overflow: hidden;
      box-sizing: border-box;
      border-radius: 4px;
    }
    .finding-img-wrap img {
      max-width: 100%;
      max-height: 75px;
      object-fit: contain;
      border-radius: 4px;
    }

    /* Two-Column Section (Photos Left, Measurement Right) */
    .split-section {
      display: grid;
      grid-template-columns: 50% 50%;
      border-bottom: 1px solid #cbd5e1;
    }
    .split-col-left {
      border-right: 1px solid #cbd5e1;
      display: flex;
      flex-direction: column;
    }
    .split-col-right {
      display: flex;
      flex-direction: column;
    }
    .col-header {
      background: #eff6ff;
      font-weight: 700;
      font-size: 7pt;
      text-align: center;
      padding: 2px 4px;
      border-bottom: 1px solid #cbd5e1;
      color: #1e3a8a;
    }

    /* 2x2 Photo Grid - Fixed Square 110px x 110px per box */
    .photo-grid-2x2 {
      display: grid;
      grid-template-columns: 110px 110px;
      grid-template-rows: 110px 110px;
      width: 224px;
      height: 224px;
      min-width: 224px;
      max-width: 224px;
      min-height: 224px;
      max-height: 224px;
      gap: 2px;
      padding: 1px;
      background: #cbd5e1;
      box-sizing: border-box;
      margin: 4px auto;
      justify-content: center;
      align-content: center;
      border-radius: 4px;
    }
    .photo-slot {
      width: 110px;
      height: 110px;
      min-width: 110px;
      max-width: 110px;
      min-height: 110px;
      max-height: 110px;
      box-sizing: border-box;
      background: #f8fafc;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      position: relative;
      border-radius: 3px;
    }
    .photo-slot img {
      width: 110px;
      height: 110px;
      min-width: 110px;
      max-width: 110px;
      min-height: 110px;
      max-height: 110px;
      object-fit: cover;
      display: block;
      border-radius: 3px;
    }
    .photo-slot .slot-placeholder {
      font-size: 6.5pt;
      font-weight: 600;
      color: #94a3b8;
      font-style: italic;
      text-align: center;
      padding: 2px;
      line-height: 1.2;
    }

    /* Data Tables inside Right Column */
    .sub-data-title {
      font-weight: 700;
      font-size: 6.8pt;
      text-align: center;
      padding: 1.5px;
      border-bottom: 1px solid #cbd5e1;
      background: #f8fafc;
      color: #334155;
    }
    .data-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 6.8pt;
    }
    .data-table th {
      border: 1px solid #cbd5e1;
      padding: 1.5px 2px;
      background: #eff6ff;
      font-weight: 700;
      text-align: center;
      color: #1e3a8a;
    }
    .data-table td {
      border: 1px solid #cbd5e1;
      padding: 1.5px 2px;
      text-align: center;
      font-family: 'JetBrains Mono', monospace;
      font-size: 6.5pt;
    }
    .data-table td.text-left {
      text-align: left;
      font-family: 'Inter', Arial, sans-serif;
    }
    .data-table tbody tr:nth-child(even) {
      background: #f8fafc;
    }

    /* Bullet Findings */
    .bullet-findings {
      padding: 4px 6px;
      font-size: 6.6pt;
      line-height: 1.35;
      flex: 1;
      color: #334155;
    }
    .bullet-findings div {
      margin-bottom: 2px;
    }
    .bullet-arrow {
      font-weight: 700;
      margin-right: 3px;
      color: #1d4ed8;
    }

    /* Legend Footer (A: Accept, R: Reject) */
    .legend-footer {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      gap: 15px;
      padding: 2px 10px;
      font-size: 6.8pt;
      font-weight: 700;
      border-bottom: 1px solid #cbd5e1;
      background: #f8fafc;
      color: #334155;
    }
    .badge-ar-a {
      background: #ecfdf5;
      color: #059669;
      padding: 1px 8px;
      border: 1px solid #059669;
      border-radius: 10px;
      font-weight: 700;
      font-size: 6.5pt;
    }
    .badge-ar-r {
      background: #fef2f2;
      color: #dc2626;
      padding: 1px 8px;
      border: 1px solid #dc2626;
      border-radius: 10px;
      font-weight: 700;
      font-size: 6.5pt;
    }
    .cell-reject {
      background: #fef2f2 !important;
      color: #dc2626 !important;
      font-weight: 700;
      font-family: 'Inter', Arial, sans-serif !important;
      font-size: 6.5pt;
    }
    .cell-accept {
      background: #ecfdf5 !important;
      color: #059669 !important;
      font-weight: 700;
      font-family: 'Inter', Arial, sans-serif !important;
      font-size: 6.5pt;
    }

    /* TIR Table (Page 5) */
    .tir-table {
      width: 80%;
      margin: 4px auto;
      border-collapse: collapse;
      font-size: 7pt;
    }
    .tir-table th {
      background: #eff6ff;
      border: 1px solid #cbd5e1;
      padding: 2px 4px;
      text-align: center;
      font-weight: 700;
      color: #1e3a8a;
    }
    .tir-table td {
      border: 1px solid #cbd5e1;
      padding: 2px 4px;
      text-align: center;
      font-family: 'JetBrains Mono', monospace;
      font-size: 6.8pt;
    }
    .tir-table tbody tr:nth-child(even) {
      background: #f8fafc;
    }

    /* Recommendation Table (Page 6) */
    .rec-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 7.2pt;
    }
    .rec-table th {
      background: #1e40af;
      border: 1px solid #1d4ed8;
      padding: 3px 6px;
      font-weight: 700;
      text-align: center;
      color: #fff;
      letter-spacing: 0.3px;
    }
    .rec-table td {
      border: 1px solid #cbd5e1;
      padding: 3.5px 6px;
      vertical-align: top;
    }
    .rec-table tbody tr:nth-child(even) td {
      background: #f8fafc;
    }

    /* Signatures (Page 6) */
    .sig-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 7.2pt;
      margin-top: 15px;
      border: 1.5px solid #cbd5e1;
    }
    .sig-table th {
      border: 1px solid #cbd5e1;
      padding: 3px 6px;
      text-align: left;
      font-weight: 700;
      background: #1e40af;
      color: #ffffff;
      letter-spacing: 0.3px;
    }
    /* Page 6 Signatures Always at the Bottom Edge */
    .a4-page.page-6-signatures {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      min-height: 1040px;
    }
    .a4-page.page-6-signatures .page-top-content {
      width: 100%;
    }
    .a4-page.page-6-signatures .page-bottom-signatures {
      width: 100%;
      margin-top: auto;
      padding-top: 20px;
    }
    .a4-page.page-6-signatures .sig-table {
      margin-top: 0;
      border-left: none;
      border-right: none;
      border-bottom: none;
    }

    @media print {
      .a4-page.page-6-signatures {
        min-height: 278mm !important;
        height: 278mm !important;
        max-height: 279mm !important;
        box-sizing: border-box !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        page-break-after: auto !important;
        break-after: auto !important;
      }
      .a4-page.page-6-signatures .page-bottom-signatures {
        margin-top: auto !important;
        padding-top: 20px !important;
        width: 100% !important;
      }
    }

    .sig-table td {
      border: 1px solid #cbd5e1;
      padding: 6px 8px;
      vertical-align: bottom;
      height: 95px;
      min-height: 95px;
      width: 33.33%;
    }
    .sig-name {
      font-weight: 700;
      border-bottom: 1px solid #94a3b8;
      padding-bottom: 2px;
      margin-bottom: 3px;
      margin-top: 45px;
      color: #1e3a8a;
    }
    .sig-meta {
      display: flex;
      justify-content: space-between;
      color: #475569;
      font-size: 6.8pt;
    }
  
    /* Empty Photo Placeholders */
    .empty-blueprint-msg {
      font-size: 7.5pt;
      color: #94a3b8;
      font-style: italic;
      padding: 30px;
      border: 1px dashed #cbd5e1;
      width: 90%;
      margin: 10px auto;
      text-align: center;
      background: #f8fafc;
    }
    .photo-slot .slot-placeholder {
      font-size: 6.5pt;
      color: #94a3b8;
      text-align: center;
      padding: 4px;
      background: #f1f5f9;
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
    }

  </style>
</head>
<body>

  <!-- Top Action Bar (Hidden on print) -->
  <div class="print-actions">
    <div>
      <strong>PT. SSC WORKS &mdash; COMPONENT CONDITION REPORT (FORM-QC-002 REV 04)</strong>
      <span style="margin-left: 10px; color: #94a3b8;">Total 6 Lembar Dokumen Resmi</span>
    </div>
    <div>
      <button class="btn" onclick="window.close()">✕ Tutup</button>
      <button class="btn btn-primary" onclick="window.print()">🖨 Cetak Semua (6 Halaman A4)</button>
    </div>
  </div>

  @php
    $sec  = is_array($report->sections)        ? $report->sections        : json_decode($report->sections        ?? '[]', true);
    $tir  = is_array($report->tir)             ? $report->tir             : json_decode($report->tir             ?? '[]', true);
    $recs = is_array($report->recommendations) ? $report->recommendations : json_decode($report->recommendations ?? '[]', true);
    $sigs = is_array($report->signatures)      ? $report->signatures      : json_decode($report->signatures      ?? '[]', true);
    $photos = is_array($report->photos)        ? $report->photos          : json_decode($report->photos          ?? '[]', true);
    $tItems = $tir['items'] ?? [];

    $v = fn($arr, ...$keys) => array_reduce($keys, fn($c, $k) => is_array($c) ? ($c[$k] ?? null) : null, $arr) ?? '-';
    $isReject = fn($val) => ($val === 'R' || $val === 'Reject');
    $cellAr = fn($val) => $isReject($val) ? 'cell-reject' : 'cell-accept';

    $dateStr = $report->date ? \Carbon\Carbon::parse($report->date)->format('d-M-y') : date('d-M-y');
    $effDateStr = $report->effective_date ? \Carbon\Carbon::parse($report->effective_date)->format('d-M-Y') : '28-Jan-2022';

    // Helper to render photo slot cleanly
    $renderPhoto = function($key, $label = 'Foto') use ($photos) {
      if (!empty($photos[$key])) {
        return '<img src="' . asset($photos[$key]) . '" alt="' . $label . '">';
      }
      return '<div class="slot-placeholder"><span>' . $label . '</span></div>';
    };

  @endphp

  <!-- =====================================================================
       LEMBAR 1 DARI 6 (PAGE 1)
  ===================================================================== -->
  <div class="a4-page">
    
    <!-- Header (Kop Dokumen) -->
    <table class="header-table">
      <tr>
        <td class="header-logo-cell" rowspan="2" style="width: 37%;">
          <img src="{{ asset('images/doc_assets/kop_logo_perfect.png') }}?v={{ file_exists(public_path('images/doc_assets/kop_logo_perfect.png')) ? filemtime(public_path('images/doc_assets/kop_logo_perfect.png')) : time() }}" alt="PT. SSC WORKS">
        </td>
        <td class="header-title-cell" style="width: 63%;">
          COMPONENT CONDITION REPORT
        </td>
      </tr>
      <tr>
        <td class="header-ctrl-cell" style="width: 63%;">
          <table class="ctrl-table">
            <tr>
              <td class="ctrl-lbl" style="width: 16.67%;">Document No</td>
              <td class="ctrl-colon" style="width: 1.6%;">:</td>
              <td class="ctrl-val bold" style="width: 31.73%;">{{ $report->doc_no ?? 'FORM-QC-002' }}</td>
              <td class="ctrl-lbl" style="width: 16.67%;">Responsibility</td>
              <td class="ctrl-colon" style="width: 1.6%;">:</td>
              <td class="ctrl-val" style="width: 31.73%;">{{ $report->responsibility ?? 'Quality Control' }}</td>
            </tr>
            <tr>
              <td class="ctrl-lbl">Effective Date.</td>
              <td class="ctrl-colon">:</td>
              <td class="ctrl-val" style="padding: 0;">
                <div class="ctrl-split-val">
                  <span class="ctrl-date-part bold">{{ $effDateStr }}</span>
                  <span class="ctrl-rev-part">Rev. <strong>{{ $report->rev ?? '04' }}</strong></span>
                </div>
              </td>
              <td class="ctrl-lbl">Page</td>
              <td class="ctrl-colon">:</td>
              <td class="ctrl-val" style="text-align: center; letter-spacing: 1px;">
                <strong>1</strong> &nbsp; of &nbsp; <strong>6</strong>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>

    <!-- Metadata Grid -->
    <table class="meta-table">
      <tr>
        <td class="lbl" style="width: 9%;">No.</td>
        <td class="colon" style="width: 1%;">:</td>
        <td class="val mono" style="width: 7.5%; text-align: center;">{{ $report->report_no ?? '0742' }}</td>
        
        <td class="lbl" style="width: 5.5%;">Date</td>
        <td class="colon" style="width: 1%;">:</td>
        <td class="val" style="width: 13%; text-align: center;">{{ $dateStr }}</td>
        
        <td class="lbl" style="width: 10.5%;">Customer</td>
        <td class="colon" style="width: 1%;">:</td>
        <td class="val bold" style="width: 20%; line-height: 1.25; font-size: 6.8pt;">{{ $report->customer ?? 'AMMAN MINERAL NUSA TENGGARA, PT' }}</td>
        
        <td class="lbl" style="width: 10.5%;">Part Name</td>
        <td class="colon" style="width: 1%;">:</td>
        <td class="val bold" style="width: 20%;">{{ $report->part_name ?? 'OUTPUT SHAFT' }}</td>
      </tr>
      <tr>
        <td class="lbl">Inspection</td>
        <td class="colon">:</td>
        <td class="val" colspan="4" style="padding-left: 6px;">
          <span>{{ ($report->inspection_type ?? 'Pre') === 'Pre' ? '☑' : '☐' }} Pre</span> &nbsp;&nbsp;&nbsp;&nbsp;
          <span>{{ ($report->inspection_type ?? '') === 'Post' ? '☑' : '☐' }} Post</span>
        </td>
        
        <td class="lbl">Job No.</td>
        <td class="colon">:</td>
        <td class="val">{{ $report->job_no ?? '-' }}</td>
        
        <td class="lbl">Model/Unit</td>
        <td class="colon">:</td>
        <td class="val">{{ $report->model_unit ?? 'HT051' }}</td>
      </tr>
      <tr>
        <td class="lbl">SSC ID</td>
        <td class="colon">:</td>
        <td class="val mono bold" colspan="4" style="padding-left: 6px;">{{ $report->ssc_id }}</td>
        
        <td class="lbl">Quote No.</td>
        <td class="colon">:</td>
        <td class="val">{{ $report->quote_no ?? '-' }}</td>
        
        <td class="lbl">Part No.</td>
        <td class="colon">:</td>
        <td class="val">{{ $report->part_no ?? '-' }}</td>
      </tr>
    </table>

    <!-- Drawing / Pictures / Dimension -->
    <div class="bar-grey">Drawing / Pictures / Dimension</div>
    <div class="blueprint-box">
      @if(!empty($photos['drawing_dimension']))
        <img src="{{ asset($photos['drawing_dimension']) }}" alt="Drawing Dimensi">
      @else
        <div class="slot-placeholder" style="height: 135px; display: flex; align-items: center; justify-content: center; background: #fafafa; border: 1px dashed #cbd5e1; color: #94a3b8; font-size: 7.5pt; font-weight: 600;">
          <span>[ Drawing / Pictures / Dimension Belum Diunggah ]</span>
        </div>
      @endif
    </div>

    <!-- Finding Section -->
    <div class="bar-grey">FINDING</div>
    <table class="finding-table">
      <thead>
        <tr>
          <th style="width: 35px;">NO</th>
          <th style="width: 50%;">IMAGE</th>
          <th>DESCRIPTION</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="text-align: center; font-weight: 700;">00</td>
          <td>
            <div class="finding-img-wrap">
              @if(!empty($photos['finding_component']))
                <img src="{{ asset($photos['finding_component']) }}" alt="Foto Komponen Utuh">
              @elseif(!empty($photos['full_component']))
                <img src="{{ asset($photos['full_component']) }}" alt="Foto Komponen Utuh">
              @else
                <div class="slot-placeholder" style="width: 100%; height: 75px; max-height: 75px; display: flex; align-items: center; justify-content: center; background: #fafafa; border: 1px dashed #cbd5e1; color: #94a3b8; font-size: 7pt; font-weight: 600; box-sizing: border-box; margin: 0;">
                  <span>[ Foto Komponen Utuh Belum Diunggah ]</span>
                </div>
              @endif
            </div>
          </td>
          <td style="vertical-align: top; padding: 6px;">
            <div><span class="bullet-arrow">&gt;</span> Full image received component OUTPUT SHAFT</div>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Yellow Banner: Area A & Spline -->
    <div class="bar-yellow">OD Area "A" and OD Spline</div>
    <div class="split-section">
      <!-- Left: 2x2 Photo Grid -->
      <div class="split-col-left">
        <div class="col-header">Image of OD Spline</div>
        <div class="photo-grid-2x2">
          <div class="photo-slot">{!! $renderPhoto('area_a_visual_1', 'Foto 1') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_a_visual_2', 'Foto 2') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_a_visual_3', 'Foto 3') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_a_visual_4', 'Foto 4') !!}</div>
        </div>
      </div>
      <!-- Right: Measurements & Bullets -->
      <div class="split-col-right">
        <div class="col-header">Actual visual &amp; measurement</div>
        <div class="sub-data-title">Actual Measurement</div>
        <table class="data-table">
          <thead>
            <tr>
              <th style="width: 35px;">Item</th>
              <th>Spec</th>
              <th style="width: 50px;">X</th>
              <th style="width: 50px;">Y</th>
              <th style="width: 50px;">Z</th>
              <th style="width: 35px;">A/R</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td style="font-weight: 700;">A</td>
              <td>Ø79 ± 0,25</td>
              <td>{{ $v($sec, 'item01', 'x') ?: '78,850' }}</td>
              <td>{{ $v($sec, 'item01', 'y') ?: '78,865' }}</td>
              <td>{{ $v($sec, 'item01', 'z') ?: '78,865' }}</td>
              <td class="{{ $cellAr($v($sec, 'item01', 'ar')) }}">{{ $v($sec, 'item01', 'ar') ?: 'A' }}</td>
            </tr>
          </tbody>
        </table>
        <!-- Bullet points visual findings -->
        <div class="bullet-findings">
          <div><span class="bullet-arrow">&gt;</span> Actual measurement OD area "A" in good condition</div>
          <div><span class="bullet-arrow">&gt;</span> Visual scratch at OD area "A"</div>
          <div><span class="bullet-arrow">&gt;</span> Visual corrosion area spline</div>
          <div><span class="bullet-arrow">&gt;</span> Visual scratch at area surface</div>
          <div><span class="bullet-arrow">&gt;</span> Visual dirty at hole thread area surface</div>
        </div>
        <!-- Length Table -->
        <div class="sub-data-title">Actual Length</div>
        <table class="data-table">
          <thead>
            <tr>
              <th style="width: 35px;">Item</th>
              <th>Spec</th>
              <th>Result of Measurement</th>
              <th style="width: 35px;">A/R</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td style="font-weight: 700;">L</td>
              <td>N/A</td>
              <td>{{ $v($sec, 'item01', 'lengthResult') ?: ($v($sec, 'item01', 'length') ?: '724,320') }}</td>
              <td>-</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Legend Footer -->
    <div class="legend-footer">
      <div>A : <span class="badge-ar-a">Accept</span></div>
      <div>R : <span class="badge-ar-r">Reject</span></div>
    </div>

  </div><!-- /page 1 -->


  <!-- =====================================================================
       LEMBAR 2 DARI 6 (PAGE 2)
  ===================================================================== -->
  <div class="a4-page">
    
    <!-- Top Block: Penetrant Check for Area A & Spline -->
    <div class="split-section" style="border-top: none;">
      <div class="split-col-left">
        <div class="col-header">Image of Penetrant Check OD Area "A" and OD Spline</div>
        <div class="photo-grid-2x2">
          <div class="photo-slot">{!! $renderPhoto('area_a_penet_1', 'Penetrant 1') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_a_penet_2', 'Penetrant 2') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_a_penet_3', 'Penetrant 3') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_a_penet_4', 'Penetrant 4') !!}</div>
        </div>
      </div>
      <div class="split-col-right">
        <div class="col-header">Actual condition</div>
        <div class="bullet-findings" style="padding-top: 15px;">
          <div><span class="bullet-arrow">&gt;</span> No indication crack at OD area "A" and OD spline after penetrant check</div>
        </div>
      </div>
    </div>
    <div class="legend-footer">
      <div>A : <span class="badge-ar-a">Accept</span></div>
      <div>R : <span class="badge-ar-r">Reject</span></div>
    </div>

    <!-- Yellow Banner: Area B and C -->
    <div class="bar-yellow">OD Area "B and C"</div>
    <div class="split-section">
      <div class="split-col-left">
        <div class="col-header">Image of OD Area "B and C"</div>
        <div class="photo-grid-2x2">
          <div class="photo-slot">{!! $renderPhoto('area_bc_visual_1', 'Foto 1') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_bc_visual_2', 'Foto 2') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_bc_visual_3', 'Foto 3') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_bc_visual_4', 'Foto 4') !!}</div>
        </div>
      </div>
      <div class="split-col-right">
        <div class="col-header">Actual visual &amp; measurement</div>
        <div class="sub-data-title">Actual Measurement</div>
        <table class="data-table">
          <thead>
            <tr>
              <th style="width: 25px;">Item</th>
              <th style="width: 65px;">Spec</th>
              <th style="width: 48px;">X</th>
              <th style="width: 48px;">Y</th>
              <th style="width: 48px;">Z</th>
              <th style="width: 30px;">A/R</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td rowspan="3" style="font-weight: 700;">B</td>
              <td rowspan="3">Ø82 ± 0,25</td>
              <td>{{ $v($sec, 'item02', 'itemB', 'row1', 'x') ?: '81,830' }}</td>
              <td>{{ $v($sec, 'item02', 'itemB', 'row1', 'y') ?: '81,840' }}</td>
              <td>{{ $v($sec, 'item02', 'itemB', 'row1', 'z') ?: '81,840' }}</td>
              <td class="{{ $cellAr($v($sec, 'item02', 'itemB', 'row1', 'ar')) }}">{{ $v($sec, 'item02', 'itemB', 'row1', 'ar') ?: 'A' }}</td>
            </tr>
            <tr>
              <td>{{ $v($sec, 'item02', 'itemB', 'row2', 'x') ?: '81,840' }}</td>
              <td>{{ $v($sec, 'item02', 'itemB', 'row2', 'y') ?: '81,850' }}</td>
              <td>{{ $v($sec, 'item02', 'itemB', 'row2', 'z') ?: '81,850' }}</td>
              <td class="{{ $cellAr($v($sec, 'item02', 'itemB', 'row2', 'ar')) }}">{{ $v($sec, 'item02', 'itemB', 'row2', 'ar') ?: 'A' }}</td>
            </tr>
            <tr>
              <td>{{ $v($sec, 'item02', 'itemB', 'row3', 'x') ?: '81,850' }}</td>
              <td>{{ $v($sec, 'item02', 'itemB', 'row3', 'y') ?: '81,860' }}</td>
              <td>{{ $v($sec, 'item02', 'itemB', 'row3', 'z') ?: '81,860' }}</td>
              <td class="{{ $cellAr($v($sec, 'item02', 'itemB', 'row3', 'ar')) }}">{{ $v($sec, 'item02', 'itemB', 'row3', 'ar') ?: 'A' }}</td>
            </tr>
            <tr>
              <td rowspan="3" style="font-weight: 700;">C</td>
              <td rowspan="3">Ø77 ± 0,25</td>
              <td>{{ $v($sec, 'item02', 'itemC', 'row1', 'x') ?: '76,850' }}</td>
              <td>{{ $v($sec, 'item02', 'itemC', 'row1', 'y') ?: '76,860' }}</td>
              <td>{{ $v($sec, 'item02', 'itemC', 'row1', 'z') ?: '76,865' }}</td>
              <td class="{{ $cellAr($v($sec, 'item02', 'itemC', 'row1', 'ar')) }}">{{ $v($sec, 'item02', 'itemC', 'row1', 'ar') ?: 'A' }}</td>
            </tr>
            <tr>
              <td>{{ $v($sec, 'item02', 'itemC', 'row2', 'x') ?: '76,855' }}</td>
              <td>{{ $v($sec, 'item02', 'itemC', 'row2', 'y') ?: '76,850' }}</td>
              <td>{{ $v($sec, 'item02', 'itemC', 'row2', 'z') ?: '76,860' }}</td>
              <td class="{{ $cellAr($v($sec, 'item02', 'itemC', 'row2', 'ar')) }}">{{ $v($sec, 'item02', 'itemC', 'row2', 'ar') ?: 'A' }}</td>
            </tr>
            <tr>
              <td>{{ $v($sec, 'item02', 'itemC', 'row3', 'x') ?: '76,860' }}</td>
              <td>{{ $v($sec, 'item02', 'itemC', 'row3', 'y') ?: '76,870' }}</td>
              <td>{{ $v($sec, 'item02', 'itemC', 'row3', 'z') ?: '76,860' }}</td>
              <td class="{{ $cellAr($v($sec, 'item02', 'itemC', 'row3', 'ar')) }}">{{ $v($sec, 'item02', 'itemC', 'row3', 'ar') ?: 'A' }}</td>
            </tr>
          </tbody>
        </table>
        <div class="bullet-findings">
          <div><span class="bullet-arrow">&gt;</span> Actual measurement OD area "B and C" in good condition</div>
          <div><span class="bullet-arrow">&gt;</span> Visual corrosion at OD area "B"</div>
          <div><span class="bullet-arrow">&gt;</span> Visual corrosion at OD area "C"</div>
        </div>
      </div>
    </div>
    <div class="legend-footer">
      <div>A : <span class="badge-ar-a">Accept</span></div>
      <div>R : <span class="badge-ar-r">Reject</span></div>
    </div>

    <!-- Penetrant Section for B and C -->
    <div class="split-section">
      <div class="split-col-left">
        <div class="col-header">Image of Penetrant Check OD Area "B and C"</div>
        <div class="photo-grid-2x2">
          <div class="photo-slot">{!! $renderPhoto('area_bc_penet_1', 'Penetrant 1') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_bc_penet_2', 'Penetrant 2') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_bc_penet_3', 'Penetrant 3') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_bc_penet_4', 'Penetrant 4') !!}</div>
        </div>
      </div>
      <div class="split-col-right">
        <div class="col-header">Actual condition</div>
        <div class="bullet-findings" style="padding-top: 15px;">
          <div><span class="bullet-arrow">&gt;</span> No indication crack at OD Area "B and C" after penetrant check</div>
        </div>
      </div>
    </div>
    <div class="legend-footer">
      <div>A : <span class="badge-ar-a">Accept</span></div>
      <div>R : <span class="badge-ar-r">Reject</span></div>
    </div>

  </div><!-- /page 2 -->


  <!-- =====================================================================
       LEMBAR 3 DARI 6 (PAGE 3)
  ===================================================================== -->
  <div class="a4-page">
    
    <!-- Yellow Banner: Area D -->
    <div class="bar-yellow">OD Area "D"</div>
    <div class="split-section">
      <div class="split-col-left">
        <div class="col-header">Image of OD Area "D"</div>
        <div class="photo-grid-2x2">
          <div class="photo-slot">{!! $renderPhoto('area_d_visual_1', 'Foto 1') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_d_visual_2', 'Foto 2') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_d_visual_3', 'Foto 3') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_d_visual_4', 'Foto 4') !!}</div>
        </div>
      </div>
      <div class="split-col-right">
        <div class="col-header">Actual visual &amp; measurement</div>
        <div class="sub-data-title">Actual Measurement</div>
        <table class="data-table">
          <thead>
            <tr>
              <th style="width: 35px;">Item</th>
              <th>Spec</th>
              <th style="width: 50px;">X</th>
              <th style="width: 50px;">Y</th>
              <th style="width: 50px;">Z</th>
              <th style="width: 35px;">A/R</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td style="font-weight: 700;">D</td>
              <td>Ø118 ± 0,5</td>
              <td>{{ $v($sec, 'item03', 'itemD', 'x') ?: '118,030' }}</td>
              <td>{{ $v($sec, 'item03', 'itemD', 'y') ?: '118,020' }}</td>
              <td>{{ $v($sec, 'item03', 'itemD', 'z') ?: '118,010' }}</td>
              <td class="{{ $cellAr($v($sec, 'item03', 'itemD', 'ar')) }}">{{ $v($sec, 'item03', 'itemD', 'ar') ?: 'A' }}</td>
            </tr>
          </tbody>
        </table>
        <div class="bullet-findings">
          <div><span class="bullet-arrow">&gt;</span> Actual measurement OD area "D" in good condition</div>
          <div><span class="bullet-arrow">&gt;</span> Visual scratch at OD area "D"</div>
        </div>
        <div class="sub-data-title">Actual Distance</div>
        <table class="data-table">
          <thead>
            <tr>
              <th style="width: 35px;">Item</th>
              <th>Spec</th>
              <th>Result of Measurement</th>
              <th style="width: 35px;">A/R</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td style="font-weight: 700;">E</td>
              <td>13 ± 0,5</td>
              <td>{{ $v($sec, 'item03', 'distanceE', 'x') ?: ($v($sec, 'item03', 'itemE', 'x') ?: '12,950') }}</td>
              <td class="{{ $cellAr($v($sec, 'item03', 'itemE', 'ar')) }}">A</td>
            </tr>
          </tbody>
        </table>
        <div class="bullet-findings">
          <div><span class="bullet-arrow">&gt;</span> Actual distance area "E" in good condition</div>
          <div><span class="bullet-arrow">&gt;</span> Visual worn out at area surface (see mark no 1)</div>
        </div>
      </div>
    </div>
    <div class="legend-footer">
      <div>A : <span class="badge-ar-a">Accept</span></div>
      <div>R : <span class="badge-ar-r">Reject</span></div>
    </div>

    <!-- Penetrant Section for D -->
    <div class="split-section">
      <div class="split-col-left">
        <div class="col-header">Image of Penetrant Check OD Area "D"</div>
        <div class="photo-grid-2x2">
          <div class="photo-slot">{!! $renderPhoto('area_d_penet_1', 'Penetrant 1') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_d_penet_2', 'Penetrant 2') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_d_penet_3', 'Penetrant 3') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_d_penet_4', 'Penetrant 4') !!}</div>
        </div>
      </div>
      <div class="split-col-right">
        <div class="col-header">Actual condition</div>
        <div class="bullet-findings" style="padding-top: 15px;">
          <div><span class="bullet-arrow">&gt;</span> No indication crack at OD area "D" after penetrant check</div>
        </div>
      </div>
    </div>
    <div class="legend-footer">
      <div>A : <span class="badge-ar-a">Accept</span></div>
      <div>R : <span class="badge-ar-r">Reject</span></div>
    </div>

    <!-- Yellow Banner: Area F -->
    <div class="bar-yellow">OD Area "F"</div>
    <div class="split-section">
      <div class="split-col-left">
        <div class="col-header">Image of OD Area "F"</div>
        <div class="photo-grid-2x2">
          <div class="photo-slot">{!! $renderPhoto('area_f_visual_1', 'Foto 1') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_f_visual_2', 'Foto 2') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_f_visual_3', 'Foto 3') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_f_visual_4', 'Foto 4') !!}</div>
        </div>
      </div>
      <div class="split-col-right">
        <div class="col-header">Actual visual &amp; measurement</div>
        <div class="sub-data-title">Actual Measurement</div>
        <table class="data-table">
          <thead>
            <tr>
              <th style="width: 25px;">Item</th>
              <th style="width: 70px;">Spec</th>
              <th style="width: 48px;">X</th>
              <th style="width: 48px;">Y</th>
              <th style="width: 48px;">Z</th>
              <th style="width: 30px;">A/R</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td rowspan="3" style="font-weight: 700;">F</td>
              <td rowspan="3">Ø100,01 ± 0,008</td>
              <td>{{ $v($sec, 'item04', 'itemF', 'row1', 'x') ?: '101,010' }}</td>
              <td>{{ $v($sec, 'item04', 'itemF', 'row1', 'y') ?: '101,010' }}</td>
              <td>{{ $v($sec, 'item04', 'itemF', 'row1', 'z') ?: '101,005' }}</td>
              <td class="{{ $cellAr($v($sec, 'item04', 'itemF', 'row1', 'ar')) }}">{{ $v($sec, 'item04', 'itemF', 'row1', 'ar') ?: 'A' }}</td>
            </tr>
            <tr>
              <td>{{ $v($sec, 'item04', 'itemF', 'row2', 'x') ?: '101,015' }}</td>
              <td>{{ $v($sec, 'item04', 'itemF', 'row2', 'y') ?: '101,015' }}</td>
              <td>{{ $v($sec, 'item04', 'itemF', 'row2', 'z') ?: '101,015' }}</td>
              <td class="{{ $cellAr($v($sec, 'item04', 'itemF', 'row2', 'ar')) }}">{{ $v($sec, 'item04', 'itemF', 'row2', 'ar') ?: 'A' }}</td>
            </tr>
            <tr>
              <td>{{ $v($sec, 'item04', 'itemF', 'row3', 'x') ?: '101,010' }}</td>
              <td>{{ $v($sec, 'item04', 'itemF', 'row3', 'y') ?: '101,010' }}</td>
              <td>{{ $v($sec, 'item04', 'itemF', 'row3', 'z') ?: '101,010' }}</td>
              <td class="{{ $cellAr($v($sec, 'item04', 'itemF', 'row3', 'ar')) }}">{{ $v($sec, 'item04', 'itemF', 'row3', 'ar') ?: 'A' }}</td>
            </tr>
          </tbody>
        </table>
        <div class="bullet-findings">
          <div><span class="bullet-arrow">&gt;</span> Actual measurement OD area "F" in good condition</div>
          <div><span class="bullet-arrow">&gt;</span> Visual pitting and scratch OD area "F" (see mark no 1)</div>
        </div>
        <div class="sub-data-title">Actual Distance</div>
        <table class="data-table">
          <thead>
            <tr>
              <th style="width: 35px;">Item</th>
              <th>Spec</th>
              <th>Result of Measurement</th>
              <th style="width: 35px;">A/R</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td style="font-weight: 700;">K</td>
              <td>N/A</td>
              <td>{{ $v($sec, 'item04', 'distK') ?: '188,550' }}</td>
              <td>-</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="legend-footer">
      <div>A : <span class="badge-ar-a">Accept</span></div>
      <div>R : <span class="badge-ar-r">Reject</span></div>
    </div>

  </div><!-- /page 3 -->


  <!-- =====================================================================
       LEMBAR 4 DARI 6 (PAGE 4)
  ===================================================================== -->
  <div class="a4-page">
    
    <!-- Penetrant Section for F -->
    <div class="split-section" style="border-top: none;">
      <div class="split-col-left">
        <div class="col-header">Image of Penetrant Check OD Area "F"</div>
        <div class="photo-grid-2x2">
          <div class="photo-slot">{!! $renderPhoto('area_f_penet_1', 'Penetrant 1') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_f_penet_2', 'Penetrant 2') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_f_penet_3', 'Penetrant 3') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_f_penet_4', 'Penetrant 4') !!}</div>
        </div>
      </div>
      <div class="split-col-right">
        <div class="col-header">Actual condition</div>
        <div class="bullet-findings" style="padding-top: 15px;">
          <div><span class="bullet-arrow">&gt;</span> No indication crack at area OD area "F" after penetrant check</div>
        </div>
      </div>
    </div>
    <div class="legend-footer">
      <div>A : <span class="badge-ar-a">Accept</span></div>
      <div>R : <span class="badge-ar-r">Reject</span></div>
    </div>

    <!-- Yellow Banner: Area H -->
    <div class="bar-yellow">OD Area "H"</div>
    <div class="split-section">
      <div class="split-col-left">
        <div class="col-header">Image of OD Area "H"</div>
        <div class="photo-grid-2x2">
          <div class="photo-slot">{!! $renderPhoto('area_h_visual_1', 'Foto 1') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_h_visual_2', 'Foto 2') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_h_visual_3', 'Foto 3') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_h_visual_4', 'Foto 4') !!}</div>
        </div>
      </div>
      <div class="split-col-right">
        <div class="col-header">Actual visual &amp; measurement</div>
        <div class="sub-data-title">Actual Measurement</div>
        <table class="data-table">
          <thead>
            <tr>
              <th style="width: 25px;">Item</th>
              <th style="width: 65px;">Spec</th>
              <th style="width: 48px;">X</th>
              <th style="width: 48px;">Y</th>
              <th style="width: 48px;">Z</th>
              <th style="width: 30px;">A/R</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td rowspan="3" style="font-weight: 700;">H</td>
              <td rowspan="3">Ø94 ± 0,013</td>
              <td class="cell-reject">{{ $v($sec, 'item05', 'itemH', 'row1', 'x') ?: '93,980' }}</td>
              <td class="cell-reject">{{ $v($sec, 'item05', 'itemH', 'row1', 'y') ?: '93,985' }}</td>
              <td class="cell-reject">{{ $v($sec, 'item05', 'itemH', 'row1', 'z') ?: '93,980' }}</td>
              <td class="cell-reject">{{ $v($sec, 'item05', 'itemH', 'row1', 'ar') ?: 'R' }}</td>
            </tr>
            <tr>
              <td class="cell-reject">{{ $v($sec, 'item05', 'itemH', 'row2', 'x') ?: '93,980' }}</td>
              <td class="cell-reject">{{ $v($sec, 'item05', 'itemH', 'row2', 'y') ?: '93,985' }}</td>
              <td class="cell-reject">{{ $v($sec, 'item05', 'itemH', 'row2', 'z') ?: '93,985' }}</td>
              <td class="cell-reject">{{ $v($sec, 'item05', 'itemH', 'row2', 'ar') ?: 'R' }}</td>
            </tr>
            <tr>
              <td>{{ $v($sec, 'item05', 'itemH', 'row3', 'x') ?: '93,995' }}</td>
              <td>{{ $v($sec, 'item05', 'itemH', 'row3', 'y') ?: '94,000' }}</td>
              <td>{{ $v($sec, 'item05', 'itemH', 'row3', 'z') ?: '93,990' }}</td>
              <td class="cell-accept">{{ $v($sec, 'item05', 'itemH', 'row3', 'ar') ?: 'A' }}</td>
            </tr>
          </tbody>
        </table>
        <div class="bullet-findings">
          <div><span class="bullet-arrow">&gt;</span> Actual measurement OD area "H" was undersize</div>
          <div><span class="bullet-arrow">&gt;</span> Visual pitting and scratch at OD area "H" (see mark no 1)</div>
        </div>
        <div class="sub-data-title">Actual Distance</div>
        <table class="data-table">
          <thead>
            <tr>
              <th style="width: 35px;">Item</th>
              <th>Spec</th>
              <th>Result of Measurement</th>
              <th style="width: 35px;">A/R</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td style="font-weight: 700;">J</td>
              <td>N/A</td>
              <td>{{ $v($sec, 'item05', 'distJ') ?: '142,800' }}</td>
              <td>-</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="legend-footer">
      <div>A : <span class="badge-ar-a">Accept</span></div>
      <div>R : <span class="badge-ar-r">Reject</span></div>
    </div>

    <!-- Penetrant Section for H -->
    <div class="split-section">
      <div class="split-col-left">
        <div class="col-header">Image of Penetrant Check OD Area "H"</div>
        <div class="photo-grid-2x2">
          <div class="photo-slot">{!! $renderPhoto('area_h_penet_1', 'Penetrant 1') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_h_penet_2', 'Penetrant 2') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_h_penet_3', 'Penetrant 3') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_h_penet_4', 'Penetrant 4') !!}</div>
        </div>
      </div>
      <div class="split-col-right">
        <div class="col-header">Actual condition</div>
        <div class="bullet-findings" style="padding-top: 15px;">
          <div><span class="bullet-arrow">&gt;</span> No indication crack at area OD area "H" after penetrant check</div>
        </div>
      </div>
    </div>
    <div class="legend-footer">
      <div>A : <span class="badge-ar-a">Accept</span></div>
      <div>R : <span class="badge-ar-r">Reject</span></div>
    </div>

  </div><!-- /page 4 -->


  <!-- =====================================================================
       LEMBAR 5 DARI 6 (PAGE 5)
  ===================================================================== -->
  <div class="a4-page">
    
    <!-- Yellow Banner: Area G and Spline -->
    <div class="bar-yellow">OD Area "G" and OD Spline</div>
    <div class="split-section">
      <div class="split-col-left">
        <div class="col-header">Image of OD Spline</div>
        <div class="photo-grid-2x2">
          <div class="photo-slot">{!! $renderPhoto('area_g_visual_1', 'Foto 1') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_g_visual_2', 'Foto 2') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_g_visual_3', 'Foto 3') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_g_visual_4', 'Foto 4') !!}</div>
        </div>
      </div>
      <div class="split-col-right">
        <div class="col-header">Actual visual &amp; measurement</div>
        <div class="sub-data-title">Actual Measurement</div>
        <table class="data-table">
          <thead>
            <tr>
              <th style="width: 35px;">Item</th>
              <th>Spec</th>
              <th style="width: 50px;">X</th>
              <th style="width: 50px;">Y</th>
              <th style="width: 50px;">Z</th>
              <th style="width: 35px;">A/R</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td style="font-weight: 700;">G</td>
              <td>Ø84,2 ± 0,13</td>
              <td class="cell-reject">{{ $v($sec, 'item06', 'itemG', 'x') ?: '83,980' }}</td>
              <td>{{ $v($sec, 'item06', 'itemG', 'y') ?: '84,010' }}</td>
              <td>{{ $v($sec, 'item06', 'itemG', 'z') ?: '84,000' }}</td>
              <td class="cell-reject">{{ $v($sec, 'item06', 'itemG', 'ar') ?: 'R' }}</td>
            </tr>
          </tbody>
        </table>
        <div class="bullet-findings">
          <div><span class="bullet-arrow">&gt;</span> Actual measurement OD area "G" was undersize</div>
          <div><span class="bullet-arrow">&gt;</span> Visual corrosion at OD area "G"</div>
          <div><span class="bullet-arrow">&gt;</span> Visual corrosion area spline</div>
          <div><span class="bullet-arrow">&gt;</span> Visual scratch and corrosion at area surface</div>
          <div><span class="bullet-arrow">&gt;</span> Visual dirty at hole thread area surface</div>
        </div>
        <div class="sub-data-title">Actual Distance</div>
        <table class="data-table">
          <thead>
            <tr>
              <th style="width: 35px;">Item</th>
              <th>Spec</th>
              <th>Result of Measurement</th>
              <th style="width: 35px;">A/R</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td style="font-weight: 700;">I</td>
              <td>N/A</td>
              <td>{{ $v($sec, 'item06', 'distI') ?: '98,780' }}</td>
              <td>-</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="legend-footer">
      <div>A : <span class="badge-ar-a">Accept</span></div>
      <div>R : <span class="badge-ar-r">Reject</span></div>
    </div>

    <!-- Penetrant Section for G and Spline -->
    <div class="split-section">
      <div class="split-col-left">
        <div class="col-header">Image of Penetrant Check Area OD Spline</div>
        <div class="photo-grid-2x2">
          <div class="photo-slot">{!! $renderPhoto('area_g_penet_1', 'Penetrant 1') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_g_penet_2', 'Penetrant 2') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_g_penet_3', 'Penetrant 3') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_g_penet_4', 'Penetrant 4') !!}</div>
        </div>
      </div>
      <div class="split-col-right">
        <div class="col-header">Actual condition</div>
        <div class="bullet-findings" style="padding-top: 15px;">
          <div><span class="bullet-arrow">&gt;</span> No indication crack at area OD spline after penetrant check</div>
        </div>
      </div>
    </div>
    <div class="legend-footer">
      <div>A : <span class="badge-ar-a">Accept</span></div>
      <div>R : <span class="badge-ar-r">Reject</span></div>
    </div>

    <!-- Yellow Banner: Check TIR -->
    <div class="bar-yellow">Check TIR</div>
    <!-- TIR Blueprint Diagram -->
    <div class="blueprint-box">
      @if(!empty($photos['tir_diagram']))
        <img src="{{ asset($photos['tir_diagram']) }}" alt="Shaft TIR Measurement Diagram">
      @else
        <div class="slot-placeholder" style="height: 105px; display: flex; align-items: center; justify-content: center; background: #fafafa; border: 1px dashed #cbd5e1; color: #94a3b8; font-size: 7.5pt; font-weight: 600;">
          <span>[ Diagram / Sketsa Titik Ukur TIR Belum Diunggah ]</span>
        </div>
      @endif
    </div>

    <!-- TIR Table -->
    <table class="tir-table">
      <thead>
        <tr>
          <th style="width: 45px;">Item</th>
          <th style="width: 75px;">Spec</th>
          <th>0°</th>
          <th>90°</th>
          <th>180°</th>
          <th>270°</th>
          <th style="width: 40px;">A/R</th>
        </tr>
      </thead>
      <tbody>
        @php
          $defaultTir = [
            ['item' => 'A', 'd0' => '0', 'd90' => '+0,05', 'd180' => '+0,18', 'd270' => '+0,10', 'ar' => 'A'],
            ['item' => 'B', 'd0' => '0', 'd90' => '+0,02', 'd180' => '0',     'd270' => '+0,02', 'ar' => 'A'],
            ['item' => 'C', 'd0' => '0', 'd90' => '+0,12', 'd180' => '+0,10', 'd270' => '+0,03', 'ar' => 'A'],
            ['item' => 'D', 'd0' => '0', 'd90' => '+0,03', 'd180' => '+0,03', 'd270' => '0',     'ar' => 'A'],
            ['item' => 'E', 'd0' => '0', 'd90' => '+0,01', 'd180' => '0',     'd270' => '0',     'ar' => 'A'],
            ['item' => 'F', 'd0' => '0', 'd90' => '-0,05', 'd180' => '-0,08', 'd270' => '+0,02', 'ar' => 'A'],
          ];
        @endphp
        @foreach($defaultTir as $idx => $def)
          @php
            $rowItem = $tItems[$idx] ?? [];
            $v0 = $rowItem['d0'] ?? $def['d0'];
            $v90 = $rowItem['d90'] ?? $def['d90'];
            $v180 = $rowItem['d180'] ?? $def['d180'];
            $v270 = $rowItem['d270'] ?? $def['d270'];
            $var = $rowItem['ar'] ?? $def['ar'];
          @endphp
          <tr>
            <td style="font-weight: 700;">{{ $def['item'] }}</td>
            @if($idx === 0)
              <td rowspan="6" style="vertical-align: middle; font-weight: 700;">Max 0,2</td>
            @endif
            <td>{{ $v0 }}</td>
            <td>{{ $v90 }}</td>
            <td>{{ $v180 }}</td>
            <td>{{ $v270 }}</td>
            <td class="{{ $cellAr($var) }}">{{ $var }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <div style="padding: 2px 15px 8px 15px; font-size: 6.8pt;">
      <span class="bullet-arrow">&gt;</span> Datum at item "B and E"
    </div>

  </div><!-- /page 5 -->


  <!-- =====================================================================
       LEMBAR 6 DARI 6 (PAGE 6)
  ===================================================================== -->
  <div class="a4-page page-6-signatures">
    
    <div class="page-top-content">
      <!-- Yellow Banner: RECOMENDATION -->
      <div class="bar-yellow" style="margin-top: 0;">RECOMENDATION</div>
      
      <table class="rec-table">
      <thead>
        <tr>
          <th style="width: 45px;">Item</th>
          <th>Remarks</th>
          <th>Recomendation</th>
        </tr>
      </thead>
      <tbody>
        @php
          $defaultRecs = [
            ['item' => '01', 'remarks' => "Visual scratch at OD area \"A\"\nVisual corrosion area spline\nVisual scratch at area surface\nVisual dirty at hole thread area surface", 'rec' => "Cleaning and polish OD area \"A\"\nCleaning area surface\nCleaning and polish area spline\nRetap hole thread area surface"],
            ['item' => '02', 'remarks' => "Visual corrosion at OD area \"B\"\nVisual corrosion at OD area \"C\"", 'rec' => "Cleaning OD area \"B and C\""],
            ['item' => '03', 'remarks' => "Visual scratch at OD area \"D\"\nVisual worn out at area surface (see mark no 1)", 'rec' => "Cleaning and polish OD area \"D\"\nRepair worn out area surface"],
            ['item' => '04', 'remarks' => "Actual measurement OD area \"F\" in good condition but, Visual pitting and scratch OD area \"F\" (see mark no 1)", 'rec' => "Repair OD area \"F\" to specification"],
            ['item' => '05', 'remarks' => "Actual measurement OD area \"H\" was undersize\nVisual pitting and scratch at OD area \"H\" (see mark no 1)", 'rec' => "Repair OD area \"H\" to specification"],
            ['item' => '06', 'remarks' => "Actual measurement OD area \"G\" was undersize\nVisual corrosion at OD area \"G\"\nVisual corrosion area spline\nVisual scratch and corrosion at area surface\nVisual dirty at hole thread area surface", 'rec' => "Repair Cleaning OD area \"G\" to specification\nCleaning and polish area spline\nCleaning and polish area surface\nRetap hole thread area surface"],
          ];
          $activeRecs = !empty($recs) && count($recs) > 0 ? $recs : $defaultRecs;

          // Group items and split multi-lines
          $groupedRecs = [];
          foreach ($activeRecs as $idx => $r) {
            $itemNum = trim($r['item'] ?? sprintf('%02d', $idx + 1));
            $rem = trim($r['remarks'] ?? '');
            $rec = trim($r['recommendation'] ?? ($r['rec'] ?? ''));

            $remLines = array_values(array_filter(array_map('trim', explode("\n", $rem)), fn($s) => $s !== ''));
            $recLines = array_values(array_filter(array_map('trim', explode("\n", $rec)), fn($s) => $s !== ''));
            $maxLines = max(count($remLines), count($recLines), 1);

            if (!isset($groupedRecs[$itemNum])) {
              $groupedRecs[$itemNum] = [];
            }

            for ($i = 0; $i < $maxLines; $i++) {
              $groupedRecs[$itemNum][] = [
                'remarks' => $remLines[$i] ?? ($maxLines === 1 ? $rem : '-'),
                'rec' => $recLines[$i] ?? ($maxLines === 1 ? $rec : '-')
              ];
            }
          }
        @endphp
        @foreach($groupedRecs as $itemNum => $points)
          @php $rowCount = count($points); @endphp
          @foreach($points as $pIdx => $pt)
            <tr>
              @if($pIdx === 0)
                <td rowspan="{{ $rowCount }}" style="text-align: center; font-weight: 700; font-family: monospace; vertical-align: middle; border: 1px solid #000; width: 45px;">{{ $itemNum }}</td>
              @endif
              <td style="border: 1px solid #000; padding: 3.5px 6px;">{{ $pt['remarks'] !== '' ? $pt['remarks'] : '-' }}</td>
              <td style="border: 1px solid #000; padding: 3.5px 6px;">{{ $pt['rec'] !== '' ? $pt['rec'] : '-' }}</td>
            </tr>
          @endforeach
        @endforeach
      </tbody>
    </table>
    </div><!-- /.page-top-content -->

    <!-- Signatures Block: Always anchored at the bottom edge of Page 6 -->
    <div class="page-bottom-signatures">
      <table class="sig-table">
        <thead>
          <tr>
            <th>Checked by:</th>
            <th>Created by:</th>
            <th>Reviewed by:</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <div class="sig-name">{{ !empty($sigs['checkedBy']['name']) ? $sigs['checkedBy']['name'] : '............................' }}</div>
              <div class="sig-meta">
                <span>{{ $sigs['checkedBy']['title'] ?? 'QC Inspector' }}</span>
                <span>Date: {{ $sigs['checkedBy']['date'] ?? date('F d, Y') }}</span>
              </div>
            </td>
            <td>
              <div class="sig-name">{{ !empty($sigs['createdBy']['name']) ? $sigs['createdBy']['name'] : '............................' }}</div>
              <div class="sig-meta">
                <span>{{ $sigs['createdBy']['title'] ?? 'QC Inspector' }}</span>
                <span>Date: {{ $sigs['createdBy']['date'] ?? date('F d, Y') }}</span>
              </div>
            </td>
            <td>
              <div class="sig-name">{{ !empty($sigs['reviewedBy']['name']) ? $sigs['reviewedBy']['name'] : '............................' }}</div>
              <div class="sig-meta">
                <span>{{ $sigs['reviewedBy']['title'] ?? 'Leader QC' }}</span>
                <span>Date: {{ $sigs['reviewedBy']['date'] ?? date('F d, Y') }}</span>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

  </div><!-- /page 6 -->

  <script>
    if (new URLSearchParams(window.location.search).get('autoprint') === '1') {
      window.addEventListener('load', () => {
        setTimeout(() => window.print(), 500);
      });
    }
  </script>
</body>
</html>
