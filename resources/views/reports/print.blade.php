<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>FORM-QC-002 - {{ $report->ssc_id }} - PT SSC Works</title>
  <style>
    @page {
      size: A4 landscape;
      margin: 8mm 10mm;
    }
    * {
      box-sizing: border-box;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }
    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 7.5pt;
      color: #000;
      margin: 0;
      padding: 0;
      background: #fff;
    }
    .print-actions {
      padding: 10px 16px;
      background: #f1f5f9;
      border-bottom: 1px solid #cbd5e1;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    @media print {
      .print-actions { display: none !important; }
      .page-break { page-break-before: always; }
    }
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 7px 13px;
      font-size: 12px;
      font-weight: 600;
      border-radius: 3px;
      cursor: pointer;
      border: 1px solid #cbd5e1;
      background: #fff;
      color: #334155;
      text-decoration: none;
      margin-left: 6px;
    }
    .btn-primary { background: #1e3a8a; color: #fff; border-color: #1e3a8a; }

    /* =========================================================
       DOCUMENT SHEET
    ========================================================= */
    .sheet {
      width: 100%;
      max-width: 1050px;
      margin: 10px auto;
      border: 1.5px solid #000;
      padding: 0;
    }

    /* =========================================================
       HEADER
    ========================================================= */
    .doc-header {
      display: grid;
      grid-template-columns: 90px 1fr 190px;
      border-bottom: 1.5px solid #000;
    }
    .doc-header-logo {
      border-right: 1px solid #000;
      padding: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .doc-header-logo img {
      width: 70px;
      height: 70px;
      object-fit: contain;
    }
    .doc-header-center {
      border-right: 1px solid #000;
      padding: 6px 10px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
    }
    .doc-header-center .company-name {
      font-size: 9pt;
      font-weight: 800;
      color: #1e3a8a;
      letter-spacing: 0.5px;
      margin: 0 0 2px 0;
    }
    .doc-header-center .doc-title {
      font-size: 13pt;
      font-weight: 900;
      color: #000;
      letter-spacing: 0.5px;
      margin: 0 0 2px 0;
    }
    .doc-header-right {
      padding: 0;
    }
    .doc-header-right table {
      width: 100%;
      height: 100%;
      border-collapse: collapse;
      font-size: 7.5pt;
    }
    .doc-header-right td {
      border-bottom: 1px solid #000;
      border-left: 1px solid #000;
      padding: 3px 6px;
    }
    .doc-header-right td:last-child { border-right: none; }
    .doc-header-right .lbl { font-weight: 700; width: 100px; }

    /* =========================================================
       INFO GRID (Rows 5-7 of the form)
    ========================================================= */
    .info-grid {
      border-bottom: 1.5px solid #000;
      font-size: 7.5pt;
    }
    .info-row {
      display: grid;
      border-bottom: 1px solid #000;
    }
    .info-row:last-child { border-bottom: none; }
    .info-cell {
      padding: 2.5px 5px;
      border-right: 1px solid #000;
      display: flex;
      align-items: center;
      gap: 3px;
    }
    .info-cell:last-child { border-right: none; }
    .info-cell .lbl { font-weight: 700; white-space: nowrap; }
    .info-cell .val { font-family: Arial, sans-serif; }
    .info-cell .colon { margin: 0 2px; }

    /* =========================================================
       FINDING SECTION (top photo strip)
    ========================================================= */
    .finding-section {
      border-bottom: 1.5px solid #000;
    }
    .section-banner {
      background: #1e3a8a;
      color: #fff;
      font-weight: 700;
      font-size: 7.5pt;
      padding: 2px 8px;
      letter-spacing: 0.5px;
    }
    .finding-table {
      width: 100%;
      border-collapse: collapse;
    }
    .finding-table th {
      background: #dbeafe;
      border: 1px solid #000;
      padding: 2px 4px;
      font-size: 7.5pt;
      font-weight: 700;
      text-align: center;
    }
    .finding-table td {
      border: 1px solid #000;
      padding: 3px 5px;
      vertical-align: middle;
      font-size: 7.5pt;
    }
    .finding-photo-cell {
      width: 180px;
      height: 100px;
      text-align: center;
      vertical-align: middle;
    }
    .finding-photo-cell img {
      max-width: 175px;
      max-height: 95px;
      object-fit: contain;
    }

    /* =========================================================
       INSPECTION SECTION (per-area blocks)
    ========================================================= */
    .inspection-section {
      border-bottom: 1px solid #000;
    }
    .insp-header-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      border-bottom: 1px solid #000;
      background: #eff6ff;
    }
    .insp-header-left {
      padding: 2px 6px;
      font-weight: 700;
      font-size: 7.5pt;
      border-right: 1px solid #000;
    }
    .insp-header-right {
      padding: 2px 6px;
      font-weight: 700;
      font-size: 7.5pt;
    }
    .insp-body {
      display: grid;
      grid-template-columns: 1fr 1fr;
      border-bottom: 1px solid #000;
    }
    .insp-body-left {
      border-right: 1px solid #000;
      padding: 4px;
    }
    .insp-body-right {
      padding: 4px;
    }
    .photo-box {
      border: 1px solid #aaa;
      height: 110px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      background: #f9fafb;
      margin-bottom: 3px;
    }
    .photo-box img {
      max-width: 100%;
      max-height: 95px;
      object-fit: contain;
    }
    .photo-box .no-photo {
      font-size: 6.5pt;
      color: #9ca3af;
      text-align: center;
    }
    .photo-caption {
      font-size: 6.5pt;
      text-align: center;
      color: #374151;
      font-style: italic;
      margin-bottom: 4px;
    }
    .meas-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 7pt;
    }
    .meas-table th {
      background: #dbeafe;
      border: 1px solid #000;
      padding: 2px 3px;
      text-align: center;
      font-weight: 700;
    }
    .meas-table td {
      border: 1px solid #000;
      padding: 2px 3px;
      text-align: center;
      font-family: monospace;
    }
    .meas-table td.lbl-cell {
      text-align: left;
      font-family: Arial, sans-serif;
      font-weight: 600;
    }
    .remark-row {
      font-size: 6.5pt;
      margin-top: 3px;
      color: #374151;
    }
    .remark-row strong { font-weight: 700; }
    .penetrant-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
    }
    .penetrant-left {
      border-right: 1px solid #000;
      padding: 4px;
    }
    .penetrant-right {
      padding: 4px;
      display: flex;
      align-items: center;
    }
    .ar-legend {
      font-size: 6.5pt;
      margin-bottom: 3px;
    }
    .penetrant-remark {
      font-size: 6.5pt;
      font-style: italic;
      color: #374151;
    }

    /* =========================================================
       CHECK TIR SECTION
    ========================================================= */
    .tir-section {
      border-bottom: 1px solid #000;
    }
    .tir-info-row {
      display: flex;
      gap: 20px;
      padding: 3px 8px;
      font-size: 7.5pt;
      border-bottom: 1px solid #ddd;
      background: #f0f9ff;
    }
    .tir-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 7.5pt;
    }
    .tir-table th {
      background: #dbeafe;
      border: 1px solid #000;
      padding: 2.5px 4px;
      text-align: center;
      font-weight: 700;
    }
    .tir-table td {
      border: 1px solid #000;
      padding: 2.5px 4px;
      text-align: center;
      font-family: monospace;
    }
    .tir-table td.row-label { text-align: left; font-family: Arial, sans-serif; font-weight: 700; }

    /* =========================================================
       RECOMMENDATION TABLE
    ========================================================= */
    .rec-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 7.5pt;
    }
    .rec-table th {
      background: #dbeafe;
      border: 1px solid #000;
      padding: 2.5px 5px;
      font-weight: 700;
      text-align: center;
    }
    .rec-table td {
      border: 1px solid #000;
      padding: 2.5px 5px;
      vertical-align: top;
    }
    .rec-table td.no-cell { text-align: center; width: 35px; }

    /* =========================================================
       SIGNATURE BLOCK
    ========================================================= */
    .sig-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      border-top: 1.5px solid #000;
      font-size: 7.5pt;
    }
    .sig-col {
      border-right: 1px solid #000;
      padding: 5px 8px;
    }
    .sig-col:last-child { border-right: none; }
    .sig-col .sig-label { font-weight: 700; margin-bottom: 2px; }
    .sig-col .sig-space { height: 42px; }
    .sig-col .sig-name { font-weight: 700; border-top: 1px solid #000; padding-top: 2px; }
    .sig-col .sig-role { color: #374151; }
    .sig-col .sig-date { color: #374151; }

    .center { text-align: center; }
    .bold { font-weight: 700; }
    .mono { font-family: monospace; }
    .ar-accept { color: #15803d; font-weight: 700; }
    .ar-reject { color: #dc2626; font-weight: 700; }
  </style>
</head>
<body>

  {{-- Print Action Bar (hidden on print) --}}
  <div class="print-actions">
    <div>
      <strong>FORM-QC-002 REV 04</strong> &mdash; COMPONENT CONDITION REPORT &mdash; {{ $report->ssc_id }}
    </div>
    <div>
      <button class="btn" onclick="window.close()">✕ Tutup</button>
      <button class="btn btn-primary" onclick="window.print()">🖨 Cetak / Simpan PDF</button>
    </div>
  </div>

  @php
    $sec  = is_array($report->sections)        ? $report->sections        : json_decode($report->sections        ?? '[]', true);
    $tir  = is_array($report->tir)             ? $report->tir             : json_decode($report->tir             ?? '[]', true);
    $recs = is_array($report->recommendations) ? $report->recommendations : json_decode($report->recommendations ?? '[]', true);
    $sigs = is_array($report->signatures)      ? $report->signatures      : json_decode($report->signatures      ?? '[]', true);
    $photos = is_array($report->photos)        ? $report->photos          : json_decode($report->photos          ?? '[]', true);
    $tItems = $tir['items'] ?? [];

    // Helper: format a measurement value or dash
    $v = fn($arr, ...$keys) => array_reduce($keys, fn($c, $k) => is_array($c) ? ($c[$k] ?? null) : null, $arr) ?? '-';
    $arClass = fn($val) => ($val === 'R' || $val === 'Reject') ? 'ar-reject' : 'ar-accept';

    $inspectionDate = $report->date ? \Carbon\Carbon::parse($report->date)->format('d/m/Y') : date('d/m/Y');
  @endphp

  <div class="sheet">

    {{-- ================================================================
         HEADER
    ================================================================ --}}
    <div class="doc-header">
      <div class="doc-header-logo">
        <img src="{{ asset('images/logo.png') }}" alt="PT SSC Works Logo">
      </div>
      <div class="doc-header-center">
        <div class="company-name">PT. SUKSES SARI COATING WORKS</div>
        <div class="doc-title">COMPONENT CONDITION REPORT</div>
      </div>
      <div class="doc-header-right">
        <table>
          <tr>
            <td class="lbl">Document No</td>
            <td>:</td>
            <td><strong>FORM-QC-002</strong></td>
            <td class="lbl">Responsibility</td>
            <td>:</td>
            <td><strong>QC Inspector</strong></td>
          </tr>
          <tr>
            <td class="lbl">Effective Date.</td>
            <td>:</td>
            <td>{{ $inspectionDate }}</td>
            <td class="lbl">Rev.</td>
            <td>:</td>
            <td><strong>04</strong></td>
          </tr>
          <tr>
            <td class="lbl">Page</td>
            <td>:</td>
            <td>1</td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
        </table>
      </div>
    </div>

    {{-- ================================================================
         INFO GRID (Form Rows 5–7)
    ================================================================ --}}
    <div class="info-grid">
      <div class="info-row" style="grid-template-columns: 1fr 2fr 1fr 2fr 1fr 2fr;">
        <div class="info-cell"><span class="lbl">No.</span><span class="colon">:</span><span class="val mono">{{ $report->report_no ?? '-' }}</span></div>
        <div class="info-cell"><span class="lbl">Date</span><span class="colon">:</span><span class="val">{{ $inspectionDate }}</span></div>
        <div class="info-cell"><span class="lbl">Customer</span><span class="colon">:</span><span class="val">{{ $report->customer ?? '-' }}</span></div>
        <div class="info-cell"><span class="lbl">Part Name</span><span class="colon">:</span><span class="val bold">{{ $report->part_name ?? 'OUTPUT SHAFT' }}</span></div>
        <div class="info-cell"><span class="lbl">Pre</span><span class="colon" style="margin:0;"> {{ ($report->inspection_type ?? 'Pre') === 'Pre' ? '☑' : '☐' }}</span></div>
        <div class="info-cell"><span class="lbl">Post</span><span class="colon" style="margin:0;"> {{ ($report->inspection_type ?? '') === 'Post' ? '☑' : '☐' }}</span></div>
      </div>
      <div class="info-row" style="grid-template-columns: 1fr 2fr 1fr 2fr 1fr 2fr;">
        <div class="info-cell"><span class="lbl">SSC ID</span><span class="colon">:</span><span class="val mono bold">{{ $report->ssc_id }}</span></div>
        <div class="info-cell"><span class="lbl">Job No.</span><span class="colon">:</span><span class="val">{{ $report->job_no ?? '-' }}</span></div>
        <div class="info-cell"><span class="lbl">Model/Unit</span><span class="colon">:</span><span class="val">{{ $report->model_unit ?? '-' }}</span></div>
        <div class="info-cell"><span class="lbl">Quote No.</span><span class="colon">:</span><span class="val">{{ $report->quote_no ?? '-' }}</span></div>
        <div class="info-cell"><span class="lbl">Part No.</span><span class="colon">:</span><span class="val">{{ $report->part_no ?? '-' }}</span></div>
        <div class="info-cell"></div>
      </div>
    </div>

    {{-- ================================================================
         FINDING SECTION — Full component image + visual findings
    ================================================================ --}}
    <div class="finding-section">
      <div class="section-banner">FINDING — DRAWING / PICTURES / DIMENSION</div>
      <table class="finding-table">
        <thead>
          <tr>
            <th style="width:35px;">NO</th>
            <th style="width:200px;">IMAGE</th>
            <th>DESCRIPTION</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="center">01</td>
            <td class="finding-photo-cell">
              @if(!empty($photos['full_component']))
                <img src="{{ asset($photos['full_component']) }}" alt="Full component image">
              @else
                <span style="font-size:6.5pt;color:#9ca3af;">Full image received component OUTPUT SHAFT</span>
              @endif
            </td>
            <td style="vertical-align: top; font-size: 7pt;">
              @if(!empty($recs) && count($recs) > 0)
                @foreach($recs as $idx => $r)
                  <div>{{ sprintf('%02d', $idx + 1) }}. {{ $r['remarks'] ?? '' }}</div>
                @endforeach
              @else
                <div style="color:#9ca3af; font-style:italic;">— Tidak ada temuan visual —</div>
              @endif
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    {{-- ================================================================
         ITEM 01 — OD Area "A" and OD Spline
    ================================================================ --}}
    <div class="inspection-section">
      <div class="insp-header-row">
        <div class="insp-header-left">01 &nbsp;|&nbsp; OD Area "A" and OD Spline</div>
        <div class="insp-header-right">Actual Measurement &nbsp;—&nbsp; OD Spline</div>
      </div>
      {{-- Visual photo + measurement table --}}
      <div class="insp-body">
        <div class="insp-body-left">
          <div class="photo-box">
            @if(!empty($photos['item01']))
              <img src="{{ asset($photos['item01']) }}" alt="Image of OD Area A and Spline">
            @else
              <div class="no-photo">Image of OD Area "A" and OD Spline</div>
            @endif
          </div>
          <div class="photo-caption">Image of OD Area "A" and OD Spline</div>
        </div>
        <div class="insp-body-right">
          <table class="meas-table">
            <thead>
              <tr>
                <th>Item</th>
                <th>Spec</th>
                <th>X</th>
                <th>Y</th>
                <th>Z</th>
                <th>A/R</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="lbl-cell">OD Area "A"</td>
                <td>O79 ± 0,25</td>
                <td>{{ $v($sec, 'item01', 'x') }}</td>
                <td>{{ $v($sec, 'item01', 'y') }}</td>
                <td>{{ $v($sec, 'item01', 'z') }}</td>
                <td class="{{ $arClass($v($sec, 'item01', 'ar')) }}">{{ $v($sec, 'item01', 'ar') }}</td>
              </tr>
              <tr>
                <td class="lbl-cell">Actual Length L</td>
                <td>N/A</td>
                <td colspan="3">{{ $v($sec, 'item01', 'length') }}</td>
                <td>-</td>
              </tr>
            </tbody>
          </table>
          <div class="remark-row"><strong>Remarks:</strong> {{ $v($sec, 'item01', 'remark1') ?: 'Actual measurement OD area "A" in good condition' }}</div>
        </div>
      </div>
      {{-- Penetrant check --}}
      <div class="penetrant-row">
        <div class="penetrant-left">
          <div class="ar-legend"><strong>A : Accept &nbsp;|&nbsp; R : Reject</strong></div>
          <div class="photo-box" style="height:90px;">
            @if(!empty($photos['penetrant_item01']))
              <img src="{{ asset($photos['penetrant_item01']) }}" alt="Penetrant OD A">
            @else
              <div class="no-photo">Image of Penetrant Check OD Area "A" and OD Spline</div>
            @endif
          </div>
          <div class="photo-caption">Image of Penetrant Check OD Area "A" and OD Spline</div>
        </div>
        <div class="penetrant-right">
          <div class="penetrant-remark">{{ $v($sec, 'item01', 'penetrant_remark') ?: 'No indication crack at OD area "A" and OD spline after penetrant check' }}</div>
        </div>
      </div>
    </div>

    {{-- ================================================================
         ITEM 02 — OD Area "B and C"
    ================================================================ --}}
    <div class="inspection-section">
      <div class="insp-header-row">
        <div class="insp-header-left">02 &nbsp;|&nbsp; OD Area "B and C"</div>
        <div class="insp-header-right">Actual Measurement</div>
      </div>
      <div class="insp-body">
        <div class="insp-body-left">
          <div class="photo-box">
            @if(!empty($photos['item02']))
              <img src="{{ asset($photos['item02']) }}" alt="Image of OD Area B and C">
            @else
              <div class="no-photo">Image of OD Area "B and C"</div>
            @endif
          </div>
          <div class="photo-caption">Image of OD Area "B and C"</div>
        </div>
        <div class="insp-body-right">
          <table class="meas-table">
            <thead>
              <tr>
                <th>Item</th>
                <th>Spec</th>
                <th>X</th>
                <th>Y</th>
                <th>Z</th>
                <th>A/R</th>
              </tr>
            </thead>
            <tbody>
              {{-- Area B: 3 rows --}}
              <tr>
                <td class="lbl-cell">B — Row 1</td>
                <td>O82 ± 0,25</td>
                <td>{{ $v($sec, 'item02', 'itemB', 'row1', 'x') }}</td>
                <td>{{ $v($sec, 'item02', 'itemB', 'row1', 'y') }}</td>
                <td>{{ $v($sec, 'item02', 'itemB', 'row1', 'z') }}</td>
                <td class="{{ $arClass($v($sec, 'item02', 'itemB', 'row1', 'ar')) }}">{{ $v($sec, 'item02', 'itemB', 'row1', 'ar') }}</td>
              </tr>
              <tr>
                <td class="lbl-cell">B — Row 2</td>
                <td>O82 ± 0,25</td>
                <td>{{ $v($sec, 'item02', 'itemB', 'row2', 'x') }}</td>
                <td>{{ $v($sec, 'item02', 'itemB', 'row2', 'y') }}</td>
                <td>{{ $v($sec, 'item02', 'itemB', 'row2', 'z') }}</td>
                <td class="{{ $arClass($v($sec, 'item02', 'itemB', 'row2', 'ar')) }}">{{ $v($sec, 'item02', 'itemB', 'row2', 'ar') }}</td>
              </tr>
              <tr>
                <td class="lbl-cell">B — Row 3</td>
                <td>O82 ± 0,25</td>
                <td>{{ $v($sec, 'item02', 'itemB', 'row3', 'x') }}</td>
                <td>{{ $v($sec, 'item02', 'itemB', 'row3', 'y') }}</td>
                <td>{{ $v($sec, 'item02', 'itemB', 'row3', 'z') }}</td>
                <td class="{{ $arClass($v($sec, 'item02', 'itemB', 'row3', 'ar')) }}">{{ $v($sec, 'item02', 'itemB', 'row3', 'ar') }}</td>
              </tr>
              {{-- Area C: 3 rows --}}
              <tr>
                <td class="lbl-cell">C — Row 1</td>
                <td>O77 ± 0,25</td>
                <td>{{ $v($sec, 'item02', 'itemC', 'row1', 'x') }}</td>
                <td>{{ $v($sec, 'item02', 'itemC', 'row1', 'y') }}</td>
                <td>{{ $v($sec, 'item02', 'itemC', 'row1', 'z') }}</td>
                <td class="{{ $arClass($v($sec, 'item02', 'itemC', 'row1', 'ar')) }}">{{ $v($sec, 'item02', 'itemC', 'row1', 'ar') }}</td>
              </tr>
              <tr>
                <td class="lbl-cell">C — Row 2</td>
                <td>O77 ± 0,25</td>
                <td>{{ $v($sec, 'item02', 'itemC', 'row2', 'x') }}</td>
                <td>{{ $v($sec, 'item02', 'itemC', 'row2', 'y') }}</td>
                <td>{{ $v($sec, 'item02', 'itemC', 'row2', 'z') }}</td>
                <td class="{{ $arClass($v($sec, 'item02', 'itemC', 'row2', 'ar')) }}">{{ $v($sec, 'item02', 'itemC', 'row2', 'ar') }}</td>
              </tr>
              <tr>
                <td class="lbl-cell">C — Row 3</td>
                <td>O77 ± 0,25</td>
                <td>{{ $v($sec, 'item02', 'itemC', 'row3', 'x') }}</td>
                <td>{{ $v($sec, 'item02', 'itemC', 'row3', 'y') }}</td>
                <td>{{ $v($sec, 'item02', 'itemC', 'row3', 'z') }}</td>
                <td class="{{ $arClass($v($sec, 'item02', 'itemC', 'row3', 'ar')) }}">{{ $v($sec, 'item02', 'itemC', 'row3', 'ar') }}</td>
              </tr>
            </tbody>
          </table>
          <div class="remark-row"><strong>Remarks:</strong> {{ $v($sec, 'item02', 'remark') ?: 'Actual measurement OD area "B and C" in good condition' }}</div>
        </div>
      </div>
      <div class="penetrant-row">
        <div class="penetrant-left">
          <div class="ar-legend"><strong>A : Accept &nbsp;|&nbsp; R : Reject</strong></div>
          <div class="photo-box" style="height:90px;">
            @if(!empty($photos['penetrant_item02']))
              <img src="{{ asset($photos['penetrant_item02']) }}" alt="Penetrant OD B and C">
            @else
              <div class="no-photo">Image of Penetrant Check OD Area "B and C"</div>
            @endif
          </div>
          <div class="photo-caption">Image of Penetrant Check OD Area "B and C"</div>
        </div>
        <div class="penetrant-right">
          <div class="penetrant-remark">{{ $v($sec, 'item02', 'penetrant_remark') ?: 'No indication crack at OD Area "B and C" after penetrant check' }}</div>
        </div>
      </div>
    </div>

    {{-- ================================================================
         ITEM 03 — OD Area "D"
    ================================================================ --}}
    <div class="inspection-section">
      <div class="insp-header-row">
        <div class="insp-header-left">03 &nbsp;|&nbsp; OD Area "D"</div>
        <div class="insp-header-right">Actual Measurement</div>
      </div>
      <div class="insp-body">
        <div class="insp-body-left">
          <div class="photo-box">
            @if(!empty($photos['item03']))
              <img src="{{ asset($photos['item03']) }}" alt="Image of OD Area D">
            @else
              <div class="no-photo">Image of OD Area "D"</div>
            @endif
          </div>
          <div class="photo-caption">Image of OD Area "D"</div>
        </div>
        <div class="insp-body-right">
          <table class="meas-table">
            <thead>
              <tr>
                <th>Item</th>
                <th>Spec</th>
                <th>X</th>
                <th>Y</th>
                <th>Z</th>
                <th>A/R</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="lbl-cell">OD Area "D"</td>
                <td>O118 ± 0,5</td>
                <td>{{ $v($sec, 'item03', 'itemD', 'x') }}</td>
                <td>{{ $v($sec, 'item03', 'itemD', 'y') }}</td>
                <td>{{ $v($sec, 'item03', 'itemD', 'z') }}</td>
                <td class="{{ $arClass($v($sec, 'item03', 'itemD', 'ar')) }}">{{ $v($sec, 'item03', 'itemD', 'ar') }}</td>
              </tr>
              <tr>
                <td class="lbl-cell">Actual Distance E</td>
                <td>13 ± 0,5</td>
                <td>{{ $v($sec, 'item03', 'itemE', 'x') }}</td>
                <td>{{ $v($sec, 'item03', 'itemE', 'y') }}</td>
                <td>{{ $v($sec, 'item03', 'itemE', 'z') }}</td>
                <td class="{{ $arClass($v($sec, 'item03', 'itemE', 'ar')) }}">{{ $v($sec, 'item03', 'itemE', 'ar') }}</td>
              </tr>
            </tbody>
          </table>
          <div class="remark-row"><strong>Remarks:</strong> {{ $v($sec, 'item03', 'remark') ?: 'Actual measurement OD area "D" in good condition' }}</div>
        </div>
      </div>
      <div class="penetrant-row">
        <div class="penetrant-left">
          <div class="ar-legend"><strong>A : Accept &nbsp;|&nbsp; R : Reject</strong></div>
          <div class="photo-box" style="height:90px;">
            @if(!empty($photos['penetrant_item03']))
              <img src="{{ asset($photos['penetrant_item03']) }}" alt="Penetrant OD D">
            @else
              <div class="no-photo">Image of Penetrant Check OD Area "D"</div>
            @endif
          </div>
          <div class="photo-caption">Image of Penetrant Check OD Area "D"</div>
        </div>
        <div class="penetrant-right">
          <div class="penetrant-remark">{{ $v($sec, 'item03', 'penetrant_remark') ?: 'No indication crack at OD area "D" after penetrant check' }}</div>
        </div>
      </div>
    </div>

    {{-- ================================================================
         ITEM 04 — OD Area "F"
    ================================================================ --}}
    <div class="inspection-section">
      <div class="insp-header-row">
        <div class="insp-header-left">04 &nbsp;|&nbsp; OD Area "F"</div>
        <div class="insp-header-right">Actual Measurement</div>
      </div>
      <div class="insp-body">
        <div class="insp-body-left">
          <div class="photo-box">
            @if(!empty($photos['item04']))
              <img src="{{ asset($photos['item04']) }}" alt="Image of OD Area F">
            @else
              <div class="no-photo">Image of OD Area "F"</div>
            @endif
          </div>
          <div class="photo-caption">Image of OD Area "F"</div>
        </div>
        <div class="insp-body-right">
          <table class="meas-table">
            <thead>
              <tr>
                <th>Item</th>
                <th>Spec</th>
                <th>X</th>
                <th>Y</th>
                <th>Z</th>
                <th>A/R</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="lbl-cell">F — Row 1</td>
                <td>O100,01 ± 0,008</td>
                <td>{{ $v($sec, 'item04', 'itemF', 'row1', 'x') }}</td>
                <td>{{ $v($sec, 'item04', 'itemF', 'row1', 'y') }}</td>
                <td>{{ $v($sec, 'item04', 'itemF', 'row1', 'z') }}</td>
                <td class="{{ $arClass($v($sec, 'item04', 'itemF', 'row1', 'ar')) }}">{{ $v($sec, 'item04', 'itemF', 'row1', 'ar') }}</td>
              </tr>
              <tr>
                <td class="lbl-cell">F — Row 2</td>
                <td>O100,01 ± 0,008</td>
                <td>{{ $v($sec, 'item04', 'itemF', 'row2', 'x') }}</td>
                <td>{{ $v($sec, 'item04', 'itemF', 'row2', 'y') }}</td>
                <td>{{ $v($sec, 'item04', 'itemF', 'row2', 'z') }}</td>
                <td class="{{ $arClass($v($sec, 'item04', 'itemF', 'row2', 'ar')) }}">{{ $v($sec, 'item04', 'itemF', 'row2', 'ar') }}</td>
              </tr>
              <tr>
                <td class="lbl-cell">F — Row 3</td>
                <td>O100,01 ± 0,008</td>
                <td>{{ $v($sec, 'item04', 'itemF', 'row3', 'x') }}</td>
                <td>{{ $v($sec, 'item04', 'itemF', 'row3', 'y') }}</td>
                <td>{{ $v($sec, 'item04', 'itemF', 'row3', 'z') }}</td>
                <td class="{{ $arClass($v($sec, 'item04', 'itemF', 'row3', 'ar')) }}">{{ $v($sec, 'item04', 'itemF', 'row3', 'ar') }}</td>
              </tr>
              <tr>
                <td class="lbl-cell">Actual Distance K</td>
                <td>N/A</td>
                <td colspan="3">{{ $v($sec, 'item04', 'distK') }}</td>
                <td>-</td>
              </tr>
            </tbody>
          </table>
          <div class="remark-row"><strong>Remarks:</strong> {{ $v($sec, 'item04', 'remark') ?: 'Actual measurement OD area "F" in good condition' }}</div>
        </div>
      </div>
      <div class="penetrant-row">
        <div class="penetrant-left">
          <div class="ar-legend"><strong>A : Accept &nbsp;|&nbsp; R : Reject</strong></div>
          <div class="photo-box" style="height:90px;">
            @if(!empty($photos['penetrant_item04']))
              <img src="{{ asset($photos['penetrant_item04']) }}" alt="Penetrant OD F">
            @else
              <div class="no-photo">Image of Penetrant Check OD Area "F"</div>
            @endif
          </div>
          <div class="photo-caption">Image of Penetrant Check OD Area "F"</div>
        </div>
        <div class="penetrant-right">
          <div class="penetrant-remark">{{ $v($sec, 'item04', 'penetrant_remark') ?: 'No indication crack at area OD area "F" after penetrant check' }}</div>
        </div>
      </div>
    </div>

    {{-- ================================================================
         ITEM 05 — OD Area "H"
    ================================================================ --}}
    <div class="inspection-section">
      <div class="insp-header-row">
        <div class="insp-header-left">05 &nbsp;|&nbsp; OD Area "H"</div>
        <div class="insp-header-right">Actual Measurement</div>
      </div>
      <div class="insp-body">
        <div class="insp-body-left">
          <div class="photo-box">
            @if(!empty($photos['item05']))
              <img src="{{ asset($photos['item05']) }}" alt="Image of OD Area H">
            @else
              <div class="no-photo">Image of OD Area "H"</div>
            @endif
          </div>
          <div class="photo-caption">Image of OD Area "H"</div>
        </div>
        <div class="insp-body-right">
          <table class="meas-table">
            <thead>
              <tr>
                <th>Item</th>
                <th>Spec</th>
                <th>X</th>
                <th>Y</th>
                <th>Z</th>
                <th>A/R</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="lbl-cell">H — Row 1</td>
                <td>O94 ± 0,013</td>
                <td>{{ $v($sec, 'item05', 'itemH', 'row1', 'x') }}</td>
                <td>{{ $v($sec, 'item05', 'itemH', 'row1', 'y') }}</td>
                <td>{{ $v($sec, 'item05', 'itemH', 'row1', 'z') }}</td>
                <td class="{{ $arClass($v($sec, 'item05', 'itemH', 'row1', 'ar')) }}">{{ $v($sec, 'item05', 'itemH', 'row1', 'ar') }}</td>
              </tr>
              <tr>
                <td class="lbl-cell">H — Row 2</td>
                <td>O94 ± 0,013</td>
                <td>{{ $v($sec, 'item05', 'itemH', 'row2', 'x') }}</td>
                <td>{{ $v($sec, 'item05', 'itemH', 'row2', 'y') }}</td>
                <td>{{ $v($sec, 'item05', 'itemH', 'row2', 'z') }}</td>
                <td class="{{ $arClass($v($sec, 'item05', 'itemH', 'row2', 'ar')) }}">{{ $v($sec, 'item05', 'itemH', 'row2', 'ar') }}</td>
              </tr>
              <tr>
                <td class="lbl-cell">H — Row 3</td>
                <td>O94 ± 0,013</td>
                <td>{{ $v($sec, 'item05', 'itemH', 'row3', 'x') }}</td>
                <td>{{ $v($sec, 'item05', 'itemH', 'row3', 'y') }}</td>
                <td>{{ $v($sec, 'item05', 'itemH', 'row3', 'z') }}</td>
                <td class="{{ $arClass($v($sec, 'item05', 'itemH', 'row3', 'ar')) }}">{{ $v($sec, 'item05', 'itemH', 'row3', 'ar') }}</td>
              </tr>
              <tr>
                <td class="lbl-cell">Actual Distance J</td>
                <td>N/A</td>
                <td colspan="3">{{ $v($sec, 'item05', 'distJ') }}</td>
                <td>-</td>
              </tr>
            </tbody>
          </table>
          <div class="remark-row"><strong>Remarks:</strong> {{ $v($sec, 'item05', 'remark') ?: 'Actual measurement OD area "H" in good condition' }}</div>
        </div>
      </div>
      <div class="penetrant-row">
        <div class="penetrant-left">
          <div class="ar-legend"><strong>A : Accept &nbsp;|&nbsp; R : Reject</strong></div>
          <div class="photo-box" style="height:90px;">
            @if(!empty($photos['penetrant_item05']))
              <img src="{{ asset($photos['penetrant_item05']) }}" alt="Penetrant OD H">
            @else
              <div class="no-photo">Image of Penetrant Check OD Area "H"</div>
            @endif
          </div>
          <div class="photo-caption">Image of Penetrant Check OD Area "H"</div>
        </div>
        <div class="penetrant-right">
          <div class="penetrant-remark">{{ $v($sec, 'item05', 'penetrant_remark') ?: 'No indication crack at area OD area "H" after penetrant check' }}</div>
        </div>
      </div>
    </div>

    {{-- ================================================================
         ITEM 06 — OD Area "G" and OD Spline
    ================================================================ --}}
    <div class="inspection-section">
      <div class="insp-header-row">
        <div class="insp-header-left">06 &nbsp;|&nbsp; OD Area "G" and OD Spline</div>
        <div class="insp-header-right">Actual Measurement &nbsp;—&nbsp; OD Spline</div>
      </div>
      <div class="insp-body">
        <div class="insp-body-left">
          <div class="photo-box">
            @if(!empty($photos['item06']))
              <img src="{{ asset($photos['item06']) }}" alt="Image of OD Area G and Spline">
            @else
              <div class="no-photo">Image of OD Area "G" and OD Spline</div>
            @endif
          </div>
          <div class="photo-caption">Image of OD Area "G" and OD Spline</div>
        </div>
        <div class="insp-body-right">
          <table class="meas-table">
            <thead>
              <tr>
                <th>Item</th>
                <th>Spec</th>
                <th>X</th>
                <th>Y</th>
                <th>Z</th>
                <th>A/R</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="lbl-cell">OD Area "G"</td>
                <td>O84,2 ± 0,13</td>
                <td>{{ $v($sec, 'item06', 'itemG', 'x') }}</td>
                <td>{{ $v($sec, 'item06', 'itemG', 'y') }}</td>
                <td>{{ $v($sec, 'item06', 'itemG', 'z') }}</td>
                <td class="{{ $arClass($v($sec, 'item06', 'itemG', 'ar')) }}">{{ $v($sec, 'item06', 'itemG', 'ar') }}</td>
              </tr>
              <tr>
                <td class="lbl-cell">Actual Distance I</td>
                <td>N/A</td>
                <td colspan="3">{{ $v($sec, 'item06', 'distI') }}</td>
                <td>-</td>
              </tr>
            </tbody>
          </table>
          <div class="remark-row"><strong>Remarks:</strong> {{ $v($sec, 'item06', 'remark') ?: 'Actual measurement OD area "G" and OD spline in good condition' }}</div>
        </div>
      </div>
      <div class="penetrant-row">
        <div class="penetrant-left">
          <div class="ar-legend"><strong>A : Accept &nbsp;|&nbsp; R : Reject</strong></div>
          <div class="photo-box" style="height:90px;">
            @if(!empty($photos['penetrant_item06']))
              <img src="{{ asset($photos['penetrant_item06']) }}" alt="Penetrant OD G Spline">
            @else
              <div class="no-photo">Image of Penetrant Check Area OD Spline</div>
            @endif
          </div>
          <div class="photo-caption">Image of Penetrant Check Area OD Spline</div>
        </div>
        <div class="penetrant-right">
          <div class="penetrant-remark">{{ $v($sec, 'item06', 'penetrant_remark') ?: 'No indication crack at area OD spline after penetrant check' }}</div>
        </div>
      </div>
    </div>

    {{-- ================================================================
         CHECK TIR (Total Indicator Reading / Runout)
    ================================================================ --}}
    <div class="tir-section">
      <div class="section-banner">CHECK TIR — TOTAL INDICATOR READING</div>
      <div class="tir-info-row">
        <span><strong>Datum at item "B and E"</strong></span>
        <span><strong>Spec:</strong> Max 0,2 mm</span>
      </div>
      <table class="tir-table">
        <thead>
          <tr>
            <th style="width:60px;">Item</th>
            <th>Spec</th>
            <th>0°</th>
            <th>90°</th>
            <th>180°</th>
            <th>270°</th>
            <th style="width:50px;">A/R</th>
          </tr>
        </thead>
        <tbody>
          @foreach(['A','B','C','D','E','F'] as $i => $tirItem)
          <tr>
            <td class="row-label">{{ $tirItem }}</td>
            <td>Max 0,2</td>
            <td>{{ $tItems[$i]['d0']   ?? '-' }}</td>
            <td>{{ $tItems[$i]['d90']  ?? '-' }}</td>
            <td>{{ $tItems[$i]['d180'] ?? '-' }}</td>
            <td>{{ $tItems[$i]['d270'] ?? '-' }}</td>
            <td class="{{ ($tItems[$i]['ar'] ?? 'A') === 'R' ? 'ar-reject' : 'ar-accept' }}">{{ $tItems[$i]['ar'] ?? 'A' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- ================================================================
         RECOMENDATION TABLE
    ================================================================ --}}
    <div style="border-bottom: 1px solid #000;">
      <div class="section-banner">RECOMENDATION</div>
      <table class="rec-table">
        <thead>
          <tr>
            <th style="width:35px;">NO</th>
            <th>DESCRIPTION</th>
            <th>Remarks</th>
            <th>Recomendation</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recs as $idx => $r)
          <tr>
            <td class="no-cell">{{ sprintf('%02d', $idx + 1) }}</td>
            <td>{{ $r['description'] ?? $r['remarks'] ?? '-' }}</td>
            <td>{{ $r['remarks'] ?? '-' }}</td>
            <td>{{ $r['recommendation'] ?? '-' }}</td>
          </tr>
          @empty
          <tr>
            <td class="no-cell">01</td>
            <td>Cleaning and polish area surface</td>
            <td>Visual dirty at area surface</td>
            <td>Cleaning and polish area surface</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- ================================================================
         SIGNATURE BLOCK
    ================================================================ --}}
    <div class="sig-grid">
      <div class="sig-col">
        <div class="sig-label">Checked by:</div>
        <div class="sig-space"></div>
        <div class="sig-name">{{ $sigs['checkedBy']['name']  ?? 'Faqih S A A' }}</div>
        <div class="sig-role">{{ $sigs['checkedBy']['title'] ?? 'QC Inspector' }}</div>
        <div class="sig-date">Date: {{ $sigs['checkedBy']['date']  ?? '-' }}</div>
      </div>
      <div class="sig-col">
        <div class="sig-label">Created by:</div>
        <div class="sig-space"></div>
        <div class="sig-name">{{ $sigs['createdBy']['name']  ?? 'M Kevin S' }}</div>
        <div class="sig-role">{{ $sigs['createdBy']['title'] ?? 'QC Inspector' }}</div>
        <div class="sig-date">Date: {{ $sigs['createdBy']['date']  ?? '-' }}</div>
      </div>
      <div class="sig-col">
        <div class="sig-label">Reviewed by:</div>
        <div class="sig-space"></div>
        <div class="sig-name">{{ $sigs['reviewedBy']['name']  ?? 'Agus Riyanto' }}</div>
        <div class="sig-role">{{ $sigs['reviewedBy']['title'] ?? 'Leader QC' }}</div>
        <div class="sig-date">Date: {{ $sigs['reviewedBy']['date']  ?? '-' }}</div>
      </div>
    </div>

  </div>{{-- /sheet --}}

</body>
</html>
