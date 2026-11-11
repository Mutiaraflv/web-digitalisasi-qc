@extends('layouts.app')

@php
  $isExistingReport = isset($report) && $report;
  $reportStatus = $isExistingReport ? ($report->review_status ?? 'waiting_review') : \App\Models\Report::STATUS_DRAFT;
  $isDraftReport = !$isExistingReport || $reportStatus === \App\Models\Report::STATUS_DRAFT;
@endphp

@section('title', isset($isEdit) && $isEdit ? __('qc.studio.title_edit') : __('qc.studio.title_create'))
@section('page_title', isset($isEdit) && $isEdit ? __('qc.studio.title_edit') : __('qc.studio.title_create'))
@section('top_back_link', route('reports.index'))
@section('top_back_title', __('qc.studio.back_to_history'))

@section('top_actions')
  <div class="top-action-bar">
    <!-- Desktop Toolbar -->
    <div class="desktop-actions">
      <!-- 1. Clear Form -->
      <button type="button" id="btnTopResetBlank" class="btn btn-secondary btn-sm" title="{{ __('qc.studio.clear_form') }}" onclick="document.getElementById('btnResetBlank')?.click()">
        <i data-lucide="rotate-ccw"></i> <span>{{ __('qc.studio.clear') }}</span>
      </button>

      <!-- 2. Save Draft -->
      @if($isDraftReport)
        <button type="button" id="btnTopDraft" class="btn btn-secondary btn-sm btn-icon" title="{{ __('qc.studio.save_draft') }}">
          <i data-lucide="file-clock"></i>
        </button>
      @endif

      <!-- 3. Export Document -->
      <div class="export-dropdown" id="topExportDropdown">
        <button type="button" class="btn btn-secondary btn-sm btn-icon export-dropdown-trigger" id="btnExportDropdownTrigger" aria-haspopup="true" aria-expanded="false" title="{{ __('qc.studio.export_pdf') }} / {{ __('qc.studio.download_excel') }}">
          <i data-lucide="download"></i>
        </button>
        <div class="export-dropdown-menu" id="topExportDropdownMenu" role="menu">
          <button type="button" class="export-menu-item" id="btnTopExportPdf" role="menuitem" onclick="window.exportOfficialPdf()">
            <i data-lucide="file-text"></i> <span>{{ __('qc.studio.export_pdf') }}</span>
          </button>
          <button type="button" class="export-menu-item" id="btnTopExportExcel" role="menuitem">
            <i data-lucide="file-spreadsheet"></i> <span>{{ __('qc.studio.download_excel') }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Action Dropdown -->
    <div class="mobile-action-dropdown" id="mobileActionDropdown">
      <button type="button" class="btn btn-secondary btn-sm btn-icon mobile-menu-trigger" id="btnMobileMenuTrigger" aria-haspopup="true" aria-expanded="false" title="{{ __('qc.common.actions') }}">
        <i data-lucide="more-horizontal"></i>
      </button>
      <div class="mobile-menu-popup" id="mobileMenuPopup" role="menu">
        <button type="button" class="mobile-menu-item text-danger" id="btnMobileResetBlank" role="menuitem">
          <i data-lucide="rotate-ccw"></i> <span>{{ __('qc.studio.clear_form') }}</span>
        </button>
        @if($isDraftReport)
          <button type="button" class="mobile-menu-item" id="btnMobileDraft" role="menuitem">
            <i data-lucide="file-clock"></i> <span>{{ __('qc.studio.save_draft') }}</span>
          </button>
        @endif
        <div class="mobile-menu-divider"></div>
        <button type="button" class="mobile-menu-item" id="btnMobileExportPdf" role="menuitem">
          <i data-lucide="file-text"></i> <span>{{ __('qc.studio.export_pdf') }}</span>
        </button>
        <button type="button" class="mobile-menu-item" id="btnMobileExportExcel" role="menuitem">
          <i data-lucide="file-spreadsheet"></i> <span>{{ __('qc.studio.download_excel') }}</span>
        </button>
      </div>
    </div>

    <!-- 4. Submit / Save Changes -->
    <button type="button" id="btnTopSave" class="btn btn-primary btn-sm" title="{{ $isDraftReport ? __('qc.studio.submit_report') : __('qc.common.save_changes') }}">
      <i data-lucide="{{ $isDraftReport ? 'send' : 'save' }}"></i> <span>{{ $isDraftReport ? __('qc.studio.submit') : __('qc.common.save') }}</span>
    </button>
  </div>
@endsection

@section('content')
@php
  $userList = $users ?? \App\Models\User::orderBy('name')->get(['id', 'name', 'role']);
  $currentAuthName = auth()->user()->name ?? '';

  $photos = isset($report) && $report && $report->photos ? (is_array($report->photos) ? $report->photos : json_decode($report->photos, true)) : [];
  if (!is_array($photos)) $photos = [];

  $signatures = isset($report) && $report && $report->signatures ? (is_array($report->signatures) ? $report->signatures : json_decode($report->signatures, true)) : [];
  if (!is_array($signatures)) $signatures = [];
  $checkedName = $signatures['checkedBy']['name'] ?? '';
  $checkedTitle = $signatures['checkedBy']['title'] ?? '';
  $checkedDate = !empty($signatures['checkedBy']['date']) ? \Carbon\Carbon::parse($signatures['checkedBy']['date'])->format('Y-m-d') : date('Y-m-d');

  $createdName = $signatures['createdBy']['name'] ?? (($isEdit ?? false) ? '' : $currentAuthName);
  $createdTitle = $signatures['createdBy']['title'] ?? '';
  $createdDate = !empty($signatures['createdBy']['date']) ? \Carbon\Carbon::parse($signatures['createdBy']['date'])->format('Y-m-d') : date('Y-m-d');

  $reviewedName = $signatures['reviewedBy']['name'] ?? '';
  $reviewedTitle = $signatures['reviewedBy']['title'] ?? '';
  $reviewedDate = !empty($signatures['reviewedBy']['date']) ? \Carbon\Carbon::parse($signatures['reviewedBy']['date'])->format('Y-m-d') : date('Y-m-d');

  $dateStr = isset($report) && $report && $report->date ? \Carbon\Carbon::parse($report->date)->format('d-M-y') : date('d-M-y');
  $effDateStr = isset($report) && $report && $report->effective_date ? \Carbon\Carbon::parse($report->effective_date)->format('d-M-Y') : '28-Jan-2022';

  $renderSlot = function($key, $label = 'Foto') use ($photos) {
    if (!empty($photos[$key])) {
      return '<img src="' . asset($photos[$key]) . '" alt="' . $label . '">';
    }
    return '<div class="slot-placeholder">' . $label . '</div>';
  };

  $renderUploadSlot = function($key, $label) use ($photos) {
    $hasPhoto = !empty($photos[$key]);
    $imgSrc = $hasPhoto ? asset($photos[$key]) : '';
    $hasPhotoClass = $hasPhoto ? 'has-image' : '';
    $remStyle = $hasPhoto ? '' : 'display:none;';
    $camLabel = __('qc.studio.photo_camera');
    $galLabel = __('qc.studio.photo_gallery');
    $remTitle = __('qc.studio.photo_remove');

    $html = '<div class="slot-item-card ' . $hasPhotoClass . '" id="card_slot_' . $key . '">';
    $html .= '<input type="file" id="input_slot_' . $key . '" accept="image/*" style="display:none;" onchange="handleSlotUpload(this, \'' . $key . '\')">';
    $html .= '<input type="file" id="camera_slot_' . $key . '" accept="image/*" capture="environment" style="display:none;" onchange="handleSlotUpload(this, \'' . $key . '\')">';
    
    $html .= '<div class="slot-item-dropzone" onclick="triggerCameraInput(\'camera_slot_' . $key . '\')" id="dropzone_slot_' . $key . '" title="' . e(__('qc.studio.photo_open_camera')) . '">';
    $html .= '<div id="view_slot_' . $key . '">';
    if ($hasPhoto) {
      $html .= '<img src="' . $imgSrc . '" alt="' . e($label) . '">';
    } else {
      $html .= '<div class="slot-empty-icon"><i data-lucide="camera"></i><span>' . e($camLabel) . '</span></div>';
    }
    $html .= '</div>';
    $html .= '</div>';
    
    $html .= '<div class="slot-item-label">' . e($label) . '</div>';
    
    $html .= '<div class="slot-item-actions">';
    $html .= '<button type="button" class="btn-slot-action btn-cam" onclick="triggerCameraInput(\'camera_slot_' . $key . '\')" title="' . e(__('qc.studio.photo_open_camera')) . '">';
    $html .= '<i data-lucide="camera"></i> <span>' . e($camLabel) . '</span>';
    $html .= '</button>';
    $html .= '<button type="button" class="btn-slot-action btn-file" onclick="triggerFileInput(\'input_slot_' . $key . '\')" title="' . e(__('qc.studio.photo_pick_gallery')) . '">';
    $html .= '<i data-lucide="folder-image"></i> <span>' . e($galLabel) . '</span>';
    $html .= '</button>';
    $html .= '<button type="button" class="btn-slot-action btn-del" id="btn_rem_slot_' . $key . '" onclick="removeSlotPhoto(\'' . $key . '\')" style="' . $remStyle . '" title="' . e($remTitle) . '">';
    $html .= '<i data-lucide="trash-2"></i>';
    $html .= '</button>';
    $html .= '</div>';
    
    $html .= '</div>';
    return $html;
  };
@endphp

<div class="workspace-container">
  
  <!-- Component Selector Bar -->
  <div class="component-selector-bar">
    <div class="selector-info">
      <label for="componentSelect"><i data-lucide="layers"></i> {{ __('qc.studio.select_component') }}</label>
      <div class="selector-wrapper">
        <select id="componentSelect" class="form-select-lg">
          <option value="output_shaft" selected>
            {{ __('qc.studio.comp_output_shaft') }}
          </option>
          <option value="hydraulic_cylinder" disabled>
            {{ __('qc.studio.comp_waiting_template', ['name' => 'Hydraulic Cylinder Rod']) }}
          </option>
          <option value="drive_pinion" disabled>
            {{ __('qc.studio.comp_waiting_template', ['name' => 'Drive Pinion Gear']) }}
          </option>
          <option value="bearing_journal" disabled>
            {{ __('qc.studio.comp_waiting_template', ['name' => 'Bearing Journal Sleeve']) }}
          </option>
          <option value="connecting_rod" disabled>
            {{ __('qc.studio.comp_waiting_template', ['name' => 'Connecting Rod']) }}
          </option>
        </select>
        <span class="badge badge-green" style="font-size: 0.75rem; padding: 0.35rem 0.6rem;">{{ __('qc.studio.status_active') }}</span>
      </div>
    </div>
    <div class="selector-meta">
      <span class="meta-item"><i data-lucide="file-check"></i> Form: <strong>{{ $report?->doc_no ?? 'FORM-QC-002' }} (Rev {{ $report?->rev ?? '04' }})</strong></span>
      <span class="meta-item"><i data-lucide="hash"></i> SSC ID: <strong id="top_badge_sscId" style="color: var(--primary-text);">{{ $report?->ssc_id ?? ($nextSscId ?? 'SSC SBY-5779') }}</strong></span>
      <span class="meta-item"><i data-lucide="file-text"></i> {{ __('qc.studio.report_no') }}: <strong id="top_badge_reportNo">{{ $report?->report_no ?? ($nextReportNo ?? '0743') }}</strong></span>
      <span class="meta-item"><i data-lucide="eye"></i> {{ __('qc.studio.live_preview') }}: <strong style="color: var(--success);">{{ __('qc.studio.status_active') }}</strong></span>
    </div>
  </div>

  <div class="notice-box">
    <i data-lucide="info" style="color: var(--primary-text); flex-shrink: 0;"></i>
    <span>
      {{ __('qc.studio.notice_text') }}
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
            {{ __('qc.studio.tab_1') }}
          </button>
          <button type="button" class="s-tab-btn" data-target="stab-dimensi">
            {{ __('qc.studio.tab_2') }}
          </button>
          <button type="button" class="s-tab-btn" data-target="stab-foto">
            {{ __('qc.studio.tab_3') }}
            <span class="tab-photo-counter" id="photoCountBadge">0</span>
          </button>
          <button type="button" class="s-tab-btn" data-target="stab-tir">
            {{ __('qc.studio.tab_4') }}
          </button>
          <button type="button" class="s-tab-btn" data-target="stab-rekomendasi">
            {{ __('qc.studio.tab_5') }}
          </button>
          <button type="button" class="s-tab-btn" data-target="stab-ttd">
            {{ __('qc.studio.tab_6') }}
          </button>
        </div>

        <form id="qcMainForm" class="studio-form">
          <input type="hidden" id="reportId" value="{{ isset($report) && $report ? $report->id : '' }}">

          <!-- TAB 1: IDENTITAS & JOB ORDER -->
          <div class="s-tab-content active" id="stab-identitas">

            <!-- Kontrol Dokumen Form Header (Input Manual) -->
            <div class="form-section-title">
              <i data-lucide="file-check"></i>
              <h4>{{ __('qc.studio.sec_doc_control') }}</h4>
            </div>
            <p style="font-size: 0.8rem; color: var(--text-subtle); margin-bottom: 0.75rem;">
              {{ __('qc.studio.sec_doc_control_desc') }}
            </p>

            <div class="form-grid-2 doc-control-box">
              <div class="form-group">
                <label>{{ __('qc.studio.lbl_doc_no') }}</label>
                <input type="text" id="g_docNo" class="mono-input" placeholder="FORM-QC-002" value="{{ $report?->doc_no ?? 'FORM-QC-002' }}">
              </div>
              <div class="form-group">
                <label>{{ __('qc.studio.lbl_responsibility') }}</label>
                <input type="text" id="g_responsibility" placeholder="QC Inspector" value="{{ $report?->responsibility ?? 'QC Inspector' }}">
              </div>
              <div class="form-group">
                <label>{{ __('qc.studio.lbl_effective_date') }}</label>
                <input type="date" id="g_effectiveDate" value="{{ $report && $report->effective_date ? \Carbon\Carbon::parse($report->effective_date)->format('Y-m-d') : date('Y-m-d') }}">
              </div>
              <div class="form-group">
                <div style="display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 0.5rem;">
                  <div>
                    <label>{{ __('qc.studio.lbl_rev') }}</label>
                    <input type="text" id="g_rev" class="mono-input" placeholder="04" value="{{ $report?->rev ?? '04' }}">
                  </div>
                  <div>
                    <label>{{ __('qc.studio.lbl_page') }}</label>
                    <input type="text" id="g_page" class="mono-input" placeholder="1" value="{{ $report->page ?? '1' }}">
                  </div>
                </div>
              </div>
            </div>

            <div class="form-section-title">
              <i data-lucide="clipboard-list"></i>
              <h4>{{ __('qc.studio.sec_identity') }}</h4>
            </div>

            <div class="form-grid-2">
              <div class="form-group">
                <label>{{ __('qc.studio.lbl_ssc_id') }} <span class="required">*</span></label>
                <input type="text" id="g_sscId" required placeholder="SSC SBY-5779" class="mono-input" value="{{ $report?->ssc_id ?? ($nextSscId ?? 'SSC SBY-5779') }}">
              </div>
              <div class="form-group">
                <label>{{ __('qc.studio.lbl_report_no') }}</label>
                <input type="text" id="g_reportNo" placeholder="0743" class="mono-input" value="{{ $report?->report_no ?? ($nextReportNo ?? '0743') }}">
              </div>
              <div class="form-group">
                <label>{{ __('qc.studio.lbl_inspection_date') }}</label>
                <input type="date" id="g_date" value="{{ $report && $report->date ? \Carbon\Carbon::parse($report->date)->format('Y-m-d') : date('Y-m-d') }}">
              </div>
              <div class="form-group">
                <label>{{ __('qc.studio.lbl_inspection_type') }}</label>
                <select id="g_inspectionType">
                  <option value="Pre" {{ ($report?->inspection_type ?? 'Pre') === 'Pre' ? 'selected' : '' }}>{{ __('qc.studio.opt_pre') }}</option>
                  <option value="Post" {{ ($report?->inspection_type ?? '') === 'Post' ? 'selected' : '' }}>{{ __('qc.studio.opt_post') }}</option>
                </select>
              </div>
              <div class="form-group full-width">
                <label>{{ __('qc.studio.lbl_customer') }}</label>
                <input type="text" id="g_customer" value="{{ $report?->customer ?? 'AMMAN MINERAL NUSA TENGGARA, PT' }}">
              </div>
              <div class="form-group">
                <label>{{ __('qc.studio.lbl_part_name') }}</label>
                <input type="text" id="g_partName" value="{{ $report?->part_name ?? 'OUTPUT SHAFT' }}" placeholder="OUTPUT SHAFT">
              </div>
              <div class="form-group">
                <label>{{ __('qc.studio.lbl_model_unit') }}</label>
                <input type="text" id="g_modelUnit" placeholder="HT051" value="{{ $report?->model_unit ?? 'HT051' }}">
              </div>
              <div class="form-group">
                <label>{{ __('qc.studio.lbl_job_no') }}</label>
                <input type="text" id="g_jobNo" placeholder="WO-2026-001" value="{{ $report?->job_no ?? '-' }}">
              </div>
              <div class="form-group">
                <label>{{ __('qc.studio.lbl_quote_no') }}</label>
                <input type="text" id="g_quoteNo" placeholder="-" value="{{ $report?->quote_no ?? '-' }}">
              </div>
              <div class="form-group">
                <label>{{ __('qc.studio.lbl_part_no') }}</label>
                <input type="text" id="g_partNo" placeholder="-" value="{{ $report?->part_no ?? '-' }}">
              </div>
            </div>
          </div>

          <!-- TAB 2: NILAI DIMENSI -->
          <div class="s-tab-content" id="stab-dimensi">
            <div class="form-section-title">
              <i data-lucide="ruler"></i>
              <h4>{{ __('qc.studio.dim_title') }}</h4>
            </div>

            <!-- Item 01 -->
            <div class="inspection-block">
              <div class="block-header">
                <div>
                  <strong>01. OD Area "A" and OD Spline</strong>
                  <span class="spec-tag">Spec: Ø79 ± 0.25</span>
                </div>
                <div class="status-selector">
                  <label>{{ __('qc.common.status') }}:</label>
                  <select id="i1_ar" class="status-select">
                    <option value="A">A (Accept)</option>
                    <option value="R">R (Reject)</option>
                  </select>
                </div>
              </div>
              <div class="input-triple-row">
                <div class="input-col">
                  <label>{{ __('qc.studio.val_x') }}</label>
                  <input type="text" id="i1_x" class="dim-input" placeholder="mm...">
                </div>
                <div class="input-col">
                  <label>{{ __('qc.studio.val_y') }}</label>
                  <input type="text" id="i1_y" class="dim-input" placeholder="mm...">
                </div>
                <div class="input-col">
                  <label>{{ __('qc.studio.val_z') }}</label>
                  <input type="text" id="i1_z" class="dim-input" placeholder="mm...">
                </div>
              </div>
              <div class="input-sub-row">
                <div class="input-col-6">
                  <label>{{ __('qc.studio.total_length_l') }}</label>
                  <input type="text" id="i1_length" class="dim-input" placeholder="724.0">
                </div>
                <div class="input-col-6">
                  <label>{{ __('qc.studio.remark_area_a') }}</label>
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
                <div class="sub-dim-title">{{ __('qc.studio.area_b_title') }}</div>
                <div class="table-compact-wrapper">
                  <table class="table-compact">
                    <thead>
                      <tr>
                        <th>{{ __('qc.studio.col_position') }}</th>
                        <th>X (mm)</th>
                        <th>Y (mm)</th>
                        <th>Z (mm)</th>
                        <th>{{ __('qc.common.status') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td><strong>{{ __('qc.studio.row_n', ['n' => 1]) }}</strong></td>
                        <td><input type="text" id="i2_b1_x" class="dim-input" placeholder="mm..."></td>
                        <td><input type="text" id="i2_b1_y" class="dim-input" placeholder="mm..."></td>
                        <td><input type="text" id="i2_b1_z" class="dim-input" placeholder="mm..."></td>
                        <td><select id="i2_b1_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                      </tr>
                      <tr>
                        <td><strong>{{ __('qc.studio.row_n', ['n' => 2]) }}</strong></td>
                        <td><input type="text" id="i2_b2_x" class="dim-input" placeholder="mm..."></td>
                        <td><input type="text" id="i2_b2_y" class="dim-input" placeholder="mm..."></td>
                        <td><input type="text" id="i2_b2_z" class="dim-input" placeholder="mm..."></td>
                        <td><select id="i2_b2_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                      </tr>
                      <tr>
                        <td><strong>{{ __('qc.studio.row_n', ['n' => 3]) }}</strong></td>
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
                <div class="sub-dim-title">{{ __('qc.studio.area_c_title') }}</div>
                <div class="table-compact-wrapper">
                  <table class="table-compact">
                    <thead>
                      <tr>
                        <th>{{ __('qc.studio.col_position') }}</th>
                        <th>X (mm)</th>
                        <th>Y (mm)</th>
                        <th>Z (mm)</th>
                        <th>{{ __('qc.common.status') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td><strong>{{ __('qc.studio.row_n', ['n' => 1]) }}</strong></td>
                        <td><input type="text" id="i2_c1_x" class="dim-input" placeholder="mm..."></td>
                        <td><input type="text" id="i2_c1_y" class="dim-input" placeholder="mm..."></td>
                        <td><input type="text" id="i2_c1_z" class="dim-input" placeholder="mm..."></td>
                        <td><select id="i2_c1_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                      </tr>
                      <tr>
                        <td><strong>{{ __('qc.studio.row_n', ['n' => 2]) }}</strong></td>
                        <td><input type="text" id="i2_c2_x" class="dim-input" placeholder="mm..."></td>
                        <td><input type="text" id="i2_c2_y" class="dim-input" placeholder="mm..."></td>
                        <td><input type="text" id="i2_c2_z" class="dim-input" placeholder="mm..."></td>
                        <td><select id="i2_c2_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                      </tr>
                      <tr>
                        <td><strong>{{ __('qc.studio.row_n', ['n' => 3]) }}</strong></td>
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
                  <strong>03. OD Area "D" &amp; Distance "E"</strong>
                  <span class="spec-tag">D: Ø118 ± 0.5 | E: 13 ± 0.5</span>
                </div>
              </div>
              <div class="form-grid-2">
                <div>
                  <label><strong>{{ __('qc.studio.area_d_label') }}</strong></label>
                  <div class="input-triple-row" style="margin-top: 0.35rem;">
                    <input type="text" id="i3_d_x" class="dim-input" placeholder="X mm">
                    <input type="text" id="i3_d_y" class="dim-input" placeholder="Y mm">
                    <input type="text" id="i3_d_z" class="dim-input" placeholder="Z mm">
                    <select id="i3_d_ar" class="status-select" style="width: 65px;"><option value="A">A</option><option value="R">R</option></select>
                  </div>
                </div>
                <div>
                  <label><strong>{{ __('qc.studio.dist_e_label') }}</strong></label>
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
                  <strong>04. OD Area "F" &amp; Distance "K"</strong>
                  <span class="spec-tag">F: Ø100,01 ± 0,008 | K: 188.55</span>
                </div>
              </div>
              <table class="table-compact">
                <thead>
                  <tr>
                    <th>{{ __('qc.studio.pos_area_f') }}</th>
                    <th>X (mm)</th>
                    <th>Y (mm)</th>
                    <th>Z (mm)</th>
                    <th>{{ __('qc.common.status') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>{{ __('qc.studio.row_n', ['n' => 1]) }}</td>
                    <td><input type="text" id="i4_f1_x" class="dim-input" placeholder="mm..."></td>
                    <td><input type="text" id="i4_f1_y" class="dim-input" placeholder="mm..."></td>
                    <td><input type="text" id="i4_f1_z" class="dim-input" placeholder="mm..."></td>
                    <td><select id="i4_f1_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                  </tr>
                  <tr>
                    <td>{{ __('qc.studio.row_n', ['n' => 2]) }}</td>
                    <td><input type="text" id="i4_f2_x" class="dim-input" placeholder="mm..."></td>
                    <td><input type="text" id="i4_f2_y" class="dim-input" placeholder="mm..."></td>
                    <td><input type="text" id="i4_f2_z" class="dim-input" placeholder="mm..."></td>
                    <td><select id="i4_f2_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                  </tr>
                  <tr>
                    <td>{{ __('qc.studio.row_n', ['n' => 3]) }}</td>
                    <td><input type="text" id="i4_f3_x" class="dim-input" placeholder="mm..."></td>
                    <td><input type="text" id="i4_f3_y" class="dim-input" placeholder="mm..."></td>
                    <td><input type="text" id="i4_f3_z" class="dim-input" placeholder="mm..."></td>
                    <td><select id="i4_f3_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                  </tr>
                </tbody>
              </table>
              <div class="form-group" style="margin-top: 0.5rem;">
                <label>{{ __('qc.studio.dist_k_label') }}</label>
                <input type="text" id="i4_k_res" class="dim-input" placeholder="188.55">
              </div>
            </div>

            <!-- Item 05 -->
            <div class="inspection-block">
              <div class="block-header">
                <div>
                  <strong>05. OD Area "H" &amp; Distance "J"</strong>
                  <span class="spec-tag">H: Ø94 ± 0,013 | J: 142.80</span>
                </div>
              </div>
              <table class="table-compact">
                <thead>
                  <tr>
                    <th>{{ __('qc.studio.pos_area_h') }}</th>
                    <th>X (mm)</th>
                    <th>Y (mm)</th>
                    <th>Z (mm)</th>
                    <th>{{ __('qc.common.status') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>{{ __('qc.studio.row_n', ['n' => 1]) }}</td>
                    <td><input type="text" id="i5_h1_x" class="dim-input" placeholder="mm..."></td>
                    <td><input type="text" id="i5_h1_y" class="dim-input" placeholder="mm..."></td>
                    <td><input type="text" id="i5_h1_z" class="dim-input" placeholder="mm..."></td>
                    <td><select id="i5_h1_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                  </tr>
                  <tr>
                    <td>{{ __('qc.studio.row_n', ['n' => 2]) }}</td>
                    <td><input type="text" id="i5_h2_x" class="dim-input" placeholder="mm..."></td>
                    <td><input type="text" id="i5_h2_y" class="dim-input" placeholder="mm..."></td>
                    <td><input type="text" id="i5_h2_z" class="dim-input" placeholder="mm..."></td>
                    <td><select id="i5_h2_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                  </tr>
                  <tr>
                    <td>{{ __('qc.studio.row_n', ['n' => 3]) }}</td>
                    <td><input type="text" id="i5_h3_x" class="dim-input" placeholder="mm..."></td>
                    <td><input type="text" id="i5_h3_y" class="dim-input" placeholder="mm..."></td>
                    <td><input type="text" id="i5_h3_z" class="dim-input" placeholder="mm..."></td>
                    <td><select id="i5_h3_ar" class="status-select"><option value="A">A</option><option value="R">R</option></select></td>
                  </tr>
                </tbody>
              </table>
              <div class="form-group" style="margin-top: 0.5rem;">
                <label>{{ __('qc.studio.dist_j_label') }}</label>
                <input type="text" id="i5_j_res" class="dim-input" placeholder="142.80">
              </div>
            </div>

            <!-- Item 06 -->
            <div class="inspection-block">
              <div class="block-header">
                <div>
                  <strong>06. OD Area "G" &amp; Spline</strong>
                  <span class="spec-tag">Spec: Ø84,2 ± 0,13 | I: 98.78</span>
                </div>
                <div class="status-selector">
                  <label>{{ __('qc.common.status') }}:</label>
                  <select id="i6_ar" class="status-select">
                    <option value="A">A (Accept)</option>
                    <option value="R">R (Reject)</option>
                  </select>
                </div>
              </div>
              <div class="input-triple-row">
                <div class="input-col">
                  <label>{{ __('qc.studio.val_x') }}</label>
                  <input type="text" id="i6_x" class="dim-input" placeholder="mm...">
                </div>
                <div class="input-col">
                  <label>{{ __('qc.studio.val_y') }}</label>
                  <input type="text" id="i6_y" class="dim-input" placeholder="mm...">
                </div>
                <div class="input-col">
                  <label>{{ __('qc.studio.val_z') }}</label>
                  <input type="text" id="i6_z" class="dim-input" placeholder="mm...">
                </div>
              </div>
              <div class="form-group" style="margin-top: 0.5rem;">
                <label>{{ __('qc.studio.dist_i_label') }}</label>
                <input type="text" id="i6_i_res" class="dim-input" placeholder="98.78">
              </div>
            </div>

          </div>

          <!-- TAB 3: UPLOAD FOTO PER BAGIAN -->
          <div class="s-tab-content" id="stab-foto">
            <div class="form-section-title">
              <i data-lucide="camera"></i>
              <h4>{{ __('qc.studio.photo_tab_title') }}</h4>
            </div>
            <p style="font-size: 0.82rem; color: var(--text-subtle); margin-bottom: 1rem;">
              {{ __('qc.studio.photo_tab_desc') }}
            </p>

            <!-- LEMBAR 1 -->
            <div class="area-photo-section">
              <div class="area-photo-section-header">
                <span><i data-lucide="file-text"></i> {{ __('qc.studio.photo_sheet_1') }}</span>
                <span class="photo-tag">{{ __('qc.studio.sheet', ['n' => 1]) }} (6 Slot)</span>
              </div>
              <div class="area-photo-section-body">
                <!-- Drawing -->
                <div class="area-subgroup">
                  <div class="area-subgroup-title">
                    <span>{{ __('qc.studio.photo_blueprint_tag') }}</span>
                    <span class="subgroup-tag">1 Slot</span>
                  </div>
                  <div class="slot-large-card {{ !empty($photos['drawing_dimension']) ? 'has-image' : '' }}" id="card_slot_drawing_dimension">
                    <input type="file" id="input_slot_drawing_dimension" accept="image/*" style="display:none;" onchange="handleSlotUpload(this, 'drawing_dimension')">
                    <input type="file" id="camera_slot_drawing_dimension" accept="image/*" capture="environment" style="display:none;" onchange="handleSlotUpload(this, 'drawing_dimension')">
                    <div class="slot-large-dropzone" onclick="triggerFileInput('input_slot_drawing_dimension')" id="dropzone_slot_drawing_dimension">
                      <div id="view_slot_drawing_dimension">
                        @if(!empty($photos['drawing_dimension']))
                          <img src="{{ asset($photos['drawing_dimension']) }}" alt="Drawing Dimensi">
                        @else
                          <div class="slot-empty-icon"><i data-lucide="image-plus"></i><span>{{ __('qc.studio.photo_empty_blueprint') }}</span></div>
                        @endif
                      </div>
                    </div>
                    <div class="slot-quick-action">
                      <button type="button" class="btn btn-primary btn-xs" onclick="triggerCameraInput('camera_slot_drawing_dimension')">
                        <i data-lucide="camera"></i> {{ __('qc.studio.photo_camera') }}
                      </button>
                      <button type="button" class="btn btn-secondary btn-xs" onclick="triggerFileInput('input_slot_drawing_dimension')">
                        <i data-lucide="folder-image"></i> {{ __('qc.studio.photo_gallery') }}
                      </button>
                      <button type="button" class="btn btn-secondary btn-xs" onclick="useDefaultBlueprint('drawing_dimension')">
                        <i data-lucide="copy"></i> {{ __('qc.studio.photo_use_blueprint') }}
                      </button>
                      <button type="button" class="btn btn-secondary btn-xs" id="btn_rem_slot_drawing_dimension" onclick="removeSlotPhoto('drawing_dimension')" style="{{ !empty($photos['drawing_dimension']) ? '' : 'display:none;' }}">
                        <i data-lucide="trash-2"></i> {{ __('qc.studio.photo_remove') }}
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Finding -->
                <div class="area-subgroup">
                  <div class="area-subgroup-title">
                    <span>{{ __('qc.studio.photo_finding_tag') }}</span>
                    <span class="subgroup-tag">1 Slot</span>
                  </div>
                  <div class="slot-large-card {{ !empty($photos['finding_component']) ? 'has-image' : '' }}" id="card_slot_finding_component">
                    <input type="file" id="input_slot_finding_component" accept="image/*" style="display:none;" onchange="handleSlotUpload(this, 'finding_component')">
                    <input type="file" id="camera_slot_finding_component" accept="image/*" capture="environment" style="display:none;" onchange="handleSlotUpload(this, 'finding_component')">
                    <div class="slot-large-dropzone" onclick="triggerCameraInput('camera_slot_finding_component')" id="dropzone_slot_finding_component" title="{{ __('qc.studio.photo_open_camera') }}">
                      <div id="view_slot_finding_component">
                        @if(!empty($photos['finding_component']))
                          <img src="{{ asset($photos['finding_component']) }}" alt="Foto Komponen Utuh">
                        @else
                          <div class="slot-empty-icon"><i data-lucide="camera"></i><span>{{ __('qc.studio.photo_empty_finding') }}</span></div>
                        @endif
                      </div>
                    </div>
                    <div class="slot-quick-action">
                      <button type="button" class="btn btn-primary btn-xs" onclick="triggerCameraInput('camera_slot_finding_component')">
                        <i data-lucide="camera"></i> {{ __('qc.studio.photo_open_camera') }}
                      </button>
                      <button type="button" class="btn btn-secondary btn-xs" onclick="triggerFileInput('input_slot_finding_component')">
                        <i data-lucide="folder-image"></i> {{ __('qc.studio.photo_pick_gallery') }}
                      </button>
                      <button type="button" class="btn btn-secondary btn-xs" id="btn_rem_slot_finding_component" onclick="removeSlotPhoto('finding_component')" style="{{ !empty($photos['finding_component']) ? '' : 'display:none;' }}">
                        <i data-lucide="trash-2"></i> {{ __('qc.studio.photo_remove') }}
                      </button>
                    </div>
                  </div>
                </div>

                <div class="area-subgroup">
                  <div class="area-subgroup-title">
                    <span>3. OD Area "A" and OD Spline — Visual</span>
                    <span class="subgroup-tag">4 Slot</span>
                  </div>
                  <div class="slot-grid-4">
                    {!! $renderUploadSlot('area_a_visual_1', 'Photo 1') !!}
                    {!! $renderUploadSlot('area_a_visual_2', 'Photo 2') !!}
                    {!! $renderUploadSlot('area_a_visual_3', 'Photo 3') !!}
                    {!! $renderUploadSlot('area_a_visual_4', 'Photo 4') !!}
                  </div>
                </div>
              </div>
            </div>

            <!-- LEMBAR 2 -->
            <div class="area-photo-section">
              <div class="area-photo-section-header">
                <span><i data-lucide="file-text"></i> {{ __('qc.studio.photo_sheet_2') }}</span>
                <span class="photo-tag">{{ __('qc.studio.sheet', ['n' => 2]) }} (12 Slot)</span>
              </div>
              <div class="area-photo-section-body">
                <div class="area-subgroup">
                  <div class="area-subgroup-title">
                    <span>1. Penetrant Check OD Area "A" and OD Spline</span>
                    <span class="subgroup-tag">4 Slot (NDT)</span>
                  </div>
                  <div class="slot-grid-4">
                    {!! $renderUploadSlot('area_a_penet_1', 'Penetrant 1') !!}
                    {!! $renderUploadSlot('area_a_penet_2', 'Penetrant 2') !!}
                    {!! $renderUploadSlot('area_a_penet_3', 'Penetrant 3') !!}
                    {!! $renderUploadSlot('area_a_penet_4', 'Penetrant 4') !!}
                  </div>
                </div>
                <div class="area-subgroup">
                  <div class="area-subgroup-title">
                    <span>2. OD Area "B and C" — Visual</span>
                    <span class="subgroup-tag">4 Slot</span>
                  </div>
                  <div class="slot-grid-4">
                    {!! $renderUploadSlot('area_bc_visual_1', 'Photo 1') !!}
                    {!! $renderUploadSlot('area_bc_visual_2', 'Photo 2') !!}
                    {!! $renderUploadSlot('area_bc_visual_3', 'Photo 3') !!}
                    {!! $renderUploadSlot('area_bc_visual_4', 'Photo 4') !!}
                  </div>
                </div>
                <div class="area-subgroup">
                  <div class="area-subgroup-title">
                    <span>3. Penetrant Check OD Area "B and C"</span>
                    <span class="subgroup-tag">4 Slot (NDT)</span>
                  </div>
                  <div class="slot-grid-4">
                    {!! $renderUploadSlot('area_bc_penet_1', 'Penetrant 1') !!}
                    {!! $renderUploadSlot('area_bc_penet_2', 'Penetrant 2') !!}
                    {!! $renderUploadSlot('area_bc_penet_3', 'Penetrant 3') !!}
                    {!! $renderUploadSlot('area_bc_penet_4', 'Penetrant 4') !!}
                  </div>
                </div>
              </div>
            </div>

            <!-- LEMBAR 3 -->
            <div class="area-photo-section">
              <div class="area-photo-section-header">
                <span><i data-lucide="file-text"></i> {{ __('qc.studio.photo_sheet_3') }}</span>
                <span class="photo-tag">{{ __('qc.studio.sheet', ['n' => 3]) }} (12 Slot)</span>
              </div>
              <div class="area-photo-section-body">
                <div class="area-subgroup">
                  <div class="area-subgroup-title">
                    <span>1. OD Area "D" — Visual</span>
                    <span class="subgroup-tag">4 Slot</span>
                  </div>
                  <div class="slot-grid-4">
                    {!! $renderUploadSlot('area_d_visual_1', 'Photo 1') !!}
                    {!! $renderUploadSlot('area_d_visual_2', 'Photo 2') !!}
                    {!! $renderUploadSlot('area_d_visual_3', 'Photo 3') !!}
                    {!! $renderUploadSlot('area_d_visual_4', 'Photo 4') !!}
                  </div>
                </div>
                <div class="area-subgroup">
                  <div class="area-subgroup-title">
                    <span>2. Penetrant Check OD Area "D"</span>
                    <span class="subgroup-tag">4 Slot (NDT)</span>
                  </div>
                  <div class="slot-grid-4">
                    {!! $renderUploadSlot('area_d_penet_1', 'Penetrant 1') !!}
                    {!! $renderUploadSlot('area_d_penet_2', 'Penetrant 2') !!}
                    {!! $renderUploadSlot('area_d_penet_3', 'Penetrant 3') !!}
                    {!! $renderUploadSlot('area_d_penet_4', 'Penetrant 4') !!}
                  </div>
                </div>
                <div class="area-subgroup">
                  <div class="area-subgroup-title">
                    <span>3. OD Area "F" — Visual</span>
                    <span class="subgroup-tag">4 Slot</span>
                  </div>
                  <div class="slot-grid-4">
                    {!! $renderUploadSlot('area_f_visual_1', 'Photo 1') !!}
                    {!! $renderUploadSlot('area_f_visual_2', 'Photo 2') !!}
                    {!! $renderUploadSlot('area_f_visual_3', 'Photo 3') !!}
                    {!! $renderUploadSlot('area_f_visual_4', 'Photo 4') !!}
                  </div>
                </div>
              </div>
            </div>

            <!-- LEMBAR 4 -->
            <div class="area-photo-section">
              <div class="area-photo-section-header">
                <span><i data-lucide="file-text"></i> {{ __('qc.studio.photo_sheet_4') }}</span>
                <span class="photo-tag">{{ __('qc.studio.sheet', ['n' => 4]) }} (12 Slot)</span>
              </div>
              <div class="area-photo-section-body">
                <div class="area-subgroup">
                  <div class="area-subgroup-title">
                    <span>1. Penetrant Check OD Area "F"</span>
                    <span class="subgroup-tag">4 Slot (NDT)</span>
                  </div>
                  <div class="slot-grid-4">
                    {!! $renderUploadSlot('area_f_penet_1', 'Penetrant 1') !!}
                    {!! $renderUploadSlot('area_f_penet_2', 'Penetrant 2') !!}
                    {!! $renderUploadSlot('area_f_penet_3', 'Penetrant 3') !!}
                    {!! $renderUploadSlot('area_f_penet_4', 'Penetrant 4') !!}
                  </div>
                </div>
                <div class="area-subgroup">
                  <div class="area-subgroup-title">
                    <span>2. OD Area "H" — Visual</span>
                    <span class="subgroup-tag">4 Slot</span>
                  </div>
                  <div class="slot-grid-4">
                    {!! $renderUploadSlot('area_h_visual_1', 'Photo 1') !!}
                    {!! $renderUploadSlot('area_h_visual_2', 'Photo 2') !!}
                    {!! $renderUploadSlot('area_h_visual_3', 'Photo 3') !!}
                    {!! $renderUploadSlot('area_h_visual_4', 'Photo 4') !!}
                  </div>
                </div>
                <div class="area-subgroup">
                  <div class="area-subgroup-title">
                    <span>3. Penetrant Check OD Area "H"</span>
                    <span class="subgroup-tag">4 Slot (NDT)</span>
                  </div>
                  <div class="slot-grid-4">
                    {!! $renderUploadSlot('area_h_penet_1', 'Penetrant 1') !!}
                    {!! $renderUploadSlot('area_h_penet_2', 'Penetrant 2') !!}
                    {!! $renderUploadSlot('area_h_penet_3', 'Penetrant 3') !!}
                    {!! $renderUploadSlot('area_h_penet_4', 'Penetrant 4') !!}
                  </div>
                </div>
              </div>
            </div>

            <!-- LEMBAR 5 -->
            <div class="area-photo-section">
              <div class="area-photo-section-header">
                <span><i data-lucide="file-text"></i> {{ __('qc.studio.photo_sheet_5') }}</span>
                <span class="photo-tag">{{ __('qc.studio.sheet', ['n' => 5]) }} (9 Slot)</span>
              </div>
              <div class="area-photo-section-body">
                <div class="area-subgroup">
                  <div class="area-subgroup-title">
                    <span>1. OD Area "G" and OD Spline — Visual</span>
                    <span class="subgroup-tag">4 Slot</span>
                  </div>
                  <div class="slot-grid-4">
                    {!! $renderUploadSlot('area_g_visual_1', 'Photo 1') !!}
                    {!! $renderUploadSlot('area_g_visual_2', 'Photo 2') !!}
                    {!! $renderUploadSlot('area_g_visual_3', 'Photo 3') !!}
                    {!! $renderUploadSlot('area_g_visual_4', 'Photo 4') !!}
                  </div>
                </div>
                <div class="area-subgroup">
                  <div class="area-subgroup-title">
                    <span>2. Penetrant Check Area OD Spline</span>
                    <span class="subgroup-tag">4 Slot (NDT)</span>
                  </div>
                  <div class="slot-grid-4">
                    {!! $renderUploadSlot('area_g_penet_1', 'Penetrant 1') !!}
                    {!! $renderUploadSlot('area_g_penet_2', 'Penetrant 2') !!}
                    {!! $renderUploadSlot('area_g_penet_3', 'Penetrant 3') !!}
                    {!! $renderUploadSlot('area_g_penet_4', 'Penetrant 4') !!}
                  </div>
                </div>
                <!-- TIR Diagram -->
                <div class="area-subgroup">
                  <div class="area-subgroup-title">
                    <span>{{ __('qc.studio.photo_tir_tag') }}</span>
                    <span class="subgroup-tag">1 Slot</span>
                  </div>
                  <div class="slot-large-card {{ !empty($photos['tir_diagram']) ? 'has-image' : '' }}" id="card_slot_tir_diagram">
                    <input type="file" id="input_slot_tir_diagram" accept="image/*" style="display:none;" onchange="handleSlotUpload(this, 'tir_diagram')">
                    <input type="file" id="camera_slot_tir_diagram" accept="image/*" capture="environment" style="display:none;" onchange="handleSlotUpload(this, 'tir_diagram')">
                    <div class="slot-large-dropzone" onclick="triggerFileInput('input_slot_tir_diagram')" id="dropzone_slot_tir_diagram">
                      <div id="view_slot_tir_diagram">
                        @if(!empty($photos['tir_diagram']))
                          <img src="{{ asset($photos['tir_diagram']) }}" alt="TIR Diagram">
                        @else
                          <div class="slot-empty-icon"><i data-lucide="image-plus"></i><span>{{ __('qc.studio.photo_empty_tir') }}</span></div>
                        @endif
                      </div>
                    </div>
                    <div class="slot-quick-action">
                      <button type="button" class="btn btn-primary btn-xs" onclick="triggerCameraInput('camera_slot_tir_diagram')">
                        <i data-lucide="camera"></i> {{ __('qc.studio.photo_camera') }}
                      </button>
                      <button type="button" class="btn btn-secondary btn-xs" onclick="triggerFileInput('input_slot_tir_diagram')">
                        <i data-lucide="folder-image"></i> {{ __('qc.studio.photo_gallery') }}
                      </button>
                      <button type="button" class="btn btn-secondary btn-xs" onclick="useDefaultBlueprint('tir_diagram')">
                        <i data-lucide="copy"></i> {{ __('qc.studio.photo_use_tir') }}
                      </button>
                      <button type="button" class="btn btn-secondary btn-xs" id="btn_rem_slot_tir_diagram" onclick="removeSlotPhoto('tir_diagram')" style="{{ !empty($photos['tir_diagram']) ? '' : 'display:none;' }}">
                        <i data-lucide="trash-2"></i> {{ __('qc.studio.photo_remove') }}
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <!-- TAB 4: RUNOUT / TIR -->
          <div class="s-tab-content" id="stab-tir">
            <div class="form-section-title">
              <i data-lucide="activity"></i>
              <h4>{{ __('qc.studio.tir_title') }}</h4>
            </div>
            
            <div class="datum-banner">
              <span><strong>{{ __('qc.studio.tir_datum') }}</strong> Datum at item "B and E"</span>
              <span class="spec-tag">{{ __('qc.studio.tir_max') }}</span>
            </div>

            <div class="table-compact-wrapper">
              <table class="table-compact">
              <thead>
                <tr>
                  <th style="width: 50px;">{{ __('qc.studio.tir_point') }}</th>
                  <th>{{ __('qc.studio.tir_limit') }}</th>
                  <th>0° (mm)</th>
                  <th>90° (mm)</th>
                  <th>180° (mm)</th>
                  <th>270° (mm)</th>
                  <th style="width: 70px;">{{ __('qc.common.status') }}</th>
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
          </div>

          <!-- TAB 5: REKOMENDASI -->
          <div class="s-tab-content" id="stab-rekomendasi">
            <div class="form-section-title" style="display: flex; justify-content: space-between; align-items: center;">
              <div>
                <i data-lucide="check-square"></i>
                <h4 style="display: inline; margin-left: 0.5rem;">{{ __('qc.studio.sec_rec_title') }}</h4>
              </div>
              <button type="button" class="btn btn-secondary btn-sm" id="btnAddRecRow">
                <i data-lucide="plus"></i> {{ __('qc.studio.add_rec_item') }}
              </button>
            </div>

            <div class="table-compact-wrapper">
              <table class="table-compact" style="margin-top: 0.5rem;">
              <thead>
                <tr>
                  <th style="width: 115px; text-align: center;">{{ __('qc.studio.rec_col_item') }}</th>
                  <th>{{ __('qc.studio.rec_col_remarks') }}</th>
                  <th>{{ __('qc.studio.rec_col_action') }}</th>
                  <th style="width: 45px; text-align: center;">{{ __('qc.studio.rec_col_act') }}</th>
                </tr>
              </thead>
              <tbody id="recTableBody">
                <!-- Dynamically filled -->
              </tbody>
            </table>
            </div>
          </div>

          <!-- TAB 6: PENGESAHAN -->
          <div class="s-tab-content" id="stab-ttd">
            <div class="form-section-title">
              <i data-lucide="user-check"></i>
              <h4>{{ __('qc.studio.sec_sign_title') }}</h4>
            </div>

            <div class="form-grid-3">
              <div class="sig-box">
                <h5>Checked by:</h5>
                <div class="form-group">
                  <label for="sig_checkedName">{{ __('qc.studio.sig_checker_name') }}</label>
                  <select id="sig_checkedName">
                    <option value="">{{ __('qc.studio.sig_pick_checker') }}</option>
                    @foreach($userList as $u)
                      <option value="{{ $u->name }}" data-user-id="{{ $u->id }}" data-user-role="{{ $u->role }}" {{ $checkedName !== '' && strcasecmp($checkedName, $u->name) === 0 ? 'selected' : '' }}>
                        {{ $u->name }}
                      </option>
                    @endforeach
                    @if($checkedName !== '' && !$userList->contains(fn($u) => strcasecmp($checkedName, $u->name) === 0))
                      <option value="{{ $checkedName }}" selected>{{ $checkedName }}</option>
                    @endif
                  </select>
                </div>
                <div class="form-group">
                  <label for="sig_checkedTitle">{{ __('qc.studio.sig_title') }}</label>
                  <select id="sig_checkedTitle">
                    <option value="">{{ __('qc.studio.sig_pick_title') }}</option>
                    <option value="QC Inspector" {{ $checkedTitle === 'QC Inspector' ? 'selected' : '' }}>QC Inspector</option>
                    <option value="Leader QC" {{ $checkedTitle === 'Leader QC' ? 'selected' : '' }}>Leader QC</option>
                    <option value="Workshop Supervisor" {{ $checkedTitle === 'Workshop Supervisor' ? 'selected' : '' }}>Workshop Supervisor</option>
                    <option value="QC Engineer" {{ $checkedTitle === 'QC Engineer' ? 'selected' : '' }}>QC Engineer</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="sig_checkedDate">{{ __('qc.studio.sig_date') }}</label>
                  <input type="date" id="sig_checkedDate" value="{{ $checkedDate }}">
                </div>
              </div>

              <div class="sig-box">
                <h5>Created by:</h5>
                <div class="form-group">
                  <label for="sig_createdName">{{ __('qc.studio.sig_creator_name') }}</label>
                  <select id="sig_createdName">
                    <option value="">{{ __('qc.studio.sig_pick_creator') }}</option>
                    @foreach($userList as $u)
                      <option value="{{ $u->name }}" data-user-id="{{ $u->id }}" data-user-role="{{ $u->role }}" {{ $createdName !== '' && strcasecmp($createdName, $u->name) === 0 ? 'selected' : '' }}>
                        {{ $u->name }}
                      </option>
                    @endforeach
                    @if($createdName !== '' && !$userList->contains(fn($u) => strcasecmp($createdName, $u->name) === 0))
                      <option value="{{ $createdName }}" selected>{{ $createdName }}</option>
                    @endif
                  </select>
                </div>
                <div class="form-group">
                  <label for="sig_createdTitle">{{ __('qc.studio.sig_title') }}</label>
                  <select id="sig_createdTitle">
                    <option value="">{{ __('qc.studio.sig_pick_title') }}</option>
                    <option value="QC Inspector" {{ $createdTitle === 'QC Inspector' ? 'selected' : '' }}>QC Inspector</option>
                    <option value="Leader QC" {{ $createdTitle === 'Leader QC' ? 'selected' : '' }}>Leader QC</option>
                    <option value="Workshop Supervisor" {{ $createdTitle === 'Workshop Supervisor' ? 'selected' : '' }}>Workshop Supervisor</option>
                    <option value="QC Engineer" {{ $createdTitle === 'QC Engineer' ? 'selected' : '' }}>QC Engineer</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="sig_createdDate">{{ __('qc.studio.sig_date') }}</label>
                  <input type="date" id="sig_createdDate" value="{{ $createdDate }}">
                </div>
              </div>

              <div class="sig-box">
                <h5>Reviewed by:</h5>
                <div class="form-group">
                  <label for="sig_reviewedName">{{ __('qc.studio.sig_reviewer_name') }}</label>
                  <select id="sig_reviewedName">
                    <option value="">{{ __('qc.studio.sig_pick_reviewer') }}</option>
                    @foreach($userList as $u)
                      @php
                        $isReviewedSelected = (isset($report) && $report && $report->reviewer_id)
                          ? ((int) $report->reviewer_id === (int) $u->id)
                          : ($reviewedName !== '' && strcasecmp($reviewedName, $u->name) === 0);
                      @endphp
                      <option value="{{ $u->name }}" data-user-id="{{ $u->id }}" data-user-role="{{ $u->role }}" {{ $isReviewedSelected ? 'selected' : '' }}>
                        {{ $u->name }}
                      </option>
                    @endforeach
                    @if($reviewedName !== '' && !$userList->contains(fn($u) => strcasecmp($reviewedName, $u->name) === 0))
                      <option value="{{ $reviewedName }}" selected>{{ $reviewedName }}</option>
                    @endif
                  </select>
                </div>
                <div class="form-group">
                  <label for="sig_reviewedTitle">{{ __('qc.studio.sig_title') }}</label>
                  <select id="sig_reviewedTitle">
                    <option value="">{{ __('qc.studio.sig_pick_title') }}</option>
                    <option value="Leader QC" {{ $reviewedTitle === 'Leader QC' ? 'selected' : '' }}>Leader QC</option>
                    <option value="Workshop Supervisor" {{ $reviewedTitle === 'Workshop Supervisor' ? 'selected' : '' }}>Workshop Supervisor</option>
                    <option value="QC Inspector" {{ $reviewedTitle === 'QC Inspector' ? 'selected' : '' }}>QC Inspector</option>
                    <option value="QC Engineer" {{ $reviewedTitle === 'QC Engineer' ? 'selected' : '' }}>QC Engineer</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="sig_reviewedDate">{{ __('qc.studio.sig_date') }}</label>
                  <input type="date" id="sig_reviewedDate" value="{{ $reviewedDate }}">
                </div>
              </div>
            </div>
          </div>

        </form>

        <!-- Studio Footer Actions -->
        <div class="studio-footer">
          <div class="footer-left">
            <button type="button" class="btn btn-secondary btn-sm" id="btnResetBlank">
              <i data-lucide="rotate-ccw"></i> {{ __('qc.studio.clear_form') }}
            </button>
          </div>
          <div class="footer-right">
            @if($isDraftReport)
              <button type="button" class="btn btn-secondary btn-sm" id="btnFooterDraft">
                <i data-lucide="file-clock"></i> {{ __('qc.studio.save_draft') }}
              </button>
            @endif
            <button type="button" class="btn btn-primary btn-sm" id="btnFooterSubmit">
              <i data-lucide="{{ $isDraftReport ? 'send' : 'save' }}"></i> {{ $isDraftReport ? __('qc.studio.submit_report') : __('qc.common.save_changes') }}
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
          <span><span class="hide-mobile">{{ __('qc.studio.live_preview') }} &mdash; </span><strong>FORM-QC-002</strong></span>
        </div>
        <div class="preview-actions">
          <button type="button" class="btn btn-secondary btn-xs" id="btnPrintPreview" title="{{ __('qc.studio.print_pdf') }}" onclick="window.exportOfficialPdf()">
            <i data-lucide="printer"></i> <span class="hide-mobile">{{ __('qc.studio.print_pdf') }}</span>
          </button>
          <button type="button" class="btn btn-danger btn-xs btn-close-preview" id="btnCloseMobilePreview" onclick="window.closeMobilePreview()" title="{{ __('qc.common.close') }}">
            <i data-lucide="x"></i> {{ __('qc.common.close') }}
          </button>
        </div>
      </div>

      <!-- Slide / Page Navigation Switcher -->
      <div class="preview-page-tabs">
        <button type="button" class="p-page-btn active" data-page="1" onclick="switchPreviewPage(1)">{{ __('qc.studio.sheet', ['n' => 1]) }}</button>
        <button type="button" class="p-page-btn" data-page="2" onclick="switchPreviewPage(2)">{{ __('qc.studio.sheet', ['n' => 2]) }}</button>
        <button type="button" class="p-page-btn" data-page="3" onclick="switchPreviewPage(3)">{{ __('qc.studio.sheet', ['n' => 3]) }}</button>
        <button type="button" class="p-page-btn" data-page="4" onclick="switchPreviewPage(4)">{{ __('qc.studio.sheet', ['n' => 4]) }}</button>
        <button type="button" class="p-page-btn" data-page="5" onclick="switchPreviewPage(5)">{{ __('qc.studio.sheet', ['n' => 5]) }}</button>
        <button type="button" class="p-page-btn" data-page="6" onclick="switchPreviewPage(6)">{{ __('qc.studio.sheet', ['n' => 6]) }}</button>
        <button type="button" class="p-page-btn" data-page="all" onclick="switchPreviewPage('all')">{{ __('qc.studio.view_all_sheets') }}</button>
      </div>

      <!-- The Official 6-Sheet Replica Container -->
      <div class="sheet-container" id="printableSheet">

        <!-- ==============================================================
             LEMBAR 1 DARI 6 (PAGE 1)
        ============================================================== -->
        <div class="sheet-paper preview-sheet" id="prev_sheet_1">
          <div class="sheet-page-indicator">Halaman 1 dari 6</div>
          <!-- Header (Kop Dokumen) -->
          <table class="p-header-table">
            <tr>
              <td class="p-logo-cell" rowspan="2" style="width: 37%;">
                <img src="{{ asset('images/doc_assets/kop_logo_perfect.png') }}?v={{ file_exists(public_path('images/doc_assets/kop_logo_perfect.png')) ? filemtime(public_path('images/doc_assets/kop_logo_perfect.png')) : time() }}" alt="PT. SSC WORKS">
              </td>
              <td class="p-title-cell" style="width: 63%;">COMPONENT CONDITION REPORT</td>
            </tr>
            <tr>
              <td class="p-ctrl-cell" style="width: 63%;">
                <table class="p-ctrl-table">
                  <tr>
                    <td class="ctrl-lbl" style="width: 16.67%;">Document No</td>
                    <td class="ctrl-colon" style="width: 1.6%;">:</td>
                    <td class="ctrl-val bold" style="width: 31.73%;" id="prev_docNo">{{ $report?->doc_no ?? 'FORM-QC-002' }}</td>
                    <td class="ctrl-lbl" style="width: 16.67%;">Responsibility</td>
                    <td class="ctrl-colon" style="width: 1.6%;">:</td>
                    <td class="ctrl-val" style="width: 31.73%;" id="prev_responsibility">{{ $report?->responsibility ?? 'Quality Control' }}</td>
                  </tr>
                  <tr>
                    <td class="ctrl-lbl">Effective Date.</td>
                    <td class="ctrl-colon">:</td>
                    <td class="ctrl-val" style="padding: 0;">
                      <div class="ctrl-split-val">
                        <span class="ctrl-date-part bold" id="prev_effectiveDate">{{ $effDateStr ?? '28-Jan-2022' }}</span>
                        <span class="ctrl-rev-part">Rev. <strong id="prev_rev">{{ $report?->rev ?? '04' }}</strong></span>
                      </div>
                    </td>
                    <td class="ctrl-lbl">Page</td>
                    <td class="ctrl-colon">:</td>
                    <td class="ctrl-val" style="text-align: center; letter-spacing: 1px;" id="prev_page">
                      <strong>1</strong> &nbsp; of &nbsp; <strong>6</strong>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>

          <!-- Metadata Grid -->
          <table class="p-meta-table">
            <tr>
              <td class="lbl" style="width: 9%;">No.</td>
              <td class="col-c" style="width: 1%;">:</td>
              <td class="val mono" style="width: 7.5%; text-align: center;" id="prev_reportNo">{{ $report?->report_no ?? ($nextReportNo ?? '0743') }}</td>
              
              <td class="lbl" style="width: 5.5%;">Date</td>
              <td class="col-c" style="width: 1%;">:</td>
              <td class="val" style="width: 13%; text-align: center;" id="prev_metaDate">{{ $dateStr ?? date('d-M-y') }}</td>
              
              <td class="lbl" style="width: 10.5%;">Customer</td>
              <td class="col-c" style="width: 1%;">:</td>
              <td class="val bold" style="width: 20%; line-height: 1.25; font-size: 6.8pt;" id="prev_customer">{{ $report?->customer ?? 'AMMAN MINERAL NUSA TENGGARA, PT' }}</td>
              
              <td class="lbl" style="width: 10.5%;">Part Name</td>
              <td class="col-c" style="width: 1%;">:</td>
              <td class="val bold" style="width: 20%;" id="prev_partName">{{ $report?->part_name ?? 'OUTPUT SHAFT' }}</td>
            </tr>
            <tr>
              <td class="lbl">Inspection</td>
              <td class="col-c">:</td>
              <td class="val" colspan="4" style="padding-left: 6px;" id="prev_inspectionType">
                <span id="prev_preCheck">&#9745; Pre</span> &nbsp;&nbsp;&nbsp;&nbsp;
                <span id="prev_postCheck">&#9744; Post</span>
              </td>
              
              <td class="lbl">Job No.</td>
              <td class="col-c">:</td>
              <td class="val" id="prev_jobNo">{{ $report?->job_no ?? '-' }}</td>
              
              <td class="lbl">Model/Unit</td>
              <td class="col-c">:</td>
              <td class="val" id="prev_modelUnit">{{ $report?->model_unit ?? 'HT051' }}</td>
            </tr>
            <tr>
              <td class="lbl">SSC ID</td>
              <td class="col-c">:</td>
              <td class="val mono bold" colspan="4" style="padding-left: 6px; color:#0f172a;" id="prev_sscId">{{ $report?->ssc_id ?? ($nextSscId ?? 'SSC SBY-5779') }}</td>
              
              <td class="lbl">Quote No.</td>
              <td class="col-c">:</td>
              <td class="val" id="prev_quoteNo">{{ $report?->quote_no ?? '-' }}</td>
              
              <td class="lbl">Part No.</td>
              <td class="col-c">:</td>
              <td class="val" id="prev_partNo">{{ $report?->part_no ?? '-' }}</td>
            </tr>
          </table>

          <!-- Drawing / Pictures / Dimension -->
          <div class="p-bar-grey">Drawing / Pictures / Dimension</div>
          <div class="p-blueprint-box" id="prev_slot_drawing_dimension">
            @if(!empty($photos['drawing_dimension']))
              <img src="{{ asset($photos['drawing_dimension']) }}" alt="Drawing Dimensi">
            @else
              <div class="empty-blueprint-msg">[ Gambar Drawing / Pictures / Dimension Belum Diunggah ]</div>
            @endif
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
                  <div class="p-finding-img-wrap" id="prev_slot_finding_component">
                    @if(!empty($photos['finding_component']))
                      <img src="{{ asset($photos['finding_component']) }}" alt="Foto Komponen Utuh">
                    @else
                      <span class="empty-finding-msg">[ Foto Komponen Utuh Belum Diunggah ]</span>
                    @endif
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
                <div class="p-photo-slot" id="prev_slot_area_a_visual_1">{!! $renderSlot('area_a_visual_1', 'Foto 1') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_a_visual_2">{!! $renderSlot('area_a_visual_2', 'Foto 2') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_a_visual_3">{!! $renderSlot('area_a_visual_3', 'Foto 3') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_a_visual_4">{!! $renderSlot('area_a_visual_4', 'Foto 4') !!}</div>
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
              <div class="p-bullet-box" id="prev_i1_remark1">
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
          <div class="p-split-section" style="border-top: none;">
            <div class="p-split-left">
              <div class="p-col-header">Image of Penetrant Check OD Area "A" and OD Spline</div>
              <div class="p-photo-grid-2x2">
                <div class="p-photo-slot" id="prev_slot_area_a_penet_1">{!! $renderSlot('area_a_penet_1', 'Penetrant 1') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_a_penet_2">{!! $renderSlot('area_a_penet_2', 'Penetrant 2') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_a_penet_3">{!! $renderSlot('area_a_penet_3', 'Penetrant 3') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_a_penet_4">{!! $renderSlot('area_a_penet_4', 'Penetrant 4') !!}</div>
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
                <div class="p-photo-slot" id="prev_slot_area_bc_visual_1">{!! $renderSlot('area_bc_visual_1', 'Foto 1') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_bc_visual_2">{!! $renderSlot('area_bc_visual_2', 'Foto 2') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_bc_visual_3">{!! $renderSlot('area_bc_visual_3', 'Foto 3') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_bc_visual_4">{!! $renderSlot('area_bc_visual_4', 'Foto 4') !!}</div>
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
                <div class="p-photo-slot" id="prev_slot_area_bc_penet_1">{!! $renderSlot('area_bc_penet_1', 'Penetrant 1') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_bc_penet_2">{!! $renderSlot('area_bc_penet_2', 'Penetrant 2') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_bc_penet_3">{!! $renderSlot('area_bc_penet_3', 'Penetrant 3') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_bc_penet_4">{!! $renderSlot('area_bc_penet_4', 'Penetrant 4') !!}</div>
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
          <div class="p-bar-yellow" style="border-top: none;">OD Area "D"</div>
          <div class="p-split-section">
            <div class="p-split-left">
              <div class="p-col-header">Image of OD Area "D"</div>
              <div class="p-photo-grid-2x2">
                <div class="p-photo-slot" id="prev_slot_area_d_visual_1">{!! $renderSlot('area_d_visual_1', 'Foto 1') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_d_visual_2">{!! $renderSlot('area_d_visual_2', 'Foto 2') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_d_visual_3">{!! $renderSlot('area_d_visual_3', 'Foto 3') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_d_visual_4">{!! $renderSlot('area_d_visual_4', 'Foto 4') !!}</div>
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
                <div class="p-photo-slot" id="prev_slot_area_d_penet_1">{!! $renderSlot('area_d_penet_1', 'Penetrant 1') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_d_penet_2">{!! $renderSlot('area_d_penet_2', 'Penetrant 2') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_d_penet_3">{!! $renderSlot('area_d_penet_3', 'Penetrant 3') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_d_penet_4">{!! $renderSlot('area_d_penet_4', 'Penetrant 4') !!}</div>
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
                <div class="p-photo-slot" id="prev_slot_area_f_visual_1">{!! $renderSlot('area_f_visual_1', 'Foto 1') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_f_visual_2">{!! $renderSlot('area_f_visual_2', 'Foto 2') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_f_visual_3">{!! $renderSlot('area_f_visual_3', 'Foto 3') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_f_visual_4">{!! $renderSlot('area_f_visual_4', 'Foto 4') !!}</div>
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
          <div class="p-split-section" style="border-top: none;">
            <div class="p-split-left">
              <div class="p-col-header">Image of Penetrant Check OD Area "F"</div>
              <div class="p-photo-grid-2x2">
                <div class="p-photo-slot" id="prev_slot_area_f_penet_1">{!! $renderSlot('area_f_penet_1', 'Penetrant 1') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_f_penet_2">{!! $renderSlot('area_f_penet_2', 'Penetrant 2') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_f_penet_3">{!! $renderSlot('area_f_penet_3', 'Penetrant 3') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_f_penet_4">{!! $renderSlot('area_f_penet_4', 'Penetrant 4') !!}</div>
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
                <div class="p-photo-slot" id="prev_slot_area_h_visual_1">{!! $renderSlot('area_h_visual_1', 'Foto 1') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_h_visual_2">{!! $renderSlot('area_h_visual_2', 'Foto 2') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_h_visual_3">{!! $renderSlot('area_h_visual_3', 'Foto 3') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_h_visual_4">{!! $renderSlot('area_h_visual_4', 'Foto 4') !!}</div>
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
                <div class="p-photo-slot" id="prev_slot_area_h_penet_1">{!! $renderSlot('area_h_penet_1', 'Penetrant 1') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_h_penet_2">{!! $renderSlot('area_h_penet_2', 'Penetrant 2') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_h_penet_3">{!! $renderSlot('area_h_penet_3', 'Penetrant 3') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_h_penet_4">{!! $renderSlot('area_h_penet_4', 'Penetrant 4') !!}</div>
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
          <div class="p-bar-yellow" style="border-top: none;">OD Area "G" and OD Spline</div>
          <div class="p-split-section">
            <div class="p-split-left">
              <div class="p-col-header">Image of OD Spline</div>
              <div class="p-photo-grid-2x2">
                <div class="p-photo-slot" id="prev_slot_area_g_visual_1">{!! $renderSlot('area_g_visual_1', 'Foto 1') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_g_visual_2">{!! $renderSlot('area_g_visual_2', 'Foto 2') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_g_visual_3">{!! $renderSlot('area_g_visual_3', 'Foto 3') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_g_visual_4">{!! $renderSlot('area_g_visual_4', 'Foto 4') !!}</div>
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
                <div class="p-photo-slot" id="prev_slot_area_g_penet_1">{!! $renderSlot('area_g_penet_1', 'Penetrant 1') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_g_penet_2">{!! $renderSlot('area_g_penet_2', 'Penetrant 2') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_g_penet_3">{!! $renderSlot('area_g_penet_3', 'Penetrant 3') !!}</div>
                <div class="p-photo-slot" id="prev_slot_area_g_penet_4">{!! $renderSlot('area_g_penet_4', 'Penetrant 4') !!}</div>
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
          <div class="p-blueprint-box" id="prev_slot_tir_diagram">
            @if(!empty($photos['tir_diagram']))
              <img src="{{ asset($photos['tir_diagram']) }}" alt="Shaft TIR Blueprint">
            @else
              <div class="empty-blueprint-msg">[ Gambar Diagram Titik Ukur TIR Belum Diunggah ]</div>
            @endif
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
          <div class="p-page-top-content">
            <!-- RECOMENDATION -->
            <div class="p-bar-yellow" style="margin-top:0;">RECOMENDATION</div>
            <table class="p-rec-table">
            <thead>
              <tr><th style="width:40px;">Item</th><th>Remarks</th><th>Recomendation</th></tr>
            </thead>
            <tbody id="prev_recTableBody">
              <!-- Item 01 -->
              <tr>
                <td rowspan="4" style="text-align:center; font-weight:700; font-family:monospace; vertical-align:middle; border:1px solid #000; width:40px;">01</td>
                <td style="border:1px solid #000; padding:2.5px 5px;">Visual scratch at OD area "A"</td>
                <td style="border:1px solid #000; padding:2.5px 5px;">Cleaning and polish OD area "A"</td>
              </tr>
              <tr>
                <td style="border:1px solid #000; padding:2.5px 5px;">Visual corrosion area spline</td>
                <td style="border:1px solid #000; padding:2.5px 5px;">Cleaning and polish area spline</td>
              </tr>
              <tr>
                <td style="border:1px solid #000; padding:2.5px 5px;">Visual scratch at area surface</td>
                <td style="border:1px solid #000; padding:2.5px 5px;">Cleaning area surface</td>
              </tr>
              <tr>
                <td style="border:1px solid #000; padding:2.5px 5px;">Visual dirty at hole thread area surface</td>
                <td style="border:1px solid #000; padding:2.5px 5px;">Retap hole thread area surface</td>
              </tr>
              <!-- Item 02 -->
              <tr>
                <td rowspan="2" style="text-align:center; font-weight:700; font-family:monospace; vertical-align:middle; border:1px solid #000; width:40px;">02</td>
                <td style="border:1px solid #000; padding:2.5px 5px;">Visual corrosion at OD area "B"</td>
                <td style="border:1px solid #000; padding:2.5px 5px;">Cleaning OD area "B and C"</td>
              </tr>
              <tr>
                <td style="border:1px solid #000; padding:2.5px 5px;">Visual corrosion at OD area "C"</td>
                <td style="border:1px solid #000; padding:2.5px 5px;">Cleaning OD area "B and C"</td>
              </tr>
              <!-- Item 03 -->
              <tr>
                <td rowspan="2" style="text-align:center; font-weight:700; font-family:monospace; vertical-align:middle; border:1px solid #000; width:40px;">03</td>
                <td style="border:1px solid #000; padding:2.5px 5px;">Visual scratch at OD area "D"</td>
                <td style="border:1px solid #000; padding:2.5px 5px;">Cleaning and polish OD area "D"</td>
              </tr>
              <tr>
                <td style="border:1px solid #000; padding:2.5px 5px;">Visual worn out at area surface (see mark no 1)</td>
                <td style="border:1px solid #000; padding:2.5px 5px;">Repair worn out area surface</td>
              </tr>
              <!-- Item 04 -->
              <tr>
                <td style="text-align:center; font-weight:700; font-family:monospace; vertical-align:middle; border:1px solid #000; width:40px;">04</td>
                <td style="border:1px solid #000; padding:2.5px 5px;">Actual measurement OD area "F" in good condition but, Visual pitting and scratch OD area "F" (see mark no 1)</td>
                <td style="border:1px solid #000; padding:2.5px 5px;">Repair OD area "F" to specification</td>
              </tr>
              <!-- Item 05 -->
              <tr>
                <td rowspan="2" style="text-align:center; font-weight:700; font-family:monospace; vertical-align:middle; border:1px solid #000; width:40px;">05</td>
                <td style="border:1px solid #000; padding:2.5px 5px;">Actual measurement OD area "H" was undersize</td>
                <td style="border:1px solid #000; padding:2.5px 5px;">Repair OD area "H" to specification</td>
              </tr>
              <tr>
                <td style="border:1px solid #000; padding:2.5px 5px;">Visual pitting and scratch at OD area "H" (see mark no 1)</td>
                <td style="border:1px solid #000; padding:2.5px 5px;">Repair OD area "H" to specification</td>
              </tr>
              <!-- Item 06 -->
              <tr>
                <td rowspan="5" style="text-align:center; font-weight:700; font-family:monospace; vertical-align:middle; border:1px solid #000; width:40px;">06</td>
                <td style="border:1px solid #000; padding:2.5px 5px;">Actual measurement OD area "G" was undersize</td>
                <td style="border:1px solid #000; padding:2.5px 5px;">Repair Cleaning OD area "G" to specification</td>
              </tr>
              <tr>
                <td style="border:1px solid #000; padding:2.5px 5px;">Visual corrosion at OD area "G"</td>
                <td style="border:1px solid #000; padding:2.5px 5px;">Cleaning and polish area spline</td>
              </tr>
              <tr>
                <td style="border:1px solid #000; padding:2.5px 5px;">Visual corrosion area spline</td>
                <td style="border:1px solid #000; padding:2.5px 5px;">Cleaning and polish area surface</td>
              </tr>
              <tr>
                <td style="border:1px solid #000; padding:2.5px 5px;">Visual scratch and corrosion at area surface</td>
                <td style="border:1px solid #000; padding:2.5px 5px;">Retap hole thread area surface</td>
              </tr>
              <tr>
                <td style="border:1px solid #000; padding:2.5px 5px;">Visual dirty at hole thread area surface</td>
                <td style="border:1px solid #000; padding:2.5px 5px;">Retap hole thread area surface</td>
              </tr>
            </tbody>
          </table>
          </div><!-- /.p-page-top-content -->

          <!-- Signatures Block: Anchored at the bottom of Page 6 -->
          <div class="p-page-bottom-signatures">
            <table class="p-sig-table">
              <thead>
                <tr><th>Checked by:</th><th>Created by:</th><th>Reviewed by:</th></tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <div class="sig-name" id="prev_sigCheckedName">{{ !empty($checkedName) ? $checkedName : '............................' }}</div>
                    <div class="sig-meta">
                      <span id="prev_sigCheckedTitle">{{ !empty($checkedTitle) ? $checkedTitle : 'QC Inspector' }}</span>
                      <span id="prev_sigCheckedDate">Date: {{ \Carbon\Carbon::parse($checkedDate)->format('F d, Y') }}</span>
                    </div>
                  </td>
                  <td>
                    <div class="sig-name" id="prev_sigCreatedName">{{ !empty($createdName) ? $createdName : '............................' }}</div>
                    <div class="sig-meta">
                      <span id="prev_sigCreatedTitle">{{ !empty($createdTitle) ? $createdTitle : 'QC Inspector' }}</span>
                      <span id="prev_sigCreatedDate">Date: {{ \Carbon\Carbon::parse($createdDate)->format('F d, Y') }}</span>
                    </div>
                  </td>
                  <td>
                    <div class="sig-name" id="prev_sigReviewedName">{{ !empty($reviewedName) ? $reviewedName : '............................' }}</div>
                    <div class="sig-meta">
                      <span id="prev_sigReviewedTitle">{{ !empty($reviewedTitle) ? $reviewedTitle : 'Leader QC' }}</span>
                      <span id="prev_sigReviewedDate">Date: {{ \Carbon\Carbon::parse($reviewedDate)->format('F d, Y') }}</span>
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
</div>

<!-- Floating Action Button for Mobile Preview -->
<button type="button" class="mobile-preview-fab" id="btnFabMobilePreview" onclick="window.openMobilePreview()" title="{{ __('qc.studio.preview_fab') }}">
  <i data-lucide="file-text"></i>
  <span>{{ __('qc.studio.preview_fab') }}</span>
</button>
@endsection

@section('scripts')
  <script>
    window.qcCurrentUserName = @json(auth()->user()->name ?? '');
    window.qcReportStatus = @json($reportStatus);
  </script>
  <script src="{{ asset('js/app.js') }}?v={{ file_exists(public_path('js/app.js')) ? filemtime(public_path('js/app.js')) : time() }}"></script>
@endsection
