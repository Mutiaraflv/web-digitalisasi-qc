<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>FORM-QC-002 - {{ $report->ssc_id }} - PT SSC Works</title>
  <style>
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
      font-family: Arial, Helvetica, sans-serif;
      font-size: 7.2pt;
      color: #000;
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
      border: 1.5px solid #000;
      padding: 0;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      position: relative;
    }

    /* Common Header for Pages */
    .header-table {
      width: 100%;
      border-collapse: collapse;
      border-bottom: 1.5px solid #000;
    }
    .header-table td {
      border: 1px solid #000;
      padding: 2px 4px;
      vertical-align: middle;
    }
    .header-logo-cell {
      width: 38%;
      padding: 2px 4px !important;
      text-align: center;
    }
    .header-logo-cell img {
      max-width: 100%;
      height: 38px;
      object-fit: contain;
    }
    .header-title-cell {
      width: 32%;
      text-align: center;
      font-weight: 900;
      font-size: 9.5pt;
      letter-spacing: 0.3px;
    }
    .header-ctrl-cell {
      width: 30%;
      padding: 0 !important;
    }
    .ctrl-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 6.8pt;
    }
    .ctrl-table td {
      border: 1px solid #000;
      padding: 1.5px 3px;
    }

    /* Metadata Table (Page 1) */
    .meta-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 7pt;
      border-bottom: 1.5px solid #000;
    }
    .meta-table td {
      border: 1px solid #000;
      padding: 2px 4px;
    }
    .meta-table .lbl {
      font-weight: 700;
      background: #fff;
      width: 13%;
      white-space: nowrap;
    }
    .meta-table .colon { width: 8px; text-align: center; padding: 0; }
    .meta-table .val { width: 35%; font-family: Arial, sans-serif; }

    /* Section Bars */
    .bar-grey {
      background: #e5e7eb;
      color: #000;
      font-weight: 700;
      font-size: 7.2pt;
      text-align: center;
      padding: 2px 4px;
      border-bottom: 1px solid #000;
      border-top: 1px solid #000;
      letter-spacing: 0.3px;
    }
    .bar-yellow {
      background: #ffeb3b;
      color: #000;
      font-weight: 700;
      font-size: 7.5pt;
      text-align: center;
      padding: 2.5px 4px;
      border-bottom: 1px solid #000;
      border-top: 1px solid #000;
      letter-spacing: 0.3px;
    }

    /* Blueprint Box */
    .blueprint-box {
      text-align: center;
      padding: 4px;
      border-bottom: 1.5px solid #000;
      background: #fff;
      min-height: 140px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .blueprint-box img {
      max-width: 100%;
      height: auto;
      max-height: 195px;
      object-fit: contain;
    }
    .empty-blueprint-msg {
      font-size: 7.5pt;
      color: #94a3b8;
      font-style: italic;
      padding: 30px;
      border: 1px dashed #cbd5e1;
      width: 90%;
      margin: 10px auto;
    }

    /* Finding Table */
    .finding-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 7pt;
      border-bottom: 1.5px solid #000;
    }
    .finding-table th {
      background: #fff;
      border: 1px solid #000;
      padding: 2px 4px;
      font-weight: 700;
      text-align: center;
    }
    .finding-table td {
      border: 1px solid #000;
      padding: 3px 4px;
      vertical-align: middle;
    }
    .finding-img-wrap {
      width: 100%;
      height: 95px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f8fafc;
      overflow: hidden;
    }
    .finding-img-wrap img {
      max-width: 100%;
      max-height: 95px;
      object-fit: contain;
    }

    /* Two-Column Section */
    .split-section {
      display: grid;
      grid-template-columns: 50% 50%;
      border-bottom: 1px solid #000;
    }
    .split-col-left {
      border-right: 1px solid #000;
      display: flex;
      flex-direction: column;
    }
    .split-col-right {
      display: flex;
      flex-direction: column;
    }
    .col-header {
      background: #e5e7eb;
      font-weight: 700;
      font-size: 7pt;
      text-align: center;
      padding: 2px 4px;
      border-bottom: 1px solid #000;
    }

    /* 2x2 Photo Grid */
    .photo-grid-2x2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      grid-template-rows: 1fr 1fr;
      gap: 2px;
      padding: 2px;
      background: #000;
      flex: 1;
      min-height: 150px;
    }
    .photo-slot {
      background: #f8fafc;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      min-height: 72px;
      position: relative;
    }
    .photo-slot img {
      width: 100%;
      height: 100%;
      object-fit: cover;
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

    /* Data Tables inside Right Column */
    .sub-data-title {
      font-weight: 700;
      font-size: 6.8pt;
      text-align: center;
      padding: 1.5px;
      border-bottom: 1px solid #000;
      background: #fff;
    }
    .data-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 6.8pt;
    }
    .data-table th {
      border: 1px solid #000;
      padding: 1.5px 2px;
      background: #f3f4f6;
      font-weight: 700;
      text-align: center;
    }
    .data-table td {
      border: 1px solid #000;
      padding: 1.5px 2px;
      text-align: center;
      font-family: monospace;
    }

    /* Bullet Findings */
    .bullet-findings {
      padding: 4px 6px;
      font-size: 6.6pt;
      line-height: 1.35;
      flex: 1;
    }
    .bullet-findings div {
      margin-bottom: 2px;
    }
    .bullet-arrow {
      font-weight: 700;
      margin-right: 3px;
    }

    /* Legend Footer */
    .legend-footer {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      gap: 15px;
      padding: 2px 10px;
      font-size: 6.8pt;
      font-weight: 700;
      border-bottom: 1px solid #000;
      background: #fff;
    }
    .badge-ar-a {
      background: #dcfce7;
      color: #15803d;
      padding: 1px 6px;
      border: 1px solid #16a34a;
      border-radius: 2px;
    }
    .badge-ar-r {
      background: #fee2e2;
      color: #dc2626;
      padding: 1px 6px;
      border: 1px solid #dc2626;
      border-radius: 2px;
    }
    .cell-reject {
      background: #fee2e2 !important;
      color: #dc2626 !important;
      font-weight: 700;
    }
    .cell-accept {
      background: #dcfce7 !important;
      color: #15803d !important;
      font-weight: 700;
    }

    /* TIR Table */
    .tir-table {
      width: 80%;
      margin: 4px auto;
      border-collapse: collapse;
      font-size: 7pt;
    }
    .tir-table th {
      background: #f3f4f6;
      border: 1px solid #000;
      padding: 2px 4px;
      text-align: center;
      font-weight: 700;
    }
    .tir-table td {
      border: 1px solid #000;
      padding: 2px 4px;
      text-align: center;
      font-family: monospace;
    }

    /* Recommendation Table */
    .rec-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 7.2pt;
    }
    .rec-table th {
      background: #f3f4f6;
      border: 1px solid #000;
      padding: 3px 6px;
      font-weight: 700;
      text-align: center;
    }
    .rec-table td {
      border: 1px solid #000;
      padding: 3.5px 6px;
      vertical-align: top;
    }

    /* Signatures Table */
    .sig-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 7.2pt;
      margin-top: 15px;
      border: 1px solid #000;
    }
    .sig-table th {
      border: 1px solid #000;
      padding: 3px 6px;
      text-align: left;
      font-weight: 700;
      background: #fff;
    }
    .sig-table td {
      border: 1px solid #000;
      padding: 4px 6px;
      vertical-align: bottom;
      height: 75px;
      width: 33.33%;
    }
    .sig-name {
      font-weight: 700;
      border-bottom: 1px solid #000;
      padding-bottom: 2px;
      margin-bottom: 2px;
    }
    .sig-meta {
      display: flex;
      justify-content: space-between;
      color: #374151;
      font-size: 6.8pt;
    }
  </style>
</head>
<body>

  <!-- Top Action Bar -->
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
    
    <!-- Header -->
    <table class="header-table">
      <tr>
        <td class="header-logo-cell">
          <img src="{{ asset('images/doc_assets/header_ssc_logo.png') }}" alt="PT. SSC WORKS">
        </td>
        <td class="header-title-cell">COMPONENT CONDITION REPORT</td>
        <td class="header-ctrl-cell">
          <table class="ctrl-table">
            <tr>
              <td><strong>Document No</strong></td><td>:</td><td><strong>{{ $report->doc_no ?? 'FORM-QC-002' }}</strong></td>
              <td><strong>Responsibility</strong></td><td>:</td><td>{{ $report->responsibility ?? 'Quality Control' }}</td>
            </tr>
            <tr>
              <td><strong>Effective Date.</strong></td><td>:</td><td>{{ $effDateStr }}</td>
              <td><strong>Rev.</strong></td><td>:</td><td><strong>{{ $report->rev ?? '04' }}</strong></td>
            </tr>
            <tr>
              <td><strong>Page</strong></td><td>:</td><td><strong>1 &nbsp; of &nbsp; 6</strong></td>
              <td></td><td></td><td></td>
            </tr>
          </table>
        </td>
      </tr>
    </table>

    <!-- Metadata Grid -->
    <table class="meta-table">
      <tr>
        <td class="lbl">No.</td><td class="colon">:</td><td class="val mono">{{ $report->report_no ?? '-' }}</td>
        <td class="lbl">Date</td><td class="colon">:</td><td class="val">{{ $dateStr }}</td>
        <td class="lbl">Customer</td><td class="colon">:</td><td class="val" colspan="3">{{ $report->customer ?? '-' }}</td>
        <td class="lbl">Part Name</td><td class="colon">:</td><td class="val bold">{{ $report->part_name ?? 'OUTPUT SHAFT' }}</td>
      </tr>
      <tr>
        <td class="lbl">Inspection</td><td class="colon">:</td>
        <td class="val">
          {{ ($report->inspection_type ?? 'Pre') === 'Pre' ? '☑' : '☐' }} Pre &nbsp;
          {{ ($report->inspection_type ?? '') === 'Post' ? '☑' : '☐' }} Post
        </td>
        <td class="lbl">Job No.</td><td class="colon">:</td><td class="val">{{ $report->job_no ?? '-' }}</td>
        <td class="lbl">Model/Unit</td><td class="colon">:</td><td class="val" colspan="5">{{ $report->model_unit ?? 'HT051' }}</td>
      </tr>
      <tr>
        <td class="lbl">SSC ID</td><td class="colon">:</td><td class="val mono bold">{{ $report->ssc_id }}</td>
        <td class="lbl">Quote No.</td><td class="colon">:</td><td class="val">{{ $report->quote_no ?? '-' }}</td>
        <td class="lbl">Part No.</td><td class="colon">:</td><td class="val" colspan="5">{{ $report->part_no ?? '-' }}</td>
      </tr>
    </table>

    <!-- Drawing / Pictures / Dimension (1 FOTO INPUT) -->
    <div class="bar-grey">Drawing / Pictures / Dimension</div>
    <div class="blueprint-box">
      @if(!empty($photos['drawing_dimension']))
        <img src="{{ asset($photos['drawing_dimension']) }}" alt="Drawing Dimensi">
      @else
        <div class="empty-blueprint-msg">[ Gambar Drawing / Pictures / Dimension Belum Diunggah ]</div>
      @endif
    </div>

    <!-- Finding Section (1 FOTO INPUT) -->
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
                <img src="{{ asset($photos['finding_component']) }}" alt="Full Component">
              @else
                <span style="font-size: 6.5pt; color: #94a3b8; font-style: italic;">[ Foto Komponen Utuh Belum Diunggah ]</span>
              @endif
            </div>
          </td>
          <td style="vertical-align: top; padding: 6px;">
            <div><span class="bullet-arrow">&gt;</span> Full image received component OUTPUT SHAFT</div>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Yellow Banner: Area A & Spline (4 FOTO INPUT) -->
    <div class="bar-yellow">OD Area "A" and OD Spline</div>
    <div class="split-section">
      <div class="split-col-left">
        <div class="col-header">Image of OD Spline</div>
        <div class="photo-grid-2x2">
          <div class="photo-slot">{!! $renderPhoto('area_a_visual_1', 'Foto 1') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_a_visual_2', 'Foto 2') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_a_visual_3', 'Foto 3') !!}</div>
          <div class="photo-slot">{!! $renderPhoto('area_a_visual_4', 'Foto 4') !!}</div>
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
              <td style="font-weight: 700;">A</td>
              <td>Ø79 ± 0,25</td>
              <td>{{ $v($sec, 'item01', 'x') ?: '-' }}</td>
              <td>{{ $v($sec, 'item01', 'y') ?: '-' }}</td>
              <td>{{ $v($sec, 'item01', 'z') ?: '-' }}</td>
              <td class="{{ $cellAr($v($sec, 'item01', 'ar')) }}">{{ $v($sec, 'item01', 'ar') ?: 'A' }}</td>
            </tr>
          </tbody>
        </table>
        <div class="bullet-findings">
          <div><span class="bullet-arrow">&gt;</span> Actual measurement OD area "A" in good condition</div>
          <div><span class="bullet-arrow">&gt;</span> Visual scratch at OD area "A"</div>
          <div><span class="bullet-arrow">&gt;</span> Visual corrosion area spline</div>
          <div><span class="bullet-arrow">&gt;</span> Visual scratch at area surface</div>
          <div><span class="bullet-arrow">&gt;</span> Visual dirty at hole thread area surface</div>
        </div>
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
              <td>{{ $v($sec, 'item01', 'lengthResult') ?: ($v($sec, 'item01', 'length') ?: '-') }}</td>
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

  </div><!-- /page 1 -->


  <!-- =====================================================================
       LEMBAR 2 DARI 6 (PAGE 2)
  ===================================================================== -->
  <div class="a4-page">
    
    <!-- Top Block: Penetrant Check for Area A & Spline (4 FOTO INPUT) -->
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

    <!-- Yellow Banner: Area B and C (4 FOTO INPUT) -->
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
              <td>{{ $v($sec, 'item02', 'itemB', 'row1', 'x') ?: '-' }}</td>
              <td>{{ $v($sec, 'item02', 'itemB', 'row1', 'y') ?: '-' }}</td>
              <td>{{ $v($sec, 'item02', 'itemB', 'row1', 'z') ?: '-' }}</td>
              <td class="{{ $cellAr($v($sec, 'item02', 'itemB', 'row1', 'ar')) }}">{{ $v($sec, 'item02', 'itemB', 'row1', 'ar') ?: 'A' }}</td>
            </tr>
            <tr>
              <td>{{ $v($sec, 'item02', 'itemB', 'row2', 'x') ?: '-' }}</td>
              <td>{{ $v($sec, 'item02', 'itemB', 'row2', 'y') ?: '-' }}</td>
              <td>{{ $v($sec, 'item02', 'itemB', 'row2', 'z') ?: '-' }}</td>
              <td class="{{ $cellAr($v($sec, 'item02', 'itemB', 'row2', 'ar')) }}">{{ $v($sec, 'item02', 'itemB', 'row2', 'ar') ?: 'A' }}</td>
            </tr>
            <tr>
              <td>{{ $v($sec, 'item02', 'itemB', 'row3', 'x') ?: '-' }}</td>
              <td>{{ $v($sec, 'item02', 'itemB', 'row3', 'y') ?: '-' }}</td>
              <td>{{ $v($sec, 'item02', 'itemB', 'row3', 'z') ?: '-' }}</td>
              <td class="{{ $cellAr($v($sec, 'item02', 'itemB', 'row3', 'ar')) }}">{{ $v($sec, 'item02', 'itemB', 'row3', 'ar') ?: 'A' }}</td>
            </tr>
            <tr>
              <td rowspan="3" style="font-weight: 700;">C</td>
              <td rowspan="3">Ø77 ± 0,25</td>
              <td>{{ $v($sec, 'item02', 'itemC', 'row1', 'x') ?: '-' }}</td>
              <td>{{ $v($sec, 'item02', 'itemC', 'row1', 'y') ?: '-' }}</td>
              <td>{{ $v($sec, 'item02', 'itemC', 'row1', 'z') ?: '-' }}</td>
              <td class="{{ $cellAr($v($sec, 'item02', 'itemC', 'row1', 'ar')) }}">{{ $v($sec, 'item02', 'itemC', 'row1', 'ar') ?: 'A' }}</td>
            </tr>
            <tr>
              <td>{{ $v($sec, 'item02', 'itemC', 'row2', 'x') ?: '-' }}</td>
              <td>{{ $v($sec, 'item02', 'itemC', 'row2', 'y') ?: '-' }}</td>
              <td>{{ $v($sec, 'item02', 'itemC', 'row2', 'z') ?: '-' }}</td>
              <td class="{{ $cellAr($v($sec, 'item02', 'itemC', 'row2', 'ar')) }}">{{ $v($sec, 'item02', 'itemC', 'row2', 'ar') ?: 'A' }}</td>
            </tr>
            <tr>
              <td>{{ $v($sec, 'item02', 'itemC', 'row3', 'x') ?: '-' }}</td>
              <td>{{ $v($sec, 'item02', 'itemC', 'row3', 'y') ?: '-' }}</td>
              <td>{{ $v($sec, 'item02', 'itemC', 'row3', 'z') ?: '-' }}</td>
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

    <!-- Penetrant B and C (4 FOTO INPUT) -->
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
    
    <!-- Yellow Banner: Area D (4 FOTO INPUT) -->
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
              <td>{{ $v($sec, 'item03', 'itemD', 'x') ?: '-' }}</td>
              <td>{{ $v($sec, 'item03', 'itemD', 'y') ?: '-' }}</td>
              <td>{{ $v($sec, 'item03', 'itemD', 'z') ?: '-' }}</td>
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
              <td>{{ $v($sec, 'item03', 'distanceE', 'x') ?: ($v($sec, 'item03', 'itemE', 'x') ?: '-') }}</td>
              <td class="{{ $cellAr($v($sec, 'item03', 'itemE', 'ar')) }}">{{ $v($sec, 'item03', 'itemE', 'ar') ?: 'A' }}</td>
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

    <!-- Penetrant D (4 FOTO INPUT) -->
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

    <!-- Yellow Banner: Area F (4 FOTO INPUT) -->
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
              <td>{{ $v($sec, 'item04', 'itemF', 'row1', 'x') ?: '-' }}</td>
              <td>{{ $v($sec, 'item04', 'itemF', 'row1', 'y') ?: '-' }}</td>
              <td>{{ $v($sec, 'item04', 'itemF', 'row1', 'z') ?: '-' }}</td>
              <td class="{{ $cellAr($v($sec, 'item04', 'itemF', 'row1', 'ar')) }}">{{ $v($sec, 'item04', 'itemF', 'row1', 'ar') ?: 'A' }}</td>
            </tr>
            <tr>
              <td>{{ $v($sec, 'item04', 'itemF', 'row2', 'x') ?: '-' }}</td>
              <td>{{ $v($sec, 'item04', 'itemF', 'row2', 'y') ?: '-' }}</td>
              <td>{{ $v($sec, 'item04', 'itemF', 'row2', 'z') ?: '-' }}</td>
              <td class="{{ $cellAr($v($sec, 'item04', 'itemF', 'row2', 'ar')) }}">{{ $v($sec, 'item04', 'itemF', 'row2', 'ar') ?: 'A' }}</td>
            </tr>
            <tr>
              <td>{{ $v($sec, 'item04', 'itemF', 'row3', 'x') ?: '-' }}</td>
              <td>{{ $v($sec, 'item04', 'itemF', 'row3', 'y') ?: '-' }}</td>
              <td>{{ $v($sec, 'item04', 'itemF', 'row3', 'z') ?: '-' }}</td>
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
              <td>{{ $v($sec, 'item04', 'distK') ?: '-' }}</td>
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
    
    <!-- Penetrant Section for F (4 FOTO INPUT) -->
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

    <!-- Yellow Banner: Area H (4 FOTO INPUT) -->
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
              <td class="{{ $cellAr($v($sec, 'item05', 'itemH', 'row1', 'ar')) }}">{{ $v($sec, 'item05', 'itemH', 'row1', 'x') ?: '-' }}</td>
              <td class="{{ $cellAr($v($sec, 'item05', 'itemH', 'row1', 'ar')) }}">{{ $v($sec, 'item05', 'itemH', 'row1', 'y') ?: '-' }}</td>
              <td class="{{ $cellAr($v($sec, 'item05', 'itemH', 'row1', 'ar')) }}">{{ $v($sec, 'item05', 'itemH', 'row1', 'z') ?: '-' }}</td>
              <td class="{{ $cellAr($v($sec, 'item05', 'itemH', 'row1', 'ar')) }}">{{ $v($sec, 'item05', 'itemH', 'row1', 'ar') ?: 'A' }}</td>
            </tr>
            <tr>
              <td class="{{ $cellAr($v($sec, 'item05', 'itemH', 'row2', 'ar')) }}">{{ $v($sec, 'item05', 'itemH', 'row2', 'x') ?: '-' }}</td>
              <td class="{{ $cellAr($v($sec, 'item05', 'itemH', 'row2', 'ar')) }}">{{ $v($sec, 'item05', 'itemH', 'row2', 'y') ?: '-' }}</td>
              <td class="{{ $cellAr($v($sec, 'item05', 'itemH', 'row2', 'ar')) }}">{{ $v($sec, 'item05', 'itemH', 'row2', 'z') ?: '-' }}</td>
              <td class="{{ $cellAr($v($sec, 'item05', 'itemH', 'row2', 'ar')) }}">{{ $v($sec, 'item05', 'itemH', 'row2', 'ar') ?: 'A' }}</td>
            </tr>
            <tr>
              <td>{{ $v($sec, 'item05', 'itemH', 'row3', 'x') ?: '-' }}</td>
              <td>{{ $v($sec, 'item05', 'itemH', 'row3', 'y') ?: '-' }}</td>
              <td>{{ $v($sec, 'item05', 'itemH', 'row3', 'z') ?: '-' }}</td>
              <td class="{{ $cellAr($v($sec, 'item05', 'itemH', 'row3', 'ar')) }}">{{ $v($sec, 'item05', 'itemH', 'row3', 'ar') ?: 'A' }}</td>
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
              <td>{{ $v($sec, 'item05', 'distJ') ?: '-' }}</td>
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

    <!-- Penetrant Section for H (4 FOTO INPUT) -->
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
    
    <!-- Yellow Banner: Area G and Spline (4 FOTO INPUT) -->
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
              <td class="{{ $cellAr($v($sec, 'item06', 'itemG', 'x')) }}">{{ $v($sec, 'item06', 'itemG', 'x') ?: '-' }}</td>
              <td>{{ $v($sec, 'item06', 'itemG', 'y') ?: '-' }}</td>
              <td>{{ $v($sec, 'item06', 'itemG', 'z') ?: '-' }}</td>
              <td class="{{ $cellAr($v($sec, 'item06', 'itemG', 'ar')) }}">{{ $v($sec, 'item06', 'itemG', 'ar') ?: 'A' }}</td>
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
              <td>{{ $v($sec, 'item06', 'distI') ?: '-' }}</td>
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

    <!-- Penetrant Section for G and Spline (4 FOTO INPUT) -->
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

    <!-- Yellow Banner: Check TIR (1 FOTO INPUT) -->
    <div class="bar-yellow">Check TIR</div>
    <div class="blueprint-box" style="padding: 6px 15px;">
      @if(!empty($photos['tir_diagram']))
        <img src="{{ asset($photos['tir_diagram']) }}" alt="Shaft TIR Blueprint">
      @else
        <div class="empty-blueprint-msg">[ Gambar Diagram Titik Ukur TIR Belum Diunggah ]</div>
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
        @foreach(['A','B','C','D','E','F'] as $idx => $itemLetter)
          @php
            $rowItem = $tItems[$idx] ?? [];
            $v0 = $rowItem['d0'] ?? '-';
            $v90 = $rowItem['d90'] ?? '-';
            $v180 = $rowItem['d180'] ?? '-';
            $v270 = $rowItem['d270'] ?? '-';
            $var = $rowItem['ar'] ?? 'A';
          @endphp
          <tr>
            <td style="font-weight: 700;">{{ $itemLetter }}</td>
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
  <div class="a4-page">
    
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
        @if(!empty($recs) && count($recs) > 0)
          @foreach($recs as $idx => $r)
            <tr>
              <td style="text-align: center; font-weight: 700; font-family: monospace;">{{ $r['item'] ?? sprintf('%02d', $idx + 1) }}</td>
              <td style="white-space: pre-line;">{{ $r['remarks'] ?? '-' }}</td>
              <td style="white-space: pre-line;">{{ $r['recommendation'] ?? ($r['rec'] ?? '-') }}</td>
            </tr>
          @endforeach
        @else
          <tr>
            <td colspan="3" style="text-align: center; padding: 20px; color: #94a3b8; font-style: italic;">
              [ Belum ada catatan rekomendasi &amp; temuan mutu diinput ]
            </td>
          </tr>
        @endif
      </tbody>
    </table>

    <!-- Signatures Block -->
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
            <div class="sig-name">{{ $sigs['checkedBy']['name'] ?? 'Faqih S A A' }}</div>
            <div class="sig-meta">
              <span>{{ $sigs['checkedBy']['title'] ?? 'QC Inspector' }}</span>
              <span>Date: {{ $sigs['checkedBy']['date'] ?? '-' }}</span>
            </div>
          </td>
          <td>
            <div class="sig-name">{{ $sigs['createdBy']['name'] ?? 'M Kevin S' }}</div>
            <div class="sig-meta">
              <span>{{ $sigs['createdBy']['title'] ?? 'QC Inspector' }}</span>
              <span>Date: {{ $sigs['createdBy']['date'] ?? '-' }}</span>
            </div>
          </td>
          <td>
            <div class="sig-name">{{ $sigs['reviewedBy']['name'] ?? 'Agus Riyanto' }}</div>
            <div class="sig-meta">
              <span>{{ $sigs['reviewedBy']['title'] ?? 'Leader QC' }}</span>
              <span>Date: {{ $sigs['reviewedBy']['date'] ?? '-' }}</span>
            </div>
          </td>
        </tr>
      </tbody>
    </table>

  </div><!-- /page 6 -->

</body>
</html>
