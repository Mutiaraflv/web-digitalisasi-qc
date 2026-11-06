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

    /* Blueprint Diagram */
    .blueprint-box {
      text-align: center;
      padding: 4px;
      border-bottom: 1.5px solid #000;
      background: #fff;
    }
    .blueprint-box img {
      max-width: 100%;
      height: auto;
      max-height: 195px;
      object-fit: contain;
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
    }
    .finding-img-wrap img {
      max-width: 100%;
      max-height: 95px;
      object-fit: contain;
    }

    /* Two-Column Section (Photos Left, Measurement Right) */
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
    }
    .photo-slot img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .photo-slot .slot-placeholder {
      font-size: 6.2pt;
      color: #94a3b8;
      font-style: italic;
      text-align: center;
      padding: 4px;
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
    .data-table td.text-left {
      text-align: left;
      font-family: Arial, sans-serif;
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

    /* Legend Footer (A: Accept, R: Reject) */
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

    /* TIR Table (Page 5) */
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

    /* Recommendation Table (Page 6) */
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

    /* Signatures (Page 6) */
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
        width: 100% !important;
      }
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
    
    <!-- Header -->
    <table class="header-table">
      <tr>
        <td class="header-logo-cell">
          <img src="{{ asset('images/doc_assets/header_ssc_logo.png') }}" alt="PT. SSC WORKS">
        </td>
        <td class="header-title-cell">
          COMPONENT CONDITION REPORT
        </td>
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
        <td class="lbl">No.</td><td class="colon">:</td><td class="val mono">{{ $report->report_no ?? '0742' }}</td>
        <td class="lbl">Date</td><td class="colon">:</td><td class="val">{{ $dateStr }}</td>
        <td class="lbl">Customer</td><td class="colon">:</td><td class="val" colspan="3">{{ $report->customer ?? 'AMMAN MINERAL NUSA TENGGARA, PT' }}</td>
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
                <div class="slot-placeholder" style="height: 110px; display: flex; align-items: center; justify-content: center; background: #fafafa; border: 1px dashed #cbd5e1; color: #94a3b8; font-size: 7.5pt; font-weight: 600;">
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
    <div class="blueprint-box" style="padding: 6px 15px;">
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
        @endphp
        @foreach($activeRecs as $idx => $r)
          <tr>
            <td style="text-align: center; font-weight: 700; font-family: monospace;">{{ $r['item'] ?? sprintf('%02d', $idx + 1) }}</td>
            <td style="white-space: pre-line;">{{ $r['remarks'] ?? '-' }}</td>
            <td style="white-space: pre-line;">{{ $r['recommendation'] ?? ($r['rec'] ?? '-') }}</td>
          </tr>
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
              <div class="sig-name">{{ $sigs['checkedBy']['name'] ?? 'Faqih S A A' }}</div>
              <div class="sig-meta">
                <span>{{ $sigs['checkedBy']['title'] ?? 'QC Inspector' }}</span>
                <span>Date: {{ $sigs['checkedBy']['date'] ?? 'October 20, 2026' }}</span>
              </div>
            </td>
            <td>
              <div class="sig-name">{{ $sigs['createdBy']['name'] ?? 'M Kevin S' }}</div>
              <div class="sig-meta">
                <span>{{ $sigs['createdBy']['title'] ?? 'QC Inspector' }}</span>
                <span>Date: {{ $sigs['createdBy']['date'] ?? 'October 21, 2026' }}</span>
              </div>
            </td>
            <td>
              <div class="sig-name">{{ $sigs['reviewedBy']['name'] ?? 'Agus Riyanto' }}</div>
              <div class="sig-meta">
                <span>{{ $sigs['reviewedBy']['title'] ?? 'Leader QC' }}</span>
                <span>Date: {{ $sigs['reviewedBy']['date'] ?? 'October 21, 2026' }}</span>
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
