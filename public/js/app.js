// Laravel CCR QC System Frontend App
let defaultTemplate = null;
let currentEditingId = null;

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// DOM Elements
const searchInput = document.getElementById('searchInput');
const typeFilter = document.getElementById('typeFilter');
const btnRefresh = document.getElementById('btnRefresh');
const btnCreateNew = document.getElementById('btnCreateNew');
const reportModal = document.getElementById('reportModal');
const btnModalClose = document.getElementById('btnModalClose');
const btnCancelModal = document.getElementById('btnCancelModal');
const btnSaveOnly = document.getElementById('btnSaveOnly');
const btnSubmitAndDownload = document.getElementById('btnSubmitAndDownload');
const modalTitle = document.getElementById('modalTitle');
const reportForm = document.getElementById('reportForm');
const recTableBody = document.getElementById('recTableBody');
const btnAddRecRow = document.getElementById('btnAddRecRow');
const toastContainer = document.getElementById('toastContainer');
const toggleBlueprint = document.getElementById('toggleBlueprint');
const blueprintContent = document.getElementById('blueprintContent');
const blueprintToggleText = document.getElementById('blueprintToggleText');

document.addEventListener('DOMContentLoaded', async () => {
    if (window.lucide) lucide.createIcons();
    setupEventListeners();
    setupToleranceAutoEvaluation();
    await fetchDefaultTemplate();
});

function setupEventListeners() {
    // Tabs Navigation
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-tab');
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            btn.classList.add('active');
            const targetEl = document.getElementById(targetId);
            if (targetEl) targetEl.classList.add('active');
        });
    });

    // Blueprint Schematic Toggle
    if (toggleBlueprint && blueprintContent) {
        toggleBlueprint.addEventListener('click', () => {
            const isHidden = blueprintContent.style.display === 'none';
            blueprintContent.style.display = isHidden ? 'block' : 'none';
            if (blueprintToggleText) {
                blueprintToggleText.textContent = isHidden ? 'sembunyikan' : 'tampilkan';
            }
        });
    }

    // Modal controls
    if (btnCreateNew) btnCreateNew.addEventListener('click', () => openCreateModal());
    if (btnModalClose) btnModalClose.addEventListener('click', () => closeModal());
    if (btnCancelModal) btnCancelModal.addEventListener('click', () => closeModal());

    // Filter controls
    if (searchInput) {
        let timer;
        searchInput.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => filterTableLocally(), 200);
        });
    }
    if (typeFilter) {
        typeFilter.addEventListener('change', () => filterTableLocally());
    }
    if (btnRefresh) {
        btnRefresh.addEventListener('click', () => window.location.reload());
    }

    // Dynamic recommendations
    if (btnAddRecRow) {
        btnAddRecRow.addEventListener('click', () => addRecommendationRow('', '', ''));
    }

    // Modal save buttons
    if (btnSaveOnly) btnSaveOnly.addEventListener('click', () => handleSave(false));
    if (btnSubmitAndDownload) btnSubmitAndDownload.addEventListener('click', () => handleSave(true));
}

// Live Tolerance Auto-Evaluation
function setupToleranceAutoEvaluation() {
    // Dimension Inputs
    document.querySelectorAll('.auto-eval').forEach(input => {
        input.addEventListener('input', () => evaluateDimensionInput(input));
    });

    // TIR Inputs
    document.querySelectorAll('.tir-eval').forEach(input => {
        input.addEventListener('input', () => evaluateTirInput(input));
    });

    // Select A/R change triggers tab status recalculation
    document.querySelectorAll('.meas-table select').forEach(sel => {
        sel.addEventListener('change', () => recalculateAllTabStatuses());
    });
}

function evaluateDimensionInput(input) {
    const nominal = parseFloat(input.dataset.specNominal);
    const tol = parseFloat(input.dataset.specTol);
    const val = parseFloat(input.value);

    if (isNaN(val) || input.value === '') {
        input.classList.remove('is-in-spec', 'is-out-of-spec');
        recalculateAllTabStatuses();
        return;
    }

    const min = nominal - tol;
    const max = nominal + tol;

    // Small rounding tolerance for floating point comparisons
    const inSpec = (val >= (min - 0.0001)) && (val <= (max + 0.0001));

    if (inSpec) {
        input.classList.add('is-in-spec');
        input.classList.remove('is-out-of-spec');
    } else {
        input.classList.add('is-out-of-spec');
        input.classList.remove('is-in-spec');

        // Automatically set nearest row A/R select to 'R' if out of spec
        const row = input.closest('tr');
        if (row) {
            const selectAr = row.querySelector('select');
            if (selectAr) {
                selectAr.value = 'R';
            }
        }
    }

    recalculateAllTabStatuses();
}

function evaluateTirInput(input) {
    const val = parseFloat(input.value);
    if (isNaN(val) || input.value === '') {
        input.classList.remove('is-in-spec', 'is-out-of-spec');
        recalculateAllTabStatuses();
        return;
    }

    // Max 0.2 mm
    const inSpec = val <= 0.2001;
    if (inSpec) {
        input.classList.add('is-in-spec');
        input.classList.remove('is-out-of-spec');
    } else {
        input.classList.add('is-out-of-spec');
        input.classList.remove('is-in-spec');

        const row = input.closest('tr');
        if (row) {
            const selectAr = row.querySelector('select');
            if (selectAr) selectAr.value = 'R';
        }
    }

    recalculateAllTabStatuses();
}

function recalculateAllTabStatuses() {
    const checkTab = (contentId, dotId) => {
        const content = document.getElementById(contentId);
        const dot = document.getElementById(dotId);
        if (!content || !dot) return;

        const hasOutOfSpec = content.querySelector('.is-out-of-spec') !== null;
        let hasRejectSelect = false;
        content.querySelectorAll('select').forEach(s => {
            if (s.value === 'R') hasRejectSelect = true;
        });

        dot.classList.remove('has-reject', 'all-accept');
        if (hasOutOfSpec || hasRejectSelect) {
            dot.classList.add('has-reject');
            dot.title = 'Terdapat parameter reject/out of spec';
        } else {
            dot.classList.add('all-accept');
            dot.title = 'Semua parameter terukur in-spec (Accept)';
        }
    };

    checkTab('tab-sec1', 'dot-sec1');
    checkTab('tab-sec2', 'dot-sec2');
    checkTab('tab-sec34', 'dot-sec34');
    checkTab('tab-sec56', 'dot-sec56');
    checkTab('tab-tir', 'dot-tir');
}

// Quick Preset Remark Helper
function appendRemark(targetId, text) {
    const el = document.getElementById(targetId);
    if (!el) return;
    if (!el.value) {
        el.value = text;
    } else if (!el.value.includes(text)) {
        el.value += '; ' + text;
    }
    showToast(`Remark "${text}" ditambahkan`, 'success');
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `<i data-lucide="${type === 'success' ? 'check-circle' : 'alert-circle'}"></i> <span>${message}</span>`;
    toastContainer.appendChild(toast);
    if (window.lucide) lucide.createIcons();
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        setTimeout(() => toast.remove(), 250);
    }, 3500);
}

async function fetchDefaultTemplate() {
    try {
        const res = await fetch('/api/default-template');
        defaultTemplate = await res.json();
    } catch (err) {
        console.error('Failed to load default template', err);
    }
}

function filterTableLocally() {
    const search = searchInput.value.toLowerCase().trim();
    const type = typeFilter.value;
    const rows = document.querySelectorAll('#reportTableBody tr');

    rows.forEach(tr => {
        const text = tr.innerText.toLowerCase();
        const rowType = tr.getAttribute('data-type') || '';
        const matchSearch = !search || text.includes(search);
        const matchType = type === 'All' || rowType === type;
        tr.style.display = (matchSearch && matchType) ? '' : 'none';
    });
}

// Download Excel
async function downloadExcel(id, sscId) {
    showToast(`Sedang menyiapkan file Excel ${sscId}...`, 'success');
    try {
        const response = await fetch(`/reports/${id}/export`);
        if (!response.ok) throw new Error('Gagal mengunduh file Excel');

        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${sscId || 'REPORT'} CCR PRE OUTPUT SHAFT.xlsx`;
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);
        showToast(`File Excel ${sscId} berhasil diunduh!`, 'success');
    } catch (err) {
        showToast(err.message, 'error');
    }
}

// Delete Report
async function deleteReport(id, sscId) {
    if (!confirm(`Konfirmasi: Apakah Anda yakin ingin menghapus data laporan "${sscId}"?`)) return;
    try {
        const res = await fetch(`/reports/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        if (!res.ok) throw new Error('Gagal menghapus laporan dari database');
        showToast('Laporan berhasil dihapus', 'success');
        setTimeout(() => window.location.reload(), 600);
    } catch (err) {
        showToast(err.message, 'error');
    }
}

// Duplicate Report
async function duplicateReport(id) {
    try {
        const res = await fetch(`/reports/${id}/duplicate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        if (!res.ok) throw new Error('Gagal menduplikat laporan');
        showToast('Laporan berhasil diduplikat!', 'success');
        setTimeout(() => window.location.reload(), 600);
    } catch (err) {
        showToast(err.message, 'error');
    }
}

// Open Modal Create
function openCreateModal() {
    currentEditingId = null;
    modalTitle.innerHTML = `<i data-lucide="plus-circle"></i> Buat Laporan Inspeksi Baru`;
    reportForm.reset();
    recTableBody.innerHTML = '';

    // Switch to first tab
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.querySelector('[data-tab="tab-general"]').classList.add('active');
    document.getElementById('tab-general').classList.add('active');

    if (defaultTemplate) {
        populateForm(defaultTemplate);
        document.getElementById('g_sscId').value = `SSC SBY-${Math.floor(1000 + Math.random() * 9000)}`;
        document.getElementById('g_reportNo').value = `0${Math.floor(700 + Math.random() * 200)}`;
        const todayStr = new Date().toISOString().split('T')[0];
        document.getElementById('g_date').value = todayStr;
        document.getElementById('sig_checkedDate').value = todayStr;
        document.getElementById('sig_createdDate').value = todayStr;
        document.getElementById('sig_reviewedDate').value = todayStr;
    }

    reportModal.classList.add('active');
    if (window.lucide) lucide.createIcons();

    // Trigger initial tolerance evaluations
    document.querySelectorAll('.auto-eval').forEach(input => evaluateDimensionInput(input));
    document.querySelectorAll('.tir-eval').forEach(input => evaluateTirInput(input));
}

// Open Modal Edit
async function openEditModal(id) {
    try {
        currentEditingId = id;
        reportForm.reset();
        recTableBody.innerHTML = '';

        const res = await fetch(`/reports/${id}`, {
            headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error('Data laporan tidak ditemukan di server');
        const data = await res.json();

        modalTitle.innerHTML = `<i data-lucide="file-edit"></i> Edit Laporan: <span class="mono" style="color:var(--primary); font-weight:700;">${data.ssc_id}</span>`;

        // Switch to first tab
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        document.querySelector('[data-tab="tab-general"]').classList.add('active');
        document.getElementById('tab-general').classList.add('active');

        populateFormFromModel(data);

        reportModal.classList.add('active');
        if (window.lucide) lucide.createIcons();

        // Run evaluation on populated values
        document.querySelectorAll('.auto-eval').forEach(input => evaluateDimensionInput(input));
        document.querySelectorAll('.tir-eval').forEach(input => evaluateTirInput(input));
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function closeModal() {
    reportModal.classList.remove('active');
}

function populateFormFromModel(data) {
    document.getElementById('reportId').value = data.id || '';

    // General
    document.getElementById('g_sscId').value = data.ssc_id || '';
    document.getElementById('g_reportNo').value = data.report_no || '';
    document.getElementById('g_date').value = data.date ? data.date.substring(0, 10) : '';
    document.getElementById('g_customer').value = data.customer || '';
    document.getElementById('g_partName').value = data.part_name || 'OUTPUT SHAFT';
    document.getElementById('g_inspectionType').value = data.inspection_type || 'Pre';
    document.getElementById('g_jobNo').value = data.job_no || '-';
    document.getElementById('g_modelUnit').value = data.model_unit || 'HT051';
    document.getElementById('g_quoteNo').value = data.quote_no || '-';
    document.getElementById('g_partNo').value = data.part_no || '-';

    // Parse sections
    const sec = typeof data.sections === 'string' ? JSON.parse(data.sections) : (data.sections || {});
    populateSections(sec);

    // Parse TIR
    const tir = typeof data.tir === 'string' ? JSON.parse(data.tir) : (data.tir || {});
    populateTir(tir);

    // Recommendations
    const recs = typeof data.recommendations === 'string' ? JSON.parse(data.recommendations) : (data.recommendations || []);
    recTableBody.innerHTML = '';
    if (recs.length === 0) {
        addRecommendationRow('01', 'Cleaning and polish OD area "A"', 'Cleaning and polish OD area "A"');
    } else {
        recs.forEach(r => addRecommendationRow(r.item, r.remarks, r.recommendation));
    }

    // Signatures
    const sig = typeof data.signatures === 'string' ? JSON.parse(data.signatures) : (data.signatures || {});
    populateSignatures(sig);
}

function populateForm(data) {
    document.getElementById('reportId').value = data.id || '';
    const g = data.general || {};
    document.getElementById('g_sscId').value = g.sscId || '';
    document.getElementById('g_reportNo').value = g.reportNo || '';
    document.getElementById('g_date').value = g.date || '';
    document.getElementById('g_customer').value = g.customer || '';
    document.getElementById('g_partName').value = g.partName || 'OUTPUT SHAFT';
    document.getElementById('g_inspectionType').value = g.inspectionType || 'Pre';
    document.getElementById('g_jobNo').value = g.jobNo || '-';
    document.getElementById('g_modelUnit').value = g.modelUnit || 'HT051';
    document.getElementById('g_quoteNo').value = g.quoteNo || '-';
    document.getElementById('g_partNo').value = g.partNo || '-';

    populateSections(data.sections || {});
    populateTir(data.tir || {});

    recTableBody.innerHTML = '';
    const recs = Array.isArray(data.recommendations) ? data.recommendations : [];
    if (recs.length === 0) {
        addRecommendationRow('01', 'Cleaning and polish OD area "A"', 'Cleaning and polish OD area "A"');
    } else {
        recs.forEach(r => addRecommendationRow(r.item, r.remarks, r.recommendation));
    }

    populateSignatures(data.signatures || {});
}

function populateSections(s) {
    const s1 = s.item01 || {};
    document.getElementById('s1_x').value = s1.x ?? 79.0;
    document.getElementById('s1_y').value = s1.y ?? 79.0;
    document.getElementById('s1_z').value = s1.z ?? 79.0;
    document.getElementById('s1_ar').value = s1.ar || 'A';
    document.getElementById('s1_lenResult').value = s1.lengthResult ?? 724.0;
    document.getElementById('s1_lenAr').value = s1.lengthAr || '-';
    document.getElementById('s1_rem1').value = s1.remark1 || '';
    document.getElementById('s1_rem2').value = s1.remark2 || '';
    document.getElementById('s1_rem3').value = s1.remark3 || '';
    document.getElementById('s1_rem4').value = s1.remark4 || '';
    document.getElementById('s1_rem5').value = s1.remark5 || '';
    document.getElementById('s1_rem6').value = s1.remark6 || '';
    document.getElementById('s1_penetrant').value = s1.penetrantRemark || '';

    const s2 = s.item02 || {};
    const b = s2.itemB || {};
    document.getElementById('s2_bx1').value = b.row1?.x ?? 82.0;
    document.getElementById('s2_by1').value = b.row1?.y ?? 82.0;
    document.getElementById('s2_bz1').value = b.row1?.z ?? 82.0;
    document.getElementById('s2_bar1').value = b.row1?.ar || 'A';
    document.getElementById('s2_bx2').value = b.row2?.x ?? 82.0;
    document.getElementById('s2_by2').value = b.row2?.y ?? 82.0;
    document.getElementById('s2_bz2').value = b.row2?.z ?? 82.0;
    document.getElementById('s2_bar2').value = b.row2?.ar || 'A';
    document.getElementById('s2_bx3').value = b.row3?.x ?? 82.0;
    document.getElementById('s2_by3').value = b.row3?.y ?? 82.0;
    document.getElementById('s2_bz3').value = b.row3?.z ?? 82.0;
    document.getElementById('s2_bar3').value = b.row3?.ar || 'A';

    const c = s2.itemC || {};
    document.getElementById('s2_cx1').value = c.row1?.x ?? 77.0;
    document.getElementById('s2_cy1').value = c.row1?.y ?? 77.0;
    document.getElementById('s2_cz1').value = c.row1?.z ?? 77.0;
    document.getElementById('s2_car1').value = c.row1?.ar || 'A';
    document.getElementById('s2_cx2').value = c.row2?.x ?? 77.0;
    document.getElementById('s2_cy2').value = c.row2?.y ?? 77.0;
    document.getElementById('s2_cz2').value = c.row2?.z ?? 77.0;
    document.getElementById('s2_car2').value = c.row2?.ar || 'A';
    document.getElementById('s2_cx3').value = c.row3?.x ?? 77.0;
    document.getElementById('s2_cy3').value = c.row3?.y ?? 77.0;
    document.getElementById('s2_cz3').value = c.row3?.z ?? 77.0;
    document.getElementById('s2_car3').value = c.row3?.ar || 'A';

    const s2rems = Array.isArray(s2.remarks) ? s2.remarks : [];
    document.getElementById('s2_rem1').value = s2rems[0] || '';
    document.getElementById('s2_rem2').value = s2rems[1] || '';
    document.getElementById('s2_rem3').value = s2rems[2] || '';
    document.getElementById('s2_rem4').value = s2rems[3] || '';
    document.getElementById('s2_penetrant').value = s2.penetrantRemark || '';

    const s3 = s.item03 || {};
    document.getElementById('s3_dx').value = s3.itemD?.x ?? 118.0;
    document.getElementById('s3_dy').value = s3.itemD?.y ?? 118.0;
    document.getElementById('s3_dz').value = s3.itemD?.z ?? 118.0;
    document.getElementById('s3_dar').value = s3.itemD?.ar || 'A';
    document.getElementById('s3_ex').value = s3.distanceE?.x ?? 13.0;
    document.getElementById('s3_ey').value = s3.distanceE?.y ?? 13.0;
    document.getElementById('s3_ez').value = s3.distanceE?.z ?? 13.0;
    document.getElementById('s3_ear').value = s3.distanceE?.ar || 'A';

    const s3rems = Array.isArray(s3.remarks) ? s3.remarks : [];
    document.getElementById('s3_rem1').value = s3rems[0] || '';
    document.getElementById('s3_rem2').value = s3rems[1] || '';
    document.getElementById('s3_rem3').value = s3rems[2] || '';
    document.getElementById('s3_rem4').value = s3rems[3] || '';
    document.getElementById('s3_penetrant').value = s3.penetrantRemark || '';

    const s4 = s.item04 || {};
    const f = s4.itemF || {};
    document.getElementById('s4_fx1').value = f.row1?.x ?? 100.01;
    document.getElementById('s4_fy1').value = f.row1?.y ?? 100.01;
    document.getElementById('s4_fz1').value = f.row1?.z ?? 100.01;
    document.getElementById('s4_far1').value = f.row1?.ar || 'A';
    document.getElementById('s4_fx2').value = f.row2?.x ?? 100.01;
    document.getElementById('s4_fy2').value = f.row2?.y ?? 100.01;
    document.getElementById('s4_fz2').value = f.row2?.z ?? 100.01;
    document.getElementById('s4_far2').value = f.row2?.ar || 'A';
    document.getElementById('s4_fx3').value = f.row3?.x ?? 100.01;
    document.getElementById('s4_fy3').value = f.row3?.y ?? 100.01;
    document.getElementById('s4_fz3').value = f.row3?.z ?? 100.01;
    document.getElementById('s4_far3').value = f.row3?.ar || 'A';
    document.getElementById('s4_kResult').value = s4.distanceK?.result ?? 188.55;
    document.getElementById('s4_kAr').value = s4.distanceK?.ar || '-';

    const s4rems = Array.isArray(s4.remarks) ? s4.remarks : [];
    document.getElementById('s4_rem1').value = s4rems[0] || '';
    document.getElementById('s4_rem2').value = s4rems[1] || '';
    document.getElementById('s4_penetrant').value = s4.penetrantRemark || '';

    const s5 = s.item05 || {};
    const h = s5.itemH || {};
    document.getElementById('s5_hx1').value = h.row1?.x ?? 94.0;
    document.getElementById('s5_hy1').value = h.row1?.y ?? 94.0;
    document.getElementById('s5_hz1').value = h.row1?.z ?? 94.0;
    document.getElementById('s5_har1').value = h.row1?.ar || 'A';
    document.getElementById('s5_hx2').value = h.row2?.x ?? 94.0;
    document.getElementById('s5_hy2').value = h.row2?.y ?? 94.0;
    document.getElementById('s5_hz2').value = h.row2?.z ?? 94.0;
    document.getElementById('s5_har2').value = h.row2?.ar || 'A';
    document.getElementById('s5_hx3').value = h.row3?.x ?? 94.0;
    document.getElementById('s5_hy3').value = h.row3?.y ?? 94.0;
    document.getElementById('s5_hz3').value = h.row3?.z ?? 94.0;
    document.getElementById('s5_har3').value = h.row3?.ar || 'A';
    document.getElementById('s5_jResult').value = s5.distanceJ?.result ?? 142.80;
    document.getElementById('s5_jAr').value = s5.distanceJ?.ar || '-';

    const s5rems = Array.isArray(s5.remarks) ? s5.remarks : [];
    document.getElementById('s5_rem1').value = s5rems[0] || '';
    document.getElementById('s5_rem2').value = s5rems[1] || '';
    document.getElementById('s5_penetrant').value = s5.penetrantRemark || '';

    const s6 = s.item06 || {};
    document.getElementById('s6_gx').value = s6.itemG?.x ?? 84.2;
    document.getElementById('s6_gy').value = s6.itemG?.y ?? 84.2;
    document.getElementById('s6_gz').value = s6.itemG?.z ?? 84.2;
    document.getElementById('s6_gar').value = s6.itemG?.ar || 'A';
    document.getElementById('s6_iResult').value = s6.distanceI?.result ?? 98.78;
    document.getElementById('s6_iAr').value = s6.distanceI?.ar || '-';

    const s6rems = Array.isArray(s6.remarks) ? s6.remarks : [];
    document.getElementById('s6_rem1').value = s6rems[0] || '';
    document.getElementById('s6_rem2').value = s6rems[1] || '';
    document.getElementById('s6_rem3').value = s6rems[2] || '';
    document.getElementById('s6_rem4').value = s6rems[3] || '';
    document.getElementById('s6_rem5').value = s6rems[4] || '';
    document.getElementById('s6_rem6').value = s6rems[5] || '';
    document.getElementById('s6_penetrant').value = s6.penetrantRemark || '';
}

function populateTir(tir) {
    document.getElementById('tir_datum').value = tir.datum || 'Datum at item "B and E"';
    const tirItems = Array.isArray(tir.items) ? tir.items : [];
    const getTir = (code) => tirItems.find(i => i.item === code) || { d0: '0', d90: '0', d180: '0', d270: '0', ar: 'A' };

    ['a', 'b', 'c', 'd', 'e', 'f'].forEach(k => {
        const itemVal = getTir(k.toUpperCase());
        document.getElementById(`tir_${k}_0`).value = itemVal.d0 ?? '0';
        document.getElementById(`tir_${k}_90`).value = itemVal.d90 ?? '0';
        document.getElementById(`tir_${k}_180`).value = itemVal.d180 ?? '0';
        document.getElementById(`tir_${k}_270`).value = itemVal.d270 ?? '0';
        document.getElementById(`tir_${k}_ar`).value = itemVal.ar || 'A';
    });
}

function populateSignatures(sig) {
    document.getElementById('sig_checkedName').value = sig.checkedBy?.name || 'Faqih S A A';
    document.getElementById('sig_checkedTitle').value = sig.checkedBy?.title || 'QC Inspector';
    document.getElementById('sig_checkedDate').value = sig.checkedBy?.date ? sig.checkedBy.date.substring(0, 10) : '';

    document.getElementById('sig_createdName').value = sig.createdBy?.name || 'M Kevin S';
    document.getElementById('sig_createdTitle').value = sig.createdBy?.title || 'QC Inspector';
    document.getElementById('sig_createdDate').value = sig.createdBy?.date ? sig.createdBy.date.substring(0, 10) : '';

    document.getElementById('sig_reviewedName').value = sig.reviewedBy?.name || 'Agus Riyanto';
    document.getElementById('sig_reviewedTitle').value = sig.reviewedBy?.title || 'Leader QC';
    document.getElementById('sig_reviewedDate').value = sig.reviewedBy?.date ? sig.reviewedBy.date.substring(0, 10) : '';
}

function addRecommendationRow(item = '01', remarks = '', recommendation = '') {
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="text" class="rec-item mono" value="${item || '01'}" style="text-align: center; font-weight:700;"></td>
        <td><input type="text" class="rec-remarks" value="${remarks || ''}" placeholder="Temuan visual atau kondisi aktual..."></td>
        <td><input type="text" class="rec-rec" value="${recommendation || ''}" placeholder="Rekomendasi tindakan teknis..."></td>
        <td style="text-align: center;">
            <button type="button" class="btn btn-secondary btn-icon btn-sm" onclick="this.closest('tr').remove()" title="Hapus baris">
                <i data-lucide="trash-2"></i>
            </button>
        </td>
    `;
    recTableBody.appendChild(tr);
    if (window.lucide) lucide.createIcons();
}

function collectFormData() {
    const sscId = document.getElementById('g_sscId').value.trim();
    if (!sscId) {
        showToast('Nomor SSC ID wajib diisi!', 'error');
        document.querySelector('[data-tab="tab-general"]').click();
        document.getElementById('g_sscId').focus();
        return null;
    }

    const recs = [];
    document.querySelectorAll('#recTableBody tr').forEach(tr => {
        const item = tr.querySelector('.rec-item').value.trim();
        const remarks = tr.querySelector('.rec-remarks').value.trim();
        const recommendation = tr.querySelector('.rec-rec').value.trim();
        if (remarks || recommendation) {
            recs.push({ item, remarks, recommendation });
        }
    });

    return {
        ssc_id: sscId,
        report_no: document.getElementById('g_reportNo').value.trim(),
        date: document.getElementById('g_date').value,
        customer: document.getElementById('g_customer').value.trim(),
        part_name: document.getElementById('g_partName').value.trim(),
        inspection_type: document.getElementById('g_inspectionType').value,
        job_no: document.getElementById('g_jobNo').value.trim(),
        model_unit: document.getElementById('g_modelUnit').value.trim(),
        quote_no: document.getElementById('g_quoteNo').value.trim(),
        part_no: document.getElementById('g_partNo').value.trim(),
        doc_no: 'FORM-QC-002',
        rev: '04',
        sections: {
            item01: {
                title: 'OD Area "A" and OD Spline',
                spec: 'Ø79 ± 0.25',
                x: parseFloat(document.getElementById('s1_x').value) || 0,
                y: parseFloat(document.getElementById('s1_y').value) || 0,
                z: parseFloat(document.getElementById('s1_z').value) || 0,
                ar: document.getElementById('s1_ar').value,
                lengthItem: 'L',
                lengthSpec: 'N/A',
                lengthResult: parseFloat(document.getElementById('s1_lenResult').value) || 0,
                lengthAr: document.getElementById('s1_lenAr').value,
                remark1: document.getElementById('s1_rem1').value.trim(),
                remark2: document.getElementById('s1_rem2').value.trim(),
                remark3: document.getElementById('s1_rem3').value.trim(),
                remark4: document.getElementById('s1_rem4').value.trim(),
                remark5: document.getElementById('s1_rem5').value.trim(),
                remark6: document.getElementById('s1_rem6').value.trim(),
                penetrantRemark: document.getElementById('s1_penetrant').value.trim()
            },
            item02: {
                title: 'OD Area "B and C"',
                itemB: {
                    spec: 'Ø82 ± 0.25',
                    row1: { x: parseFloat(document.getElementById('s2_bx1').value) || 0, y: parseFloat(document.getElementById('s2_by1').value) || 0, z: parseFloat(document.getElementById('s2_bz1').value) || 0, ar: document.getElementById('s2_bar1').value },
                    row2: { x: parseFloat(document.getElementById('s2_bx2').value) || 0, y: parseFloat(document.getElementById('s2_by2').value) || 0, z: parseFloat(document.getElementById('s2_bz2').value) || 0, ar: document.getElementById('s2_bar2').value },
                    row3: { x: parseFloat(document.getElementById('s2_bx3').value) || 0, y: parseFloat(document.getElementById('s2_by3').value) || 0, z: parseFloat(document.getElementById('s2_bz3').value) || 0, ar: document.getElementById('s2_bar3').value }
                },
                itemC: {
                    spec: 'Ø77 ± 0.25',
                    row1: { x: parseFloat(document.getElementById('s2_cx1').value) || 0, y: parseFloat(document.getElementById('s2_cy1').value) || 0, z: parseFloat(document.getElementById('s2_cz1').value) || 0, ar: document.getElementById('s2_car1').value },
                    row2: { x: parseFloat(document.getElementById('s2_cx2').value) || 0, y: parseFloat(document.getElementById('s2_cy2').value) || 0, z: parseFloat(document.getElementById('s2_cz2').value) || 0, ar: document.getElementById('s2_car2').value },
                    row3: { x: parseFloat(document.getElementById('s2_cx3').value) || 0, y: parseFloat(document.getElementById('s2_cy3').value) || 0, z: parseFloat(document.getElementById('s2_cz3').value) || 0, ar: document.getElementById('s2_car3').value }
                },
                remarks: [
                    document.getElementById('s2_rem1').value.trim(),
                    document.getElementById('s2_rem2').value.trim(),
                    document.getElementById('s2_rem3').value.trim(),
                    document.getElementById('s2_rem4').value.trim()
                ].filter(Boolean),
                penetrantRemark: document.getElementById('s2_penetrant').value.trim()
            },
            item03: {
                title: 'OD Area "D"',
                itemD: {
                    spec: 'Ø118 ± 0.5',
                    x: parseFloat(document.getElementById('s3_dx').value) || 0,
                    y: parseFloat(document.getElementById('s3_dy').value) || 0,
                    z: parseFloat(document.getElementById('s3_dz').value) || 0,
                    ar: document.getElementById('s3_dar').value
                },
                distanceE: {
                    spec: '13 ± 0.5',
                    x: parseFloat(document.getElementById('s3_ex').value) || 0,
                    y: parseFloat(document.getElementById('s3_ey').value) || 0,
                    z: parseFloat(document.getElementById('s3_ez').value) || 0,
                    ar: document.getElementById('s3_ear').value
                },
                remarks: [
                    document.getElementById('s3_rem1').value.trim(),
                    document.getElementById('s3_rem2').value.trim(),
                    document.getElementById('s3_rem3').value.trim(),
                    document.getElementById('s3_rem4').value.trim()
                ].filter(Boolean),
                penetrantRemark: document.getElementById('s3_penetrant').value.trim()
            },
            item04: {
                title: 'OD Area "F"',
                itemF: {
                    spec: 'Ø100,01 ± 0,008',
                    row1: { x: parseFloat(document.getElementById('s4_fx1').value) || 0, y: parseFloat(document.getElementById('s4_fy1').value) || 0, z: parseFloat(document.getElementById('s4_fz1').value) || 0, ar: document.getElementById('s4_far1').value },
                    row2: { x: parseFloat(document.getElementById('s4_fx2').value) || 0, y: parseFloat(document.getElementById('s4_fy2').value) || 0, z: parseFloat(document.getElementById('s4_fz2').value) || 0, ar: document.getElementById('s4_far2').value },
                    row3: { x: parseFloat(document.getElementById('s4_fx3').value) || 0, y: parseFloat(document.getElementById('s4_fy3').value) || 0, z: parseFloat(document.getElementById('s4_fz3').value) || 0, ar: document.getElementById('s4_far3').value }
                },
                distanceK: {
                    spec: 'N/A',
                    result: parseFloat(document.getElementById('s4_kResult').value) || 0,
                    ar: document.getElementById('s4_kAr').value
                },
                remarks: [
                    document.getElementById('s4_rem1').value.trim(),
                    document.getElementById('s4_rem2').value.trim()
                ].filter(Boolean),
                penetrantRemark: document.getElementById('s4_penetrant').value.trim()
            },
            item05: {
                title: 'OD Area "H"',
                itemH: {
                    spec: 'Ø94 ± 0,013',
                    row1: { x: parseFloat(document.getElementById('s5_hx1').value) || 0, y: parseFloat(document.getElementById('s5_hy1').value) || 0, z: parseFloat(document.getElementById('s5_hz1').value) || 0, ar: document.getElementById('s5_har1').value },
                    row2: { x: parseFloat(document.getElementById('s5_hx2').value) || 0, y: parseFloat(document.getElementById('s5_hy2').value) || 0, z: parseFloat(document.getElementById('s5_hz2').value) || 0, ar: document.getElementById('s5_har2').value },
                    row3: { x: parseFloat(document.getElementById('s5_hx3').value) || 0, y: parseFloat(document.getElementById('s5_hy3').value) || 0, z: parseFloat(document.getElementById('s5_hz3').value) || 0, ar: document.getElementById('s5_har3').value }
                },
                distanceJ: {
                    spec: 'N/A',
                    result: parseFloat(document.getElementById('s5_jResult').value) || 0,
                    ar: document.getElementById('s5_jAr').value
                },
                remarks: [
                    document.getElementById('s5_rem1').value.trim(),
                    document.getElementById('s5_rem2').value.trim()
                ].filter(Boolean),
                penetrantRemark: document.getElementById('s5_penetrant').value.trim()
            },
            item06: {
                title: 'OD Area "G" and OD Spline',
                itemG: {
                    spec: 'Ø84,2 ± 0,13',
                    x: parseFloat(document.getElementById('s6_gx').value) || 0,
                    y: parseFloat(document.getElementById('s6_gy').value) || 0,
                    z: parseFloat(document.getElementById('s6_gz').value) || 0,
                    ar: document.getElementById('s6_gar').value
                },
                distanceI: {
                    spec: 'N/A',
                    result: parseFloat(document.getElementById('s6_iResult').value) || 0,
                    ar: document.getElementById('s6_iAr').value
                },
                remarks: [
                    document.getElementById('s6_rem1').value.trim(),
                    document.getElementById('s6_rem2').value.trim(),
                    document.getElementById('s6_rem3').value.trim(),
                    document.getElementById('s6_rem4').value.trim(),
                    document.getElementById('s6_rem5').value.trim(),
                    document.getElementById('s6_rem6').value.trim()
                ].filter(Boolean),
                penetrantRemark: document.getElementById('s6_penetrant').value.trim()
            }
        },
        tir: {
            spec: 'Max 0,2',
            datum: document.getElementById('tir_datum').value.trim(),
            items: ['a', 'b', 'c', 'd', 'e', 'f'].map(k => ({
                item: k.toUpperCase(),
                d0: document.getElementById(`tir_${k}_0`).value.trim(),
                d90: document.getElementById(`tir_${k}_90`).value.trim(),
                d180: document.getElementById(`tir_${k}_180`).value.trim(),
                d270: document.getElementById(`tir_${k}_270`).value.trim(),
                ar: document.getElementById(`tir_${k}_ar`).value
            }))
        },
        recommendations: recs,
        signatures: {
            checkedBy: {
                name: document.getElementById('sig_checkedName').value.trim(),
                title: document.getElementById('sig_checkedTitle').value.trim(),
                date: document.getElementById('sig_checkedDate').value
            },
            createdBy: {
                name: document.getElementById('sig_createdName').value.trim(),
                title: document.getElementById('sig_createdTitle').value.trim(),
                date: document.getElementById('sig_createdDate').value
            },
            reviewedBy: {
                name: document.getElementById('sig_reviewedName').value.trim(),
                title: document.getElementById('sig_reviewedTitle').value.trim(),
                date: document.getElementById('sig_reviewedDate').value
            }
        }
    };
}

async function handleSave(downloadDirectly = false) {
    const payload = collectFormData();
    if (!payload) return;

    if (downloadDirectly) {
        showToast('Sedang menyimpan & men-generate file Excel FORM-QC-002...', 'success');
        try {
            const res = await fetch('/reports/submit-and-download', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            if (!res.ok) throw new Error('Gagal memproses data laporan ke server');

            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${payload.ssc_id} CCR PRE OUTPUT SHAFT.xlsx`;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);

            showToast('Laporan tersimpan di MySQL & Excel telah diunduh!', 'success');
            closeModal();
            setTimeout(() => window.location.reload(), 600);
        } catch (err) {
            showToast(err.message, 'error');
        }
    } else {
        try {
            const url = currentEditingId ? `/reports/${currentEditingId}` : '/reports';
            const method = currentEditingId ? 'PUT' : 'POST';

            const res = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            if (!res.ok) {
                const errData = await res.json().catch(() => ({}));
                throw new Error(errData.message || 'Gagal menyimpan laporan');
            }

            showToast('Laporan berhasil disimpan ke database!', 'success');
            closeModal();
            setTimeout(() => window.location.reload(), 600);
        } catch (err) {
            showToast(err.message, 'error');
        }
    }
}
