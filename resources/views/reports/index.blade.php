<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>CCR Pre Output Shaft QC System (Laravel + MySQL) - FORM-QC-002</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

  <!-- Header -->
  <header class="app-header">
    <div class="header-container">
      <div class="brand">
        <div class="logo-badge" style="background: linear-gradient(135deg, #dc2626, #b91c1c);">LV</div>
        <div class="brand-text">
          <h1>CCR PRE OUTPUT SHAFT <span class="doc-tag">FORM-QC-002</span> <span class="badge badge-blue" style="font-size: 0.7rem; margin-left: 6px;">Laravel & MySQL</span></h1>
          <p>Component Condition Report & Quality Control Management System</p>
        </div>
      </div>
      <div>
        <button id="btnCreateNew" class="btn btn-primary">
          <i data-lucide="plus-circle"></i> Buat Laporan Baru
        </button>
      </div>
    </div>
  </header>

  <!-- Main Container -->
  <main class="main-container">

    <!-- Flash Alerts -->
    @if(session('success'))
      <div class="toast success" style="margin-bottom: 1rem; position: static; animation: none;">
        <i data-lucide="check-circle"></i> <span>{{ session('success') }}</span>
      </div>
    @endif
    @if(session('error'))
      <div class="toast error" style="margin-bottom: 1rem; position: static; animation: none;">
        <i data-lucide="alert-circle"></i> <span>{{ session('error') }}</span>
      </div>
    @endif

    <!-- Stats Grid -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon blue"><i data-lucide="file-spreadsheet"></i></div>
        <div class="stat-info">
          <p>Total Laporan (MySQL)</p>
          <h3 id="statTotal">{{ $totalCount }}</h3>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green"><i data-lucide="check-circle-2"></i></div>
        <div class="stat-info">
          <p>All Accept (Lolos)</p>
          <h3 id="statAccept">{{ $acceptCount }}</h3>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon orange"><i data-lucide="alert-triangle"></i></div>
        <div class="stat-info">
          <p>With Rejects / Findings</p>
          <h3 id="statReject">{{ $rejectCount }}</h3>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon purple"><i data-lucide="database"></i></div>
        <div class="stat-info">
          <p>Database Engine</p>
          <h3 style="font-size: 1.25rem;">MariaDB / MySQL</h3>
        </div>
      </div>
    </div>

    <!-- Control Bar -->
    <div class="control-bar">
      <div class="search-box">
        <i data-lucide="search" class="search-icon"></i>
        <input type="text" id="searchInput" placeholder="Cari nomor SSC, Customer, No. Laporan, Part Name...">
      </div>

      <div class="filter-group">
        <select id="typeFilter" class="select-filter">
          <option value="All">Semua Tipe Inspeksi</option>
          <option value="Pre">Pre-Inspection</option>
          <option value="Post">Post-Inspection</option>
        </select>
        <button id="btnRefresh" class="btn btn-secondary btn-icon" title="Refresh Halaman">
          <i data-lucide="refresh-cw"></i>
        </button>
      </div>
    </div>

    <!-- Table Card -->
    <div class="table-card">
      <div class="table-responsive">
        <table class="qc-table">
          <thead>
            <tr>
              <th>SSC ID</th>
              <th>No. Laporan</th>
              <th>Tanggal</th>
              <th>Customer</th>
              <th>Part Name / Unit</th>
              <th>Inspeksi</th>
              <th>Status QC</th>
              <th style="text-align: right;">Aksi</th>
            </tr>
          </thead>
          <tbody id="reportTableBody">
            @forelse($reports as $item)
              <tr data-type="{{ $item->inspection_type }}">
                <td class="mono" style="font-weight: 700; color: var(--primary);">
                  {{ $item->ssc_id }}
                </td>
                <td>{{ $item->report_no ?? '-' }}</td>
                <td>{{ $item->date ? \Carbon\Carbon::parse($item->date)->format('Y-m-d') : '-' }}</td>
                <td style="font-weight: 500;">{{ $item->customer ?? '-' }}</td>
                <td>{{ $item->part_name }} <span style="color: var(--gray-400); font-size: 0.8rem;">({{ $item->model_unit }})</span></td>
                <td>
                  <span class="badge {{ $item->inspection_type === 'Pre' ? 'badge-blue' : 'badge-purple' }}">
                    {{ $item->inspection_type }}
                  </span>
                </td>
                <td>
                  <span class="badge {{ $item->has_rejects ? 'badge-red' : 'badge-green' }}">
                    {{ $item->has_rejects ? 'REJECT / FINDINGS' : 'ACCEPT' }}
                  </span>
                </td>
                <td style="text-align: right; white-space: nowrap;">
                  <button class="btn btn-success btn-sm" onclick="downloadExcel({{ $item->id }}, '{{ $item->ssc_id }}')" title="Download Excel Sesuai Format Asli">
                    <i data-lucide="download"></i> Excel
                  </button>
                  <button class="btn btn-secondary btn-sm" onclick="openEditModal({{ $item->id }})" title="Edit Laporan">
                    <i data-lucide="edit-3"></i> Edit
                  </button>
                  <button class="btn btn-secondary btn-sm" onclick="duplicateReport({{ $item->id }})" title="Duplikat Laporan">
                    <i data-lucide="copy"></i> Duplikat
                  </button>
                  <button class="btn btn-danger btn-sm" onclick="deleteReport({{ $item->id }}, '{{ $item->ssc_id }}')" title="Hapus Laporan">
                    <i data-lucide="trash-2"></i>
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" style="text-align: center; padding: 3rem; color: var(--gray-400);">
                  <i data-lucide="file-x" style="width: 36px; height: 36px; margin-bottom: 0.5rem;"></i><br>
                  Belum ada data laporan di database MySQL. Klik <strong>Buat Laporan Baru</strong> untuk mulai.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </main>

  <!-- Modal Form -->
  <div class="modal-overlay" id="reportModal">
    <div class="modal-dialog">
      <div class="modal-header">
        <div>
          <h2 id="modalTitle"><i data-lucide="file-edit"></i> Edit Laporan CCR</h2>
          <p style="font-size: 0.8rem; color: var(--gray-500); margin-top: 2px;">
            Data disimpan ke MySQL <code>ccr_db</code> dan diekspor tepat sesuai format template Excel <code>FORM-QC-002</code>
          </p>
        </div>
        <button class="modal-close" id="btnModalClose">&times;</button>
      </div>

      <!-- Navigation Tabs -->
      <div class="modal-tabs">
        <button class="tab-btn active" data-tab="tab-general">
          <i data-lucide="info"></i> 1. Informasi Umum
        </button>
        <button class="tab-btn" data-tab="tab-sec1">
          <i data-lucide="ruler"></i> 2. Item 01 (Area A)
        </button>
        <button class="tab-btn" data-tab="tab-sec2">
          <i data-lucide="ruler"></i> 3. Item 02 (Area B & C)
        </button>
        <button class="tab-btn" data-tab="tab-sec34">
          <i data-lucide="ruler"></i> 4. Item 03 & 04 (Area D & F)
        </button>
        <button class="tab-btn" data-tab="tab-sec56">
          <i data-lucide="ruler"></i> 5. Item 05 & 06 (Area H & G)
        </button>
        <button class="tab-btn" data-tab="tab-tir">
          <i data-lucide="compass"></i> 6. Check TIR
        </button>
        <button class="tab-btn" data-tab="tab-recommendations">
          <i data-lucide="clipboard-list"></i> 7. Rekomendasi
        </button>
        <button class="tab-btn" data-tab="tab-signatures">
          <i data-lucide="pen-tool"></i> 8. Tanda Tangan
        </button>
      </div>

      <!-- Modal Body -->
      <form id="reportForm" class="modal-body">
        <input type="hidden" id="reportId">

        <!-- TAB 1: General Info -->
        <div class="tab-content active" id="tab-general">
          <div class="form-grid">
            <div class="form-group">
              <label>SSC ID <span style="color:red">*</span></label>
              <input type="text" id="g_sscId" required placeholder="Contoh: SSC SBY-5778" class="mono">
            </div>
            <div class="form-group">
              <label>No. Laporan</label>
              <input type="text" id="g_reportNo" placeholder="Contoh: 0742">
            </div>
            <div class="form-group">
              <label>Tanggal Inspeksi</label>
              <input type="date" id="g_date">
            </div>
            <div class="form-group">
              <label>Tipe Inspeksi</label>
              <select id="g_inspectionType">
                <option value="Pre">Pre</option>
                <option value="Post">Post</option>
              </select>
            </div>
            <div class="form-group span-2">
              <label>Customer</label>
              <input type="text" id="g_customer" placeholder="Nama Perusahaan / Customer">
            </div>
            <div class="form-group">
              <label>Part Name</label>
              <input type="text" id="g_partName" value="OUTPUT SHAFT">
            </div>
            <div class="form-group">
              <label>Model / Unit</label>
              <input type="text" id="g_modelUnit" placeholder="Contoh: HT051">
            </div>
            <div class="form-group">
              <label>Job No.</label>
              <input type="text" id="g_jobNo" placeholder="-">
            </div>
            <div class="form-group">
              <label>Quote No.</label>
              <input type="text" id="g_quoteNo" placeholder="-">
            </div>
            <div class="form-group">
              <label>Part No.</label>
              <input type="text" id="g_partNo" placeholder="-">
            </div>
            <div class="form-group">
              <label>Document No</label>
              <input type="text" id="g_docNo" value="FORM-QC-002" readonly style="background: var(--gray-100);">
            </div>
          </div>
        </div>

        <!-- TAB 2: Item 01 -->
        <div class="tab-content" id="tab-sec1">
          <div class="section-box">
            <h3>
              <span>01. OD Area "A" and OD Spline</span>
              <span class="spec-pill">Spec: Ø79 ± 0.25</span>
            </h3>
            
            <p style="font-size: 0.8rem; color: var(--gray-500); margin-bottom: 0.5rem;">Hasil Pengukuran Dimensi (X, Y, Z, A/R):</p>
            <table class="meas-table">
              <thead>
                <tr>
                  <th>Item</th>
                  <th>Spec</th>
                  <th>X</th>
                  <th>Y</th>
                  <th>Z</th>
                  <th>A / R</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>A</strong></td>
                  <td>Ø79 ± 0.25</td>
                  <td><input type="number" step="0.001" id="s1_x"></td>
                  <td><input type="number" step="0.001" id="s1_y"></td>
                  <td><input type="number" step="0.001" id="s1_z"></td>
                  <td>
                    <select id="s1_ar">
                      <option value="A">A (Accept)</option>
                      <option value="R">R (Reject)</option>
                    </select>
                  </td>
                </tr>
              </tbody>
            </table>

            <div style="margin-top: 1rem;">
              <p style="font-size: 0.8rem; color: var(--gray-500); margin-bottom: 0.5rem;">Panjang Aktual (Item L):</p>
              <table class="meas-table">
                <thead>
                  <tr>
                    <th>Item</th>
                    <th>Spec</th>
                    <th>Hasil Pengukuran (L)</th>
                    <th>A / R</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><strong>L</strong></td>
                    <td>N/A</td>
                    <td><input type="number" step="0.01" id="s1_lenResult"></td>
                    <td>
                      <select id="s1_lenAr">
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="R">R</option>
                      </select>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div style="margin-top: 1.25rem;">
              <label style="font-size: 0.82rem; font-weight: 600;">Temuan Visual & Pengukuran:</label>
              <div style="display: grid; gap: 0.5rem; margin-top: 0.4rem;">
                <input type="text" id="s1_rem1" placeholder="Remark 1 (misal: Actual measurement OD area 'A' in good condition)">
                <input type="text" id="s1_rem2" placeholder="Remark 2 (misal: Visual minor damage at OD area 'A')">
                <input type="text" id="s1_rem3" placeholder="Remark 3 (misal: Visual scratch at OD area 'A')">
                <input type="text" id="s1_rem4" placeholder="Remark 4 (misal: Visual corrosion area spline)">
                <input type="text" id="s1_rem5" placeholder="Remark 5 (misal: Visual scratch at area surface)">
                <input type="text" id="s1_rem6" placeholder="Remark 6 (misal: Visual dirty at hole thread area surface)">
              </div>
            </div>

            <div style="margin-top: 1rem;">
              <label style="font-size: 0.82rem; font-weight: 600;">Penetrant Check Result:</label>
              <input type="text" id="s1_penetrant" style="width: 100%; margin-top: 0.35rem;" placeholder="Hasil cek penetrant">
            </div>
          </div>
        </div>

        <!-- TAB 3: Item 02 -->
        <div class="tab-content" id="tab-sec2">
          <div class="section-box">
            <h3>
              <span>02. OD Area "B and C"</span>
              <div>
                <span class="spec-pill">Item B: Ø82 ± 0.25</span>
                <span class="spec-pill">Item C: Ø77 ± 0.25</span>
              </div>
            </h3>

            <p style="font-size: 0.82rem; font-weight: 600; margin-top: 0.5rem;">Pengukuran Item B (Ø82 ± 0.25):</p>
            <table class="meas-table">
              <thead>
                <tr>
                  <th>Titik / Baris</th>
                  <th>X</th>
                  <th>Y</th>
                  <th>Z</th>
                  <th>A / R</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Posisi 1</td>
                  <td><input type="number" step="0.001" id="s2_bx1"></td>
                  <td><input type="number" step="0.001" id="s2_by1"></td>
                  <td><input type="number" step="0.001" id="s2_bz1"></td>
                  <td><select id="s2_bar1"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td>Posisi 2</td>
                  <td><input type="number" step="0.001" id="s2_bx2"></td>
                  <td><input type="number" step="0.001" id="s2_by2"></td>
                  <td><input type="number" step="0.001" id="s2_bz2"></td>
                  <td><select id="s2_bar2"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td>Posisi 3</td>
                  <td><input type="number" step="0.001" id="s2_bx3"></td>
                  <td><input type="number" step="0.001" id="s2_by3"></td>
                  <td><input type="number" step="0.001" id="s2_bz3"></td>
                  <td><select id="s2_bar3"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
              </tbody>
            </table>

            <p style="font-size: 0.82rem; font-weight: 600; margin-top: 1rem;">Pengukuran Item C (Ø77 ± 0.25):</p>
            <table class="meas-table">
              <thead>
                <tr>
                  <th>Titik / Baris</th>
                  <th>X</th>
                  <th>Y</th>
                  <th>Z</th>
                  <th>A / R</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Posisi 1</td>
                  <td><input type="number" step="0.001" id="s2_cx1"></td>
                  <td><input type="number" step="0.001" id="s2_cy1"></td>
                  <td><input type="number" step="0.001" id="s2_cz1"></td>
                  <td><select id="s2_car1"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td>Posisi 2</td>
                  <td><input type="number" step="0.001" id="s2_cx2"></td>
                  <td><input type="number" step="0.001" id="s2_cy2"></td>
                  <td><input type="number" step="0.001" id="s2_cz2"></td>
                  <td><select id="s2_car2"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td>Posisi 3</td>
                  <td><input type="number" step="0.001" id="s2_cx3"></td>
                  <td><input type="number" step="0.001" id="s2_cy3"></td>
                  <td><input type="number" step="0.001" id="s2_cz3"></td>
                  <td><select id="s2_car3"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
              </tbody>
            </table>

            <div style="margin-top: 1.25rem;">
              <label style="font-size: 0.82rem; font-weight: 600;">Temuan & Remark Visual Area B & C:</label>
              <div style="display: grid; gap: 0.5rem; margin-top: 0.4rem;">
                <input type="text" id="s2_rem1" placeholder="Remark 1 (misal: Actual measurement OD area 'B and C' in good condition)">
                <input type="text" id="s2_rem2" placeholder="Remark 2 (misal: Visual corrosion at OD area 'B')">
                <input type="text" id="s2_rem3" placeholder="Remark 3 (misal: Bolt still install at hole thread area bottom surface)">
                <input type="text" id="s2_rem4" placeholder="Remark 4 (misal: Visual corrosion at OD area 'C')">
              </div>
            </div>

            <div style="margin-top: 1rem;">
              <label style="font-size: 0.82rem; font-weight: 600;">Penetrant Check Result:</label>
              <input type="text" id="s2_penetrant" style="width: 100%; margin-top: 0.35rem;" placeholder="Hasil cek penetrant Area B & C">
            </div>
          </div>
        </div>

        <!-- TAB 4: Items 03 & 04 -->
        <div class="tab-content" id="tab-sec34">
          <!-- Item 03 -->
          <div class="section-box">
            <h3>
              <span>03. OD Area "D" & Jarak E</span>
              <div>
                <span class="spec-pill">Item D: Ø118 ± 0.5</span>
                <span class="spec-pill">Distance E: 13 ± 0.5</span>
              </div>
            </h3>
            <table class="meas-table">
              <thead>
                <tr>
                  <th>Item</th>
                  <th>Spec</th>
                  <th>X</th>
                  <th>Y</th>
                  <th>Z</th>
                  <th>A / R</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>D</strong></td>
                  <td>Ø118 ± 0.5</td>
                  <td><input type="number" step="0.001" id="s3_dx"></td>
                  <td><input type="number" step="0.001" id="s3_dy"></td>
                  <td><input type="number" step="0.001" id="s3_dz"></td>
                  <td><select id="s3_dar"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td><strong>E</strong> (Distance)</td>
                  <td>13 ± 0.5</td>
                  <td><input type="number" step="0.001" id="s3_ex"></td>
                  <td><input type="number" step="0.001" id="s3_ey"></td>
                  <td><input type="number" step="0.001" id="s3_ez"></td>
                  <td><select id="s3_ear"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
              </tbody>
            </table>

            <div style="margin-top: 1rem; display: grid; gap: 0.5rem;">
              <input type="text" id="s3_rem1" placeholder="Remark 1 (misal: Actual measurement OD area 'D' in good condition)">
              <input type="text" id="s3_rem2" placeholder="Remark 2 (misal: Visual scratch at OD area 'D')">
              <input type="text" id="s3_rem3" placeholder="Remark 3 (misal: Actual distance area 'E' in good condition)">
              <input type="text" id="s3_rem4" placeholder="Remark 4 (misal: Visual worn out at area surface)">
              <input type="text" id="s3_penetrant" placeholder="Hasil cek penetrant OD Area D">
            </div>
          </div>

          <!-- Item 04 -->
          <div class="section-box">
            <h3>
              <span>04. OD Area "F" & Jarak K</span>
              <div>
                <span class="spec-pill">Item F: Ø100.01 ± 0.008</span>
                <span class="spec-pill">Distance K: N/A (188.55)</span>
              </div>
            </h3>
            <table class="meas-table">
              <thead>
                <tr>
                  <th>Posisi</th>
                  <th>X</th>
                  <th>Y</th>
                  <th>Z</th>
                  <th>A / R</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Posisi 1</td>
                  <td><input type="number" step="0.001" id="s4_fx1"></td>
                  <td><input type="number" step="0.001" id="s4_fy1"></td>
                  <td><input type="number" step="0.001" id="s4_fz1"></td>
                  <td><select id="s4_far1"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td>Posisi 2</td>
                  <td><input type="number" step="0.001" id="s4_fx2"></td>
                  <td><input type="number" step="0.001" id="s4_fy2"></td>
                  <td><input type="number" step="0.001" id="s4_fz2"></td>
                  <td><select id="s4_far2"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td>Posisi 3</td>
                  <td><input type="number" step="0.001" id="s4_fx3"></td>
                  <td><input type="number" step="0.001" id="s4_fy3"></td>
                  <td><input type="number" step="0.001" id="s4_fz3"></td>
                  <td><select id="s4_far3"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
              </tbody>
            </table>

            <div style="margin-top: 0.75rem; display: flex; gap: 1rem; align-items: center;">
              <span style="font-size: 0.82rem; font-weight: 600;">Distance K (Result):</span>
              <input type="number" step="0.01" id="s4_kResult" style="width: 120px;" placeholder="188.55">
              <select id="s4_kAr"><option value="-">-</option><option value="A">A</option><option value="R">R</option></select>
            </div>

            <div style="margin-top: 1rem; display: grid; gap: 0.5rem;">
              <input type="text" id="s4_rem1" placeholder="Remark 1 (misal: Actual measurement OD area 'F' in good condition)">
              <input type="text" id="s4_rem2" placeholder="Remark 2 (misal: Visual pitting and scratch OD area 'F')">
              <input type="text" id="s4_penetrant" placeholder="Hasil cek penetrant OD Area F">
            </div>
          </div>
        </div>

        <!-- TAB 5: Items 05 & 06 -->
        <div class="tab-content" id="tab-sec56">
          <!-- Item 05 -->
          <div class="section-box">
            <h3>
              <span>05. OD Area "H" & Jarak J</span>
              <div>
                <span class="spec-pill">Item H: Ø94 ± 0.013</span>
                <span class="spec-pill">Distance J: N/A (142.80)</span>
              </div>
            </h3>
            <table class="meas-table">
              <thead>
                <tr>
                  <th>Posisi</th>
                  <th>X</th>
                  <th>Y</th>
                  <th>Z</th>
                  <th>A / R</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Posisi 1</td>
                  <td><input type="number" step="0.001" id="s5_hx1"></td>
                  <td><input type="number" step="0.001" id="s5_hy1"></td>
                  <td><input type="number" step="0.001" id="s5_hz1"></td>
                  <td><select id="s5_har1"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td>Posisi 2</td>
                  <td><input type="number" step="0.001" id="s5_hx2"></td>
                  <td><input type="number" step="0.001" id="s5_hy2"></td>
                  <td><input type="number" step="0.001" id="s5_hz2"></td>
                  <td><select id="s5_har2"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td>Posisi 3</td>
                  <td><input type="number" step="0.001" id="s5_hx3"></td>
                  <td><input type="number" step="0.001" id="s5_hy3"></td>
                  <td><input type="number" step="0.001" id="s5_hz3"></td>
                  <td><select id="s5_har3"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
              </tbody>
            </table>

            <div style="margin-top: 0.75rem; display: flex; gap: 1rem; align-items: center;">
              <span style="font-size: 0.82rem; font-weight: 600;">Distance J (Result):</span>
              <input type="number" step="0.01" id="s5_jResult" style="width: 120px;" placeholder="142.80">
              <select id="s5_jAr"><option value="-">-</option><option value="A">A</option><option value="R">R</option></select>
            </div>

            <div style="margin-top: 1rem; display: grid; gap: 0.5rem;">
              <input type="text" id="s5_rem1" placeholder="Remark 1 (misal: Actual measurement OD area 'H' was undersize)">
              <input type="text" id="s5_rem2" placeholder="Remark 2 (misal: Visual pitting and scratch at OD area 'H')">
              <input type="text" id="s5_penetrant" placeholder="Hasil cek penetrant Area H">
            </div>
          </div>

          <!-- Item 06 -->
          <div class="section-box">
            <h3>
              <span>06. OD Area "G", OD Spline & Jarak I</span>
              <div>
                <span class="spec-pill">Item G: Ø84.2 ± 0.13</span>
                <span class="spec-pill">Distance I: N/A (98.78)</span>
              </div>
            </h3>
            <table class="meas-table">
              <thead>
                <tr>
                  <th>Item</th>
                  <th>Spec</th>
                  <th>X</th>
                  <th>Y</th>
                  <th>Z</th>
                  <th>A / R</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>G</strong></td>
                  <td>Ø84.2 ± 0.13</td>
                  <td><input type="number" step="0.001" id="s6_gx"></td>
                  <td><input type="number" step="0.001" id="s6_gy"></td>
                  <td><input type="number" step="0.001" id="s6_gz"></td>
                  <td><select id="s6_gar"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
              </tbody>
            </table>

            <div style="margin-top: 0.75rem; display: flex; gap: 1rem; align-items: center;">
              <span style="font-size: 0.82rem; font-weight: 600;">Distance I (Result):</span>
              <input type="number" step="0.01" id="s6_iResult" style="width: 120px;" placeholder="98.78">
              <select id="s6_iAr"><option value="-">-</option><option value="A">A</option><option value="R">R</option></select>
            </div>

            <div style="margin-top: 1rem; display: grid; gap: 0.5rem;">
              <input type="text" id="s6_rem1" placeholder="Remark 1 (misal: Actual measurement OD area 'G' was undersize)">
              <input type="text" id="s6_rem2" placeholder="Remark 2 (misal: Visual scratch area spline)">
              <input type="text" id="s6_rem3" placeholder="Remark 3 (misal: Visual corrosion at OD area 'G')">
              <input type="text" id="s6_rem4" placeholder="Remark 4 (misal: Visual corrosion area spline)">
              <input type="text" id="s6_rem5" placeholder="Remark 5 (misal: Visual scratch and corrosion at area surface)">
              <input type="text" id="s6_rem6" placeholder="Remark 6 (misal: Visual dirty at hole thread area surface)">
              <input type="text" id="s6_penetrant" placeholder="Hasil cek penetrant Area G & Spline">
            </div>
          </div>
        </div>

        <!-- TAB 6: Check TIR -->
        <div class="tab-content" id="tab-tir">
          <div class="section-box">
            <h3>
              <span>Pemeriksaan Runout (Check TIR)</span>
              <span class="spec-pill">Spec: Max 0,2</span>
            </h3>

            <div style="margin-bottom: 0.75rem;">
              <label style="font-size: 0.82rem; font-weight: 600;">Datum Referensi:</label>
              <input type="text" id="tir_datum" style="width: 100%; margin-top: 0.25rem;" value="Datum at item &quot;B and E&quot;">
            </div>

            <table class="meas-table">
              <thead>
                <tr>
                  <th>Item</th>
                  <th>Spec</th>
                  <th>0°</th>
                  <th>90°</th>
                  <th>180°</th>
                  <th>270°</th>
                  <th>A / R</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>A</strong></td>
                  <td>Max 0,2</td>
                  <td><input type="text" id="tir_a_0"></td>
                  <td><input type="text" id="tir_a_90"></td>
                  <td><input type="text" id="tir_a_180"></td>
                  <td><input type="text" id="tir_a_270"></td>
                  <td><select id="tir_a_ar"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td><strong>B</strong></td>
                  <td>Max 0,2</td>
                  <td><input type="text" id="tir_b_0"></td>
                  <td><input type="text" id="tir_b_90"></td>
                  <td><input type="text" id="tir_b_180"></td>
                  <td><input type="text" id="tir_b_270"></td>
                  <td><select id="tir_b_ar"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td><strong>C</strong></td>
                  <td>Max 0,2</td>
                  <td><input type="text" id="tir_c_0"></td>
                  <td><input type="text" id="tir_c_90"></td>
                  <td><input type="text" id="tir_c_180"></td>
                  <td><input type="text" id="tir_c_270"></td>
                  <td><select id="tir_c_ar"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td><strong>D</strong></td>
                  <td>Max 0,2</td>
                  <td><input type="text" id="tir_d_0"></td>
                  <td><input type="text" id="tir_d_90"></td>
                  <td><input type="text" id="tir_d_180"></td>
                  <td><input type="text" id="tir_d_270"></td>
                  <td><select id="tir_d_ar"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td><strong>E</strong></td>
                  <td>Max 0,2</td>
                  <td><input type="text" id="tir_e_0"></td>
                  <td><input type="text" id="tir_e_90"></td>
                  <td><input type="text" id="tir_e_180"></td>
                  <td><input type="text" id="tir_e_270"></td>
                  <td><select id="tir_e_ar"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td><strong>F</strong></td>
                  <td>Max 0,2</td>
                  <td><input type="text" id="tir_f_0"></td>
                  <td><input type="text" id="tir_f_90"></td>
                  <td><input type="text" id="tir_f_180"></td>
                  <td><input type="text" id="tir_f_270"></td>
                  <td><select id="tir_f_ar"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- TAB 7: Recommendations -->
        <div class="tab-content" id="tab-recommendations">
          <div class="section-box">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
              <h3 style="margin: 0;">Tabel Temuan & Rekomendasi (Summary)</h3>
              <button type="button" id="btnAddRecRow" class="btn btn-secondary btn-sm">
                <i data-lucide="plus"></i> Tambah Baris
              </button>
            </div>
            
            <table class="dyn-table">
              <thead>
                <tr>
                  <th style="width: 70px;">Item</th>
                  <th>Remarks (Temuan Visual / Kondisi)</th>
                  <th>Recommendation (Tindakan Rekomendasi)</th>
                  <th style="width: 50px;">Aksi</th>
                </tr>
              </thead>
              <tbody id="recTableBody">
                <!-- Dynamically filled -->
              </tbody>
            </table>
          </div>
        </div>

        <!-- TAB 8: Signatures -->
        <div class="tab-content" id="tab-signatures">
          <div class="form-grid">
            <div class="section-box" style="margin-bottom: 0;">
              <h3>Checked by:</h3>
              <div class="form-group" style="margin-bottom: 0.5rem;">
                <label>Nama Pemeriksa</label>
                <input type="text" id="sig_checkedName" value="Faqih S A A">
              </div>
              <div class="form-group" style="margin-bottom: 0.5rem;">
                <label>Jabatan</label>
                <input type="text" id="sig_checkedTitle" value="QC Inspector">
              </div>
              <div class="form-group">
                <label>Tanggal</label>
                <input type="date" id="sig_checkedDate">
              </div>
            </div>

            <div class="section-box" style="margin-bottom: 0;">
              <h3>Created by:</h3>
              <div class="form-group" style="margin-bottom: 0.5rem;">
                <label>Nama Pembuat</label>
                <input type="text" id="sig_createdName" value="M Kevin S">
              </div>
              <div class="form-group" style="margin-bottom: 0.5rem;">
                <label>Jabatan</label>
                <input type="text" id="sig_createdTitle" value="QC Inspector">
              </div>
              <div class="form-group">
                <label>Tanggal</label>
                <input type="date" id="sig_createdDate">
              </div>
            </div>

            <div class="section-box" style="margin-bottom: 0;">
              <h3>Reviewed by:</h3>
              <div class="form-group" style="margin-bottom: 0.5rem;">
                <label>Nama Peninjau</label>
                <input type="text" id="sig_reviewedName" value="Agus Riyanto">
              </div>
              <div class="form-group" style="margin-bottom: 0.5rem;">
                <label>Jabatan</label>
                <input type="text" id="sig_reviewedTitle" value="Leader QC">
              </div>
              <div class="form-group">
                <label>Tanggal</label>
                <input type="date" id="sig_reviewedDate">
              </div>
            </div>
          </div>
        </div>

      </form>

      <!-- Modal Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="btnCancelModal">Batal</button>
        <div class="modal-footer-actions">
          <button type="button" class="btn btn-primary" id="btnSaveOnly">
            <i data-lucide="save"></i> Simpan ke MySQL Saja
          </button>
          <button type="button" class="btn btn-success" id="btnSubmitAndDownload">
            <i data-lucide="download"></i> 🚀 Simpan & Download Excel (.xlsx)
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Toast Container -->
  <div class="toast-container" id="toastContainer"></div>

  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>

