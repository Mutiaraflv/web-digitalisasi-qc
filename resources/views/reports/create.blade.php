@extends('layouts.app')

@section('title', isset($isEdit) && $isEdit ? 'Edit Laporan QC' : 'Input Inspeksi Baru')
@section('page_title', isset($isEdit) && $isEdit ? 'Edit Laporan QC' : 'Input Inspeksi Baru')

@section('top_actions')
  <div style="display: flex; gap: 0.5rem; align-items: center;">
    <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm">
      <i data-lucide="arrow-left"></i> Kembali ke Riwayat
    </a>
    <button type="button" id="btnTopSave" class="btn btn-secondary btn-sm">
      <i data-lucide="save"></i> Simpan Data
    </button>
    <button type="button" id="btnTopExportExcel" class="btn btn-primary btn-sm">
      <i data-lucide="file-spreadsheet"></i> Unduh Excel (.xlsx)
    </button>
  </div>
@endsection

@section('content')
<div class="workspace-container">
  
  <!-- Component Selector Bar -->
  <div class="component-selector-bar">
    <div class="selector-info">
      <label for="componentSelect"><i data-lucide="layers"></i> Pilih Komponen Inspeksi QC:</label>
      <div class="selector-wrapper">
        <select id="componentSelect" class="form-select-lg">
          <option value="output_shaft" selected>
            Output Shaft (Poros Output &mdash; Aktif & Siap Digunakan)
          </option>
          <option value="hydraulic_cylinder" disabled>
            Hydraulic Cylinder Rod (Menunggu Template Divisi Terkait)
          </option>
          <option value="drive_pinion" disabled>
            Drive Pinion Gear (Menunggu Template Divisi Terkait)
          </option>
          <option value="bearing_journal" disabled>
            Bearing Journal Sleeve (Menunggu Template Divisi Terkait)
          </option>
          <option value="connecting_rod" disabled>
            Connecting Rod (Menunggu Template Divisi Terkait)
          </option>
        </select>
        <span class="badge badge-green" style="font-size: 0.75rem; padding: 0.35rem 0.6rem;">Aktif</span>
      </div>
    </div>
    <div class="selector-meta">
      <span class="meta-item"><i data-lucide="file-text"></i> No. Dok: <strong id="top_badge_docNo">{{ $report->doc_no ?? 'FORM-QC-002' }}</strong></span>
      <span class="meta-item"><i data-lucide="git-branch"></i> Rev: <strong id="top_badge_rev">{{ $report->rev ?? '04' }}</strong></span>
      <span class="meta-item"><i data-lucide="layout"></i> Live Preview: <strong style="color: var(--success);">Aktif</strong></span>
    </div>
  </div>

  <div class="notice-box">
    <i data-lucide="info" style="color: var(--primary); flex-shrink: 0;"></i>
    <span>
      Formulir telah dikosongkan untuk input data aktual teknisi. Hasil ketikan dan unggahan foto pada panel kiri akan langsung ditampilkan secara <strong>real-time</strong> pada lembar preview dokumen di sebelah kanan sebelum diekspor ke Excel maupun PDF.
    </span>
  </div>

  <!-- 2-Column Split Workspace -->
  <div class="studio-grid">
    
    <!-- LEFT COLUMN: Form Inputs & Section Photo Uploaders -->
    <div class="studio-editor-pane">
      <div class="pane-card">
        
        <!-- Navigation Tabs -->
        <div class="studio-tabs">
          <button type="button" class="s-tab-btn active" data-target="stab-identitas">
            1. Identitas & Job
          </button>
          <button type="button" class="s-tab-btn" data-target="stab-dimensi">
            2. Nilai Dimensi
          </button>
          <button type="button" class="s-tab-btn" data-target="stab-foto">
            3. Upload Foto Bagian
            <span class="tab-photo-counter" id="photoCountBadge">0</span>
          </button>
          <button type="button" class="s-tab-btn" data-target="stab-tir">
            4. Runout (TIR)
          </button>
          <button type="button" class="s-tab-btn" data-target="stab-rekomendasi">
            5. Rekomendasi
          </button>
          <button type="button" class="s-tab-btn" data-target="stab-ttd">
            6. Pengesahan
          </button>
        </div>

        <form id="qcMainForm" class="studio-form">
          <input type="hidden" id="reportId" value="{{ isset($report) && $report ? $report->id : '' }}">

          <!-- TAB 1: IDENTITAS & JOB ORDER -->
          <div class="s-tab-content active" id="stab-identitas">

            <!-- Kontrol Dokumen Form Header (Input Manual) -->
            <div class="form-section-title">
              <i data-lucide="file-check"></i>
              <h4>1. Kontrol Dokumen Standar (Document Control)</h4>
            </div>
            <p style="font-size: 0.8rem; color: var(--text-subtle); margin-bottom: 0.75rem;">
              Parameter nomor formulir, revisi, dan penanggung jawab dapat disesuaikan manual sesuai ketentuan dokumen mutu perusahaan.
            </p>

            <div class="form-grid-2" style="background: #f8fafc; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); margin-bottom: 1.25rem;">
              <div class="form-group">
                <label>Document No (Nomor Dokumen)</label>
                <input type="text" id="g_docNo" class="mono-input" placeholder="FORM-QC-002" value="{{ $report->doc_no ?? 'FORM-QC-002' }}">
                <small class="helper-text">Contoh: FORM-QC-002, FORM-QC-005, dll.</small>
              </div>
              <div class="form-group">
                <label>Responsibility (Tanggung Jawab)</label>
                <input type="text" id="g_responsibility" placeholder="QC Inspector" value="{{ $report->responsibility ?? 'QC Inspector' }}">
              </div>
              <div class="form-group">
                <label>Effective Date. (Tanggal Berlaku Dokumen)</label>
                <input type="date" id="g_effectiveDate" value="{{ $report && $report->effective_date ? \Carbon\Carbon::parse($report->effective_date)->format('Y-m-d') : date('Y-m-d') }}">
              </div>
              <div class="form-group">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                  <div>
                    <label>Rev. (Revisi)</label>
                    <input type="text" id="g_rev" class="mono-input" placeholder="04" value="{{ $report->rev ?? '04' }}">
                  </div>
                  <div>
                    <label>Page (Halaman)</label>
                    <input type="text" id="g_page" class="mono-input" placeholder="1" value="{{ $report->page ?? '1' }}">
                  </div>
                </div>
              </div>
            </div>

            <div class="form-section-title">
              <i data-lucide="clipboard-list"></i>
              <h4>2. Identitas Komponen & Order Kerja</h4>
            </div>

            <div class="form-grid-2">
              <div class="form-group">
                <label>SSC ID <span class="required">*</span></label>
                <input type="text" id="g_sscId" required placeholder="Contoh: SSC SBY-5778" class="mono-input" value="{{ $report->ssc_id ?? '' }}">
                <small class="helper-text">Nomor identitas unik unit workshop</small>
              </div>
              <div class="form-group">
                <label>No. Laporan</label>
                <input type="text" id="g_reportNo" placeholder="Contoh: 0742" class="mono-input" value="{{ $report->report_no ?? '' }}">
              </div>
              <div class="form-group">
                <label>Tanggal Inspeksi</label>
                <input type="date" id="g_date" value="{{ $report && $report->date ? \Carbon\Carbon::parse($report->date)->format('Y-m-d') : date('Y-m-d') }}">
              </div>
              <div class="form-group">
                <label>Tipe Inspeksi</label>
                <select id="g_inspectionType">
                  <option value="Pre" {{ ($report->inspection_type ?? 'Pre') === 'Pre' ? 'selected' : '' }}>Pre-Inspection (Awal)</option>
                  <option value="Post" {{ ($report->inspection_type ?? '') === 'Post' ? 'selected' : '' }}>Post-Inspection (Akhir)</option>
                </select>
              </div>
              <div class="form-group full-width">
                <label>Customer / Perusahaan</label>
                <input type="text" id="g_customer" placeholder="Nama Perusahaan Pelanggan" value="{{ $report->customer ?? '' }}">
              </div>
              <div class="form-group">
                <label>Nama Komponen (Part Name)</label>
                <input type="text" id="g_partName" value="{{ $report->part_name ?? 'OUTPUT SHAFT' }}" placeholder="OUTPUT SHAFT">
              </div>
              <div class="form-group">
                <label>Model Unit</label>
                <input type="text" id="g_modelUnit" placeholder="Contoh: HT051" value="{{ $report->model_unit ?? 'HT051' }}">
              </div>
              <div class="form-group">
                <label>Job No.</label>
                <input type="text" id="g_jobNo" placeholder="Contoh: WO-2026-001" value="{{ $report->job_no ?? '-' }}">
              </div>
              <div class="form-group">
                <label>Quote No.</label>
                <input type="text" id="g_quoteNo" placeholder="-" value="{{ $report->quote_no ?? '-' }}">
              </div>
              <div class="form-group">
                <label>Part No.</label>
                <input type="text" id="g_partNo" placeholder="-" value="{{ $report->part_no ?? '-' }}">
              </div>
            </div>
          </div>

          <!-- TAB 2: NILAI DIMENSI -->
          <div class="s-tab-content" id="stab-dimensi">
            <div class="form-section-title">
              <i data-lucide="ruler"></i>
              <h4>Pengukuran Dimensi Poros (Berdasarkan Titik Ukur Standar)</h4>
            </div>

            <!-- Item 01 -->
            <div class="inspection-block">
              <div class="block-header">
                <div>
                  <strong>01. OD Area "A" and OD Spline</strong>
                  <span class="spec-tag">Spec: Ø79 ± 0.25</span>
                </div>
                <div class="status-selector">
                  <label>Status:</label>
                  <select id="i1_ar" class="status-select">
                    <option value="A">A (Accept)</option>
                    <option value="R">R (Reject)</option>
                  </select>
                </div>
              </div>
              <div class="input-triple-row">
                <div class="input-col">
                  <label>Nilai X (mm)</label>
                  <input type="text" id="i1_x" class="dim-input" placeholder="Isi mm...">
                </div>
                <div class="input-col">
                  <label>Nilai Y (mm)</label>
                  <input type="text" id="i1_y" class="dim-input" placeholder="Isi mm...">
                </div>
                <div class="input-col">
                  <label>Nilai Z (mm)</label>
                  <input type="text" id="i1_z" class="dim-input" placeholder="Isi mm...">
                </div>
              </div>
              <div class="input-sub-row">
                <div class="input-col-6">
                  <label>Panjang Total L (Spec: N/A)</label>
                  <input type="text" id="i1_length" class="dim-input" placeholder="Contoh: 724.0">
                </div>
                <div class="input-col-6">
                  <label>Catatan Visual Area A</label>
                  <input type="text" id="i1_remark1" placeholder="Actual measurement OD area 'A' in good condition">
                </div>
              </div>
            </div>

            <!-- Item 02 -->
            <div class="inspection-block">
              <div class="block-header">
                <div>
                  <strong>02. OD Area "B and C"</strong>
                  <span class="spec-tag">B: Ø82 ± 0.25 | C: Ø77 ± 0.25</span>
                </div>
              </div>

              <!-- Sub B -->
              <div class="sub-dimension-section">
                <div class="sub-dim-title">Area B (Titik 1, 2, 3) &mdash; Spec: Ø82 ± 0.25</div>
                <div class="table-compact-wrapper">
                  <table class="table-compact">
                    <thead>
                      <tr>
                        <th>Posisi</th>
                        <th>X (mm)</th>
                        <th>Y (mm)</th>
                        <th>Z (mm)</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td><strong>Baris 1</strong></td>
                        <td><input type="text" id="i2_b1_x" class="dim-input" placeholder="mm..."></td>
                        <td><input type="text" id="i2_b1_y" class="dim-input" placeholder="mm..."></td>
                        <td><input type="text" id="i2_b1_z" class="dim-input" placeholder="mm..."></td>
                        <td><select id="i2_b1_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                      </tr>
                      <tr>
                        <td><strong>Baris 2</strong></td>
                        <td><input type="text" id="i2_b2_x" class="dim-input" placeholder="mm..."></td>
                        <td><input type="text" id="i2_b2_y" class="dim-input" placeholder="mm..."></td>
                        <td><input type="text" id="i2_b2_z" class="dim-input" placeholder="mm..."></td>
                        <td><select id="i2_b2_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                      </tr>
                      <tr>
                        <td><strong>Baris 3</strong></td>
                        <td><input type="text" id="i2_b3_x" class="dim-input" placeholder="mm..."></td>
                        <td><input type="text" id="i2_b3_y" class="dim-input" placeholder="mm..."></td>
                        <td><input type="text" id="i2_b3_z" class="dim-input" placeholder="mm..."></td>
                        <td><select id="i2_b3_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Sub C -->
              <div class="sub-dimension-section" style="margin-top: 0.5rem;">
                <div class="sub-dim-title">Area C (Titik 1, 2, 3) &mdash; Spec: Ø77 ± 0.25</div>
                <div class="table-compact-wrapper">
                  <table class="table-compact">
                    <thead>
                      <tr>
                        <th>Posisi</th>
                        <th>X (mm)</th>
                        <th>Y (mm)</th>
                        <th>Z (mm)</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td><strong>Baris 1</strong></td>
                        <td><input type="text" id="i2_c1_x" class="dim-input" placeholder="mm..."></td>
                        <td><input type="text" id="i2_c1_y" class="dim-input" placeholder="mm..."></td>
                        <td><input type="text" id="i2_c1_z" class="dim-input" placeholder="mm..."></td>
                        <td><select id="i2_c1_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                      </tr>
                      <tr>
                        <td><strong>Baris 2</strong></td>
                        <td><input type="text" id="i2_c2_x" class="dim-input" placeholder="mm..."></td>
                        <td><input type="text" id="i2_c2_y" class="dim-input" placeholder="mm..."></td>
                        <td><input type="text" id="i2_c2_z" class="dim-input" placeholder="mm..."></td>
                        <td><select id="i2_c2_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                      </tr>
                      <tr>
                        <td><strong>Baris 3</strong></td>
                        <td><input type="text" id="i2_c3_x" class="dim-input" placeholder="mm..."></td>
                        <td><input type="text" id="i2_c3_y" class="dim-input" placeholder="mm..."></td>
                        <td><input type="text" id="i2_c3_z" class="dim-input" placeholder="mm..."></td>
                        <td><select id="i2_c3_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Item 03 -->
            <div class="inspection-block">
              <div class="block-header">
                <div>
                  <strong>03. OD Area "D" & Distance "E"</strong>
                  <span class="spec-tag">D: Ø118 ± 0.5 | E: 13 ± 0.5</span>
                </div>
              </div>
              <div class="form-grid-2">
                <div>
                  <label><strong>Area D (OD Ø118)</strong></label>
                  <div class="input-triple-row" style="margin-top: 0.35rem;">
                    <input type="text" id="i3_d_x" class="dim-input" placeholder="X mm">
                    <input type="text" id="i3_d_y" class="dim-input" placeholder="Y mm">
                    <input type="text" id="i3_d_z" class="dim-input" placeholder="Z mm">
                    <select id="i3_d_ar" class="status-select" style="width: 65px;"><option value="A">A</option><option value="R">R</option></select>
                  </div>
                </div>
                <div>
                  <label><strong>Jarak E (Distance 13)</strong></label>
                  <div class="input-triple-row" style="margin-top: 0.35rem;">
                    <input type="text" id="i3_e_x" class="dim-input" placeholder="X mm">
                    <input type="text" id="i3_e_y" class="dim-input" placeholder="Y mm">
                    <input type="text" id="i3_e_z" class="dim-input" placeholder="Z mm">
                    <select id="i3_e_ar" class="status-select" style="width: 65px;"><option value="A">A</option><option value="R">R</option></select>
                  </div>
                </div>
              </div>
            </div>

            <!-- Item 04 -->
            <div class="inspection-block">
              <div class="block-header">
                <div>
                  <strong>04. OD Area "F" & Jarak "K"</strong>
                  <span class="spec-tag">F: Ø100,01 ± 0,008 | K: 188.55</span>
                </div>
              </div>
              <table class="table-compact">
                <thead>
                  <tr>
                    <th>Posisi Area F</th>
                    <th>X (mm)</th>
                    <th>Y (mm)</th>
                    <th>Z (mm)</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Baris 1</td>
                    <td><input type="text" id="i4_f1_x" class="dim-input" placeholder="mm..."></td>
                    <td><input type="text" id="i4_f1_y" class="dim-input" placeholder="mm..."></td>
                    <td><input type="text" id="i4_f1_z" class="dim-input" placeholder="mm..."></td>
                    <td><select id="i4_f1_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                  </tr>
                  <tr>
                    <td>Baris 2</td>
                    <td><input type="text" id="i4_f2_x" class="dim-input" placeholder="mm..."></td>
                    <td><input type="text" id="i4_f2_y" class="dim-input" placeholder="mm..."></td>
                    <td><input type="text" id="i4_f2_z" class="dim-input" placeholder="mm..."></td>
                    <td><select id="i4_f2_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                  </tr>
                  <tr>
                    <td>Baris 3</td>
                    <td><input type="text" id="i4_f3_x" class="dim-input" placeholder="mm..."></td>
                    <td><input type="text" id="i4_f3_y" class="dim-input" placeholder="mm..."></td>
                    <td><input type="text" id="i4_f3_z" class="dim-input" placeholder="mm..."></td>
                    <td><select id="i4_f3_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                  </tr>
                </tbody>
              </table>
              <div class="form-group" style="margin-top: 0.5rem;">
                <label>Jarak K (Hasil Ukur mm):</label>
                <input type="text" id="i4_k_res" class="dim-input" placeholder="Contoh: 188.55">
              </div>
            </div>

            <!-- Item 05 -->
            <div class="inspection-block">
              <div class="block-header">
                <div>
                  <strong>05. OD Area "H" & Jarak "J"</strong>
                  <span class="spec-tag">H: Ø94 ± 0,013 | J: 142.80</span>
                </div>
              </div>
              <table class="table-compact">
                <thead>
                  <tr>
                    <th>Posisi Area H</th>
                    <th>X (mm)</th>
                    <th>Y (mm)</th>
                    <th>Z (mm)</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Baris 1</td>
                    <td><input type="text" id="i5_h1_x" class="dim-input" placeholder="mm..."></td>
                    <td><input type="text" id="i5_h1_y" class="dim-input" placeholder="mm..."></td>
                    <td><input type="text" id="i5_h1_z" class="dim-input" placeholder="mm..."></td>
                    <td><select id="i5_h1_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                  </tr>
                  <tr>
                    <td>Baris 2</td>
                    <td><input type="text" id="i5_h2_x" class="dim-input" placeholder="mm..."></td>
                    <td><input type="text" id="i5_h2_y" class="dim-input" placeholder="mm..."></td>
                    <td><input type="text" id="i5_h2_z" class="dim-input" placeholder="mm..."></td>
                    <td><select id="i5_h2_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                  </tr>
                  <tr>
                    <td>Baris 3</td>
                    <td><input type="text" id="i5_h3_x" class="dim-input" placeholder="mm..."></td>
                    <td><input type="text" id="i5_h3_y" class="dim-input" placeholder="mm..."></td>
                    <td><input type="text" id="i5_h3_z" class="dim-input" placeholder="mm..."></td>
                    <td><select id="i5_h3_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                  </tr>
                </tbody>
              </table>
              <div class="form-group" style="margin-top: 0.5rem;">
                <label>Jarak J (Hasil Ukur mm):</label>
                <input type="text" id="i5_j_res" class="dim-input" placeholder="Contoh: 142.80">
              </div>
            </div>

            <!-- Item 06 -->
            <div class="inspection-block">
              <div class="block-header">
                <div>
                  <strong>06. OD Area "G" & Spline</strong>
                  <span class="spec-tag">Spec: Ø84,2 ± 0,13 | Jarak I: 98.78</span>
                </div>
                <div class="status-selector">
                  <label>Status:</label>
                  <select id="i6_ar" class="status-select">
                    <option value="A">A (Accept)</option>
                    <option value="R">R (Reject)</option>
                  </select>
                </div>
              </div>
              <div class="input-triple-row">
                <div class="input-col">
                  <label>Nilai X (mm)</label>
                  <input type="text" id="i6_x" class="dim-input" placeholder="Isi mm...">
                </div>
                <div class="input-col">
                  <label>Nilai Y (mm)</label>
                  <input type="text" id="i6_y" class="dim-input" placeholder="Isi mm...">
                </div>
                <div class="input-col">
                  <label>Nilai Z (mm)</label>
                  <input type="text" id="i6_z" class="dim-input" placeholder="Isi mm...">
                </div>
              </div>
              <div class="form-group" style="margin-top: 0.5rem;">
                <label>Jarak I (Hasil Ukur mm):</label>
                <input type="text" id="i6_i_res" class="dim-input" placeholder="Contoh: 98.78">
              </div>
            </div>

          </div>

          <!-- TAB 3: UPLOAD FOTO PER BAGIAN -->
          <div class="s-tab-content" id="stab-foto">
            <div class="form-section-title">
              <i data-lucide="camera"></i>
              <h4>Unggah Foto Dokumentasi Pemeriksaan Tiap Bagian</h4>
            </div>
            <p style="font-size: 0.85rem; color: var(--text-subtle); margin-bottom: 1rem;">
              Foto yang diunggah akan otomatis disinkronkan dan ditampilkan pada kotak dokumentasi lembar QC preview di sebelah kanan. Format: JPG, PNG, WEBP.
            </p>

            <div class="photo-upload-grid">
              
              <!-- Foto 0: Full Component Shaft -->
              <div class="photo-card" id="card_photo_full_component" style="grid-column: 1 / -1;">
                <div class="photo-card-header">
                  <strong>Foto Komponen Utuh (Full Image Received Component)</strong>
                  <span class="photo-tag" style="background:#e0f2fe; color:#0369a1;">Halaman 1 &mdash; Finding</span>
                </div>
                <div class="photo-dropzone" onclick="triggerFileInput('input_photo_full_component')" style="height: 100px;">
                  <input type="file" id="input_photo_full_component" accept="image/*" style="display:none;" onchange="handlePhotoSelect(this, 'full_component')">
                  <div class="dropzone-content" id="view_photo_full_component">
                    <i data-lucide="image-plus"></i>
                    <p>Klik atau geser foto keseluruhan fisik poros yang diterima (Output Shaft)</p>
                  </div>
                </div>
                <div class="photo-card-footer">
                  <button type="button" class="btn btn-secondary btn-xs btn-remove-photo" onclick="removePhoto('full_component')" style="display: none;" id="btn_rem_full_component">
                    <i data-lucide="trash-2"></i> Hapus Foto
                  </button>
                </div>
              </div>

              <!-- Foto 1: Area A -->
              <div class="photo-card" id="card_photo_item01">
                <div class="photo-card-header">
                  <strong>Foto 1: Area "A" & Spline</strong>
                  <span class="photo-tag">Bagian 01</span>
                </div>
                <div class="photo-dropzone" onclick="triggerFileInput('input_photo_item01')">
                  <input type="file" id="input_photo_item01" accept="image/*" style="display:none;" onchange="handlePhotoSelect(this, 'item01')">
                  <div class="dropzone-content" id="view_photo_item01">
                    <i data-lucide="image-plus"></i>
                    <p>Klik atau geser foto Area A</p>
                  </div>
                </div>
                <div class="photo-card-footer">
                  <button type="button" class="btn btn-secondary btn-xs btn-remove-photo" onclick="removePhoto('item01')" style="display: none;" id="btn_rem_item01">
                    <i data-lucide="trash-2"></i> Hapus Foto
                  </button>
                </div>
              </div>

              <!-- Foto 2: Area B & C -->
              <div class="photo-card" id="card_photo_item02">
                <div class="photo-card-header">
                  <strong>Foto 2: Area "B & C"</strong>
                  <span class="photo-tag">Bagian 02</span>
                </div>
                <div class="photo-dropzone" onclick="triggerFileInput('input_photo_item02')">
                  <input type="file" id="input_photo_item02" accept="image/*" style="display:none;" onchange="handlePhotoSelect(this, 'item02')">
                  <div class="dropzone-content" id="view_photo_item02">
                    <i data-lucide="image-plus"></i>
                    <p>Klik atau geser foto Area B & C</p>
                  </div>
                </div>
                <div class="photo-card-footer">
                  <button type="button" class="btn btn-secondary btn-xs btn-remove-photo" onclick="removePhoto('item02')" style="display: none;" id="btn_rem_item02">
                    <i data-lucide="trash-2"></i> Hapus Foto
                  </button>
                </div>
              </div>

              <!-- Foto 3: Area D & E -->
              <div class="photo-card" id="card_photo_item03">
                <div class="photo-card-header">
                  <strong>Foto 3: Area "D & E"</strong>
                  <span class="photo-tag">Bagian 03</span>
                </div>
                <div class="photo-dropzone" onclick="triggerFileInput('input_photo_item03')">
                  <input type="file" id="input_photo_item03" accept="image/*" style="display:none;" onchange="handlePhotoSelect(this, 'item03')">
                  <div class="dropzone-content" id="view_photo_item03">
                    <i data-lucide="image-plus"></i>
                    <p>Klik atau geser foto Area D & E</p>
                  </div>
                </div>
                <div class="photo-card-footer">
                  <button type="button" class="btn btn-secondary btn-xs btn-remove-photo" onclick="removePhoto('item03')" style="display: none;" id="btn_rem_item03">
                    <i data-lucide="trash-2"></i> Hapus Foto
                  </button>
                </div>
              </div>

              <!-- Foto 4: Area F -->
              <div class="photo-card" id="card_photo_item04">
                <div class="photo-card-header">
                  <strong>Foto 4: Area "F"</strong>
                  <span class="photo-tag">Bagian 04</span>
                </div>
                <div class="photo-dropzone" onclick="triggerFileInput('input_photo_item04')">
                  <input type="file" id="input_photo_item04" accept="image/*" style="display:none;" onchange="handlePhotoSelect(this, 'item04')">
                  <div class="dropzone-content" id="view_photo_item04">
                    <i data-lucide="image-plus"></i>
                    <p>Klik atau geser foto Area F</p>
                  </div>
                </div>
                <div class="photo-card-footer">
                  <button type="button" class="btn btn-secondary btn-xs btn-remove-photo" onclick="removePhoto('item04')" style="display: none;" id="btn_rem_item04">
                    <i data-lucide="trash-2"></i> Hapus Foto
                  </button>
                </div>
              </div>

              <!-- Foto 5: Area H -->
              <div class="photo-card" id="card_photo_item05">
                <div class="photo-card-header">
                  <strong>Foto 5: Area "H"</strong>
                  <span class="photo-tag">Bagian 05</span>
                </div>
                <div class="photo-dropzone" onclick="triggerFileInput('input_photo_item05')">
                  <input type="file" id="input_photo_item05" accept="image/*" style="display:none;" onchange="handlePhotoSelect(this, 'item05')">
                  <div class="dropzone-content" id="view_photo_item05">
                    <i data-lucide="image-plus"></i>
                    <p>Klik atau geser foto Area H</p>
                  </div>
                </div>
                <div class="photo-card-footer">
                  <button type="button" class="btn btn-secondary btn-xs btn-remove-photo" onclick="removePhoto('item05')" style="display: none;" id="btn_rem_item05">
                    <i data-lucide="trash-2"></i> Hapus Foto
                  </button>
                </div>
              </div>

              <!-- Foto 6: Area G & Spline -->
              <div class="photo-card" id="card_photo_item06">
                <div class="photo-card-header">
                  <strong>Foto 6: Area "G" & Spline</strong>
                  <span class="photo-tag">Bagian 06</span>
                </div>
                <div class="photo-dropzone" onclick="triggerFileInput('input_photo_item06')">
                  <input type="file" id="input_photo_item06" accept="image/*" style="display:none;" onchange="handlePhotoSelect(this, 'item06')">
                  <div class="dropzone-content" id="view_photo_item06">
                    <i data-lucide="image-plus"></i>
                    <p>Klik atau geser foto Area G & Spline</p>
                  </div>
                </div>
                <div class="photo-card-footer">
                  <button type="button" class="btn btn-secondary btn-xs btn-remove-photo" onclick="removePhoto('item06')" style="display: none;" id="btn_rem_item06">
                    <i data-lucide="trash-2"></i> Hapus Foto
                  </button>
                </div>
              </div>

              <!-- Foto 7: Liquid Penetrant Test -->
              <div class="photo-card" id="card_photo_penetrant">
                <div class="photo-card-header">
                  <strong>Foto 7: Uji Keretakan (Penetrant)</strong>
                  <span class="photo-tag" style="background:#fee2e2; color:#b91c1c;">NDT Test</span>
                </div>
                <div class="photo-dropzone" onclick="triggerFileInput('input_photo_penetrant')">
                  <input type="file" id="input_photo_penetrant" accept="image/*" style="display:none;" onchange="handlePhotoSelect(this, 'penetrant')">
                  <div class="dropzone-content" id="view_photo_penetrant">
                    <i data-lucide="image-plus"></i>
                    <p>Klik atau geser foto Hasil Penetrant Test</p>
                  </div>
                </div>
                <div class="photo-card-footer">
                  <button type="button" class="btn btn-secondary btn-xs btn-remove-photo" onclick="removePhoto('penetrant')" style="display: none;" id="btn_rem_penetrant">
                    <i data-lucide="trash-2"></i> Hapus Foto
                  </button>
                </div>
              </div>

              <!-- Foto 8: Dial Indicator / Cacat -->
              <div class="photo-card" id="card_photo_defect">
                <div class="photo-card-header">
                  <strong>Foto 8: Dial Indicator / Cacat Visual</strong>
                  <span class="photo-tag">Runout/Temuan</span>
                </div>
                <div class="photo-dropzone" onclick="triggerFileInput('input_photo_defect')">
                  <input type="file" id="input_photo_defect" accept="image/*" style="display:none;" onchange="handlePhotoSelect(this, 'defect')">
                  <div class="dropzone-content" id="view_photo_defect">
                    <i data-lucide="image-plus"></i>
                    <p>Klik atau geser foto Dial Gauge / Defect</p>
                  </div>
                </div>
                <div class="photo-card-footer">
                  <button type="button" class="btn btn-secondary btn-xs btn-remove-photo" onclick="removePhoto('defect')" style="display: none;" id="btn_rem_defect">
                    <i data-lucide="trash-2"></i> Hapus Foto
                  </button>
                </div>
              </div>

            </div>
          </div>

          <!-- TAB 4: RUNOUT / TIR -->
          <div class="s-tab-content" id="stab-tir">
            <div class="form-section-title">
              <i data-lucide="activity"></i>
              <h4>Total Indicator Reading (TIR / Runout Pengukuran Poros)</h4>
            </div>
            
            <div class="datum-banner">
              <span><strong>Datum Acuan:</strong> Datum at item "B and E"</span>
              <span class="spec-tag">Batas Maksimal: Max 0,2 mm</span>
            </div>

            <table class="table-compact">
              <thead>
                <tr>
                  <th style="width: 50px;">Titik</th>
                  <th>Batas Spec</th>
                  <th>0° (mm)</th>
                  <th>90° (mm)</th>
                  <th>180° (mm)</th>
                  <th>270° (mm)</th>
                  <th style="width: 70px;">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>A</strong></td>
                  <td class="mono">Max 0,2</td>
                  <td><input type="text" id="tir_a_0" class="tir-input" placeholder="0.00"></td>
                  <td><input type="text" id="tir_a_90" class="tir-input" placeholder="0.00"></td>
                  <td><input type="text" id="tir_a_180" class="tir-input" placeholder="0.00"></td>
                  <td><input type="text" id="tir_a_270" class="tir-input" placeholder="0.00"></td>
                  <td><select id="tir_a_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td><strong>B</strong></td>
                  <td class="mono">Max 0,2</td>
                  <td><input type="text" id="tir_b_0" class="tir-input" placeholder="0.00"></td>
                  <td><input type="text" id="tir_b_90" class="tir-input" placeholder="0.00"></td>
                  <td><input type="text" id="tir_b_180" class="tir-input" placeholder="0.00"></td>
                  <td><input type="text" id="tir_b_270" class="tir-input" placeholder="0.00"></td>
                  <td><select id="tir_b_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td><strong>C</strong></td>
                  <td class="mono">Max 0,2</td>
                  <td><input type="text" id="tir_c_0" class="tir-input" placeholder="0.00"></td>
                  <td><input type="text" id="tir_c_90" class="tir-input" placeholder="0.00"></td>
                  <td><input type="text" id="tir_c_180" class="tir-input" placeholder="0.00"></td>
                  <td><input type="text" id="tir_c_270" class="tir-input" placeholder="0.00"></td>
                  <td><select id="tir_c_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td><strong>D</strong></td>
                  <td class="mono">Max 0,2</td>
                  <td><input type="text" id="tir_d_0" class="tir-input" placeholder="0.00"></td>
                  <td><input type="text" id="tir_d_90" class="tir-input" placeholder="0.00"></td>
                  <td><input type="text" id="tir_d_180" class="tir-input" placeholder="0.00"></td>
                  <td><input type="text" id="tir_d_270" class="tir-input" placeholder="0.00"></td>
                  <td><select id="tir_d_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td><strong>E</strong></td>
                  <td class="mono">Max 0,2</td>
                  <td><input type="text" id="tir_e_0" class="tir-input" placeholder="0.00"></td>
                  <td><input type="text" id="tir_e_90" class="tir-input" placeholder="0.00"></td>
                  <td><input type="text" id="tir_e_180" class="tir-input" placeholder="0.00"></td>
                  <td><input type="text" id="tir_e_270" class="tir-input" placeholder="0.00"></td>
                  <td><select id="tir_e_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
                <tr>
                  <td><strong>F</strong></td>
                  <td class="mono">Max 0,2</td>
                  <td><input type="text" id="tir_f_0" class="tir-input" placeholder="0.00"></td>
                  <td><input type="text" id="tir_f_90" class="tir-input" placeholder="0.00"></td>
                  <td><input type="text" id="tir_f_180" class="tir-input" placeholder="0.00"></td>
                  <td><input type="text" id="tir_f_270" class="tir-input" placeholder="0.00"></td>
                  <td><select id="tir_f_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- TAB 5: REKOMENDASI -->
          <div class="s-tab-content" id="stab-rekomendasi">
            <div class="form-section-title" style="display: flex; justify-content: space-between; align-items: center;">
              <div>
                <i data-lucide="check-square"></i>
                <h4 style="display: inline; margin-left: 0.5rem;">Temuan & Rekomendasi Tindakan</h4>
              </div>
              <button type="button" class="btn btn-secondary btn-sm" id="btnAddRecRow">
                <i data-lucide="plus"></i> Tambah Baris
              </button>
            </div>

            <table class="table-compact" style="margin-top: 0.75rem;">
              <thead>
                <tr>
                  <th style="width: 60px;">Item</th>
                  <th>Remarks (Temuan Visual / Kondisi Bagian)</th>
                  <th>Rekomendasi Tindakan QC</th>
                  <th style="width: 45px;">Aksi</th>
                </tr>
              </thead>
              <tbody id="recTableBody">
                <!-- Dynamically filled -->
              </tbody>
            </table>
          </div>

          <!-- TAB 6: PENGESAHAN -->
          <div class="s-tab-content" id="stab-ttd">
            <div class="form-section-title">
              <i data-lucide="user-check"></i>
              <h4>Pengesahan & Personel Inspeksi</h4>
            </div>

            <div class="form-grid-3">
              <div class="sig-box">
                <h5>Checked by:</h5>
                <div class="form-group">
                  <label>Nama</label>
                  <input type="text" id="sig_checkedName" value="Faqih S A A">
                </div>
                <div class="form-group">
                  <label>Jabatan</label>
                  <input type="text" id="sig_checkedTitle" value="QC Inspector">
                </div>
                <div class="form-group">
                  <label>Tanggal</label>
                  <input type="date" id="sig_checkedDate">
                </div>
              </div>

              <div class="sig-box">
                <h5>Created by:</h5>
                <div class="form-group">
                  <label>Nama</label>
                  <input type="text" id="sig_createdName" value="M Kevin S">
                </div>
                <div class="form-group">
                  <label>Jabatan</label>
                  <input type="text" id="sig_createdTitle" value="QC Inspector">
                </div>
                <div class="form-group">
                  <label>Tanggal</label>
                  <input type="date" id="sig_createdDate">
                </div>
              </div>

              <div class="sig-box">
                <h5>Reviewed by:</h5>
                <div class="form-group">
                  <label>Nama</label>
                  <input type="text" id="sig_reviewedName" value="Agus Riyanto">
                </div>
                <div class="form-group">
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

        <!-- Studio Footer Actions -->
        <div class="studio-footer">
          <div class="footer-left">
            <button type="button" class="btn btn-secondary btn-sm" id="btnResetBlank">
              <i data-lucide="rotate-ccw"></i> Kosongkan Form
            </button>
          </div>
          <div class="footer-right">
            <button type="button" class="btn btn-secondary" id="btnBottomSave">
              <i data-lucide="save"></i> Simpan Data
            </button>
            <button type="button" class="btn btn-primary" id="btnBottomExportExcel">
              <i data-lucide="download"></i> Simpan & Unduh Excel
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- RIGHT COLUMN: Real-Time Official Document Preview (FORM-QC-002) -->
    <div class="studio-preview-pane">
      <div class="preview-toolbar">
        <div class="preview-title">
          <i data-lucide="eye"></i>
          <span>Live Document Preview &mdash; <strong>COMPONENT CONDITION REPORT (6 Lembar)</strong></span>
        </div>
        <div class="preview-actions">
          <button type="button" class="btn btn-secondary btn-xs" id="btnPrintPreview" title="Cetak / Unduh PDF">
            <i data-lucide="printer"></i> Cetak / PDF (6 Lembar)
          </button>
        </div>
      </div>

      <!-- Slide / Page Navigation Switcher -->
      <div class="preview-page-tabs">
        <button type="button" class="p-page-btn active" onclick="switchPreviewPage(1)">Lembar 1</button>
        <button type="button" class="p-page-btn" onclick="switchPreviewPage(2)">Lembar 2</button>
        <button type="button" class="p-page-btn" onclick="switchPreviewPage(3)">Lembar 3</button>
        <button type="button" class="p-page-btn" onclick="switchPreviewPage(4)">Lembar 4</button>
        <button type="button" class="p-page-btn" onclick="switchPreviewPage(5)">Lembar 5</button>
        <button type="button" class="p-page-btn" onclick="switchPreviewPage(6)">Lembar 6</button>
        <button type="button" class="p-page-btn" onclick="switchPreviewPage('all')">Lihat Semua (1-6)</button>
      </div>

      <!-- The Official 6-Sheet Replica Container -->
      <div class="sheet-container" id="printableSheet">

        <!-- ==============================================================
             LEMBAR 1 DARI 6 (PAGE 1)
        ============================================================== -->
        <div class="sheet-paper preview-sheet" id="prev_sheet_1">
          <div class="sheet-page-indicator">Halaman 1 dari 6</div>
          <!-- Header -->
          <table class="p-header-table">
            <tr>
              <td class="p-logo-cell">
                <img src="{{ asset('images/doc_assets/header_ssc_logo.png') }}" alt="PT. SSC WORKS">
              </td>
              <td class="p-title-cell">COMPONENT CONDITION REPORT</td>
              <td class="p-ctrl-cell">
                <table class="p-ctrl-table">
                  <tr>
                    <td><strong>Document No</strong></td><td>:</td><td><strong id="prev_docNo">FORM-QC-002</strong></td>
                    <td><strong>Responsibility</strong></td><td>:</td><td id="prev_responsibility">Quality Control</td>
                  </tr>
                  <tr>
                    <td><strong>Effective Date.</strong></td><td>:</td><td id="prev_effectiveDate">28-Jan-2022</td>
                    <td><strong>Rev.</strong></td><td>:</td><td><strong id="prev_rev">04</strong></td>
                  </tr>
                  <tr>
                    <td><strong>Page</strong></td><td>:</td><td><strong id="prev_page">1 &nbsp; of &nbsp; 6</strong></td>
                    <td></td><td></td><td></td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>

          <!-- Metadata Grid -->
          <table class="p-meta-table">
            <tr>
              <td class="lbl">No.</td><td class="col-c">:</td><td class="val mono" id="prev_reportNo">0742</td>
              <td class="lbl">Date</td><td class="col-c">:</td><td class="val" id="prev_metaDate">17-Oct-26</td>
              <td class="lbl">Customer</td><td class="col-c">:</td><td class="val" colspan="3" id="prev_customer">AMMAN MINERAL NUSA TENGGARA, PT</td>
              <td class="lbl">Part Name</td><td class="col-c">:</td><td class="val bold" id="prev_partName">OUTPUT SHAFT</td>
            </tr>
            <tr>
              <td class="lbl">Inspection</td><td class="col-c">:</td>
              <td class="val">
                <span id="prev_preCheck">&#9745; Pre</span> &nbsp;
                <span id="prev_postCheck">&#9744; Post</span>
              </td>
              <td class="lbl">Job No.</td><td class="col-c">:</td><td class="val" id="prev_jobNo">-</td>
              <td class="lbl">Model/Unit</td><td class="col-c">:</td><td class="val" colspan="5" id="prev_modelUnit">HT051</td>
            </tr>
            <tr>
              <td class="lbl">SSC ID</td><td class="col-c">:</td><td class="val mono bold" id="prev_sscId" style="color:#1e3a8a;">SSC SBY-5778</td>
              <td class="lbl">Quote No.</td><td class="col-c">:</td><td class="val" id="prev_quoteNo">-</td>
              <td class="lbl">Part No.</td><td class="col-c">:</td><td class="val" colspan="5" id="prev_partNo">-</td>
            </tr>
          </table>

          <!-- Drawing / Pictures / Dimension -->
          <div class="p-bar-grey">Drawing / Pictures / Dimension</div>
          <div class="p-blueprint-box">
            <img src="{{ asset('images/doc_assets/shaft_blueprint.png') }}" alt="Technical Drawing Shaft">
          </div>

          <!-- Finding Section -->
          <div class="p-bar-grey">FINDING</div>
          <table class="p-finding-table">
            <thead>
              <tr>
                <th style="width:35px;">NO</th>
                <th style="width:50%;">IMAGE</th>
                <th>DESCRIPTION</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="text-align:center; font-weight:700;">00</td>
                <td>
                  <div class="p-finding-img-wrap" id="prev_img_full">
                    <img src="{{ asset('images/doc_assets/p1_Image23.jpg') }}" alt="Full Component Shaft">
                  </div>
                </td>
                <td style="vertical-align:top; padding:4px;" id="prev_finding_desc">
                  <div><span style="font-weight:700;">&gt;</span> Full image received component OUTPUT SHAFT</div>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- OD Area A & Spline -->
          <div class="p-bar-yellow">OD Area "A" and OD Spline</div>
          <div class="p-split-section">
            <div class="p-split-left">
              <div class="p-col-header">Image of OD Spline</div>
              <div class="p-photo-grid-2x2">
                <div class="p-photo-slot" id="prev_img_item01"><img src="{{ asset('images/doc_assets/p1_Image24.jpg') }}" alt="Spline 1"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p1_Image25.jpg') }}" alt="Spline 2"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p1_Image26.jpg') }}" alt="Spline 3"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p1_Image27.jpg') }}" alt="Spline 4"></div>
              </div>
            </div>
            <div class="p-split-right">
              <div class="p-col-header">Actual visual &amp; measurement</div>
              <div class="p-sub-title">Actual Measurement</div>
              <table class="p-data-table">
                <thead><tr><th>Item</th><th>Spec</th><th>X</th><th>Y</th><th>Z</th><th>A/R</th></tr></thead>
                <tbody>
                  <tr>
                    <td style="font-weight:700;">A</td><td>Ø79 ± 0,25</td>
                    <td id="prev_i1_x">78,850</td><td id="prev_i1_y">78,865</td><td id="prev_i1_z">78,865</td>
                    <td class="cell-accept" id="prev_i1_ar">A</td>
                  </tr>
                </tbody>
              </table>
              <div class="p-bullet-box">
                <div><strong>&gt;</strong> Actual measurement OD area "A" in good condition</div>
                <div><strong>&gt;</strong> Visual scratch at OD area "A"</div>
                <div><strong>&gt;</strong> Visual corrosion area spline</div>
                <div><strong>&gt;</strong> Visual scratch at area surface</div>
                <div><strong>&gt;</strong> Visual dirty at hole thread area surface</div>
              </div>
              <div class="p-sub-title">Actual Length</div>
              <table class="p-data-table">
                <thead><tr><th>Item</th><th>Spec</th><th>Result of Measurement</th><th>A/R</th></tr></thead>
                <tbody>
                  <tr><td style="font-weight:700;">L</td><td>N/A</td><td id="prev_i1_len">724,320</td><td>-</td></tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="p-legend-footer">
            <div>A : <span class="badge-ar-a">Accept</span></div>
            <div>R : <span class="badge-ar-r">Reject</span></div>
          </div>
        </div>

        <!-- ==============================================================
             LEMBAR 2 DARI 6 (PAGE 2)
        ============================================================== -->
        <div class="sheet-paper preview-sheet" id="prev_sheet_2">
          <div class="sheet-page-indicator">Halaman 2 dari 6</div>
          <!-- Item 01 Penetrant -->
          <div class="p-split-section" style="border-top: 1px solid #000;">
            <div class="p-split-left">
              <div class="p-col-header">Image of Penetrant Check OD Area "A" and OD Spline</div>
              <div class="p-photo-grid-2x2">
                <div class="p-photo-slot" id="prev_img_penet01"><img src="{{ asset('images/doc_assets/p2_Image30.jpg') }}" alt="Penetrant A 1"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p2_Image31.jpg') }}" alt="Penetrant A 2"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p2_Image32.jpg') }}" alt="Penetrant A 3"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p2_Image33.jpg') }}" alt="Penetrant A 4"></div>
              </div>
            </div>
            <div class="p-split-right">
              <div class="p-col-header">Actual condition</div>
              <div class="p-bullet-box" style="padding-top:12px;">
                <div><strong>&gt;</strong> No indication crack at OD area "A" and OD spline after penetrant check</div>
              </div>
            </div>
          </div>
          <div class="p-legend-footer">
            <div>A : <span class="badge-ar-a">Accept</span></div>
            <div>R : <span class="badge-ar-r">Reject</span></div>
          </div>

          <!-- Item 02: B and C -->
          <div class="p-bar-yellow">OD Area "B and C"</div>
          <div class="p-split-section">
            <div class="p-split-left">
              <div class="p-col-header">Image of OD Area "B and C"</div>
              <div class="p-photo-grid-2x2">
                <div class="p-photo-slot" id="prev_img_item02"><img src="{{ asset('images/doc_assets/p2_Image34.jpg') }}" alt="BC 1"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p2_Image35.jpg') }}" alt="BC 2"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p2_Image36.jpg') }}" alt="BC 3"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p2_Image37.jpg') }}" alt="BC 4"></div>
              </div>
            </div>
            <div class="p-split-right">
              <div class="p-col-header">Actual visual &amp; measurement</div>
              <div class="p-sub-title">Actual Measurement</div>
              <table class="p-data-table">
                <thead><tr><th>Item</th><th>Spec</th><th>X</th><th>Y</th><th>Z</th><th>A/R</th></tr></thead>
                <tbody>
                  <tr>
                    <td rowspan="3" style="font-weight:700;">B</td><td rowspan="3">Ø82 ± 0,25</td>
                    <td id="prev_i2_b1_x">81,830</td><td id="prev_i2_b1_y">81,840</td><td id="prev_i2_b1_z">81,840</td><td class="cell-accept" id="prev_i2_b1_ar">A</td>
                  </tr>
                  <tr>
                    <td id="prev_i2_b2_x">81,840</td><td id="prev_i2_b2_y">81,850</td><td id="prev_i2_b2_z">81,850</td><td class="cell-accept" id="prev_i2_b2_ar">A</td>
                  </tr>
                  <tr>
                    <td id="prev_i2_b3_x">81,850</td><td id="prev_i2_b3_y">81,860</td><td id="prev_i2_b3_z">81,860</td><td class="cell-accept" id="prev_i2_b3_ar">A</td>
                  </tr>
                  <tr>
                    <td rowspan="3" style="font-weight:700;">C</td><td rowspan="3">Ø77 ± 0,25</td>
                    <td id="prev_i2_c1_x">76,850</td><td id="prev_i2_c1_y">76,860</td><td id="prev_i2_c1_z">76,865</td><td class="cell-accept" id="prev_i2_c1_ar">A</td>
                  </tr>
                  <tr>
                    <td id="prev_i2_c2_x">76,855</td><td id="prev_i2_c2_y">76,850</td><td id="prev_i2_c2_z">76,860</td><td class="cell-accept" id="prev_i2_c2_ar">A</td>
                  </tr>
                  <tr>
                    <td id="prev_i2_c3_x">76,860</td><td id="prev_i2_c3_y">76,870</td><td id="prev_i2_c3_z">76,860</td><td class="cell-accept" id="prev_i2_c3_ar">A</td>
                  </tr>
                </tbody>
              </table>
              <div class="p-bullet-box">
                <div><strong>&gt;</strong> Actual measurement OD area "B and C" in good condition</div>
                <div><strong>&gt;</strong> Visual corrosion at OD area "B"</div>
                <div><strong>&gt;</strong> Visual corrosion at OD area "C"</div>
              </div>
            </div>
          </div>
          <div class="p-legend-footer">
            <div>A : <span class="badge-ar-a">Accept</span></div>
            <div>R : <span class="badge-ar-r">Reject</span></div>
          </div>

          <!-- Penetrant B and C -->
          <div class="p-split-section">
            <div class="p-split-left">
              <div class="p-col-header">Image of Penetrant Check OD Area "B and C"</div>
              <div class="p-photo-grid-2x2">
                <div class="p-photo-slot" id="prev_img_penet02"><img src="{{ asset('images/doc_assets/p2_Image38.jpg') }}" alt="Penetrant BC 1"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p2_Image39.jpg') }}" alt="Penetrant BC 2"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p2_Image40.jpg') }}" alt="Penetrant BC 3"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p2_Image41.jpg') }}" alt="Penetrant BC 4"></div>
              </div>
            </div>
            <div class="p-split-right">
              <div class="p-col-header">Actual condition</div>
              <div class="p-bullet-box" style="padding-top:12px;">
                <div><strong>&gt;</strong> No indication crack at OD Area "B and C" after penetrant check</div>
              </div>
            </div>
          </div>
          <div class="p-legend-footer">
            <div>A : <span class="badge-ar-a">Accept</span></div>
            <div>R : <span class="badge-ar-r">Reject</span></div>
          </div>
        </div>

        <!-- ==============================================================
             LEMBAR 3 DARI 6 (PAGE 3)
        ============================================================== -->
        <div class="sheet-paper preview-sheet" id="prev_sheet_3">
          <div class="sheet-page-indicator">Halaman 3 dari 6</div>
          <!-- Item 03: Area D -->
          <div class="p-bar-yellow" style="border-top:1px solid #000;">OD Area "D"</div>
          <div class="p-split-section">
            <div class="p-split-left">
              <div class="p-col-header">Image of OD Area "D"</div>
              <div class="p-photo-grid-2x2">
                <div class="p-photo-slot" id="prev_img_item03"><img src="{{ asset('images/doc_assets/p3_Image44.jpg') }}" alt="Area D 1"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p3_Image45.jpg') }}" alt="Area D 2"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p3_Image46.jpg') }}" alt="Area D 3"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p3_Image47.jpg') }}" alt="Area D 4"></div>
              </div>
            </div>
            <div class="p-split-right">
              <div class="p-col-header">Actual visual &amp; measurement</div>
              <div class="p-sub-title">Actual Measurement</div>
              <table class="p-data-table">
                <thead><tr><th>Item</th><th>Spec</th><th>X</th><th>Y</th><th>Z</th><th>A/R</th></tr></thead>
                <tbody>
                  <tr>
                    <td style="font-weight:700;">D</td><td>Ø118 ± 0,5</td>
                    <td id="prev_i3_d_x">118,030</td><td id="prev_i3_d_y">118,020</td><td id="prev_i3_d_z">118,010</td>
                    <td class="cell-accept" id="prev_i3_d_ar">A</td>
                  </tr>
                </tbody>
              </table>
              <div class="p-bullet-box">
                <div><strong>&gt;</strong> Actual measurement OD area "D" in good condition</div>
                <div><strong>&gt;</strong> Visual scratch at OD area "D"</div>
              </div>
              <div class="p-sub-title">Actual Distance</div>
              <table class="p-data-table">
                <thead><tr><th>Item</th><th>Spec</th><th>Result of Measurement</th><th>A/R</th></tr></thead>
                <tbody>
                  <tr><td style="font-weight:700;">E</td><td>13 ± 0,5</td><td id="prev_i3_e_x">12,950</td><td class="cell-accept" id="prev_i3_e_ar">A</td></tr>
                </tbody>
              </table>
              <div class="p-bullet-box">
                <div><strong>&gt;</strong> Actual distance area "E" in good condition</div>
                <div><strong>&gt;</strong> Visual worn out at area surface (see mark no 1)</div>
              </div>
            </div>
          </div>
          <div class="p-legend-footer">
            <div>A : <span class="badge-ar-a">Accept</span></div>
            <div>R : <span class="badge-ar-r">Reject</span></div>
          </div>

          <!-- Penetrant D -->
          <div class="p-split-section">
            <div class="p-split-left">
              <div class="p-col-header">Image of Penetrant Check OD Area "D"</div>
              <div class="p-photo-grid-2x2">
                <div class="p-photo-slot" id="prev_img_penet03"><img src="{{ asset('images/doc_assets/p3_Image50.jpg') }}" alt="Penetrant D 1"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p3_Image51.jpg') }}" alt="Penetrant D 2"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p3_Image52.jpg') }}" alt="Penetrant D 3"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p3_Image53.jpg') }}" alt="Penetrant D 4"></div>
              </div>
            </div>
            <div class="p-split-right">
              <div class="p-col-header">Actual condition</div>
              <div class="p-bullet-box" style="padding-top:12px;">
                <div><strong>&gt;</strong> No indication crack at OD area "D" after penetrant check</div>
              </div>
            </div>
          </div>
          <div class="p-legend-footer">
            <div>A : <span class="badge-ar-a">Accept</span></div>
            <div>R : <span class="badge-ar-r">Reject</span></div>
          </div>

          <!-- Item 04: Area F -->
          <div class="p-bar-yellow">OD Area "F"</div>
          <div class="p-split-section">
            <div class="p-split-left">
              <div class="p-col-header">Image of OD Area "F"</div>
              <div class="p-photo-grid-2x2">
                <div class="p-photo-slot" id="prev_img_item04"><img src="{{ asset('images/doc_assets/p3_Image54.jpg') }}" alt="Area F 1"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p3_Image55.jpg') }}" alt="Area F 2"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p3_Image56.jpg') }}" alt="Area F 3"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p3_Image57.jpg') }}" alt="Area F 4"></div>
              </div>
            </div>
            <div class="p-split-right">
              <div class="p-col-header">Actual visual &amp; measurement</div>
              <div class="p-sub-title">Actual Measurement</div>
              <table class="p-data-table">
                <thead><tr><th>Item</th><th>Spec</th><th>X</th><th>Y</th><th>Z</th><th>A/R</th></tr></thead>
                <tbody>
                  <tr>
                    <td rowspan="3" style="font-weight:700;">F</td><td rowspan="3">Ø100,01 ± 0,008</td>
                    <td id="prev_i4_f1_x">101,010</td><td id="prev_i4_f1_y">101,010</td><td id="prev_i4_f1_z">101,005</td><td class="cell-accept" id="prev_i4_f1_ar">A</td>
                  </tr>
                  <tr>
                    <td id="prev_i4_f2_x">101,015</td><td id="prev_i4_f2_y">101,015</td><td id="prev_i4_f2_z">101,015</td><td class="cell-accept" id="prev_i4_f2_ar">A</td>
                  </tr>
                  <tr>
                    <td id="prev_i4_f3_x">101,010</td><td id="prev_i4_f3_y">101,010</td><td id="prev_i4_f3_z">101,010</td><td class="cell-accept" id="prev_i4_f3_ar">A</td>
                  </tr>
                </tbody>
              </table>
              <div class="p-bullet-box">
                <div><strong>&gt;</strong> Actual measurement OD area "F" in good condition</div>
                <div><strong>&gt;</strong> Visual pitting and scratch OD area "F" (see mark no 1)</div>
              </div>
              <div class="p-sub-title">Actual Distance</div>
              <table class="p-data-table">
                <thead><tr><th>Item</th><th>Spec</th><th>Result of Measurement</th><th>A/R</th></tr></thead>
                <tbody>
                  <tr><td style="font-weight:700;">K</td><td>N/A</td><td id="prev_i4_k">188,550</td><td>-</td></tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="p-legend-footer">
            <div>A : <span class="badge-ar-a">Accept</span></div>
            <div>R : <span class="badge-ar-r">Reject</span></div>
          </div>
        </div>

        <!-- ==============================================================
             LEMBAR 4 DARI 6 (PAGE 4)
        ============================================================== -->
        <div class="sheet-paper preview-sheet" id="prev_sheet_4">
          <div class="sheet-page-indicator">Halaman 4 dari 6</div>
          <!-- Penetrant F -->
          <div class="p-split-section" style="border-top: 1px solid #000;">
            <div class="p-split-left">
              <div class="p-col-header">Image of Penetrant Check OD Area "F"</div>
              <div class="p-photo-grid-2x2">
                <div class="p-photo-slot" id="prev_img_penet04"><img src="{{ asset('images/doc_assets/p4_Image60.jpg') }}" alt="Penetrant F 1"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p4_Image61.jpg') }}" alt="Penetrant F 2"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p4_Image62.jpg') }}" alt="Penetrant F 3"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p4_Image63.jpg') }}" alt="Penetrant F 4"></div>
              </div>
            </div>
            <div class="p-split-right">
              <div class="p-col-header">Actual condition</div>
              <div class="p-bullet-box" style="padding-top:12px;">
                <div><strong>&gt;</strong> No indication crack at area OD area "F" after penetrant check</div>
              </div>
            </div>
          </div>
          <div class="p-legend-footer">
            <div>A : <span class="badge-ar-a">Accept</span></div>
            <div>R : <span class="badge-ar-r">Reject</span></div>
          </div>

          <!-- Item 05: Area H -->
          <div class="p-bar-yellow">OD Area "H"</div>
          <div class="p-split-section">
            <div class="p-split-left">
              <div class="p-col-header">Image of OD Area "H"</div>
              <div class="p-photo-grid-2x2">
                <div class="p-photo-slot" id="prev_img_item05"><img src="{{ asset('images/doc_assets/p4_Image64.jpg') }}" alt="Area H 1"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p4_Image65.jpg') }}" alt="Area H 2"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p4_Image66.jpg') }}" alt="Area H 3"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p4_Image67.jpg') }}" alt="Area H 4"></div>
              </div>
            </div>
            <div class="p-split-right">
              <div class="p-col-header">Actual visual &amp; measurement</div>
              <div class="p-sub-title">Actual Measurement</div>
              <table class="p-data-table">
                <thead><tr><th>Item</th><th>Spec</th><th>X</th><th>Y</th><th>Z</th><th>A/R</th></tr></thead>
                <tbody>
                  <tr>
                    <td rowspan="3" style="font-weight:700;">H</td><td rowspan="3">Ø94 ± 0,013</td>
                    <td class="cell-reject" id="prev_i5_h1_x">93,980</td><td class="cell-reject" id="prev_i5_h1_y">93,985</td><td class="cell-reject" id="prev_i5_h1_z">93,980</td><td class="cell-reject" id="prev_i5_h1_ar">R</td>
                  </tr>
                  <tr>
                    <td class="cell-reject" id="prev_i5_h2_x">93,980</td><td class="cell-reject" id="prev_i5_h2_y">93,985</td><td class="cell-reject" id="prev_i5_h2_z">93,985</td><td class="cell-reject" id="prev_i5_h2_ar">R</td>
                  </tr>
                  <tr>
                    <td id="prev_i5_h3_x">93,995</td><td id="prev_i5_h3_y">94,000</td><td id="prev_i5_h3_z">93,990</td><td class="cell-accept" id="prev_i5_h3_ar">A</td>
                  </tr>
                </tbody>
              </table>
              <div class="p-bullet-box">
                <div><strong>&gt;</strong> Actual measurement OD area "H" was undersize</div>
                <div><strong>&gt;</strong> Visual pitting and scratch at OD area "H" (see mark no 1)</div>
              </div>
              <div class="p-sub-title">Actual Distance</div>
              <table class="p-data-table">
                <thead><tr><th>Item</th><th>Spec</th><th>Result of Measurement</th><th>A/R</th></tr></thead>
                <tbody>
                  <tr><td style="font-weight:700;">J</td><td>N/A</td><td id="prev_i5_j">142,800</td><td>-</td></tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="p-legend-footer">
            <div>A : <span class="badge-ar-a">Accept</span></div>
            <div>R : <span class="badge-ar-r">Reject</span></div>
          </div>

          <!-- Penetrant H -->
          <div class="p-split-section">
            <div class="p-split-left">
              <div class="p-col-header">Image of Penetrant Check OD Area "H"</div>
              <div class="p-photo-grid-2x2">
                <div class="p-photo-slot" id="prev_img_penet05"><img src="{{ asset('images/doc_assets/p4_Image68.jpg') }}" alt="Penetrant H 1"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p4_Image69.jpg') }}" alt="Penetrant H 2"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p4_Image70.jpg') }}" alt="Penetrant H 3"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p4_Image71.jpg') }}" alt="Penetrant H 4"></div>
              </div>
            </div>
            <div class="p-split-right">
              <div class="p-col-header">Actual condition</div>
              <div class="p-bullet-box" style="padding-top:12px;">
                <div><strong>&gt;</strong> No indication crack at area OD area "H" after penetrant check</div>
              </div>
            </div>
          </div>
          <div class="p-legend-footer">
            <div>A : <span class="badge-ar-a">Accept</span></div>
            <div>R : <span class="badge-ar-r">Reject</span></div>
          </div>
        </div>

        <!-- ==============================================================
             LEMBAR 5 DARI 6 (PAGE 5)
        ============================================================== -->
        <div class="sheet-paper preview-sheet" id="prev_sheet_5">
          <div class="sheet-page-indicator">Halaman 5 dari 6</div>
          <!-- Item 06: Area G and Spline -->
          <div class="p-bar-yellow" style="border-top:1px solid #000;">OD Area "G" and OD Spline</div>
          <div class="p-split-section">
            <div class="p-split-left">
              <div class="p-col-header">Image of OD Spline</div>
              <div class="p-photo-grid-2x2">
                <div class="p-photo-slot" id="prev_img_item06"><img src="{{ asset('images/doc_assets/p5_Image75.jpg') }}" alt="Area G 1"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p5_Image76.jpg') }}" alt="Area G 2"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p5_Image77.jpg') }}" alt="Area G 3"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p5_Image78.jpg') }}" alt="Area G 4"></div>
              </div>
            </div>
            <div class="p-split-right">
              <div class="p-col-header">Actual visual &amp; measurement</div>
              <div class="p-sub-title">Actual Measurement</div>
              <table class="p-data-table">
                <thead><tr><th>Item</th><th>Spec</th><th>X</th><th>Y</th><th>Z</th><th>A/R</th></tr></thead>
                <tbody>
                  <tr>
                    <td style="font-weight:700;">G</td><td>Ø84,2 ± 0,13</td>
                    <td class="cell-reject" id="prev_i6_x">83,980</td><td id="prev_i6_y">84,010</td><td id="prev_i6_z">84,000</td>
                    <td class="cell-reject" id="prev_i6_ar">R</td>
                  </tr>
                </tbody>
              </table>
              <div class="p-bullet-box">
                <div><strong>&gt;</strong> Actual measurement OD area "G" was undersize</div>
                <div><strong>&gt;</strong> Visual corrosion at OD area "G"</div>
                <div><strong>&gt;</strong> Visual corrosion area spline</div>
                <div><strong>&gt;</strong> Visual scratch and corrosion at area surface</div>
                <div><strong>&gt;</strong> Visual dirty at hole thread area surface</div>
              </div>
              <div class="p-sub-title">Actual Distance</div>
              <table class="p-data-table">
                <thead><tr><th>Item</th><th>Spec</th><th>Result of Measurement</th><th>A/R</th></tr></thead>
                <tbody>
                  <tr><td style="font-weight:700;">I</td><td>N/A</td><td id="prev_i6_i">98,780</td><td>-</td></tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="p-legend-footer">
            <div>A : <span class="badge-ar-a">Accept</span></div>
            <div>R : <span class="badge-ar-r">Reject</span></div>
          </div>

          <!-- Penetrant G -->
          <div class="p-split-section">
            <div class="p-split-left">
              <div class="p-col-header">Image of Penetrant Check Area OD Spline</div>
              <div class="p-photo-grid-2x2">
                <div class="p-photo-slot" id="prev_img_penet06"><img src="{{ asset('images/doc_assets/p5_Image79.jpg') }}" alt="Penetrant G 1"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p5_Image80.jpg') }}" alt="Penetrant G 2"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p5_Image81.jpg') }}" alt="Penetrant G 3"></div>
                <div class="p-photo-slot"><img src="{{ asset('images/doc_assets/p5_Image82.jpg') }}" alt="Penetrant G 4"></div>
              </div>
            </div>
            <div class="p-split-right">
              <div class="p-col-header">Actual condition</div>
              <div class="p-bullet-box" style="padding-top:12px;">
                <div><strong>&gt;</strong> No indication crack at area OD spline after penetrant check</div>
              </div>
            </div>
          </div>
          <div class="p-legend-footer">
            <div>A : <span class="badge-ar-a">Accept</span></div>
            <div>R : <span class="badge-ar-r">Reject</span></div>
          </div>

          <!-- Check TIR -->
          <div class="p-bar-yellow">Check TIR</div>
          <div class="p-blueprint-box" style="padding:4px 10px;">
            <img src="{{ asset('images/doc_assets/shaft_tir_blueprint.png') }}" alt="Shaft TIR Blueprint">
          </div>
          <table class="p-tir-table">
            <thead>
              <tr>
                <th style="width:40px;">Item</th><th style="width:65px;">Spec</th><th>0°</th><th>90°</th><th>180°</th><th>270°</th><th style="width:35px;">A/R</th>
              </tr>
            </thead>
            <tbody>
              <tr><td style="font-weight:700;">A</td><td rowspan="6" style="vertical-align:middle; font-weight:700;">Max 0,2</td><td id="prev_tir_a_0">0</td><td id="prev_tir_a_90">+0,05</td><td id="prev_tir_a_180">+0,18</td><td id="prev_tir_a_270">+0,10</td><td class="cell-accept" id="prev_tir_a_ar">A</td></tr>
              <tr><td style="font-weight:700;">B</td><td id="prev_tir_b_0">0</td><td id="prev_tir_b_90">+0,02</td><td id="prev_tir_b_180">0</td><td id="prev_tir_b_270">+0,02</td><td class="cell-accept" id="prev_tir_b_ar">A</td></tr>
              <tr><td style="font-weight:700;">C</td><td id="prev_tir_c_0">0</td><td id="prev_tir_c_90">+0,12</td><td id="prev_tir_c_180">+0,10</td><td id="prev_tir_c_270">+0,03</td><td class="cell-accept" id="prev_tir_c_ar">A</td></tr>
              <tr><td style="font-weight:700;">D</td><td id="prev_tir_d_0">0</td><td id="prev_tir_d_90">+0,03</td><td id="prev_tir_d_180">+0,03</td><td id="prev_tir_d_270">0</td><td class="cell-accept" id="prev_tir_d_ar">A</td></tr>
              <tr><td style="font-weight:700;">E</td><td id="prev_tir_e_0">0</td><td id="prev_tir_e_90">+0,01</td><td id="prev_tir_e_180">0</td><td id="prev_tir_e_270">0</td><td class="cell-accept" id="prev_tir_e_ar">A</td></tr>
              <tr><td style="font-weight:700;">F</td><td id="prev_tir_f_0">0</td><td id="prev_tir_f_90">-0,05</td><td id="prev_tir_f_180">-0,08</td><td id="prev_tir_f_270">+0,02</td><td class="cell-accept" id="prev_tir_f_ar">A</td></tr>
            </tbody>
          </table>
          <div style="padding:2px 10px 6px 10px; font-size:6.8pt;">
            <strong>&gt;</strong> Datum at item "B and E"
          </div>
        </div>

        <!-- ==============================================================
             LEMBAR 6 DARI 6 (PAGE 6)
        ============================================================== -->
        <div class="sheet-paper preview-sheet" id="prev_sheet_6">
          <div class="sheet-page-indicator">Halaman 6 dari 6</div>
          <!-- RECOMENDATION -->
          <div class="p-bar-yellow" style="margin-top:0;">RECOMENDATION</div>
          <table class="p-rec-table">
            <thead>
              <tr><th style="width:40px;">Item</th><th>Remarks</th><th>Recomendation</th></tr>
            </thead>
            <tbody id="prev_recTableBody">
              <tr>
                <td style="text-align:center; font-weight:700; font-family:monospace;">01</td>
                <td style="white-space:pre-line;">Visual scratch at OD area "A"&#10;Visual corrosion area spline&#10;Visual scratch at area surface&#10;Visual dirty at hole thread area surface</td>
                <td style="white-space:pre-line;">Cleaning and polish OD area "A"&#10;Cleaning area surface&#10;Cleaning and polish area spline&#10;Retap hole thread area surface</td>
              </tr>
              <tr>
                <td style="text-align:center; font-weight:700; font-family:monospace;">02</td>
                <td style="white-space:pre-line;">Visual corrosion at OD area "B"&#10;Visual corrosion at OD area "C"</td>
                <td style="white-space:pre-line;">Cleaning OD area "B and C"</td>
              </tr>
              <tr>
                <td style="text-align:center; font-weight:700; font-family:monospace;">03</td>
                <td style="white-space:pre-line;">Visual scratch at OD area "D"&#10;Visual worn out at area surface (see mark no 1)</td>
                <td style="white-space:pre-line;">Cleaning and polish OD area "D"&#10;Repair worn out area surface</td>
              </tr>
              <tr>
                <td style="text-align:center; font-weight:700; font-family:monospace;">04</td>
                <td style="white-space:pre-line;">Actual measurement OD area "F" in good condition but, Visual pitting and scratch OD area "F" (see mark no 1)</td>
                <td style="white-space:pre-line;">Repair OD area "F" to specification</td>
              </tr>
              <tr>
                <td style="text-align:center; font-weight:700; font-family:monospace;">05</td>
                <td style="white-space:pre-line;">Actual measurement OD area "H" was undersize&#10;Visual pitting and scratch at OD area "H" (see mark no 1)</td>
                <td style="white-space:pre-line;">Repair OD area "H" to specification</td>
              </tr>
              <tr>
                <td style="text-align:center; font-weight:700; font-family:monospace;">06</td>
                <td style="white-space:pre-line;">Actual measurement OD area "G" was undersize&#10;Visual corrosion at OD area "G"&#10;Visual corrosion area spline&#10;Visual scratch and corrosion at area surface&#10;Visual dirty at hole thread area surface</td>
                <td style="white-space:pre-line;">Repair Cleaning OD area "G" to specification&#10;Cleaning and polish area spline&#10;Cleaning and polish area surface&#10;Retap hole thread area surface</td>
              </tr>
            </tbody>
          </table>

          <!-- Signatures -->
          <table class="p-sig-table">
            <thead>
              <tr><th>Checked by:</th><th>Created by:</th><th>Reviewed by:</th></tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <div class="sig-name" id="prev_sigCheckedName">Faqih S A A</div>
                  <div class="sig-meta">
                    <span id="prev_sigCheckedTitle">QC Inspector</span>
                    <span id="prev_sigCheckedDate">Date: October 20, 2026</span>
                  </div>
                </td>
                <td>
                  <div class="sig-name" id="prev_sigCreatedName">M Kevin S</div>
                  <div class="sig-meta">
                    <span id="prev_sigCreatedTitle">QC Inspector</span>
                    <span id="prev_sigCreatedDate">Date: October 21, 2026</span>
                  </div>
                </td>
                <td>
                  <div class="sig-name" id="prev_sigReviewedName">Agus Riyanto</div>
                  <div class="sig-meta">
                    <span id="prev_sigReviewedTitle">Leader QC</span>
                    <span id="prev_sigReviewedDate">Date: October 21, 2026</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </div>

  </div>
</div>
@endsection

@section('scripts')
  <script src="{{ asset('js/app.js') }}"></script>
@endsection

