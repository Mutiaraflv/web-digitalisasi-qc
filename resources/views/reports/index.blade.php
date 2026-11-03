@extends('layouts.app')

@section('title', 'Inspeksi QC')
@section('page_title', 'Inspeksi QC')

@section('top_actions')
  <button id="btnCreateNew" class="btn btn-primary btn-sm">
    <i data-lucide="plus"></i> Inspeksi Baru
  </button>
@endsection

@section('content')
  <div class="page-header">
    <div class="page-title">
      <h1>Daftar Inspeksi Komponen</h1>
      <p>Pemeriksaan dimensi, pengujian keretakan (NDT), dan pengukuran runout poros.</p>
    </div>
  </div>

  <!-- Flat Corporate KPI Grid (No AI icon bubbles, No Database Storage card) -->
  <div class="stats-grid">
    <div class="stat-card">
      <p>Total Laporan</p>
      <h3>{{ $totalCount }}</h3>
      <div class="stat-sub">Inspeksi tercatat</div>
    </div>
    <div class="stat-card">
      <p>Lolos (Accept)</p>
      <h3 style="color: var(--success);">{{ $acceptCount }}</h3>
      <div class="stat-sub">Sesuai spesifikasi</div>
    </div>
    <div class="stat-card">
      <p>Temuan (Reject)</p>
      <h3 style="color: var(--danger);">{{ $rejectCount }}</h3>
      <div class="stat-sub">Di luar toleransi</div>
    </div>
    <div class="stat-card">
      <p>Tingkat Kelulusan</p>
      <h3>{{ $totalCount > 0 ? round(($acceptCount / $totalCount) * 100, 1) : 100 }}%</h3>
      <div class="stat-sub">Rasio lolos mutu</div>
    </div>
  </div>

  <!-- Control Bar -->
  <div class="control-bar">
    <div class="search-box">
      <i data-lucide="search" class="search-icon"></i>
      <input type="text" id="searchInput" placeholder="Cari nomor SSC, customer, laporan, part...">
    </div>

    <div class="filter-group">
      <select id="typeFilter" class="select-filter">
        <option value="All">Semua Tipe</option>
        <option value="Pre">Pre-Inspection</option>
        <option value="Post">Post-Inspection</option>
      </select>
      <button id="btnRefresh" class="btn btn-secondary btn-icon" title="Refresh">
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
            <th>Komponen</th>
            <th>Tipe</th>
            <th>Status</th>
            <th style="text-align: right;">Aksi</th>
          </tr>
        </thead>
        <tbody id="reportTableBody">
          @forelse($reports as $item)
            <tr data-type="{{ $item->inspection_type }}">
              <td class="mono" style="font-weight: 600; color: var(--primary);">
                {{ $item->ssc_id }}
              </td>
              <td class="mono">{{ $item->report_no ?? '-' }}</td>
              <td>{{ $item->date ? \Carbon\Carbon::parse($item->date)->format('Y-m-d') : '-' }}</td>
              <td>{{ $item->customer ?? '-' }}</td>
              <td>
                <strong>{{ $item->part_name }}</strong>
                <span style="color: var(--text-subtle); font-size: 0.75rem;">({{ $item->model_unit }})</span>
              </td>
              <td>
                <span class="badge {{ $item->inspection_type === 'Pre' ? 'badge-blue' : 'badge-purple' }}">
                  {{ $item->inspection_type }}
                </span>
              </td>
              <td>
                <span class="badge {{ $item->has_rejects ? 'badge-red' : 'badge-green' }}">
                  {{ $item->has_rejects ? 'REJECT' : 'ACCEPT' }}
                </span>
              </td>
              <td style="text-align: right; white-space: nowrap;">
                <button class="btn btn-secondary btn-sm" onclick="downloadExcel({{ $item->id }}, '{{ $item->ssc_id }}')" title="Download Excel">
                  <i data-lucide="download"></i> Excel
                </button>
                <button class="btn btn-secondary btn-sm" onclick="openEditModal({{ $item->id }})" title="Edit Laporan">
                  <i data-lucide="edit"></i> Edit
                </button>
                <button class="btn btn-secondary btn-sm" onclick="duplicateReport({{ $item->id }})" title="Duplikat">
                  <i data-lucide="copy"></i>
                </button>
                <button class="btn btn-danger btn-sm" onclick="deleteReport({{ $item->id }}, '{{ $item->ssc_id }}')" title="Hapus">
                  <i data-lucide="trash-2"></i>
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" style="text-align: center; padding: 2.5rem; color: var(--text-subtle);">
                Belum ada data inspeksi. Klik <strong>Inspeksi Baru</strong> untuk menambahkan.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Form Dialog -->
  <div class="modal-overlay" id="reportModal">
    <div class="modal-dialog">
      <!-- Modal Header -->
      <div class="modal-header">
        <h2 id="modalTitle">Form Inspeksi Komponen</h2>
        <button class="modal-close" id="btnModalClose">&times;</button>
      </div>

      <!-- Blueprint / Shaft Visual Guide Banner (Clean & Collapsible) -->
      <div class="blueprint-card">
        <div class="blueprint-header" id="toggleBlueprint">
          <h4>Skema Titik Ukur Poros</h4>
          <span style="font-size: 0.75rem; color: var(--text-subtle);">
            <span id="blueprintToggleText">Sembunyikan</span> diagram
          </span>
        </div>
        <div class="blueprint-content" id="blueprintContent">
          <div class="shaft-svg-wrapper">
            <svg viewBox="0 0 880 140" width="880" height="140" xmlns="http://www.w3.org/2000/svg" style="background: #f8fafc; border: 1px solid var(--border-subtle); border-radius: 4px;">
              <!-- Shaft Profile Sections -->
              <rect x="30" y="47" width="70" height="46" fill="#e2e8f0" stroke="#64748b" stroke-width="1.5" />
              <line x1="45" y1="47" x2="45" y2="93" stroke="#94a3b8" stroke-width="1" stroke-dasharray="2,2"/>
              <line x1="60" y1="47" x2="60" y2="93" stroke="#94a3b8" stroke-width="1" stroke-dasharray="2,2"/>
              <line x1="75" y1="47" x2="75" y2="93" stroke="#94a3b8" stroke-width="1" stroke-dasharray="2,2"/>
              <line x1="90" y1="47" x2="90" y2="93" stroke="#94a3b8" stroke-width="1" stroke-dasharray="2,2"/>

              <rect x="100" y="43" width="80" height="54" fill="#dcfce7" stroke="#16a34a" stroke-width="1.5" />
              <rect x="180" y="49" width="70" height="42" fill="#e2e8f0" stroke="#64748b" stroke-width="1.5" />
              <rect x="250" y="20" width="45" height="100" fill="#e2e8f0" stroke="#64748b" stroke-width="1.5" />
              <rect x="295" y="43" width="60" height="54" fill="#dcfce7" stroke="#16a34a" stroke-width="1.5" />
              <rect x="355" y="37" width="170" height="66" fill="#e2e8f0" stroke="#64748b" stroke-width="1.5" />
              <rect x="525" y="41" width="150" height="58" fill="#e2e8f0" stroke="#64748b" stroke-width="1.5" />

              <rect x="675" y="45" width="130" height="50" fill="#e2e8f0" stroke="#64748b" stroke-width="1.5" />
              <line x1="720" y1="45" x2="720" y2="95" stroke="#94a3b8" stroke-width="1" stroke-dasharray="2,2"/>
              <line x1="740" y1="45" x2="740" y2="95" stroke="#94a3b8" stroke-width="1" stroke-dasharray="2,2"/>
              <line x1="760" y1="45" x2="760" y2="95" stroke="#94a3b8" stroke-width="1" stroke-dasharray="2,2"/>
              <line x1="780" y1="45" x2="780" y2="95" stroke="#94a3b8" stroke-width="1" stroke-dasharray="2,2"/>

              <line x1="20" y1="70" x2="820" y2="70" stroke="#dc2626" stroke-width="1" stroke-dasharray="8,4,2,4"/>

              <!-- Labels -->
              <text x="65" y="35" fill="#334155" font-size="11" font-weight="600" text-anchor="middle">Item 01 (A)</text>
              <text x="65" y="112" fill="#64748b" font-size="10" font-family="monospace" text-anchor="middle">Ø79±0.25</text>

              <text x="140" y="30" fill="#15803d" font-size="11" font-weight="700" text-anchor="middle">Item 02 (B)*</text>
              <text x="140" y="112" fill="#15803d" font-size="10" font-family="monospace" text-anchor="middle">Ø82±0.25</text>

              <text x="215" y="35" fill="#334155" font-size="11" font-weight="600" text-anchor="middle">Item 02 (C)</text>
              <text x="215" y="112" fill="#64748b" font-size="10" font-family="monospace" text-anchor="middle">Ø77±0.25</text>

              <text x="272" y="14" fill="#334155" font-size="11" font-weight="600" text-anchor="middle">03 (D)</text>
              <text x="272" y="132" fill="#64748b" font-size="10" font-family="monospace" text-anchor="middle">Ø118±0.5</text>
              
              <text x="325" y="30" fill="#15803d" font-size="11" font-weight="700" text-anchor="middle">03 (E)*</text>
              <text x="325" y="112" fill="#15803d" font-size="10" font-family="monospace" text-anchor="middle">13±0.5</text>

              <text x="440" y="26" fill="#334155" font-size="11" font-weight="600" text-anchor="middle">Item 04 (Area F & K)</text>
              <text x="440" y="118" fill="#64748b" font-size="10" font-family="monospace" text-anchor="middle">Ø100.01 ± 0.008 | K: 188.55</text>

              <text x="600" y="28" fill="#334155" font-size="11" font-weight="600" text-anchor="middle">Item 05 (Area H & J)</text>
              <text x="600" y="116" fill="#64748b" font-size="10" font-family="monospace" text-anchor="middle">Ø94 ± 0.013 | J: 142.80</text>

              <text x="740" y="32" fill="#334155" font-size="11" font-weight="600" text-anchor="middle">Item 06 (Area G & Spline)</text>
              <text x="740" y="112" fill="#64748b" font-size="10" font-family="monospace" text-anchor="middle">Ø84.2 ± 0.13 | I: 98.78</text>

              <text x="830" y="74" fill="#15803d" font-size="9" font-weight="600">*Datum TIR: B & E</text>
            </svg>
          </div>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="modal-tabs">
        <button class="tab-btn active" data-tab="tab-general">
          1. Data Umum
        </button>
        <button class="tab-btn" data-tab="tab-sec1">
          2. Area A
          <span class="tab-status-dot" id="dot-sec1"></span>
        </button>
        <button class="tab-btn" data-tab="tab-sec2">
          3. Area B & C
          <span class="tab-status-dot" id="dot-sec2"></span>
        </button>
        <button class="tab-btn" data-tab="tab-sec34">
          4. Area D & F
          <span class="tab-status-dot" id="dot-sec34"></span>
        </button>
        <button class="tab-btn" data-tab="tab-sec56">
          5. Area H & G
          <span class="tab-status-dot" id="dot-sec56"></span>
        </button>
        <button class="tab-btn" data-tab="tab-tir">
          6. Runout (TIR)
          <span class="tab-status-dot" id="dot-tir"></span>
        </button>
        <button class="tab-btn" data-tab="tab-recommendations">
          7. Rekomendasi
        </button>
        <button class="tab-btn" data-tab="tab-signatures">
          8. Tanda Tangan
        </button>
      </div>

      <!-- Modal Body -->
      <form id="reportForm" class="modal-body">
        <input type="hidden" id="reportId">

        <!-- TAB 1: General Info -->
        <div class="tab-content active" id="tab-general">
          <div class="form-grid">
            <div class="form-group">
              <label>SSC ID <span style="color:var(--danger)">*</span></label>
              <input type="text" id="g_sscId" required placeholder="Contoh: SSC SBY-5778" class="mono">
            </div>
            <div class="form-group">
              <label>No. Laporan</label>
              <input type="text" id="g_reportNo" placeholder="Contoh: 0742" class="mono">
            </div>
            <div class="form-group">
              <label>Tanggal</label>
              <input type="date" id="g_date">
            </div>
            <div class="form-group">
              <label>Tipe Inspeksi</label>
              <select id="g_inspectionType">
                <option value="Pre">Pre-Inspection</option>
                <option value="Post">Post-Inspection</option>
              </select>
            </div>
            <div class="form-group span-2">
              <label>Customer</label>
              <input type="text" id="g_customer" placeholder="Nama Perusahaan Customer">
            </div>
            <div class="form-group">
              <label>Part Name</label>
              <input type="text" id="g_partName" value="OUTPUT SHAFT">
            </div>
            <div class="form-group">
              <label>Model Unit</label>
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
              <label>No. Dokumen</label>
              <input type="text" id="g_docNo" value="FORM-QC-002" readonly style="background: #f9fafb;">
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
            
            <table class="meas-table">
              <thead>
                <tr>
                  <th style="width: 70px;">Item</th>
                  <th>Spec</th>
                  <th>X</th>
                  <th>Y</th>
                  <th>Z</th>
                  <th style="width: 100px;">A / R</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>A</strong></td>
                  <td class="mono">Ø79 ± 0.25</td>
                  <td><input type="number" step="0.001" id="s1_x" data-spec-nominal="79.0" data-spec-tol="0.25" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s1_y" data-spec-nominal="79.0" data-spec-tol="0.25" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s1_z" data-spec-nominal="79.0" data-spec-tol="0.25" class="auto-eval"></td>
                  <td>
                    <select id="s1_ar">
                      <option value="A">A (Accept)</option>
                      <option value="R">R (Reject)</option>
                    </select>
                  </td>
                </tr>
              </tbody>
            </table>

            <div style="margin-top: 0.75rem;">
              <table class="meas-table">
                <thead>
                  <tr>
                    <th style="width: 70px;">Item</th>
                    <th>Spec</th>
                    <th>Hasil Pengukuran Panjang (L)</th>
                    <th style="width: 100px;">A / R</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><strong>L</strong></td>
                    <td class="mono">N/A (724.0)</td>
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

            <div style="margin-top: 1rem;">
              <label style="font-size: 0.76rem; font-weight: 600; color: var(--text-muted);">Remark & Temuan Visual:</label>
              <div style="display: grid; gap: 0.4rem; margin-top: 0.3rem;">
                <input type="text" id="s1_rem1" placeholder="Remark 1 (misal: Actual measurement OD area 'A' in good condition)">
                <input type="text" id="s1_rem2" placeholder="Remark 2">
                <input type="text" id="s1_rem3" placeholder="Remark 3">
                <input type="text" id="s1_rem4" placeholder="Remark 4">
                <input type="text" id="s1_rem5" placeholder="Remark 5">
                <input type="text" id="s1_rem6" placeholder="Remark 6">
              </div>
              <div class="quick-remark-tags">
                <span class="quick-tag" onclick="appendRemark('s1_rem1', 'Actual measurement OD area \'A\' in good condition')">+ Good condition</span>
                <span class="quick-tag" onclick="appendRemark('s1_rem2', 'Visual minor scratch at OD area \'A\'')">+ Minor scratch</span>
              </div>
            </div>

            <div style="margin-top: 1rem;">
              <label style="font-size: 0.76rem; font-weight: 600; color: var(--text-muted);">Hasil Penetrant Check:</label>
              <input type="text" id="s1_penetrant" style="width: 100%; margin-top: 0.3rem;" placeholder="Hasil penetrant check">
              <div class="quick-remark-tags">
                <span class="quick-tag" onclick="appendRemark('s1_penetrant', 'No indication crack at OD area \'A\' and OD spline after penetrant check')">+ No indication crack</span>
              </div>
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
                <span class="spec-pill" style="margin-left: 4px;">Item C: Ø77 ± 0.25</span>
              </div>
            </h3>

            <p style="font-size: 0.76rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.35rem;">Item B (Datum: Ø82 ± 0.25):</p>
            <table class="meas-table">
              <thead>
                <tr>
                  <th style="width: 80px;">Posisi</th>
                  <th>X</th>
                  <th>Y</th>
                  <th>Z</th>
                  <th style="width: 100px;">A / R</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Posisi 1</td>
                  <td><input type="number" step="0.001" id="s2_bx1" data-spec-nominal="82.0" data-spec-tol="0.25" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s2_by1" data-spec-nominal="82.0" data-spec-tol="0.25" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s2_bz1" data-spec-nominal="82.0" data-spec-tol="0.25" class="auto-eval"></td>
                  <td><select id="s2_bar1"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td>Posisi 2</td>
                  <td><input type="number" step="0.001" id="s2_bx2" data-spec-nominal="82.0" data-spec-tol="0.25" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s2_by2" data-spec-nominal="82.0" data-spec-tol="0.25" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s2_bz2" data-spec-nominal="82.0" data-spec-tol="0.25" class="auto-eval"></td>
                  <td><select id="s2_bar2"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td>Posisi 3</td>
                  <td><input type="number" step="0.001" id="s2_bx3" data-spec-nominal="82.0" data-spec-tol="0.25" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s2_by3" data-spec-nominal="82.0" data-spec-tol="0.25" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s2_bz3" data-spec-nominal="82.0" data-spec-tol="0.25" class="auto-eval"></td>
                  <td><select id="s2_bar3"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
              </tbody>
            </table>

            <p style="font-size: 0.76rem; font-weight: 600; color: var(--text-muted); margin: 0.75rem 0 0.35rem 0;">Item C (Ø77 ± 0.25):</p>
            <table class="meas-table">
              <thead>
                <tr>
                  <th style="width: 80px;">Posisi</th>
                  <th>X</th>
                  <th>Y</th>
                  <th>Z</th>
                  <th style="width: 100px;">A / R</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Posisi 1</td>
                  <td><input type="number" step="0.001" id="s2_cx1" data-spec-nominal="77.0" data-spec-tol="0.25" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s2_cy1" data-spec-nominal="77.0" data-spec-tol="0.25" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s2_cz1" data-spec-nominal="77.0" data-spec-tol="0.25" class="auto-eval"></td>
                  <td><select id="s2_car1"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td>Posisi 2</td>
                  <td><input type="number" step="0.001" id="s2_cx2" data-spec-nominal="77.0" data-spec-tol="0.25" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s2_cy2" data-spec-nominal="77.0" data-spec-tol="0.25" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s2_cz2" data-spec-nominal="77.0" data-spec-tol="0.25" class="auto-eval"></td>
                  <td><select id="s2_car2"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td>Posisi 3</td>
                  <td><input type="number" step="0.001" id="s2_cx3" data-spec-nominal="77.0" data-spec-tol="0.25" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s2_cy3" data-spec-nominal="77.0" data-spec-tol="0.25" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s2_cz3" data-spec-nominal="77.0" data-spec-tol="0.25" class="auto-eval"></td>
                  <td><select id="s2_car3"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
              </tbody>
            </table>

            <div style="margin-top: 1rem;">
              <label style="font-size: 0.76rem; font-weight: 600; color: var(--text-muted);">Remark Area B & C:</label>
              <div style="display: grid; gap: 0.4rem; margin-top: 0.3rem;">
                <input type="text" id="s2_rem1" placeholder="Remark 1">
                <input type="text" id="s2_rem2" placeholder="Remark 2">
                <input type="text" id="s2_rem3" placeholder="Remark 3">
                <input type="text" id="s2_rem4" placeholder="Remark 4">
              </div>
            </div>

            <div style="margin-top: 0.75rem;">
              <label style="font-size: 0.76rem; font-weight: 600; color: var(--text-muted);">Penetrant Check Area B & C:</label>
              <input type="text" id="s2_penetrant" style="width: 100%; margin-top: 0.3rem;" placeholder="Hasil penetrant check Area B & C">
            </div>
          </div>
        </div>

        <!-- TAB 4: Items 03 & 04 -->
        <div class="tab-content" id="tab-sec34">
          <div class="section-box">
            <h3>
              <span>03. OD Area "D" & Distance E</span>
              <div>
                <span class="spec-pill">D: Ø118 ± 0.5</span>
                <span class="spec-pill" style="margin-left: 4px;">E: 13 ± 0.5</span>
              </div>
            </h3>
            <table class="meas-table">
              <thead>
                <tr>
                  <th style="width: 100px;">Item</th>
                  <th>Spec</th>
                  <th>X</th>
                  <th>Y</th>
                  <th>Z</th>
                  <th style="width: 100px;">A / R</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>D</strong></td>
                  <td class="mono">Ø118 ± 0.5</td>
                  <td><input type="number" step="0.001" id="s3_dx" data-spec-nominal="118.0" data-spec-tol="0.5" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s3_dy" data-spec-nominal="118.0" data-spec-tol="0.5" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s3_dz" data-spec-nominal="118.0" data-spec-tol="0.5" class="auto-eval"></td>
                  <td><select id="s3_dar"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td><strong>E</strong> (Distance)</td>
                  <td class="mono">13 ± 0.5</td>
                  <td><input type="number" step="0.001" id="s3_ex" data-spec-nominal="13.0" data-spec-tol="0.5" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s3_ey" data-spec-nominal="13.0" data-spec-tol="0.5" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s3_ez" data-spec-nominal="13.0" data-spec-tol="0.5" class="auto-eval"></td>
                  <td><select id="s3_ear"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
              </tbody>
            </table>

            <div style="margin-top: 0.75rem; display: grid; gap: 0.4rem;">
              <input type="text" id="s3_rem1" placeholder="Remark 1">
              <input type="text" id="s3_rem2" placeholder="Remark 2">
              <input type="text" id="s3_penetrant" placeholder="Hasil penetrant check Area D">
            </div>
          </div>

          <div class="section-box">
            <h3>
              <span>04. OD Area "F" & Distance K</span>
              <div>
                <span class="spec-pill">F: Ø100.01 ± 0.008</span>
                <span class="spec-pill" style="margin-left: 4px;">K: 188.55</span>
              </div>
            </h3>
            <table class="meas-table">
              <thead>
                <tr>
                  <th style="width: 80px;">Posisi</th>
                  <th>X</th>
                  <th>Y</th>
                  <th>Z</th>
                  <th style="width: 100px;">A / R</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Posisi 1</td>
                  <td><input type="number" step="0.001" id="s4_fx1" data-spec-nominal="100.01" data-spec-tol="0.008" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s4_fy1" data-spec-nominal="100.01" data-spec-tol="0.008" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s4_fz1" data-spec-nominal="100.01" data-spec-tol="0.008" class="auto-eval"></td>
                  <td><select id="s4_far1"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td>Posisi 2</td>
                  <td><input type="number" step="0.001" id="s4_fx2" data-spec-nominal="100.01" data-spec-tol="0.008" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s4_fy2" data-spec-nominal="100.01" data-spec-tol="0.008" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s4_fz2" data-spec-nominal="100.01" data-spec-tol="0.008" class="auto-eval"></td>
                  <td><select id="s4_far2"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td>Posisi 3</td>
                  <td><input type="number" step="0.001" id="s4_fx3" data-spec-nominal="100.01" data-spec-tol="0.008" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s4_fy3" data-spec-nominal="100.01" data-spec-tol="0.008" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s4_fz3" data-spec-nominal="100.01" data-spec-tol="0.008" class="auto-eval"></td>
                  <td><select id="s4_far3"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
              </tbody>
            </table>

            <div style="margin-top: 0.5rem; display: flex; gap: 0.75rem; align-items: center;">
              <span style="font-size: 0.76rem; font-weight: 600; color: var(--text-muted);">Distance K:</span>
              <input type="number" step="0.01" id="s4_kResult" style="width: 120px;" placeholder="188.55">
              <select id="s4_kAr"><option value="-">-</option><option value="A">A</option><option value="R">R</option></select>
            </div>

            <div style="margin-top: 0.75rem; display: grid; gap: 0.4rem;">
              <input type="text" id="s4_rem1" placeholder="Remark 1">
              <input type="text" id="s4_rem2" placeholder="Remark 2">
              <input type="text" id="s4_penetrant" placeholder="Hasil penetrant check Area F">
            </div>
          </div>
        </div>

        <!-- TAB 5: Items 05 & 06 -->
        <div class="tab-content" id="tab-sec56">
          <div class="section-box">
            <h3>
              <span>05. OD Area "H" & Distance J</span>
              <div>
                <span class="spec-pill">H: Ø94 ± 0.013</span>
                <span class="spec-pill" style="margin-left: 4px;">J: 142.80</span>
              </div>
            </h3>
            <table class="meas-table">
              <thead>
                <tr>
                  <th style="width: 80px;">Posisi</th>
                  <th>X</th>
                  <th>Y</th>
                  <th>Z</th>
                  <th style="width: 100px;">A / R</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Posisi 1</td>
                  <td><input type="number" step="0.001" id="s5_hx1" data-spec-nominal="94.0" data-spec-tol="0.013" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s5_hy1" data-spec-nominal="94.0" data-spec-tol="0.013" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s5_hz1" data-spec-nominal="94.0" data-spec-tol="0.013" class="auto-eval"></td>
                  <td><select id="s5_har1"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td>Posisi 2</td>
                  <td><input type="number" step="0.001" id="s5_hx2" data-spec-nominal="94.0" data-spec-tol="0.013" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s5_hy2" data-spec-nominal="94.0" data-spec-tol="0.013" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s5_hz2" data-spec-nominal="94.0" data-spec-tol="0.013" class="auto-eval"></td>
                  <td><select id="s5_har2"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td>Posisi 3</td>
                  <td><input type="number" step="0.001" id="s5_hx3" data-spec-nominal="94.0" data-spec-tol="0.013" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s5_hy3" data-spec-nominal="94.0" data-spec-tol="0.013" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s5_hz3" data-spec-nominal="94.0" data-spec-tol="0.013" class="auto-eval"></td>
                  <td><select id="s5_har3"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
              </tbody>
            </table>

            <div style="margin-top: 0.5rem; display: flex; gap: 0.75rem; align-items: center;">
              <span style="font-size: 0.76rem; font-weight: 600; color: var(--text-muted);">Distance J:</span>
              <input type="number" step="0.01" id="s5_jResult" style="width: 120px;" placeholder="142.80">
              <select id="s5_jAr"><option value="-">-</option><option value="A">A</option><option value="R">R</option></select>
            </div>

            <div style="margin-top: 0.75rem; display: grid; gap: 0.4rem;">
              <input type="text" id="s5_rem1" placeholder="Remark 1">
              <input type="text" id="s5_penetrant" placeholder="Hasil penetrant check Area H">
            </div>
          </div>

          <div class="section-box">
            <h3>
              <span>06. OD Area "G", OD Spline & Distance I</span>
              <div>
                <span class="spec-pill">G: Ø84.2 ± 0.13</span>
                <span class="spec-pill" style="margin-left: 4px;">I: 98.78</span>
              </div>
            </h3>
            <table class="meas-table">
              <thead>
                <tr>
                  <th style="width: 80px;">Item</th>
                  <th>Spec</th>
                  <th>X</th>
                  <th>Y</th>
                  <th>Z</th>
                  <th style="width: 100px;">A / R</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>G</strong></td>
                  <td class="mono">Ø84.2 ± 0.13</td>
                  <td><input type="number" step="0.001" id="s6_gx" data-spec-nominal="84.2" data-spec-tol="0.13" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s6_gy" data-spec-nominal="84.2" data-spec-tol="0.13" class="auto-eval"></td>
                  <td><input type="number" step="0.001" id="s6_gz" data-spec-nominal="84.2" data-spec-tol="0.13" class="auto-eval"></td>
                  <td><select id="s6_gar"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
              </tbody>
            </table>

            <div style="margin-top: 0.5rem; display: flex; gap: 0.75rem; align-items: center;">
              <span style="font-size: 0.76rem; font-weight: 600; color: var(--text-muted);">Distance I:</span>
              <input type="number" step="0.01" id="s6_iResult" style="width: 120px;" placeholder="98.78">
              <select id="s6_iAr"><option value="-">-</option><option value="A">A</option><option value="R">R</option></select>
            </div>

            <div style="margin-top: 0.75rem; display: grid; gap: 0.4rem;">
              <input type="text" id="s6_rem1" placeholder="Remark 1">
              <input type="text" id="s6_penetrant" placeholder="Hasil penetrant check Area G">
            </div>
          </div>
        </div>

        <!-- TAB 6: Check TIR -->
        <div class="tab-content" id="tab-tir">
          <div class="section-box">
            <h3>
              <span>Runout (Total Indicator Reading - TIR)</span>
              <span class="spec-pill">Max: 0.20 mm</span>
            </h3>

            <div style="margin-bottom: 0.75rem;">
              <label style="font-size: 0.76rem; font-weight: 600; color: var(--text-muted);">Datum Referensi:</label>
              <input type="text" id="tir_datum" style="width: 100%; margin-top: 0.25rem;" value="Datum at item &quot;B and E&quot;">
            </div>

            <table class="meas-table">
              <thead>
                <tr>
                  <th style="width: 60px;">Titik</th>
                  <th>Spec</th>
                  <th>0°</th>
                  <th>90°</th>
                  <th>180°</th>
                  <th>270°</th>
                  <th style="width: 100px;">A / R</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>A</strong></td>
                  <td class="mono">Max 0,2</td>
                  <td><input type="text" id="tir_a_0" class="tir-eval" placeholder="0"></td>
                  <td><input type="text" id="tir_a_90" class="tir-eval" placeholder="0"></td>
                  <td><input type="text" id="tir_a_180" class="tir-eval" placeholder="0"></td>
                  <td><input type="text" id="tir_a_270" class="tir-eval" placeholder="0"></td>
                  <td><select id="tir_a_ar"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td><strong>B</strong></td>
                  <td class="mono">Max 0,2</td>
                  <td><input type="text" id="tir_b_0" class="tir-eval" placeholder="0"></td>
                  <td><input type="text" id="tir_b_90" class="tir-eval" placeholder="0"></td>
                  <td><input type="text" id="tir_b_180" class="tir-eval" placeholder="0"></td>
                  <td><input type="text" id="tir_b_270" class="tir-eval" placeholder="0"></td>
                  <td><select id="tir_b_ar"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td><strong>C</strong></td>
                  <td class="mono">Max 0,2</td>
                  <td><input type="text" id="tir_c_0" class="tir-eval" placeholder="0"></td>
                  <td><input type="text" id="tir_c_90" class="tir-eval" placeholder="0"></td>
                  <td><input type="text" id="tir_c_180" class="tir-eval" placeholder="0"></td>
                  <td><input type="text" id="tir_c_270" class="tir-eval" placeholder="0"></td>
                  <td><select id="tir_c_ar"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td><strong>D</strong></td>
                  <td class="mono">Max 0,2</td>
                  <td><input type="text" id="tir_d_0" class="tir-eval" placeholder="0"></td>
                  <td><input type="text" id="tir_d_90" class="tir-eval" placeholder="0"></td>
                  <td><input type="text" id="tir_d_180" class="tir-eval" placeholder="0"></td>
                  <td><input type="text" id="tir_d_270" class="tir-eval" placeholder="0"></td>
                  <td><select id="tir_d_ar"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td><strong>E</strong></td>
                  <td class="mono">Max 0,2</td>
                  <td><input type="text" id="tir_e_0" class="tir-eval" placeholder="0"></td>
                  <td><input type="text" id="tir_e_90" class="tir-eval" placeholder="0"></td>
                  <td><input type="text" id="tir_e_180" class="tir-eval" placeholder="0"></td>
                  <td><input type="text" id="tir_e_270" class="tir-eval" placeholder="0"></td>
                  <td><select id="tir_e_ar"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td><strong>F</strong></td>
                  <td class="mono">Max 0,2</td>
                  <td><input type="text" id="tir_f_0" class="tir-eval" placeholder="0"></td>
                  <td><input type="text" id="tir_f_90" class="tir-eval" placeholder="0"></td>
                  <td><input type="text" id="tir_f_180" class="tir-eval" placeholder="0"></td>
                  <td><input type="text" id="tir_f_270" class="tir-eval" placeholder="0"></td>
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
              <h3 style="margin: 0;">Temuan & Rekomendasi</h3>
              <button type="button" id="btnAddRecRow" class="btn btn-secondary btn-sm">
                <i data-lucide="plus"></i> Tambah Baris
              </button>
            </div>
            
            <table class="dyn-table">
              <thead>
                <tr>
                  <th style="width: 70px;">Item</th>
                  <th>Remarks (Temuan Visual / Kondisi)</th>
                  <th>Rekomendasi Tindakan</th>
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
            <div class="section-box">
              <h3>Checked by:</h3>
              <div class="form-group" style="margin-bottom: 0.4rem;">
                <label>Nama</label>
                <input type="text" id="sig_checkedName" value="Faqih S A A">
              </div>
              <div class="form-group" style="margin-bottom: 0.4rem;">
                <label>Jabatan</label>
                <input type="text" id="sig_checkedTitle" value="QC Inspector">
              </div>
              <div class="form-group">
                <label>Tanggal</label>
                <input type="date" id="sig_checkedDate">
              </div>
            </div>

            <div class="section-box">
              <h3>Created by:</h3>
              <div class="form-group" style="margin-bottom: 0.4rem;">
                <label>Nama</label>
                <input type="text" id="sig_createdName" value="M Kevin S">
              </div>
              <div class="form-group" style="margin-bottom: 0.4rem;">
                <label>Jabatan</label>
                <input type="text" id="sig_createdTitle" value="QC Inspector">
              </div>
              <div class="form-group">
                <label>Tanggal</label>
                <input type="date" id="sig_createdDate">
              </div>
            </div>

            <div class="section-box">
              <h3>Reviewed by:</h3>
              <div class="form-group" style="margin-bottom: 0.4rem;">
                <label>Nama</label>
                <input type="text" id="sig_reviewedName" value="Agus Riyanto">
              </div>
              <div class="form-group" style="margin-bottom: 0.4rem;">
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
          <button type="button" class="btn btn-secondary" id="btnSaveOnly">
            Simpan Data
          </button>
          <button type="button" class="btn btn-primary" id="btnSubmitAndDownload">
            <i data-lucide="download"></i> Simpan & Unduh Excel
          </button>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script src="{{ asset('js/app.js') }}"></script>
@endsection
