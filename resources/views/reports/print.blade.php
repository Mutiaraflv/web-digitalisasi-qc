<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>FORM-QC-002 - {{ $report->ssc_id }} - PT SSC Works</title>
  <style>
    @page {
      size: A4 portrait;
      margin: 10mm 12mm;
    }
    * {
      box-sizing: border-box;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }
    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 10pt;
      color: #111827;
      margin: 0;
      padding: 0;
      background: #fff;
    }
    .print-actions {
      padding: 12px 20px;
      background: #f1f5f9;
      border-bottom: 1px solid #cbd5e1;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    @media print {
      .print-actions { display: none !important; }
    }
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 14px;
      font-size: 13px;
      font-weight: 600;
      border-radius: 4px;
      cursor: pointer;
      border: 1px solid #cbd5e1;
      background: #fff;
      color: #334155;
      text-decoration: none;
    }
    .btn-primary {
      background: #1e3a8a;
      color: #fff;
      border-color: #1e3a8a;
    }
    .sheet {
      width: 100%;
      max-width: 800px;
      margin: 15px auto;
      border: 2px solid #000;
      padding: 15px;
    }
    .header-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 10px;
    }
    .header-table td {
      vertical-align: middle;
      border: none;
    }
    .logo-img {
      width: 65px;
      height: 65px;
      object-fit: contain;
    }
    .comp-title {
      padding-left: 10px;
    }
    .comp-title h1 {
      margin: 0;
      font-size: 14pt;
      font-weight: 800;
      color: #1e3a8a;
      letter-spacing: 0.5px;
    }
    .comp-title h2 {
      margin: 2px 0 0 0;
      font-size: 11pt;
      font-weight: 700;
      color: #111827;
    }
    .comp-title p {
      margin: 2px 0 0 0;
      font-size: 8pt;
      color: #64748b;
    }
    .doc-box {
      border: 1px solid #000;
      padding: 4px 8px;
      font-size: 8.5pt;
      text-align: right;
    }
    .meta-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 9pt;
      margin-bottom: 12px;
      border: 1px solid #000;
    }
    .meta-table td {
      border: 1px solid #000;
      padding: 4px 6px;
    }
    .meta-table .lbl {
      background: #f3f4f6;
      font-weight: 600;
      width: 14%;
    }
    .meta-table .val {
      width: 20%;
    }
    .meta-table .mono {
      font-family: monospace;
    }
    .section-title {
      background: #1e3a8a;
      color: #fff;
      font-size: 8.5pt;
      font-weight: 700;
      padding: 4px 8px;
      margin: 8px 0 4px 0;
      letter-spacing: 0.5px;
    }
    .grid-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 8.5pt;
      margin-bottom: 8px;
    }
    .grid-table th, .grid-table td {
      border: 1px solid #000;
      padding: 3.5px 5px;
      text-align: left;
    }
    .grid-table th {
      background: #f3f4f6;
      font-weight: 700;
      text-align: center;
    }
    .center { text-align: center !important; }
    .mono { font-family: monospace; }
    .bold { font-weight: 700; }
    
    .photo-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 6px;
      margin-bottom: 10px;
    }
    .photo-cell {
      border: 1px solid #000;
      height: 110px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      background: #fafafa;
    }
    .photo-cell img {
      max-width: 100%;
      max-height: 90px;
      object-fit: contain;
    }
    .photo-cell .caption {
      font-size: 7.5pt;
      font-weight: 600;
      text-align: center;
      background: #f1f5f9;
      width: 100%;
      padding: 2px 0;
      border-top: 1px solid #000;
    }
    .stamp-wrap {
      display: flex;
      justify-content: flex-end;
      margin-bottom: 6px;
    }
    .stamp {
      display: inline-block;
      padding: 4px 14px;
      font-weight: 900;
      font-size: 11pt;
      border: 2px solid #16a34a;
      color: #16a34a;
      border-radius: 4px;
      letter-spacing: 1px;
    }
    .stamp.reject {
      border-color: #dc2626;
      color: #dc2626;
    }
    .sig-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
      font-size: 8.5pt;
      border: 1px solid #000;
    }
    .sig-table th {
      background: #f3f4f6;
      border: 1px solid #000;
      padding: 4px;
      text-align: center;
    }
    .sig-table td {
      border: 1px solid #000;
      padding: 6px;
      vertical-align: top;
      width: 33.33%;
    }
    .sig-space {
      height: 40px;
    }
  </style>
</head>
<body>

  <div class="print-actions">
    <div>
      <strong>Laporan QC: {{ $report->ssc_id }}</strong> &mdash; FORM-QC-002 REV 04
    </div>
    <div>
      <button class="btn" onclick="window.close()">Tutup</button>
      <button class="btn btn-primary" onclick="window.print()">
        Cetak ke Printer / Simpan PDF
      </button>
    </div>
  </div>

  <div class="sheet">
    
    <!-- Header -->
    <table class="header-table">
      <tr>
        <td style="width: 70px;">
          <img src="{{ asset('images/logo.png') }}" alt="PT SSC Works" class="logo-img">
        </td>
        <td class="comp-title">
          <h1>PT. SUKSES SARI COATING WORKS</h1>
          <h2>DIMENSIONAL INSPECTION REPORT</h2>
          <p>Workshop & Precision Quality Control Services</p>
        </td>
        <td style="width: 180px; text-align: right;">
          <div class="doc-box">
            <div><strong>DOC NO:</strong> FORM-QC-002</div>
            <div><strong>REV:</strong> 04</div>
            <div><strong>DATE:</strong> {{ $report->date ? \Carbon\Carbon::parse($report->date)->format('d/m/Y') : date('d/m/Y') }}</div>
          </div>
        </td>
      </tr>
    </table>

    <!-- Info Table -->
    <table class="meta-table">
      <tr>
        <td class="lbl">Report No.</td>
        <td class="val mono">{{ $report->report_no ?? '-' }}</td>
        <td class="lbl">Customer</td>
        <td class="val">{{ $report->customer ?? '-' }}</td>
        <td class="lbl">Part Name</td>
        <td class="val bold">{{ $report->part_name }}</td>
      </tr>
      <tr>
        <td class="lbl">SSC ID</td>
        <td class="val mono bold" style="color: #1e3a8a;">{{ $report->ssc_id }}</td>
        <td class="lbl">Job No.</td>
        <td class="val">{{ $report->job_no ?? '-' }}</td>
        <td class="lbl">Model Unit</td>
        <td class="val">{{ $report->model_unit ?? '-' }}</td>
      </tr>
      <tr>
        <td class="lbl">Type</td>
        <td class="val">{{ $report->inspection_type }}</td>
        <td class="lbl">Quote No.</td>
        <td class="val">{{ $report->quote_no ?? '-' }}</td>
        <td class="lbl">Part No.</td>
        <td class="val">{{ $report->part_no ?? '-' }}</td>
      </tr>
    </table>

    <div class="stamp-wrap">
      <div class="stamp {{ $report->has_rejects ? 'reject' : '' }}">
        {{ $report->has_rejects ? 'REJECTED' : 'ACCEPTED' }}
      </div>
    </div>

    <!-- Section 1: Dimensions -->
    <div class="section-title">I. PEMERIKSAAN DIMENSI (DIMENSIONAL INSPECTION)</div>
    @php
      $sec = is_array($report->sections) ? $report->sections : json_decode($report->sections ?? '[]', true);
    @endphp
    <table class="grid-table">
      <thead>
        <tr>
          <th style="width: 30px;">No</th>
          <th>Area / Titik Pengukuran</th>
          <th>Target Spesifikasi</th>
          <th style="width: 65px;">X (mm)</th>
          <th style="width: 65px;">Y (mm)</th>
          <th style="width: 65px;">Z (mm)</th>
          <th style="width: 45px;">A/R</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="center">01</td>
          <td>OD Area "A" & OD Spline</td>
          <td class="mono">Ø79 ± 0.25</td>
          <td class="mono center">{{ $sec['item01']['x'] ?? '-' }}</td>
          <td class="mono center">{{ $sec['item01']['y'] ?? '-' }}</td>
          <td class="mono center">{{ $sec['item01']['z'] ?? '-' }}</td>
          <td class="center bold">{{ $sec['item01']['ar'] ?? 'A' }}</td>
        </tr>
        <tr>
          <td class="center">02</td>
          <td>OD Area "B" (Baris 1)</td>
          <td class="mono">Ø82 ± 0.25</td>
          <td class="mono center">{{ $sec['item02']['itemB']['row1']['x'] ?? '-' }}</td>
          <td class="mono center">{{ $sec['item02']['itemB']['row1']['y'] ?? '-' }}</td>
          <td class="mono center">{{ $sec['item02']['itemB']['row1']['z'] ?? '-' }}</td>
          <td class="center bold">{{ $sec['item02']['itemB']['row1']['ar'] ?? 'A' }}</td>
        </tr>
        <tr>
          <td class="center">-</td>
          <td>OD Area "C" (Baris 1)</td>
          <td class="mono">Ø77 ± 0.25</td>
          <td class="mono center">{{ $sec['item02']['itemC']['row1']['x'] ?? '-' }}</td>
          <td class="mono center">{{ $sec['item02']['itemC']['row1']['y'] ?? '-' }}</td>
          <td class="mono center">{{ $sec['item02']['itemC']['row1']['z'] ?? '-' }}</td>
          <td class="center bold">{{ $sec['item02']['itemC']['row1']['ar'] ?? 'A' }}</td>
        </tr>
        <tr>
          <td class="center">03</td>
          <td>OD Area "D"</td>
          <td class="mono">Ø118 ± 0.5</td>
          <td class="mono center">{{ $sec['item03']['itemD']['x'] ?? '-' }}</td>
          <td class="mono center">{{ $sec['item03']['itemD']['y'] ?? '-' }}</td>
          <td class="mono center">{{ $sec['item03']['itemD']['z'] ?? '-' }}</td>
          <td class="center bold">{{ $sec['item03']['itemD']['ar'] ?? 'A' }}</td>
        </tr>
        <tr>
          <td class="center">04</td>
          <td>OD Area "F" (Baris 1)</td>
          <td class="mono">Ø100.01 ± 0.008</td>
          <td class="mono center">{{ $sec['item04']['itemF']['row1']['x'] ?? '-' }}</td>
          <td class="mono center">{{ $sec['item04']['itemF']['row1']['y'] ?? '-' }}</td>
          <td class="mono center">{{ $sec['item04']['itemF']['row1']['z'] ?? '-' }}</td>
          <td class="center bold">{{ $sec['item04']['itemF']['row1']['ar'] ?? 'A' }}</td>
        </tr>
        <tr>
          <td class="center">05</td>
          <td>OD Area "H" (Baris 1)</td>
          <td class="mono">Ø94 ± 0.013</td>
          <td class="mono center">{{ $sec['item05']['itemH']['row1']['x'] ?? '-' }}</td>
          <td class="mono center">{{ $sec['item05']['itemH']['row1']['y'] ?? '-' }}</td>
          <td class="mono center">{{ $sec['item05']['itemH']['row1']['z'] ?? '-' }}</td>
          <td class="center bold">{{ $sec['item05']['itemH']['row1']['ar'] ?? 'A' }}</td>
        </tr>
        <tr>
          <td class="center">06</td>
          <td>OD Area "G" & OD Spline</td>
          <td class="mono">Ø84.2 ± 0.13</td>
          <td class="mono center">{{ $sec['item06']['itemG']['x'] ?? '-' }}</td>
          <td class="mono center">{{ $sec['item06']['itemG']['y'] ?? '-' }}</td>
          <td class="mono center">{{ $sec['item06']['itemG']['z'] ?? '-' }}</td>
          <td class="center bold">{{ $sec['item06']['itemG']['ar'] ?? 'A' }}</td>
        </tr>
      </tbody>
    </table>

    <!-- Section 2: Runout TIR -->
    <div class="section-title">II. PENGUKURAN RUNOUT (TIR) &mdash; DATUM AT B & E (MAX 0.2 mm)</div>
    @php
      $tir = is_array($report->tir) ? $report->tir : json_decode($report->tir ?? '[]', true);
      $tItems = $tir['items'] ?? [];
    @endphp
    <table class="grid-table">
      <thead>
        <tr>
          <th>Titik Ukur</th>
          <th>A</th>
          <th>B</th>
          <th>C</th>
          <th>D</th>
          <th>E</th>
          <th>F</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="bold">Hasil 0°</td>
          @for($idx = 0; $idx < 6; $idx++)
            <td class="mono center">{{ $tItems[$idx]['d0'] ?? '-' }}</td>
          @endfor
        </tr>
        <tr>
          <td class="bold">Hasil 90°</td>
          @for($idx = 0; $idx < 6; $idx++)
            <td class="mono center">{{ $tItems[$idx]['d90'] ?? '-' }}</td>
          @endfor
        </tr>
        <tr>
          <td class="bold">Status A/R</td>
          @for($idx = 0; $idx < 6; $idx++)
            <td class="bold center">{{ $tItems[$idx]['ar'] ?? 'A' }}</td>
          @endfor
        </tr>
      </tbody>
    </table>

    <!-- Section 3: Photos -->
    <div class="section-title">III. DOKUMENTASI FOTO PEMERIKSAAN & NDT CRACK TEST</div>
    @php
      $photos = is_array($report->photos) ? $report->photos : json_decode($report->photos ?? '[]', true);
    @endphp
    <div class="photo-grid">
      <div class="photo-cell">
        @if(!empty($photos['item01']))
          <img src="{{ asset($photos['item01']) }}" alt="Foto Area A">
        @else
          <span style="font-size: 7.5pt; color: #94a3b8;">Tidak ada foto</span>
        @endif
        <div class="caption">Area A & Spline</div>
      </div>
      <div class="photo-cell">
        @if(!empty($photos['item02']))
          <img src="{{ asset($photos['item02']) }}" alt="Foto Area B/C">
        @else
          <span style="font-size: 7.5pt; color: #94a3b8;">Tidak ada foto</span>
        @endif
        <div class="caption">Area B & C</div>
      </div>
      <div class="photo-cell">
        @if(!empty($photos['penetrant']))
          <img src="{{ asset($photos['penetrant']) }}" alt="Foto Penetrant">
        @else
          <span style="font-size: 7.5pt; color: #94a3b8;">Tidak ada foto</span>
        @endif
        <div class="caption" style="color: #b91c1c;">Dye Penetrant Crack</div>
      </div>
      <div class="photo-cell">
        @if(!empty($photos['defect']))
          <img src="{{ asset($photos['defect']) }}" alt="Foto Dial/Defect">
        @else
          <span style="font-size: 7.5pt; color: #94a3b8;">Tidak ada foto</span>
        @endif
        <div class="caption">Dial Test / Temuan</div>
      </div>
    </div>

    <!-- Section 4: Recommendations -->
    <div class="section-title">IV. TEMUAN & REKOMENDASI MUTU</div>
    @php
      $recs = is_array($report->recommendations) ? $report->recommendations : json_decode($report->recommendations ?? '[]', true);
    @endphp
    <table class="grid-table">
      <thead>
        <tr>
          <th style="width: 35px;">No</th>
          <th>Remarks (Temuan Visual & Dimensi)</th>
          <th>Rekomendasi Tindakan</th>
        </tr>
      </thead>
      <tbody>
        @forelse($recs as $idx => $r)
          <tr>
            <td class="center">{{ $r['item'] ?? sprintf('%02d', $idx + 1) }}</td>
            <td>{{ $r['remarks'] ?? '-' }}</td>
            <td>{{ $r['recommendation'] ?? '-' }}</td>
          </tr>
        @empty
          <tr>
            <td class="center">01</td>
            <td>Cleaning and polish OD area "A"</td>
            <td>Cleaning and polish OD area "A"</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <!-- Signatures -->
    @php
      $sigs = is_array($report->signatures) ? $report->signatures : json_decode($report->signatures ?? '[]', true);
    @endphp
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
            <div class="sig-space"></div>
            <div class="bold">{{ $sigs['checkedBy']['name'] ?? 'Faqih S A A' }}</div>
            <div style="font-size: 7.5pt; color: #475569;">{{ $sigs['checkedBy']['title'] ?? 'QC Inspector' }}</div>
            <div style="font-size: 7.5pt;">Date: {{ $sigs['checkedBy']['date'] ?? '-' }}</div>
          </td>
          <td>
            <div class="sig-space"></div>
            <div class="bold">{{ $sigs['createdBy']['name'] ?? 'M Kevin S' }}</div>
            <div style="font-size: 7.5pt; color: #475569;">{{ $sigs['createdBy']['title'] ?? 'QC Inspector' }}</div>
            <div style="font-size: 7.5pt;">Date: {{ $sigs['createdBy']['date'] ?? '-' }}</div>
          </td>
          <td>
            <div class="sig-space"></div>
            <div class="bold">{{ $sigs['reviewedBy']['name'] ?? 'Agus Riyanto' }}</div>
            <div style="font-size: 7.5pt; color: #475569;">{{ $sigs['reviewedBy']['title'] ?? 'Leader QC' }}</div>
            <div style="font-size: 7.5pt;">Date: {{ $sigs['reviewedBy']['date'] ?? '-' }}</div>
          </td>
        </tr>
      </tbody>
    </table>

  </div>

</body>
</html>
