// Laravel CCR QC System Frontend App - Resilient Corporate Edition
let defaultTemplate = null;
let currentEditingId = null;

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// Embedded fallback template
const fallbackDefaultTemplate = {
  general: {
    docNo: "FORM-QC-002",
    rev: "04",
    reportNo: "",
    date: "",
    customer: "",
    partName: "OUTPUT SHAFT",
    inspectionType: "Pre",
    jobNo: "-",
    modelUnit: "HT051",
    sscId: "",
    quoteNo: "-",
    partNo: "-"
  },
  sections: {
    item01: {
      title: "OD Area \"A\" and OD Spline",
      spec: "Ø79 ± 0.25",
      x: 79.0, y: 79.0, z: 79.0, ar: "A",
      remark1: "Actual measurement OD area \"A\" in good condition",
      lengthItem: "L", lengthSpec: "N/A", lengthResult: 724.0, lengthAr: "-",
      penetrantRemark: "No indication crack at OD area \"A\" and OD spline after penetrant check"
    },
    item02: {
      title: "OD Area \"B and C\"",
      itemB: {
        spec: "Ø82 ± 0.25",
        row1: { x: 82.0, y: 82.0, z: 82.0, ar: "A" },
        row2: { x: 82.0, y: 82.0, z: 82.0, ar: "A" },
        row3: { x: 82.0, y: 82.0, z: 82.0, ar: "A" }
      },
      itemC: {
        spec: "Ø77 ± 0.25",
        row1: { x: 77.0, y: 77.0, z: 77.0, ar: "A" },
        row2: { x: 77.0, y: 77.0, z: 77.0, ar: "A" },
        row3: { x: 77.0, y: 77.0, z: 77.0, ar: "A" }
      },
      remarks: ["Actual measurement OD area \"B and C\" in good condition"],
      penetrantRemark: "No indication crack at OD Area \"B and C\" after penetrant check"
    },
    item03: {
      title: "OD Area \"D\"",
      itemD: { spec: "Ø118 ± 0.5", x: 118.0, y: 118.0, z: 118.0, ar: "A" },
      distanceE: { spec: "13 ± 0.5", x: 13.0, y: 13.0, z: 13.0, ar: "A" },
      remarks: ["Actual measurement OD area \"D\" in good condition", "Actual distance area \"E\" in good condition"],
      penetrantRemark: "No indication crack at OD area \"D\" after penetrant check"
    },
    item04: {
      title: "OD Area \"F\"",
      itemF: {
        spec: "Ø100,01 ± 0,008",
        row1: { x: 100.01, y: 100.01, z: 100.01, ar: "A" },
        row2: { x: 100.01, y: 100.01, z: 100.01, ar: "A" },
        row3: { x: 100.01, y: 100.01, z: 100.01, ar: "A" }
      },
      distanceK: { spec: "N/A", result: 188.55, ar: "-" },
      remarks: ["Actual measurement OD area \"F\" in good condition"],
      penetrantRemark: "No indication crack at area OD area \"F\" after penetrant check"
    },
    item05: {
      title: "OD Area \"H\"",
      itemH: {
        spec: "Ø94 ± 0,013",
        row1: { x: 94.0, y: 94.0, z: 94.0, ar: "A" },
        row2: { x: 94.0, y: 94.0, z: 94.0, ar: "A" },
        row3: { x: 94.0, y: 94.0, z: 94.0, ar: "A" }
      },
      distanceJ: { spec: "N/A", result: 142.8, ar: "-" },
      remarks: ["Actual measurement OD area \"H\" in good condition"],
      penetrantRemark: "No indication crack at area OD area \"H\" after penetrant check"
    },
    item06: {
      title: "OD Area \"G\" and OD Spline",
      itemG: { spec: "Ø84,2 ± 0,13", x: 84.2, y: 84.2, z: 84.2, ar: "A" },
      distanceI: { spec: "N/A", result: 98.78, ar: "-" },
      remarks: ["Actual measurement OD area \"G\" in good condition"],
      penetrantRemark: "No indication crack at area OD spline after penetrant check"
    }
  },
  tir: {
    spec: "Max 0,2",
    datum: "Datum at item \"B and E\"",
    items: [
      { item: "A", d0: "0", d90: "0", d180: "0", d270: "0", ar: "A" },
      { item: "B", d0: "0", d90: "0", d180: "0", d270: "0", ar: "A" },
      { item: "C", d0: "0", d90: "0", d180: "0", d270: "0", ar: "A" },
      { item: "D", d0: "0", d90: "0", d180: "0", d270: "0", ar: "A" },
      { item: "E", d0: "0", d90: "0", d180: "0", d270: "0", ar: "A" },
      { item: "F", d0: "0", d90: "0", d180: "0", d270: "0", ar: "A" }
    ]
  },
  recommendations: [
    { item: "01", remarks: "Cleaning and polish OD area \"A\"", recommendation: "Cleaning and polish OD area \"A\"" }
  ],
  signatures: {
    checkedBy: { name: "Faqih S A A", title: "QC Inspector", date: "" },
    createdBy: { name: "M Kevin S", title: "QC Inspector", date: "" },
    reviewedBy: { name: "Agus Riyanto", title: "Leader QC", date: "" }
  }
};

function initApp() {
    if (window.lucide) lucide.createIcons();
    setupEventListeners();
    setupToleranceAutoEvaluation();
    fetchDefaultTemplate();

    // Check URL parameters for direct modal open (?create=1 or ?new=1)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('create') === '1' || urlParams.get('new') === '1' || window.location.hash === '#create') {
        setTimeout(() => openCreateModal(), 60);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApp);
} else {
    initApp();
}

function setupEventListeners() {
    // Tabs Navigation
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = btn.getAttribute('data-tab');
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            btn.classList.add('active');
            const targetEl = document.getElementById(targetId);
            if (targetEl) targetEl.classList.add('active');
        });
    });

    // Blueprint Schematic Toggle
    const toggleBlueprint = document.getElementById('toggleBlueprint');
    const blueprintContent = document.getElementById('blueprintContent');
    const blueprintToggleText = document.getElementById('blueprintToggleText');
    if (toggleBlueprint && blueprintContent) {
        toggleBlueprint.addEventListener('click', () => {
            const isHidden = blueprintContent.style.display === 'none';
            blueprintContent.style.display = isHidden ? 'block' : 'none';
            if (blueprintToggleText) {
                blueprintToggleText.textContent = isHidden ? 'Sembunyikan' : 'Tampilkan';
            }
        });
    }

    // Modal controls
    const btnCreateNew = document.getElementById('btnCreateNew');
    const btnModalClose = document.getElementById('btnModalClose');
    const btnCancelModal = document.getElementById('btnCancelModal');
    const btnSaveOnly = document.getElementById('btnSaveOnly');
    const btnSubmitAndDownload = document.getElementById('btnSubmitAndDownload');
    const btnAddRecRow = document.getElementById('btnAddRecRow');
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    const btnRefresh = document.getElementById('btnRefresh');

    if (btnCreateNew) btnCreateNew.addEventListener('click', (e) => { e.preventDefault(); openCreateModal(); });
    if (btnModalClose) btnModalClose.addEventListener('click', (e) => { e.preventDefault(); closeModal(); });
    if (btnCancelModal) btnCancelModal.addEventListener('click', (e) => { e.preventDefault(); closeModal(); });

    // Filter controls
    if (searchInput) {
        let timer;
        searchInput.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => filterTableLocally(), 150);
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
    document.querySelectorAll('.auto-eval').forEach(input => {
        input.addEventListener('input', () => evaluateDimensionInput(input));
    });

    document.querySelectorAll('.tir-eval').forEach(input => {
        input.addEventListener('input', () => evaluateTirInput(input));
    });

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
    const inSpec = (val >= (min - 0.0001)) && (val <= (max + 0.0001));

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

function evaluateTirInput(input) {
    const val = parseFloat(input.value);
    if (isNaN(val) || input.value === '') {
        input.classList.remove('is-in-spec', 'is-out-of-spec');
        recalculateAllTabStatuses();
        return;
    }

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
        } else {
            dot.classList.add('all-accept');
        }
    };

    checkTab('tab-sec1', 'dot-sec1');
    checkTab('tab-sec2', 'dot-sec2');
    checkTab('tab-sec34', 'dot-sec34');
    checkTab('tab-sec56', 'dot-sec56');
    checkTab('tab-tir', 'dot-tir');
}

function appendRemark(targetId, text) {
    const el = document.getElementById(targetId);
    if (!el) return;
    if (!el.value) {
        el.value = text;
    } else if (!el.value.includes(text)) {
        el.value += '; ' + text;
    }
}

function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        alert(message);
        return;
    }
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `<span>${message}</span>`;
    toastContainer.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 250);
    }, 3500);
}

async function fetchDefaultTemplate() {
    try {
        const res = await fetch('/api/default-template');
        if (res.ok) {
            defaultTemplate = await res.json();
        }
    } catch (err) {
        // Fallback already ready
    }
}

function filterTableLocally() {
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    if (!searchInput || !typeFilter) return;

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
    showToast(`Mempersiapkan file Excel ${sscId}...`, 'success');
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
        showToast(`File Excel ${sscId} berhasil diunduh.`, 'success');
    } catch (err) {
        showToast(err.message, 'error');
    }
}

// Delete Report
async function deleteReport(id, sscId) {
    if (!confirm(`Konfirmasi: Hapus data laporan "${sscId}"?`)) return;
    try {
        const res = await fetch(`/reports/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        if (!res.ok) throw new Error('Gagal menghapus laporan');
        showToast('Laporan berhasil dihapus', 'success');
        setTimeout(() => window.location.reload(), 500);
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
        showToast('Laporan berhasil diduplikat', 'success');
        setTimeout(() => window.location.reload(), 500);
    } catch (err) {
        showToast(err.message, 'error');
    }
}

// Open Modal Create (New Inspection Form)
function openCreateModal() {
    currentEditingId = null;
    const modal = document.getElementById('reportModal');
    const form = document.getElementById('reportForm');
    const title = document.getElementById('modalTitle');
    const recBody = document.getElementById('recTableBody');

    if (!modal) {
        // If not on reports page, redirect to reports with create=1
        window.location.href = '/reports?create=1';
        return;
    }

    if (title) title.innerText = 'Form Inspeksi Baru (FORM-QC-002)';
    if (form) form.reset();
    if (recBody) recBody.innerHTML = '';

    // Switch to first tab
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    const firstTabBtn = document.querySelector('[data-tab="tab-general"]');
    const firstTabContent = document.getElementById('tab-general');
    if (firstTabBtn) firstTabBtn.classList.add('active');
    if (firstTabContent) firstTabContent.classList.add('active');

    // Populate with template
    const templateToUse = defaultTemplate || fallbackDefaultTemplate;
    populateForm(templateToUse);

    const sscIdInput = document.getElementById('g_sscId');
    if (sscIdInput) sscIdInput.value = `SSC SBY-${Math.floor(1000 + Math.random() * 9000)}`;

    const reportNoInput = document.getElementById('g_reportNo');
    if (reportNoInput) reportNoInput.value = `0${Math.floor(700 + Math.random() * 200)}`;

    const todayStr = new Date().toISOString().split('T')[0];
    const dateInput = document.getElementById('g_date');
    if (dateInput) dateInput.value = todayStr;
    const sigCheckDate = document.getElementById('sig_checkedDate');
    if (sigCheckDate) sigCheckDate.value = todayStr;
    const sigCreateDate = document.getElementById('sig_createdDate');
    if (sigCreateDate) sigCreateDate.value = todayStr;
    const sigReviewDate = document.getElementById('sig_reviewedDate');
    if (sigReviewDate) sigReviewDate.value = todayStr;

    modal.classList.add('active');
    if (window.lucide) lucide.createIcons();

    document.querySelectorAll('.auto-eval').forEach(input => evaluateDimensionInput(input));
    document.querySelectorAll('.tir-eval').forEach(input => evaluateTirInput(input));
}

// Open Modal Edit
async function openEditModal(id) {
    try {
        currentEditingId = id;
        const modal = document.getElementById('reportModal');
        const form = document.getElementById('reportForm');
        const title = document.getElementById('modalTitle');
        const recBody = document.getElementById('recTableBody');

        if (!modal) return;
        if (form) form.reset();
        if (recBody) recBody.innerHTML = '';

        const res = await fetch(`/reports/${id}`, {
            headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error('Data laporan tidak ditemukan');
        const data = await res.json();

        if (title) title.innerText = `Edit Laporan: ${data.ssc_id}`;

        // Switch to first tab
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        const firstTabBtn = document.querySelector('[data-tab="tab-general"]');
        const firstTabContent = document.getElementById('tab-general');
        if (firstTabBtn) firstTabBtn.classList.add('active');
        if (firstTabContent) firstTabContent.classList.add('active');

        populateFormFromModel(data);

        modal.classList.add('active');
        if (window.lucide) lucide.createIcons();

        document.querySelectorAll('.auto-eval').forEach(input => evaluateDimensionInput(input));
        document.querySelectorAll('.tir-eval').forEach(input => evaluateTirInput(input));
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function closeModal() {
    const modal = document.getElementById('reportModal');
    if (modal) modal.classList.remove('active');
}

function populateFormFromModel(data) {
    const reportId = document.getElementById('reportId');
    if (reportId) reportId.value = data.id || '';

    const setVal = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.value = val ?? '';
    };

    setVal('g_sscId', data.ssc_id);
    setVal('g_reportNo', data.report_no);
    setVal('g_date', data.date ? data.date.substring(0, 10) : '');
    setVal('g_customer', data.customer);
    setVal('g_partName', data.part_name || 'OUTPUT SHAFT');
    setVal('g_inspectionType', data.inspection_type || 'Pre');
    setVal('g_jobNo', data.job_no || '-');
    setVal('g_modelUnit', data.model_unit || 'HT051');
    setVal('g_quoteNo', data.quote_no || '-');
    setVal('g_partNo', data.part_no || '-');

    const sec = typeof data.sections === 'string' ? JSON.parse(data.sections) : (data.sections || {});
    populateSections(sec);

    const tir = typeof data.tir === 'string' ? JSON.parse(data.tir) : (data.tir || {});
    populateTir(tir);

    const recTableBody = document.getElementById('recTableBody');
    if (recTableBody) recTableBody.innerHTML = '';
    const recs = typeof data.recommendations === 'string' ? JSON.parse(data.recommendations) : (data.recommendations || []);
    if (recs.length === 0) {
        addRecommendationRow('01', 'Cleaning and polish OD area "A"', 'Cleaning and polish OD area "A"');
    } else {
        recs.forEach(r => addRecommendationRow(r.item, r.remarks, r.recommendation));
    }

    const sig = typeof data.signatures === 'string' ? JSON.parse(data.signatures) : (data.signatures || {});
    populateSignatures(sig);
}

function populateForm(data) {
    const setVal = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.value = val ?? '';
    };

    setVal('reportId', data.id || '');
    const g = data.general || {};
    setVal('g_sscId', g.sscId);
    setVal('g_reportNo', g.reportNo);
    setVal('g_date', g.date);
    setVal('g_customer', g.customer);
    setVal('g_partName', g.partName || 'OUTPUT SHAFT');
    setVal('g_inspectionType', g.inspectionType || 'Pre');
    setVal('g_jobNo', g.jobNo || '-');
    setVal('g_modelUnit', g.modelUnit || 'HT051');
    setVal('g_quoteNo', g.quoteNo || '-');
    setVal('g_partNo', g.partNo || '-');

    populateSections(data.sections || {});
    populateTir(data.tir || {});

    const recTableBody = document.getElementById('recTableBody');
    if (recTableBody) recTableBody.innerHTML = '';
    const recs = Array.isArray(data.recommendations) ? data.recommendations : [];
    if (recs.length === 0) {
        addRecommendationRow('01', 'Cleaning and polish OD area "A"', 'Cleaning and polish OD area "A"');
    } else {
        recs.forEach(r => addRecommendationRow(r.item, r.remarks, r.recommendation));
    }

    populateSignatures(data.signatures || {});
}

function populateSections(s) {
    const setVal = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.value = val ?? '';
    };

    const s1 = s.item01 || {};
    setVal('s1_x', s1.x ?? 79.0);
    setVal('s1_y', s1.y ?? 79.0);
    setVal('s1_z', s1.z ?? 79.0);
    setVal('s1_ar', s1.ar || 'A');
    setVal('s1_lenResult', s1.lengthResult ?? 724.0);
    setVal('s1_lenAr', s1.lengthAr || '-');
    setVal('s1_rem1', s1.remark1 || '');
    setVal('s1_rem2', s1.remark2 || '');
    setVal('s1_rem3', s1.remark3 || '');
    setVal('s1_rem4', s1.remark4 || '');
    setVal('s1_rem5', s1.remark5 || '');
    setVal('s1_rem6', s1.remark6 || '');
    setVal('s1_penetrant', s1.penetrantRemark || '');

    const s2 = s.item02 || {};
    const b = s2.itemB || {};
    setVal('s2_bx1', b.row1?.x ?? 82.0);
    setVal('s2_by1', b.row1?.y ?? 82.0);
    setVal('s2_bz1', b.row1?.z ?? 82.0);
    setVal('s2_bar1', b.row1?.ar || 'A');
    setVal('s2_bx2', b.row2?.x ?? 82.0);
    setVal('s2_by2', b.row2?.y ?? 82.0);
    setVal('s2_bz2', b.row2?.z ?? 82.0);
    setVal('s2_bar2', b.row2?.ar || 'A');
    setVal('s2_bx3', b.row3?.x ?? 82.0);
    setVal('s2_by3', b.row3?.y ?? 82.0);
    setVal('s2_bz3', b.row3?.z ?? 82.0);
    setVal('s2_bar3', b.row3?.ar || 'A');

    const c = s2.itemC || {};
    setVal('s2_cx1', c.row1?.x ?? 77.0);
    setVal('s2_cy1', c.row1?.y ?? 77.0);
    setVal('s2_cz1', c.row1?.z ?? 77.0);
    setVal('s2_car1', c.row1?.ar || 'A');
    setVal('s2_cx2', c.row2?.x ?? 77.0);
    setVal('s2_cy2', c.row2?.y ?? 77.0);
    setVal('s2_cz2', c.row2?.z ?? 77.0);
    setVal('s2_car2', c.row2?.ar || 'A');
    setVal('s2_cx3', c.row3?.x ?? 77.0);
    setVal('s2_cy3', c.row3?.y ?? 77.0);
    setVal('s2_cz3', c.row3?.z ?? 77.0);
    setVal('s2_car3', c.row3?.ar || 'A');

    const s2rems = Array.isArray(s2.remarks) ? s2.remarks : [];
    setVal('s2_rem1', s2rems[0] || '');
    setVal('s2_rem2', s2rems[1] || '');
    setVal('s2_rem3', s2rems[2] || '');
    setVal('s2_rem4', s2rems[3] || '');
    setVal('s2_penetrant', s2.penetrantRemark || '');

    const s3 = s.item03 || {};
    setVal('s3_dx', s3.itemD?.x ?? 118.0);
    setVal('s3_dy', s3.itemD?.y ?? 118.0);
    setVal('s3_dz', s3.itemD?.z ?? 118.0);
    setVal('s3_dar', s3.itemD?.ar || 'A');
    setVal('s3_ex', s3.distanceE?.x ?? 13.0);
    setVal('s3_ey', s3.distanceE?.y ?? 13.0);
    setVal('s3_ez', s3.distanceE?.z ?? 13.0);
    setVal('s3_ear', s3.distanceE?.ar || 'A');

    const s3rems = Array.isArray(s3.remarks) ? s3.remarks : [];
    setVal('s3_rem1', s3rems[0] || '');
    setVal('s3_rem2', s3rems[1] || '');
    setVal('s3_rem3', s3rems[2] || '');
    setVal('s3_rem4', s3rems[3] || '');
    setVal('s3_penetrant', s3.penetrantRemark || '');

    const s4 = s.item04 || {};
    const f = s4.itemF || {};
    setVal('s4_fx1', f.row1?.x ?? 100.01);
    setVal('s4_fy1', f.row1?.y ?? 100.01);
    setVal('s4_fz1', f.row1?.z ?? 100.01);
    setVal('s4_far1', f.row1?.ar || 'A');
    setVal('s4_fx2', f.row2?.x ?? 100.01);
    setVal('s4_fy2', f.row2?.y ?? 100.01);
    setVal('s4_fz2', f.row2?.z ?? 100.01);
    setVal('s4_far2', f.row2?.ar || 'A');
    setVal('s4_fx3', f.row3?.x ?? 100.01);
    setVal('s4_fy3', f.row3?.y ?? 100.01);
    setVal('s4_fz3', f.row3?.z ?? 100.01);
    setVal('s4_far3', f.row3?.ar || 'A');
    setVal('s4_kResult', s4.distanceK?.result ?? 188.55);
    setVal('s4_kAr', s4.distanceK?.ar || '-');

    const s4rems = Array.isArray(s4.remarks) ? s4.remarks : [];
    setVal('s4_rem1', s4rems[0] || '');
    setVal('s4_rem2', s4rems[1] || '');
    setVal('s4_penetrant', s4.penetrantRemark || '');

    const s5 = s.item05 || {};
    const h = s5.itemH || {};
    setVal('s5_hx1', h.row1?.x ?? 94.0);
    setVal('s5_hy1', h.row1?.y ?? 94.0);
    setVal('s5_hz1', h.row1?.z ?? 94.0);
    setVal('s5_har1', h.row1?.ar || 'A');
    setVal('s5_hx2', h.row2?.x ?? 94.0);
    setVal('s5_hy2', h.row2?.y ?? 94.0);
    setVal('s5_hz2', h.row2?.z ?? 94.0);
    setVal('s5_har2', h.row2?.ar || 'A');
    setVal('s5_hx3', h.row3?.x ?? 94.0);
    setVal('s5_hy3', h.row3?.y ?? 94.0);
    setVal('s5_hz3', h.row3?.z ?? 94.0);
    setVal('s5_har3', h.row3?.ar || 'A');
    setVal('s5_jResult', s5.distanceJ?.result ?? 142.80);
    setVal('s5_jAr', s5.distanceJ?.ar || '-');

    const s5rems = Array.isArray(s5.remarks) ? s5.remarks : [];
    setVal('s5_rem1', s5rems[0] || '');
    setVal('s5_rem2', s5rems[1] || '');
    setVal('s5_penetrant', s5.penetrantRemark || '');

    const s6 = s.item06 || {};
    setVal('s6_gx', s6.itemG?.x ?? 84.2);
    setVal('s6_gy', s6.itemG?.y ?? 84.2);
    setVal('s6_gz', s6.itemG?.z ?? 84.2);
    setVal('s6_gar', s6.itemG?.ar || 'A');
    setVal('s6_iResult', s6.distanceI?.result ?? 98.78);
    setVal('s6_iAr', s6.distanceI?.ar || '-');

    const s6rems = Array.isArray(s6.remarks) ? s6.remarks : [];
    setVal('s6_rem1', s6rems[0] || '');
    setVal('s6_rem2', s6rems[1] || '');
    setVal('s6_rem3', s6rems[2] || '');
    setVal('s6_rem4', s6rems[3] || '');
    setVal('s6_rem5', s6rems[4] || '');
    setVal('s6_rem6', s6rems[5] || '');
    setVal('s6_penetrant', s6.penetrantRemark || '');
}

function populateTir(tir) {
    const setVal = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.value = val ?? '';
    };

    setVal('tir_datum', tir.datum || 'Datum at item "B and E"');
    const tirItems = Array.isArray(tir.items) ? tir.items : [];
    const getTir = (code) => tirItems.find(i => i.item === code) || { d0: '0', d90: '0', d180: '0', d270: '0', ar: 'A' };

    ['a', 'b', 'c', 'd', 'e', 'f'].forEach(k => {
        const itemVal = getTir(k.toUpperCase());
        setVal(`tir_${k}_0`, itemVal.d0 ?? '0');
        setVal(`tir_${k}_90`, itemVal.d90 ?? '0');
        setVal(`tir_${k}_180`, itemVal.d180 ?? '0');
        setVal(`tir_${k}_270`, itemVal.d270 ?? '0');
        setVal(`tir_${k}_ar`, itemVal.ar || 'A');
    });
}

function populateSignatures(sig) {
    const setVal = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.value = val ?? '';
    };

    setVal('sig_checkedName', sig.checkedBy?.name || 'Faqih S A A');
    setVal('sig_checkedTitle', sig.checkedBy?.title || 'QC Inspector');
    setVal('sig_checkedDate', sig.checkedBy?.date ? sig.checkedBy.date.substring(0, 10) : '');

    setVal('sig_createdName', sig.createdBy?.name || 'M Kevin S');
    setVal('sig_createdTitle', sig.createdBy?.title || 'QC Inspector');
    setVal('sig_createdDate', sig.createdBy?.date ? sig.createdBy.date.substring(0, 10) : '');

    setVal('sig_reviewedName', sig.reviewedBy?.name || 'Agus Riyanto');
    setVal('sig_reviewedTitle', sig.reviewedBy?.title || 'Leader QC');
    setVal('sig_reviewedDate', sig.reviewedBy?.date ? sig.reviewedBy.date.substring(0, 10) : '');
}

function addRecommendationRow(item = '01', remarks = '', recommendation = '') {
    const recTableBody = document.getElementById('recTableBody');
    if (!recTableBody) return;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="text" class="rec-item mono" value="${item || '01'}" style="text-align: center; font-weight:600;"></td>
        <td><input type="text" class="rec-remarks" value="${remarks || ''}" placeholder="Temuan visual..."></td>
        <td><input type="text" class="rec-rec" value="${recommendation || ''}" placeholder="Rekomendasi tindakan..."></td>
        <td style="text-align: center;">
            <button type="button" class="btn btn-secondary btn-icon btn-sm" onclick="this.closest('tr').remove()" title="Hapus">
                <i data-lucide="x"></i>
            </button>
        </td>
    `;
    recTableBody.appendChild(tr);
    if (window.lucide) lucide.createIcons();
}

function collectFormData() {
    const sscIdInput = document.getElementById('g_sscId');
    const sscId = sscIdInput ? sscIdInput.value.trim() : '';
    if (!sscId) {
        showToast('Nomor SSC ID wajib diisi!', 'error');
        const tabGen = document.querySelector('[data-tab="tab-general"]');
        if (tabGen) tabGen.click();
        if (sscIdInput) sscIdInput.focus();
        return null;
    }

    const getVal = (id, defaultVal = '') => {
        const el = document.getElementById(id);
        return el ? el.value.trim() : defaultVal;
    };

    const getNum = (id, defaultVal = 0) => {
        const el = document.getElementById(id);
        return el ? (parseFloat(el.value) || defaultVal) : defaultVal;
    };

    const recs = [];
    document.querySelectorAll('#recTableBody tr').forEach(tr => {
        const item = tr.querySelector('.rec-item')?.value.trim() || '';
        const remarks = tr.querySelector('.rec-remarks')?.value.trim() || '';
        const recommendation = tr.querySelector('.rec-rec')?.value.trim() || '';
        if (remarks || recommendation) {
            recs.push({ item, remarks, recommendation });
        }
    });

    return {
        ssc_id: sscId,
        report_no: getVal('g_reportNo'),
        date: getVal('g_date'),
        customer: getVal('g_customer'),
        part_name: getVal('g_partName', 'OUTPUT SHAFT'),
        inspection_type: getVal('g_inspectionType', 'Pre'),
        job_no: getVal('g_jobNo', '-'),
        model_unit: getVal('g_modelUnit', 'HT051'),
        quote_no: getVal('g_quoteNo', '-'),
        part_no: getVal('g_partNo', '-'),
        doc_no: 'FORM-QC-002',
        rev: '04',
        sections: {
            item01: {
                title: 'OD Area "A" and OD Spline',
                spec: 'Ø79 ± 0.25',
                x: getNum('s1_x', 79),
                y: getNum('s1_y', 79),
                z: getNum('s1_z', 79),
                ar: getVal('s1_ar', 'A'),
                lengthItem: 'L',
                lengthSpec: 'N/A',
                lengthResult: getNum('s1_lenResult', 724),
                lengthAr: getVal('s1_lenAr', '-'),
                remark1: getVal('s1_rem1'),
                remark2: getVal('s1_rem2'),
                remark3: getVal('s1_rem3'),
                remark4: getVal('s1_rem4'),
                remark5: getVal('s1_rem5'),
                remark6: getVal('s1_rem6'),
                penetrantRemark: getVal('s1_penetrant')
            },
            item02: {
                title: 'OD Area "B and C"',
                itemB: {
                    spec: 'Ø82 ± 0.25',
                    row1: { x: getNum('s2_bx1', 82), y: getNum('s2_by1', 82), z: getNum('s2_bz1', 82), ar: getVal('s2_bar1', 'A') },
                    row2: { x: getNum('s2_bx2', 82), y: getNum('s2_by2', 82), z: getNum('s2_bz2', 82), ar: getVal('s2_bar2', 'A') },
                    row3: { x: getNum('s2_bx3', 82), y: getNum('s2_by3', 82), z: getNum('s2_bz3', 82), ar: getVal('s2_bar3', 'A') }
                },
                itemC: {
                    spec: 'Ø77 ± 0.25',
                    row1: { x: getNum('s2_cx1', 77), y: getNum('s2_cy1', 77), z: getNum('s2_cz1', 77), ar: getVal('s2_car1', 'A') },
                    row2: { x: getNum('s2_cx2', 77), y: getNum('s2_cy2', 77), z: getNum('s2_cz2', 77), ar: getVal('s2_car2', 'A') },
                    row3: { x: getNum('s2_cx3', 77), y: getNum('s2_cy3', 77), z: getNum('s2_cz3', 77), ar: getVal('s2_car3', 'A') }
                },
                remarks: [
                    getVal('s2_rem1'), getVal('s2_rem2'), getVal('s2_rem3'), getVal('s2_rem4')
                ].filter(Boolean),
                penetrantRemark: getVal('s2_penetrant')
            },
            item03: {
                title: 'OD Area "D"',
                itemD: {
                    spec: 'Ø118 ± 0.5',
                    x: getNum('s3_dx', 118),
                    y: getNum('s3_dy', 118),
                    z: getNum('s3_dz', 118),
                    ar: getVal('s3_dar', 'A')
                },
                distanceE: {
                    spec: '13 ± 0.5',
                    x: getNum('s3_ex', 13),
                    y: getNum('s3_ey', 13),
                    z: getNum('s3_ez', 13),
                    ar: getVal('s3_ear', 'A')
                },
                remarks: [
                    getVal('s3_rem1'), getVal('s3_rem2')
                ].filter(Boolean),
                penetrantRemark: getVal('s3_penetrant')
            },
            item04: {
                title: 'OD Area "F"',
                itemF: {
                    spec: 'Ø100,01 ± 0,008',
                    row1: { x: getNum('s4_fx1', 100.01), y: getNum('s4_fy1', 100.01), z: getNum('s4_fz1', 100.01), ar: getVal('s4_far1', 'A') },
                    row2: { x: getNum('s4_fx2', 100.01), y: getNum('s4_fy2', 100.01), z: getNum('s4_fz2', 100.01), ar: getVal('s4_far2', 'A') },
                    row3: { x: getNum('s4_fx3', 100.01), y: getNum('s4_fy3', 100.01), z: getNum('s4_fz3', 100.01), ar: getVal('s4_far3', 'A') }
                },
                distanceK: {
                    spec: 'N/A',
                    result: getNum('s4_kResult', 188.55),
                    ar: getVal('s4_kAr', '-')
                },
                remarks: [
                    getVal('s4_rem1'), getVal('s4_rem2')
                ].filter(Boolean),
                penetrantRemark: getVal('s4_penetrant')
            },
            item05: {
                title: 'OD Area "H"',
                itemH: {
                    spec: 'Ø94 ± 0,013',
                    row1: { x: getNum('s5_hx1', 94), y: getNum('s5_hy1', 94), z: getNum('s5_hz1', 94), ar: getVal('s5_har1', 'A') },
                    row2: { x: getNum('s5_hx2', 94), y: getNum('s5_hy2', 94), z: getNum('s5_hz2', 94), ar: getVal('s5_har2', 'A') },
                    row3: { x: getNum('s5_hx3', 94), y: getNum('s5_hy3', 94), z: getNum('s5_hz3', 94), ar: getVal('s5_har3', 'A') }
                },
                distanceJ: {
                    spec: 'N/A',
                    result: getNum('s5_jResult', 142.8),
                    ar: getVal('s5_jAr', '-')
                },
                remarks: [
                    getVal('s5_rem1')
                ].filter(Boolean),
                penetrantRemark: getVal('s5_penetrant')
            },
            item06: {
                title: 'OD Area "G" and OD Spline',
                itemG: {
                    spec: 'Ø84,2 ± 0,13',
                    x: getNum('s6_gx', 84.2),
                    y: getNum('s6_gy', 84.2),
                    z: getNum('s6_gz', 84.2),
                    ar: getVal('s6_gar', 'A')
                },
                distanceI: {
                    spec: 'N/A',
                    result: getNum('s6_iResult', 98.78),
                    ar: getVal('s6_iAr', '-')
                },
                remarks: [
                    getVal('s6_rem1')
                ].filter(Boolean),
                penetrantRemark: getVal('s6_penetrant')
            }
        },
        tir: {
            spec: 'Max 0,2',
            datum: getVal('tir_datum', 'Datum at item "B and E"'),
            items: ['a', 'b', 'c', 'd', 'e', 'f'].map(k => ({
                item: k.toUpperCase(),
                d0: getVal(`tir_${k}_0`, '0'),
                d90: getVal(`tir_${k}_90`, '0'),
                d180: getVal(`tir_${k}_180`, '0'),
                d270: getVal(`tir_${k}_270`, '0'),
                ar: getVal(`tir_${k}_ar`, 'A')
            }))
        },
        recommendations: recs,
        signatures: {
            checkedBy: {
                name: getVal('sig_checkedName', 'Faqih S A A'),
                title: getVal('sig_checkedTitle', 'QC Inspector'),
                date: getVal('sig_checkedDate')
            },
            createdBy: {
                name: getVal('sig_createdName', 'M Kevin S'),
                title: getVal('sig_createdTitle', 'QC Inspector'),
                date: getVal('sig_createdDate')
            },
            reviewedBy: {
                name: getVal('sig_reviewedName', 'Agus Riyanto'),
                title: getVal('sig_reviewedTitle', 'Leader QC'),
                date: getVal('sig_reviewedDate')
            }
        }
    };
}

async function handleSave(downloadDirectly = false) {
    const payload = collectFormData();
    if (!payload) return;

    if (downloadDirectly) {
        showToast('Menyimpan & mengunduh Excel...', 'success');
        try {
            const res = await fetch('/reports/submit-and-download', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            if (!res.ok) throw new Error('Gagal menyimpan laporan');

            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${payload.ssc_id} CCR PRE OUTPUT SHAFT.xlsx`;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);

            showToast('Laporan berhasil disimpan dan diunduh.', 'success');
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

            showToast('Laporan berhasil disimpan.', 'success');
            closeModal();
            setTimeout(() => window.location.reload(), 600);
        } catch (err) {
            showToast(err.message, 'error');
        }
    }
}

// Global exports so inline onclick always works
window.openCreateModal = openCreateModal;
window.closeModal = closeModal;
window.openEditModal = openEditModal;
window.deleteReport = deleteReport;
window.duplicateReport = duplicateReport;
window.downloadExcel = downloadExcel;
window.appendRemark = appendRemark;
