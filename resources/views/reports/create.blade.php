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
            Output Shaft &mdash; FORM-QC-002 REV 04 (Aktif & Siap Digunakan)
          </option>
          <option value="hydraulic_cylinder" disabled>
            Hydraulic Cylinder Rod &mdash; FORM-QC-005 (Menunggu Template Divisi Terkait)
          </option>
          <option value="drive_pinion" disabled>
            Drive Pinion Gear &mdash; FORM-QC-008 (Menunggu Template Divisi Terkait)
          </option>
          <option value="bearing_journal" disabled>
            Bearing Journal Sleeve &mdash; FORM-QC-011 (Menunggu Template Divisi Terkait)
          </option>
          <option value="connecting_rod" disabled>
            Connecting Rod &mdash; FORM-QC-014 (Menunggu Template Divisi Terkait)
          </option>
        </select>
        <span class="badge badge-green" style="font-size: 0.75rem; padding: 0.35rem 0.6rem;">Template Aktif</span>
      </div>
    </div>
    <div class="selector-meta">
      <span class="meta-item"><i data-lucide="file-text"></i> No. Dok: <strong>FORM-QC-002</strong></span>
      <span class="meta-item"><i data-lucide="git-branch"></i> Revisi: <strong>04</strong></span>
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
            <div class="form-section-title">
              <i data-lucide="clipboard-list"></i>
              <h4>Identitas Inspeksi & Order Kerja</h4>
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
                <label>Nama Komponen</label>
                <input type="text" id="g_partName" value="{{ $report->part_name ?? 'OUTPUT SHAFT' }}" readonly style="background: #f8fafc;">
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
              <div class="form-group">
                <label>No. Dokumen Standar</label>
                <input type="text" id="g_docNo" value="FORM-QC-002" readonly style="background: #f8fafc;">
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
          <span>Live Document Preview &mdash; <strong>FORM-QC-002 REV 04</strong></span>
        </div>
        <div class="preview-actions">
          <button type="button" class="btn btn-secondary btn-xs" id="btnPrintPreview" title="Cetak / Unduh PDF">
            <i data-lucide="printer"></i> Cetak / PDF
          </button>
        </div>
      </div>

      <!-- The Official Sheet Replica — COMPONENT CONDITION REPORT -->
      <div class="sheet-container" id="printableSheet">
        <div class="sheet-paper">

          <!-- DOCUMENT HEADER -->
          <div class="doc-header">
            <div class="doc-header-logo">
              <img src="{{ asset('images/logo.png') }}" alt="PT SSC Works" class="doc-logo-img">
            </div>
            <div class="doc-header-title">
              <div class="doc-company-name">PT. SUKSES SARI COATING WORKS</div>
              <div class="doc-main-title">COMPONENT CONDITION REPORT</div>
            </div>
            <div class="doc-header-meta">
              <table>
                <tr><td>Document No</td><td>:</td><td class="bold">FORM-QC-002</td><td>Responsibility</td><td>:</td><td>QC Inspector</td></tr>
                <tr><td>Effective Date.</td><td>:</td><td id="prev_metaDate">-</td><td>Rev.</td><td>:</td><td class="bold">04</td></tr>
                <tr><td>Page</td><td>:</td><td>1</td><td></td><td></td><td></td></tr>
              </table>
            </div>
          </div>

          <!-- Info Grid -->
          <div class="doc-info-grid">
            <div class="doc-info-row">
              <div class="dic"><span class="lbl">No.</span></div><div class="dic val mono" id="prev_reportNo">-</div>
              <div class="dic"><span class="lbl">Date</span></div><div class="dic val" id="prev_metaDate2">-</div>
              <div class="dic"><span class="lbl">Customer</span></div><div class="dic val" id="prev_customer">-</div>
              <div class="dic"><span class="lbl">Part Name</span></div><div class="dic val bold" id="prev_partName">OUTPUT SHAFT</div>
              <div class="dic center" id="prev_preCheck">Pre &#9744;</div>
              <div class="dic center" id="prev_postCheck">Post &#9744;</div>
            </div>
            <div class="doc-info-row doc-info-row-2">
              <div class="dic bold">SSC ID</div><div class="dic val mono bold" id="prev_sscId" style="color:#1e3a8a;">-</div>
              <div class="dic">Job No.</div><div class="dic val" id="prev_jobNo">-</div>
              <div class="dic">Model/Unit</div><div class="dic val" id="prev_modelUnit">-</div>
              <div class="dic">Quote No.</div><div class="dic val" id="prev_quoteNo">-</div>
              <div class="dic">Part No. <span id="prev_partNo" style="font-weight:400;">-</span></div>
            </div>
          </div>

          <!-- FINDING SECTION -->
          <div class="doc-section-banner">FINDING &mdash; DRAWING / PICTURES / DIMENSION</div>
          <table class="doc-finding-table">
            <thead>
              <tr><th style="width:28px;">NO</th><th style="width:38%;">IMAGE</th><th>DESCRIPTION</th></tr>
            </thead>
            <tbody>
              <tr>
                <td class="center">01</td>
                <td>
                  <div class="doc-photo-wrapper" style="height:80px;" id="prev_img_full">
                    <span class="no-photo-txt">Full image received component OUTPUT SHAFT</span>
                  </div>
                </td>
                <td id="prev_finding_desc" style="font-size:0.68rem; vertical-align:top; padding:3px;">
                  <span style="color:#9ca3af; font-style:italic;">&#8212; Temuan akan muncul dari tab Rekomendasi &#8212;</span>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- ITEM 01 — OD Area "A" and OD Spline -->
          <div class="doc-insp-section">
            <div class="doc-insp-header">
              <div class="doc-insp-hdr-left">01 | OD Area "A" and OD Spline</div>
              <div class="doc-insp-hdr-right">Actual Measurement &mdash; OD Spline</div>
            </div>
            <div class="doc-insp-body">
              <div class="doc-insp-photo-col">
                <div class="doc-photo-wrapper" id="prev_img_item01"><span class="no-photo-txt">Image of OD Area "A" and OD Spline</span></div>
                <div class="doc-photo-caption">Image of OD Area "A" and OD Spline</div>
              </div>
              <div class="doc-insp-meas-col">
                <table class="doc-meas-table">
                  <thead><tr><th>Item</th><th>Spec</th><th>X</th><th>Y</th><th>Z</th><th>A/R</th></tr></thead>
                  <tbody>
                    <tr><td>OD Area "A"</td><td>O79 &plusmn; 0,25</td><td id="prev_i1_x">-</td><td id="prev_i1_y">-</td><td id="prev_i1_z">-</td><td class="center bold" id="prev_i1_ar">A</td></tr>
                    <tr><td>Actual Length L</td><td>N/A</td><td colspan="3" id="prev_i1_len">-</td><td>-</td></tr>
                  </tbody>
                </table>
                <div class="doc-insp-remark">Remarks: <span id="prev_i1_rem">Actual measurement OD area "A" in good condition</span></div>
              </div>
            </div>
            <div class="doc-penetrant-row">
              <div class="doc-penet-left">
                <div class="doc-ar-legend">A : Accept &nbsp;|&nbsp; R : Reject</div>
                <div class="doc-photo-wrapper" style="height:68px;" id="prev_img_penet01"><span class="no-photo-txt">Penetrant Check OD Area "A"</span></div>
                <div class="doc-photo-caption">Penetrant Check OD Area "A" and OD Spline</div>
              </div>
              <div class="doc-penet-right">
                <div class="doc-penet-remark" id="prev_i1_prem">No indication crack at OD area "A" and OD spline after penetrant check</div>
              </div>
            </div>
          </div>

          <!-- ITEM 02 — OD Area "B and C" -->
          <div class="doc-insp-section">
            <div class="doc-insp-header">
              <div class="doc-insp-hdr-left">02 | OD Area "B and C"</div>
              <div class="doc-insp-hdr-right">Actual Measurement</div>
            </div>
            <div class="doc-insp-body">
              <div class="doc-insp-photo-col">
                <div class="doc-photo-wrapper" id="prev_img_item02"><span class="no-photo-txt">Image of OD Area "B and C"</span></div>
                <div class="doc-photo-caption">Image of OD Area "B and C"</div>
              </div>
              <div class="doc-insp-meas-col">
                <table class="doc-meas-table">
                  <thead><tr><th>Item</th><th>Spec</th><th>X</th><th>Y</th><th>Z</th><th>A/R</th></tr></thead>
                  <tbody>
                    <tr><td>B &mdash; Row 1</td><td>O82 &plusmn; 0,25</td><td id="prev_i2_b1_x">-</td><td id="prev_i2_b1_y">-</td><td id="prev_i2_b1_z">-</td><td class="center bold" id="prev_i2_b1_ar">A</td></tr>
                    <tr><td>B &mdash; Row 2</td><td>O82 &plusmn; 0,25</td><td id="prev_i2_b2_x">-</td><td id="prev_i2_b2_y">-</td><td id="prev_i2_b2_z">-</td><td class="center bold" id="prev_i2_b2_ar">A</td></tr>
                    <tr><td>B &mdash; Row 3</td><td>O82 &plusmn; 0,25</td><td id="prev_i2_b3_x">-</td><td id="prev_i2_b3_y">-</td><td id="prev_i2_b3_z">-</td><td class="center bold" id="prev_i2_b3_ar">A</td></tr>
                    <tr><td>C &mdash; Row 1</td><td>O77 &plusmn; 0,25</td><td id="prev_i2_c1_x">-</td><td id="prev_i2_c1_y">-</td><td id="prev_i2_c1_z">-</td><td class="center bold" id="prev_i2_c1_ar">A</td></tr>
                    <tr><td>C &mdash; Row 2</td><td>O77 &plusmn; 0,25</td><td id="prev_i2_c2_x">-</td><td id="prev_i2_c2_y">-</td><td id="prev_i2_c2_z">-</td><td class="center bold" id="prev_i2_c2_ar">A</td></tr>
                    <tr><td>C &mdash; Row 3</td><td>O77 &plusmn; 0,25</td><td id="prev_i2_c3_x">-</td><td id="prev_i2_c3_y">-</td><td id="prev_i2_c3_z">-</td><td class="center bold" id="prev_i2_c3_ar">A</td></tr>
                  </tbody>
                </table>
                <div class="doc-insp-remark">Remarks: <span id="prev_i2_rem">Actual measurement OD area "B and C" in good condition</span></div>
              </div>
            </div>
            <div class="doc-penetrant-row">
              <div class="doc-penet-left">
                <div class="doc-ar-legend">A : Accept &nbsp;|&nbsp; R : Reject</div>
                <div class="doc-photo-wrapper" style="height:68px;" id="prev_img_penet02"><span class="no-photo-txt">Penetrant Check OD Area "B and C"</span></div>
                <div class="doc-photo-caption">Penetrant Check OD Area "B and C"</div>
              </div>
              <div class="doc-penet-right">
                <div class="doc-penet-remark" id="prev_i2_prem">No indication crack at OD Area "B and C" after penetrant check</div>
              </div>
            </div>
          </div>

          <!-- ITEM 03 — OD Area "D" -->
          <div class="doc-insp-section">
            <div class="doc-insp-header">
              <div class="doc-insp-hdr-left">03 | OD Area "D"</div>
              <div class="doc-insp-hdr-right">Actual Measurement</div>
            </div>
            <div class="doc-insp-body">
              <div class="doc-insp-photo-col">
                <div class="doc-photo-wrapper" id="prev_img_item03"><span class="no-photo-txt">Image of OD Area "D"</span></div>
                <div class="doc-photo-caption">Image of OD Area "D"</div>
              </div>
              <div class="doc-insp-meas-col">
                <table class="doc-meas-table">
                  <thead><tr><th>Item</th><th>Spec</th><th>X</th><th>Y</th><th>Z</th><th>A/R</th></tr></thead>
                  <tbody>
                    <tr><td>OD Area "D"</td><td>O118 &plusmn; 0,5</td><td id="prev_i3_d_x">-</td><td id="prev_i3_d_y">-</td><td id="prev_i3_d_z">-</td><td class="center bold" id="prev_i3_d_ar">A</td></tr>
                    <tr><td>Actual Distance E</td><td>13 &plusmn; 0,5</td><td id="prev_i3_e_x">-</td><td id="prev_i3_e_y">-</td><td id="prev_i3_e_z">-</td><td class="center bold" id="prev_i3_e_ar">A</td></tr>
                  </tbody>
                </table>
                <div class="doc-insp-remark">Remarks: <span id="prev_i3_rem">Actual measurement OD area "D" in good condition</span></div>
              </div>
            </div>
            <div class="doc-penetrant-row">
              <div class="doc-penet-left">
                <div class="doc-ar-legend">A : Accept &nbsp;|&nbsp; R : Reject</div>
                <div class="doc-photo-wrapper" style="height:68px;" id="prev_img_penet03"><span class="no-photo-txt">Penetrant Check OD Area "D"</span></div>
                <div class="doc-photo-caption">Penetrant Check OD Area "D"</div>
              </div>
              <div class="doc-penet-right">
                <div class="doc-penet-remark" id="prev_i3_prem">No indication crack at OD area "D" after penetrant check</div>
              </div>
            </div>
          </div>

          <!-- ITEM 04 — OD Area "F" -->
          <div class="doc-insp-section">
            <div class="doc-insp-header">
              <div class="doc-insp-hdr-left">04 | OD Area "F"</div>
              <div class="doc-insp-hdr-right">Actual Measurement</div>
            </div>
            <div class="doc-insp-body">
              <div class="doc-insp-photo-col">
                <div class="doc-photo-wrapper" id="prev_img_item04"><span class="no-photo-txt">Image of OD Area "F"</span></div>
                <div class="doc-photo-caption">Image of OD Area "F"</div>
              </div>
              <div class="doc-insp-meas-col">
                <table class="doc-meas-table">
                  <thead><tr><th>Item</th><th>Spec</th><th>X</th><th>Y</th><th>Z</th><th>A/R</th></tr></thead>
                  <tbody>
                    <tr><td>F &mdash; Row 1</td><td>O100,01 &plusmn; 0,008</td><td id="prev_i4_f1_x">-</td><td id="prev_i4_f1_y">-</td><td id="prev_i4_f1_z">-</td><td class="center bold" id="prev_i4_f1_ar">A</td></tr>
                    <tr><td>F &mdash; Row 2</td><td>O100,01 &plusmn; 0,008</td><td id="prev_i4_f2_x">-</td><td id="prev_i4_f2_y">-</td><td id="prev_i4_f2_z">-</td><td class="center bold" id="prev_i4_f2_ar">A</td></tr>
                    <tr><td>F &mdash; Row 3</td><td>O100,01 &plusmn; 0,008</td><td id="prev_i4_f3_x">-</td><td id="prev_i4_f3_y">-</td><td id="prev_i4_f3_z">-</td><td class="center bold" id="prev_i4_f3_ar">A</td></tr>
                    <tr><td>Actual Distance K</td><td>N/A</td><td colspan="3" id="prev_i4_k">-</td><td>-</td></tr>
                  </tbody>
                </table>
                <div class="doc-insp-remark">Remarks: <span id="prev_i4_rem">Actual measurement OD area "F" in good condition</span></div>
              </div>
            </div>
            <div class="doc-penetrant-row">
              <div class="doc-penet-left">
                <div class="doc-ar-legend">A : Accept &nbsp;|&nbsp; R : Reject</div>
                <div class="doc-photo-wrapper" style="height:68px;" id="prev_img_penet04"><span class="no-photo-txt">Penetrant Check OD Area "F"</span></div>
                <div class="doc-photo-caption">Penetrant Check OD Area "F"</div>
              </div>
              <div class="doc-penet-right">
                <div class="doc-penet-remark" id="prev_i4_prem">No indication crack at area OD area "F" after penetrant check</div>
              </div>
            </div>
          </div>

          <!-- ITEM 05 — OD Area "H" -->
          <div class="doc-insp-section">
            <div class="doc-insp-header">
              <div class="doc-insp-hdr-left">05 | OD Area "H"</div>
              <div class="doc-insp-hdr-right">Actual Measurement</div>
            </div>
            <div class="doc-insp-body">
              <div class="doc-insp-photo-col">
                <div class="doc-photo-wrapper" id="prev_img_item05"><span class="no-photo-txt">Image of OD Area "H"</span></div>
                <div class="doc-photo-caption">Image of OD Area "H"</div>
              </div>
              <div class="doc-insp-meas-col">
                <table class="doc-meas-table">
                  <thead><tr><th>Item</th><th>Spec</th><th>X</th><th>Y</th><th>Z</th><th>A/R</th></tr></thead>
                  <tbody>
                    <tr><td>H &mdash; Row 1</td><td>O94 &plusmn; 0,013</td><td id="prev_i5_h1_x">-</td><td id="prev_i5_h1_y">-</td><td id="prev_i5_h1_z">-</td><td class="center bold" id="prev_i5_h1_ar">A</td></tr>
                    <tr><td>H &mdash; Row 2</td><td>O94 &plusmn; 0,013</td><td id="prev_i5_h2_x">-</td><td id="prev_i5_h2_y">-</td><td id="prev_i5_h2_z">-</td><td class="center bold" id="prev_i5_h2_ar">A</td></tr>
                    <tr><td>H &mdash; Row 3</td><td>O94 &plusmn; 0,013</td><td id="prev_i5_h3_x">-</td><td id="prev_i5_h3_y">-</td><td id="prev_i5_h3_z">-</td><td class="center bold" id="prev_i5_h3_ar">A</td></tr>
                    <tr><td>Actual Distance J</td><td>N/A</td><td colspan="3" id="prev_i5_j">-</td><td>-</td></tr>
                  </tbody>
                </table>
                <div class="doc-insp-remark">Remarks: <span id="prev_i5_rem">Actual measurement OD area "H" in good condition</span></div>
              </div>
            </div>
            <div class="doc-penetrant-row">
              <div class="doc-penet-left">
                <div class="doc-ar-legend">A : Accept &nbsp;|&nbsp; R : Reject</div>
                <div class="doc-photo-wrapper" style="height:68px;" id="prev_img_penet05"><span class="no-photo-txt">Penetrant Check OD Area "H"</span></div>
                <div class="doc-photo-caption">Penetrant Check OD Area "H"</div>
              </div>
              <div class="doc-penet-right">
                <div class="doc-penet-remark" id="prev_i5_prem">No indication crack at area OD area "H" after penetrant check</div>
              </div>
            </div>
          </div>

          <!-- ITEM 06 — OD Area "G" and OD Spline -->
          <div class="doc-insp-section">
            <div class="doc-insp-header">
              <div class="doc-insp-hdr-left">06 | OD Area "G" and OD Spline</div>
              <div class="doc-insp-hdr-right">Actual Measurement &mdash; OD Spline</div>
            </div>
            <div class="doc-insp-body">
              <div class="doc-insp-photo-col">
                <div class="doc-photo-wrapper" id="prev_img_item06"><span class="no-photo-txt">Image of OD Area "G" and OD Spline</span></div>
                <div class="doc-photo-caption">Image of OD Area "G" and OD Spline</div>
              </div>
              <div class="doc-insp-meas-col">
                <table class="doc-meas-table">
                  <thead><tr><th>Item</th><th>Spec</th><th>X</th><th>Y</th><th>Z</th><th>A/R</th></tr></thead>
                  <tbody>
                    <tr><td>OD Area "G"</td><td>O84,2 &plusmn; 0,13</td><td id="prev_i6_x">-</td><td id="prev_i6_y">-</td><td id="prev_i6_z">-</td><td class="center bold" id="prev_i6_ar">A</td></tr>
                    <tr><td>Actual Distance I</td><td>N/A</td><td colspan="3" id="prev_i6_i">-</td><td>-</td></tr>
                  </tbody>
                </table>
                <div class="doc-insp-remark">Remarks: <span id="prev_i6_rem">Actual measurement OD area "G" and OD spline in good condition</span></div>
              </div>
            </div>
            <div class="doc-penetrant-row">
              <div class="doc-penet-left">
                <div class="doc-ar-legend">A : Accept &nbsp;|&nbsp; R : Reject</div>
                <div class="doc-photo-wrapper" style="height:68px;" id="prev_img_penet06"><span class="no-photo-txt">Penetrant Check Area OD Spline</span></div>
                <div class="doc-photo-caption">Penetrant Check Area OD Spline</div>
              </div>
              <div class="doc-penet-right">
                <div class="doc-penet-remark" id="prev_i6_prem">No indication crack at area OD spline after penetrant check</div>
              </div>
            </div>
          </div>

          <!-- CHECK TIR -->
          <div class="doc-tir-section">
            <div class="doc-section-banner">CHECK TIR &mdash; TOTAL INDICATOR READING</div>
            <div class="doc-tir-info">
              <span><strong>Datum at item "B and E"</strong></span>
              <span>Spec: Max 0,2 mm</span>
            </div>
            <table class="doc-tir-table">
              <thead>
                <tr>
                  <th style="width:40px;">Item</th><th>Spec</th><th>0&deg;</th><th>90&deg;</th><th>180&deg;</th><th>270&deg;</th><th style="width:38px;">A/R</th>
                </tr>
              </thead>
              <tbody>
                <tr><td class="bold">A</td><td>Max 0,2</td><td id="prev_tir_a_0">-</td><td id="prev_tir_a_90">-</td><td id="prev_tir_a_180">-</td><td id="prev_tir_a_270">-</td><td class="center bold" id="prev_tir_a_ar">A</td></tr>
                <tr><td class="bold">B</td><td>Max 0,2</td><td id="prev_tir_b_0">-</td><td id="prev_tir_b_90">-</td><td id="prev_tir_b_180">-</td><td id="prev_tir_b_270">-</td><td class="center bold" id="prev_tir_b_ar">A</td></tr>
                <tr><td class="bold">C</td><td>Max 0,2</td><td id="prev_tir_c_0">-</td><td id="prev_tir_c_90">-</td><td id="prev_tir_c_180">-</td><td id="prev_tir_c_270">-</td><td class="center bold" id="prev_tir_c_ar">A</td></tr>
                <tr><td class="bold">D</td><td>Max 0,2</td><td id="prev_tir_d_0">-</td><td id="prev_tir_d_90">-</td><td id="prev_tir_d_180">-</td><td id="prev_tir_d_270">-</td><td class="center bold" id="prev_tir_d_ar">A</td></tr>
                <tr><td class="bold">E</td><td>Max 0,2</td><td id="prev_tir_e_0">-</td><td id="prev_tir_e_90">-</td><td id="prev_tir_e_180">-</td><td id="prev_tir_e_270">-</td><td class="center bold" id="prev_tir_e_ar">A</td></tr>
                <tr><td class="bold">F</td><td>Max 0,2</td><td id="prev_tir_f_0">-</td><td id="prev_tir_f_90">-</td><td id="prev_tir_f_180">-</td><td id="prev_tir_f_270">-</td><td class="center bold" id="prev_tir_f_ar">A</td></tr>
              </tbody>
            </table>
          </div>

          <!-- RECOMMENDATION TABLE -->
          <div class="doc-rec-section">
            <div class="doc-section-banner">RECOMENDATION</div>
            <table class="doc-rec-table">
              <thead>
                <tr><th style="width:28px;">NO</th><th>DESCRIPTION</th><th>Remarks</th><th>Recomendation</th></tr>
              </thead>
              <tbody id="prev_recTableBody">
                <tr><td class="center">01</td><td>&mdash;</td><td>&mdash;</td><td>&mdash;</td></tr>
              </tbody>
            </table>
          </div>

          <!-- Signatures -->
          <div class="doc-sig-grid">
            <div class="doc-sig-col">
              <div class="sig-header">Checked by:</div>
              <div class="sig-space"></div>
              <div class="sig-name" id="prev_sigCheckedName">Faqih S A A</div>
              <div class="sig-role" id="prev_sigCheckedTitle">QC Inspector</div>
              <div class="sig-date" id="prev_sigCheckedDate">-</div>
            </div>
            <div class="doc-sig-col">
              <div class="sig-header">Created by:</div>
              <div class="sig-space"></div>
              <div class="sig-name" id="prev_sigCreatedName">M Kevin S</div>
              <div class="sig-role" id="prev_sigCreatedTitle">QC Inspector</div>
              <div class="sig-date" id="prev_sigCreatedDate">-</div>
            </div>
            <div class="doc-sig-col">
              <div class="sig-header">Reviewed by:</div>
              <div class="sig-space"></div>
              <div class="sig-name" id="prev_sigReviewedName">Agus Riyanto</div>
              <div class="sig-role" id="prev_sigReviewedTitle">Leader QC</div>
              <div class="sig-date" id="prev_sigReviewedDate">-</div>
            </div>
          </div>

        </div>
      </div>

    </div>

  </div>
</div>
@endsection

@section('scripts')
  <script src="{{ asset('js/app.js') }}"></script>
@endsection

