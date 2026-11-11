// PT SSC Works - Quality Control Digitalization System (QCMS)
// Frontend Controller & Real-Time Live Preview Engine

let defaultTemplate = null;
let currentReportId = null;
let lastPreviewFocusTarget = null;
const photoSlotKeys = [
    'drawing_dimension',
    'finding_component',
    'area_a_visual_1', 'area_a_visual_2', 'area_a_visual_3', 'area_a_visual_4',
    'area_a_penet_1', 'area_a_penet_2', 'area_a_penet_3', 'area_a_penet_4',
    'area_bc_visual_1', 'area_bc_visual_2', 'area_bc_visual_3', 'area_bc_visual_4',
    'area_bc_penet_1', 'area_bc_penet_2', 'area_bc_penet_3', 'area_bc_penet_4',
    'area_d_visual_1', 'area_d_visual_2', 'area_d_visual_3', 'area_d_visual_4',
    'area_d_penet_1', 'area_d_penet_2', 'area_d_penet_3', 'area_d_penet_4',
    'area_f_visual_1', 'area_f_visual_2', 'area_f_visual_3', 'area_f_visual_4',
    'area_f_penet_1', 'area_f_penet_2', 'area_f_penet_3', 'area_f_penet_4',
    'area_h_visual_1', 'area_h_visual_2', 'area_h_visual_3', 'area_h_visual_4',
    'area_h_penet_1', 'area_h_penet_2', 'area_h_penet_3', 'area_h_penet_4',
    'area_g_visual_1', 'area_g_visual_2', 'area_g_visual_3', 'area_g_visual_4',
    'area_g_penet_1', 'area_g_penet_2', 'area_g_penet_3', 'area_g_penet_4',
    'tir_diagram'
];

let reportPhotos = {};
photoSlotKeys.forEach(k => reportPhotos[k] = null);

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// Blank Template (Zero fake measurement data)
const blankDefaultTemplate = {
    general: {
        docNo: "FORM-QC-002",
        rev: "04",
        reportNo: "",
        date: new Date().toISOString().split('T')[0],
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
            x: "", y: "", z: "", ar: "A",
            remark1: "Actual measurement OD area \"A\" in good condition",
            lengthItem: "L", lengthSpec: "N/A", lengthResult: "", lengthAr: "-",
            penetrantRemark: "No indication crack at OD area \"A\" and OD spline after penetrant check"
        },
        item02: {
            title: "OD Area \"B and C\"",
            itemB: {
                spec: "Ø82 ± 0.25",
                row1: { x: "", y: "", z: "", ar: "A" },
                row2: { x: "", y: "", z: "", ar: "A" },
                row3: { x: "", y: "", z: "", ar: "A" }
            },
            itemC: {
                spec: "Ø77 ± 0.25",
                row1: { x: "", y: "", z: "", ar: "A" },
                row2: { x: "", y: "", z: "", ar: "A" },
                row3: { x: "", y: "", z: "", ar: "A" }
            },
            remarks: ["Actual measurement OD area \"B and C\" in good condition"],
            penetrantRemark: "No indication crack at OD Area \"B and C\" after penetrant check"
        },
        item03: {
            title: "OD Area \"D\"",
            itemD: { spec: "Ø118 ± 0.5", x: "", y: "", z: "", ar: "A" },
            distanceE: { spec: "13 ± 0.5", x: "", y: "", z: "", ar: "A" },
            remarks: ["Actual measurement OD area \"D\" in good condition", "Actual distance area \"E\" in good condition"],
            penetrantRemark: "No indication crack at OD area \"D\" after penetrant check"
        },
        item04: {
            title: "OD Area \"F\"",
            itemF: {
                spec: "Ø100,01 ± 0,008",
                row1: { x: "", y: "", z: "", ar: "A" },
                row2: { x: "", y: "", z: "", ar: "A" },
                row3: { x: "", y: "", z: "", ar: "A" }
            },
            distanceK: { spec: "N/A", result: "", ar: "-" },
            remarks: ["Actual measurement OD area \"F\" in good condition"],
            penetrantRemark: "No indication crack at area OD area \"F\" after penetrant check"
        },
        item05: {
            title: "OD Area \"H\"",
            itemH: {
                spec: "Ø94 ± 0,013",
                row1: { x: "", y: "", z: "", ar: "A" },
                row2: { x: "", y: "", z: "", ar: "A" },
                row3: { x: "", y: "", z: "", ar: "A" }
            },
            distanceJ: { spec: "N/A", result: "", ar: "-" },
            remarks: ["Actual measurement OD area \"H\" in good condition"],
            penetrantRemark: "No indication crack at area OD area \"H\" after penetrant check"
        },
        item06: {
            title: "OD Area \"G\" and OD Spline",
            itemG: { spec: "Ø84,2 ± 0,13", x: "", y: "", z: "", ar: "A" },
            distanceI: { spec: "N/A", result: "", ar: "-" },
            remarks: ["Actual measurement OD area \"G\" in good condition"],
            penetrantRemark: "No indication crack at area OD spline after penetrant check"
        }
    },
    tir: {
        spec: "Max 0,2",
        datum: "Datum at item \"B and E\"",
        items: [
            { item: "A", d0: "", d90: "", d180: "", d270: "", ar: "A" },
            { item: "B", d0: "", d90: "", d180: "", d270: "", ar: "A" },
            { item: "C", d0: "", d90: "", d180: "", d270: "", ar: "A" },
            { item: "D", d0: "", d90: "", d180: "", d270: "", ar: "A" },
            { item: "E", d0: "", d90: "", d180: "", d270: "", ar: "A" },
            { item: "F", d0: "", d90: "", d180: "", d270: "", ar: "A" }
        ]
    },
    recommendations: [
        { item: "01", remarks: "", recommendation: "" }
    ],
    photos: {
        item01: null, item02: null, item03: null, item04: null,
        item05: null, item06: null, penetrant: null, defect: null
    },
    signatures: {
        checkedBy: { name: "", title: "QC Inspector", date: "" },
        createdBy: { name: "", title: "QC Inspector", date: "" },
        reviewedBy: { name: "", title: "Leader QC", date: "" }
    }
};

function initPersonnelAutoRole() {
    const rolesMap = {
        'faqih': 'QC Inspector',
        'faqih s a a': 'QC Inspector',
        'm kevin s': 'QC Inspector',
        'kevin': 'QC Inspector',
        'indra': 'QC Inspector',
        'sahid': 'QC Inspector',
        'rafli': 'QC Inspector',
        'agus': 'Leader QC',
        'agus riyanto': 'Leader QC',
        'firman': 'Workshop Supervisor',
        'qc inspector': 'QC Inspector',
        'qc leader': 'Leader QC',
        'leader qc': 'Leader QC',
    };

    const pairs = [
        { nameId: 'sig_checkedName', titleId: 'sig_checkedTitle' },
        { nameId: 'sig_createdName', titleId: 'sig_createdTitle' },
        { nameId: 'sig_reviewedName', titleId: 'sig_reviewedTitle' },
    ];

    pairs.forEach(({ nameId, titleId }) => {
        const nameInput = document.getElementById(nameId);
        const titleInput = document.getElementById(titleId);
        if (!nameInput || !titleInput) return;

        const updateTitle = () => {
            const val = (nameInput.value || '').trim().toLowerCase();
            const userRole = nameInput.selectedOptions?.[0]?.dataset?.userRole || '';
            let resolvedTitle = rolesMap[val] || '';
            if (!resolvedTitle && val) {
                if (val.includes('leader')) resolvedTitle = 'Leader QC';
                else if (val.includes('supervisor')) resolvedTitle = 'Workshop Supervisor';
                else if (val.includes('engineer')) resolvedTitle = 'QC Engineer';
                else if (val.includes('inspector')) resolvedTitle = 'QC Inspector';
            }
            if (!resolvedTitle && userRole) {
                if (userRole === 'qc_leader') resolvedTitle = 'Leader QC';
                else if (userRole === 'user') resolvedTitle = 'QC Inspector';
            }
            if (resolvedTitle) {
                titleInput.value = resolvedTitle;
                titleInput.dispatchEvent(new Event('change', { bubbles: true }));
                titleInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
        };

        nameInput.addEventListener('input', updateTitle);
        nameInput.addEventListener('change', updateTitle);
    });
}

function initApp() {
    if (window.lucide) lucide.createIcons();
    setupStudioTabs();
    setupLivePreviewSync();
    setupButtonActions();
    initPersonnelAutoRole();

    // Check if we are on inspection create/edit page
    const reportIdInput = document.getElementById('reportId');
    if (reportIdInput) {
        if (reportIdInput.value) {
            currentReportId = reportIdInput.value;
            loadReportForEdit(currentReportId);
        } else {
            // New blank inspection
            loadDefaultBlankForm();
        }
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApp);
} else {
    initApp();
}

// -------------------------------------------------------------
// Tabs Switching
// -------------------------------------------------------------
function setupStudioTabs() {
    const tabButtons = document.querySelectorAll('.s-tab-btn');
    tabButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = btn.getAttribute('data-target');
            tabButtons.forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.s-tab-content').forEach(c => c.classList.remove('active'));

            btn.classList.add('active');
            const targetContent = document.getElementById(targetId);
            if (targetContent) targetContent.classList.add('active');
            document.querySelector('.studio-form')?.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
}

// -------------------------------------------------------------
// Live Preview Synchronization
// -------------------------------------------------------------
function setupLivePreviewSync() {
    const form = document.getElementById('qcMainForm');
    if (!form) return;

    // Keep the preview synchronized, then reveal the document field being edited.
    form.addEventListener('input', (event) => {
        updateLivePreview();
        focusPreviewForInput(event.target);
        if (event.target && event.target.id && event.target.id.startsWith('sig_')) {
            syncSigChips(event.target.id);
        }
    });
    form.addEventListener('focusin', (event) => {
        focusPreviewForInput(event.target);
    });
    form.addEventListener('change', (event) => {
        updateLivePreview();
        focusPreviewForInput(event.target);
        if (event.target && event.target.id && event.target.id.startsWith('sig_')) {
            syncSigChips(event.target.id);
        }
    });

    const compSelect = document.getElementById('componentSelect');
    if (compSelect) {
        compSelect.addEventListener('change', () => focusPreviewForInput(compSelect));
        compSelect.addEventListener('focus', () => focusPreviewForInput(compSelect));
    }

    // Initial sync
    setTimeout(() => {
        updateLivePreview();
        ['sig_checkedName', 'sig_createdName', 'sig_reviewedName'].forEach(syncSigChips);
    }, 100);
}

function focusPreviewForInput(input) {
    if (!input || ['button', 'submit', 'reset'].includes(input.type)) return;

    const id = input.id || '';
    let target = null;
    if (id.startsWith('input_slot_') || id.startsWith('camera_slot_')) {
        const slotKey = id.replace(/^(input|camera)_slot_/, '');
        target = document.getElementById(`prev_slot_${slotKey}`);
    }
    if (!target && input.closest('#recTableBody')) {
        const tr = input.closest('tr');
        const tbody = tr ? tr.parentElement : null;
        if (tbody) {
            const allFormRows = Array.from(tbody.querySelectorAll('tr'));
            const rowIndex = allFormRows.indexOf(tr);
            const prevRows = document.querySelectorAll('#prev_recTableBody tr');
            const prevTr = prevRows[rowIndex];
            if (prevTr) {
                const hasItemCell = prevTr.children.length >= 3;
                if (input.classList.contains('rec-item')) {
                    let cur = prevTr;
                    let idx = rowIndex;
                    while (cur && cur.children.length < 3 && idx > 0) {
                        idx--;
                        cur = prevRows[idx];
                    }
                    target = cur ? cur.children[0] : prevTr;
                } else if (input.classList.contains('rec-remarks')) {
                    target = hasItemCell ? prevTr.children[1] : prevTr.children[0];
                } else if (input.classList.contains('rec-rec')) {
                    target = hasItemCell ? prevTr.children[2] : prevTr.children[1];
                } else {
                    target = prevTr;
                }
            }
        }
        target = target || document.getElementById('prev_recTableBody');
    }

    const aliases = {
        componentSelect: 'prev_partName',
        g_date: 'prev_metaDate',
        g_inspectionType: 'prev_inspectionType',
        i1_length: 'prev_i1_len',
        i1_remark1: 'prev_i1_remark1',
        i3_e_y: 'prev_i3_e_x',
        i3_e_z: 'prev_i3_e_x',
        i4_k_res: 'prev_i4_k',
        i5_j_res: 'prev_i5_j',
        i6_i_res: 'prev_i6_i',
        sig_checkedName: 'prev_sigCheckedName', sig_checkedTitle: 'prev_sigCheckedTitle', sig_checkedDate: 'prev_sigCheckedDate',
        sig_createdName: 'prev_sigCreatedName', sig_createdTitle: 'prev_sigCreatedTitle', sig_createdDate: 'prev_sigCreatedDate',
        sig_reviewedName: 'prev_sigReviewedName', sig_reviewedTitle: 'prev_sigReviewedTitle', sig_reviewedDate: 'prev_sigReviewedDate'
    };
    if (!target) {
        const previewId = aliases[id] || (id.startsWith('g_') ? `prev_${id.slice(2)}` : `prev_${id}`);
        target = document.getElementById(previewId);
    }

    // Some measurement inputs use a slightly different preview suffix.
    if (!target && /^i[1-6]_/.test(id)) {
        const previewId = id.replace(/^i([1-6])_/, 'prev_i$1_').replace(/_length$/, '_len').replace(/_res$/, '');
        target = document.getElementById(previewId);
    }

    let page = target?.closest('.preview-sheet')?.id?.replace('prev_sheet_', '');
    if (!page) {
        if (id.startsWith('i1_') || id.startsWith('i2_')) page = '2';
        else if (id.startsWith('i3_') || id.startsWith('i4_')) page = '3';
        else if (id.startsWith('i5_') || id.startsWith('i6_')) page = '4';
        else if (id.startsWith('tir_')) page = '5';
        else if (input.closest('#recTableBody')) page = '6';
        else page = '1';
        target = target || document.getElementById(`prev_sheet_${page}`);
    }

    if (!target) return;
    window.switchPreviewPage?.(page);
    document.querySelectorAll('.preview-focus-highlight').forEach(el => el.classList.remove('preview-focus-highlight'));
    target.classList.add('preview-focus-highlight');
    scrollPreviewWithinPane(target);
    window.clearTimeout(target._previewFocusTimer);
    target._previewFocusTimer = window.setTimeout(() => target.classList.remove('preview-focus-highlight'), 1200);
}

function scrollPreviewWithinPane(target) {
    const container = target.closest('.sheet-container');
    if (!container) return;

    const containerRect = container.getBoundingClientRect();
    const targetRect = target.getBoundingClientRect();
    const visible = targetRect.top >= containerRect.top + 8 && targetRect.bottom <= containerRect.bottom - 8;
    if (visible || lastPreviewFocusTarget === target) return;

    const targetTop = targetRect.top - containerRect.top + container.scrollTop;
    const offset = targetRect.height > container.clientHeight
        ? 0
        : (container.clientHeight - targetRect.height) / 2;
    container.scrollTo({ top: Math.max(0, targetTop - offset), behavior: 'smooth' });
    lastPreviewFocusTarget = target;
}

function updateLivePreview() {
    // Official template defaults from PDF (SSC SBY-5778 CCR PRE OUTPUT SHAFT.pdf)
    const defVals = {
        docNo: 'FORM-QC-002', rev: '04', responsibility: 'Quality Control', effectiveDate: '28-Jan-2022', page: '1   of   6',
        reportNo: '0742', sscId: 'SSC SBY-5778', customer: 'AMMAN MINERAL NUSA TENGGARA, PT', partName: 'OUTPUT SHAFT',
        inspectionType: 'Pre', modelUnit: 'HT051', jobNo: '-', quoteNo: '-', partNo: '-',
        i1_x: '78,850', i1_y: '78,865', i1_z: '78,865', i1_ar: 'A', i1_len: '724,320',
        i2_b1_x: '81,830', i2_b1_y: '81,840', i2_b1_z: '81,840', i2_b1_ar: 'A',
        i2_b2_x: '81,840', i2_b2_y: '81,850', i2_b2_z: '81,850', i2_b2_ar: 'A',
        i2_b3_x: '81,850', i2_b3_y: '81,860', i2_b3_z: '81,860', i2_b3_ar: 'A',
        i2_c1_x: '76,850', i2_c1_y: '76,860', i2_c1_z: '76,865', i2_c1_ar: 'A',
        i2_c2_x: '76,855', i2_c2_y: '76,850', i2_c2_z: '76,860', i2_c2_ar: 'A',
        i2_c3_x: '76,860', i2_c3_y: '76,870', i2_c3_z: '76,860', i2_c3_ar: 'A',
        i3_d_x: '118,030', i3_d_y: '118,020', i3_d_z: '118,010', i3_d_ar: 'A',
        i3_e_x: '12,950', i3_e_ar: 'A',
        i4_f1_x: '101,010', i4_f1_y: '101,010', i4_f1_z: '101,005', i4_f1_ar: 'A',
        i4_f2_x: '101,015', i4_f2_y: '101,015', i4_f2_z: '101,015', i4_f2_ar: 'A',
        i4_f3_x: '101,010', i4_f3_y: '101,010', i4_f3_z: '101,010', i4_f3_ar: 'A',
        i4_k: '188,550',
        i5_h1_x: '93,980', i5_h1_y: '93,985', i5_h1_z: '93,980', i5_h1_ar: 'R',
        i5_h2_x: '93,980', i5_h2_y: '93,985', i5_h2_z: '93,985', i5_h2_ar: 'R',
        i5_h3_x: '93,995', i5_h3_y: '94,000', i5_h3_z: '93,990', i5_h3_ar: 'A',
        i5_j: '142,800',
        i6_x: '83,980', i6_y: '84,010', i6_z: '84,000', i6_ar: 'R',
        i6_i: '98,780',
        tir: {
            a: { d0: '0', d90: '+0,05', d180: '+0,18', d270: '+0,10', ar: 'A' },
            b: { d0: '0', d90: '+0,02', d180: '0', d270: '+0,02', ar: 'A' },
            c: { d0: '0', d90: '+0,12', d180: '+0,10', d270: '+0,03', ar: 'A' },
            d: { d0: '0', d90: '+0,03', d180: '+0,03', d270: '0', ar: 'A' },
            e: { d0: '0', d90: '+0,01', d180: '0', d270: '0', ar: 'A' },
            f: { d0: '0', d90: '-0,05', d180: '-0,08', d270: '+0,02', ar: 'A' }
        }
    };

    // 1. General & Document Control
    const docNo = document.getElementById('g_docNo')?.value || defVals.docNo;
    const rev = document.getElementById('g_rev')?.value || defVals.rev;
    const responsibility = document.getElementById('g_responsibility')?.value || defVals.responsibility;
    const effectiveDate = document.getElementById('g_effectiveDate')?.value || defVals.effectiveDate;
    const page = document.getElementById('g_page')?.value || defVals.page;

    setPreviewText('prev_docNo', docNo);
    setPreviewText('prev_rev', rev);
    setPreviewText('prev_responsibility', responsibility);
    setPreviewText('prev_effectiveDate', effectiveDate);
    setPreviewText('prev_page', page);

    // Info Grid
    const sscId = document.getElementById('g_sscId')?.value || defVals.sscId;
    const reportNo = document.getElementById('g_reportNo')?.value || defVals.reportNo;
    const date = document.getElementById('g_date')?.value || '17-Oct-26';
    const customer = document.getElementById('g_customer')?.value || defVals.customer;
    const partName = document.getElementById('g_partName')?.value || defVals.partName;
    const inspectionType = document.getElementById('g_inspectionType')?.value || defVals.inspectionType;
    const modelUnit = document.getElementById('g_modelUnit')?.value || defVals.modelUnit;
    const jobNo = document.getElementById('g_jobNo')?.value || defVals.jobNo;
    const quoteNo = document.getElementById('g_quoteNo')?.value || defVals.quoteNo;
    const partNo = document.getElementById('g_partNo')?.value || defVals.partNo;

    setPreviewText('prev_sscId', sscId);
    setPreviewText('prev_reportNo', reportNo);

    // Sync top bar badges
    const topSsc = document.getElementById('top_badge_sscId');
    if (topSsc && sscId) topSsc.textContent = sscId;
    const topRep = document.getElementById('top_badge_reportNo');
    if (topRep && reportNo) topRep.textContent = reportNo;
    setPreviewText('prev_customer', customer);
    setPreviewText('prev_partName', partName);
    setPreviewText('prev_inspectionType', inspectionType);
    setPreviewText('prev_modelUnit', modelUnit);
    setPreviewText('prev_jobNo', jobNo);
    setPreviewText('prev_quoteNo', quoteNo);
    setPreviewText('prev_partNo', partNo);
    setPreviewText('prev_metaDate', date);

    const isPre = inspectionType === 'Pre';
    const preEl = document.getElementById('prev_preCheck');
    const postEl = document.getElementById('prev_postCheck');
    if (preEl) preEl.innerHTML = isPre ? '&#9745; Pre' : '&#9744; Pre';
    if (postEl) postEl.innerHTML = !isPre ? '&#9745; Post' : '&#9744; Post';

    // 2. Section 01 (Area A)
    setPreviewText('prev_i1_x', document.getElementById('i1_x')?.value || defVals.i1_x);
    setPreviewText('prev_i1_y', document.getElementById('i1_y')?.value || defVals.i1_y);
    setPreviewText('prev_i1_z', document.getElementById('i1_z')?.value || defVals.i1_z);
    const i1Ar = document.getElementById('i1_ar')?.value || defVals.i1_ar;
    setPreviewText('prev_i1_ar', i1Ar);
    colorStatusCell('prev_i1_ar', i1Ar);
    setPreviewText('prev_i1_len', document.getElementById('i1_length')?.value || defVals.i1_len);

    // 3. Section 02 (B & C)
    ['1', '2', '3'].forEach(r => {
        ['x', 'y', 'z'].forEach(axis => {
            const bVal = document.getElementById(`i2_b${r}_${axis}`)?.value || defVals[`i2_b${r}_${axis}`];
            const cVal = document.getElementById(`i2_c${r}_${axis}`)?.value || defVals[`i2_c${r}_${axis}`];
            setPreviewText(`prev_i2_b${r}_${axis}`, bVal);
            setPreviewText(`prev_i2_c${r}_${axis}`, cVal);
        });
        const bAr = document.getElementById(`i2_b${r}_ar`)?.value || defVals[`i2_b${r}_ar`];
        setPreviewText(`prev_i2_b${r}_ar`, bAr);
        colorStatusCell(`prev_i2_b${r}_ar`, bAr);

        const cAr = document.getElementById(`i2_c${r}_ar`)?.value || defVals[`i2_c${r}_ar`];
        setPreviewText(`prev_i2_c${r}_ar`, cAr);
        colorStatusCell(`prev_i2_c${r}_ar`, cAr);
    });

    // 4. Section 03 (D & E)
    ['x', 'y', 'z'].forEach(axis => {
        setPreviewText(`prev_i3_d_${axis}`, document.getElementById(`i3_d_${axis}`)?.value || defVals[`i3_d_${axis}`]);
    });
    setPreviewText('prev_i3_e_x', document.getElementById('i3_e_x')?.value || defVals.i3_e_x);
    const dAr = document.getElementById('i3_d_ar')?.value || defVals.i3_d_ar;
    setPreviewText('prev_i3_d_ar', dAr);
    colorStatusCell('prev_i3_d_ar', dAr);

    const eAr = document.getElementById('i3_e_ar')?.value || defVals.i3_e_ar;
    setPreviewText('prev_i3_e_ar', eAr);
    colorStatusCell('prev_i3_e_ar', eAr);

    // 5. Section 04 (F & K)
    ['1', '2', '3'].forEach(r => {
        ['x', 'y', 'z'].forEach(axis => {
            setPreviewText(`prev_i4_f${r}_${axis}`, document.getElementById(`i4_f${r}_${axis}`)?.value || defVals[`i4_f${r}_${axis}`]);
        });
        const fAr = document.getElementById(`i4_f${r}_ar`)?.value || defVals[`i4_f${r}_ar`];
        setPreviewText(`prev_i4_f${r}_ar`, fAr);
        colorStatusCell(`prev_i4_f${r}_ar`, fAr);
    });
    setPreviewText('prev_i4_k', document.getElementById('i4_k_res')?.value || defVals.i4_k);

    // 6. Section 05 (H & J)
    ['1', '2', '3'].forEach(r => {
        ['x', 'y', 'z'].forEach(axis => {
            setPreviewText(`prev_i5_h${r}_${axis}`, document.getElementById(`i5_h${r}_${axis}`)?.value || defVals[`i5_h${r}_${axis}`]);
        });
        const hAr = document.getElementById(`i5_h${r}_ar`)?.value || defVals[`i5_h${r}_ar`];
        setPreviewText(`prev_i5_h${r}_ar`, hAr);
        colorStatusCell(`prev_i5_h${r}_ar`, hAr);
    });
    setPreviewText('prev_i5_j', document.getElementById('i5_j_res')?.value || defVals.i5_j);

    // 7. Section 06 (G & I)
    ['x', 'y', 'z'].forEach(axis => {
        setPreviewText(`prev_i6_${axis}`, document.getElementById(`i6_${axis}`)?.value || defVals[`i6_${axis}`]);
    });
    const gAr = document.getElementById('i6_ar')?.value || defVals.i6_ar;
    setPreviewText('prev_i6_ar', gAr);
    colorStatusCell('prev_i6_ar', gAr);
    setPreviewText('prev_i6_i', document.getElementById('i6_i_res')?.value || defVals.i6_i);

    // 8. TIR Table
    ['a', 'b', 'c', 'd', 'e', 'f'].forEach(pt => {
        ['0', '90', '180', '270'].forEach(deg => {
            const val = document.getElementById(`tir_${pt}_${deg}`)?.value || defVals.tir[pt][`d${deg}`];
            setPreviewText(`prev_tir_${pt}_${deg}`, val);
        });
        const ar = document.getElementById(`tir_${pt}_ar`)?.value || defVals.tir[pt].ar;
        setPreviewText(`prev_tir_${pt}_ar`, ar);
        colorStatusCell(`prev_tir_${pt}_ar`, ar);
    });

    // 9. Recommendations
    syncRecommendationsPreview();

    // 10. Signatures
    const formatSigDate = (val, def) => {
        if (!val) return def;
        try {
            const parts = val.split('-');
            if (parts.length === 3) {
                const d = new Date(parts[0], parts[1] - 1, parts[2]);
                return d.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
            }
        } catch (e) { }
        return val;
    };

    const checkedD = document.getElementById('sig_checkedDate')?.value;
    const createdD = document.getElementById('sig_createdDate')?.value;
    const reviewedD = document.getElementById('sig_reviewedDate')?.value;

    setPreviewText('prev_sigCheckedName', document.getElementById('sig_checkedName')?.value?.trim() || '............................');
    setPreviewText('prev_sigCheckedTitle', document.getElementById('sig_checkedTitle')?.value || 'QC Inspector');
    setPreviewText('prev_sigCheckedDate', 'Date: ' + formatSigDate(checkedD, 'October 20, 2026'));

    setPreviewText('prev_sigCreatedName', document.getElementById('sig_createdName')?.value?.trim() || '............................');
    setPreviewText('prev_sigCreatedTitle', document.getElementById('sig_createdTitle')?.value || 'QC Inspector');
    setPreviewText('prev_sigCreatedDate', 'Date: ' + formatSigDate(createdD, 'October 21, 2026'));

    setPreviewText('prev_sigReviewedName', document.getElementById('sig_reviewedName')?.value?.trim() || '............................');
    setPreviewText('prev_sigReviewedTitle', document.getElementById('sig_reviewedTitle')?.value || 'Leader QC');
    setPreviewText('prev_sigReviewedDate', 'Date: ' + formatSigDate(reviewedD, 'October 21, 2026'));
}

function evaluateOverallStatus() {
    let hasReject = false;

    // Check all status dropdowns
    document.querySelectorAll('.status-select').forEach(sel => {
        if (sel.value === 'R') hasReject = true;
    });

    const stamp = document.getElementById('prev_stamp');
    if (stamp) {
        if (hasReject) {
            stamp.innerText = 'REJECTED';
            stamp.className = 'doc-stamp badge-red';
            stamp.style.color = '#dc2626';
            stamp.style.borderColor = '#dc2626';
        } else {
            stamp.innerText = 'ACCEPTED';
            stamp.className = 'doc-stamp badge-green';
            stamp.style.color = '#16a34a';
            stamp.style.borderColor = '#16a34a';
        }
    }
}

function setPreviewText(id, text) {
    const el = document.getElementById(id);
    if (el) el.innerText = text;
}

function colorStatusCell(id, status) {
    const el = document.getElementById(id);
    if (el) {
        el.style.color = status === 'R' ? '#dc2626' : '#16a34a';
    }
}

// -------------------------------------------------------------
// Photo Upload and Section Handling
// -------------------------------------------------------------
window.triggerFileInput = function (inputId) {
    document.getElementById(inputId)?.click();
};

window.triggerCameraInput = function (inputId) {
    document.getElementById(inputId)?.click();
};

window.handleSlotUpload = function (input, slotKey) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];

    // Immediate local preview via FileReader
    const reader = new FileReader();
    reader.onload = function (e) {
        const dataUrl = e.target.result;
        renderSlotPreview(slotKey, dataUrl);
    };
    reader.readAsDataURL(file);

    // Asynchronously upload to server
    const formData = new FormData();
    formData.append('photo', file);
    formData.append('section', slotKey);

    const card = document.getElementById(`card_slot_${slotKey}`);
    if (card) card.style.opacity = '0.5';

    fetch('/reports/upload-photo', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: formData
    })
        .then(r => r.json())
        .then(data => {
            if (card) card.style.opacity = '1';
            if (data.success) {
                reportPhotos[slotKey] = data.url;
                renderSlotPreview(slotKey, data.url);
                updatePhotoBadgeCount();
                showToast('Foto berhasil diunggah', 'success');
            } else {
                showToast(data.message || 'Gagal mengunggah foto', 'error');
            }
        })
        .catch(err => {
            if (card) card.style.opacity = '1';
            console.error('Upload photo error:', err);
            showToast('Foto tersimpan di sesi lokal', 'success');
            updatePhotoBadgeCount();
        });
};

window.removeSlotPhoto = function (slotKey, silent = false) {
    const hadPhoto = Boolean(reportPhotos[slotKey]);
    reportPhotos[slotKey] = null;

    // Reset file input and camera input
    const input = document.getElementById(`input_slot_${slotKey}`);
    if (input) input.value = '';
    const camInput = document.getElementById(`camera_slot_${slotKey}`);
    if (camInput) camInput.value = '';

    // Reset card UI
    const card = document.getElementById(`card_slot_${slotKey}`);
    if (card) card.classList.remove('has-image');

    // Reset dropzone view in Tab 3
    const viewContainer = document.getElementById(`view_slot_${slotKey}`);
    if (viewContainer) {
        if (slotKey === 'drawing_dimension') {
            viewContainer.innerHTML = `<div class="slot-empty-icon"><i data-lucide="image-plus"></i><span>Klik atau geser file gambar cetak biru (blueprint dimensi poros)</span></div>`;
        } else if (slotKey === 'finding_component') {
            viewContainer.innerHTML = `<div class="slot-empty-icon"><i data-lucide="camera"></i><span>Klik untuk ambil foto utuh fisik komponen</span></div>`;
        } else if (slotKey === 'tir_diagram') {
            viewContainer.innerHTML = `<div class="slot-empty-icon"><i data-lucide="image-plus"></i><span>Klik atau geser file gambar diagram titik pengukuran runout</span></div>`;
        } else {
            viewContainer.innerHTML = `<div class="slot-empty-icon"><i data-lucide="camera"></i><span>Kamera</span></div>`;
        }
        if (window.lucide) lucide.createIcons();
    }

    // Hide remove button
    const remBtn = document.getElementById(`btn_rem_slot_${slotKey}`);
    if (remBtn) remBtn.style.display = 'none';

    // Reset right-hand preview slot to empty placeholder
    const prevSlot = document.getElementById(`prev_slot_${slotKey}`);
    if (prevSlot) {
        if (slotKey === 'drawing_dimension') {
            prevSlot.innerHTML = `<div class="empty-blueprint-msg">[ Gambar Drawing / Pictures / Dimension Belum Diunggah ]</div>`;
        } else if (slotKey === 'finding_component') {
            prevSlot.innerHTML = `<span class="empty-finding-msg">[ Foto Komponen Utuh Belum Diunggah ]</span>`;
        } else if (slotKey === 'tir_diagram') {
            prevSlot.innerHTML = `<div class="empty-blueprint-msg">[ Gambar Diagram Titik Ukur TIR Belum Diunggah ]</div>`;
        } else if (slotKey.includes('penet')) {
            const num = slotKey.slice(-1);
            prevSlot.innerHTML = `<div class="slot-placeholder">Penetrant ${num}</div>`;
        } else {
            const num = slotKey.slice(-1);
            prevSlot.innerHTML = `<div class="slot-placeholder">Foto ${num}</div>`;
        }
    }

    updatePhotoBadgeCount();
    if (!silent && hadPhoto) {
        showToast('Foto dihapus', 'success');
    }
};

window.useDefaultBlueprint = function (slotKey) {
    let url = '';
    if (slotKey === 'drawing_dimension') {
        url = '/images/doc_assets/shaft_blueprint.png';
    } else if (slotKey === 'tir_diagram') {
        url = '/images/doc_assets/shaft_tir_blueprint.png';
    }
    if (!url) return;

    reportPhotos[slotKey] = url;
    renderSlotPreview(slotKey, url);
    updatePhotoBadgeCount();
    showToast('Standar gambar diterapkan', 'success');
};

function renderSlotPreview(slotKey, imageUrl) {
    reportPhotos[slotKey] = imageUrl;

    // 1. Dropzone preview in Tab 3
    const card = document.getElementById(`card_slot_${slotKey}`);
    if (card) card.classList.add('has-image');

    const viewContainer = document.getElementById(`view_slot_${slotKey}`);
    if (viewContainer) {
        viewContainer.innerHTML = `<img src="${imageUrl}" alt="${slotKey}">`;
    }

    // 2. Show remove button
    const remBtn = document.getElementById(`btn_rem_slot_${slotKey}`);
    if (remBtn) remBtn.style.display = 'inline-flex';

    // 3. Right-hand live document preview slot
    const prevSlot = document.getElementById(`prev_slot_${slotKey}`);
    if (prevSlot) {
        prevSlot.innerHTML = `<img src="${imageUrl}" alt="${slotKey}">`;
        focusPreviewForInput({ id: `input_slot_${slotKey}` });
    }

    updatePhotoBadgeCount();
}

function updatePhotoBadgeCount() {
    let count = 0;
    for (const key in reportPhotos) {
        if (reportPhotos[key]) count++;
    }
    const badge = document.getElementById('photoCountBadge');
    if (badge) badge.innerText = count;
}

// Aliases for backwards compatibility
window.handlePhotoSelect = window.handleSlotUpload;
window.removePhoto = window.removeSlotPhoto;
window.renderPhotoPreview = renderSlotPreview;

// -------------------------------------------------------------
// Recommendations Dynamic Table

// -------------------------------------------------------------
function setupRecommendationsLegacy(recs) {
    const tbody = document.getElementById('recTableBody');
    if (!tbody) return;
    tbody.innerHTML = '';

    const list = recs && recs.length > 0 ? recs : blankDefaultTemplate.recommendations;
    list.forEach((r, idx) => {
        addRecRow(r.item || sprintf('%02d', idx + 1), r.remarks || '', r.recommendation || '');
    });
}

function addRecRowLegacy(item = '', remarks = '', recommendation = '') {
    const tbody = document.getElementById('recTableBody');
    if (!tbody) return;
    const rowIdx = tbody.querySelectorAll('tr').length + 1;
    const formattedItem = item || (rowIdx < 10 ? `0${rowIdx}` : `${rowIdx}`);

    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="text" class="rec-item mono" value="${formattedItem}" style="width:50px;"></td>
        <td><input type="text" class="rec-remarks" value="${remarks}" placeholder="Temuan visual..."></td>
        <td><input type="text" class="rec-rec" value="${recommendation}" placeholder="Rekomendasi tindakan..."></td>
        <td style="text-align: center;">
            <button type="button" class="btn btn-secondary btn-xs" onclick="this.closest('tr').remove(); syncRecommendationsPreview();">
                &times;
            </button>
        </td>
    `;
    tbody.appendChild(tr);

    tr.querySelectorAll('input').forEach(inp => {
        inp.addEventListener('input', syncRecommendationsPreview);
    });

    syncRecommendationsPreview();
}

function syncRecommendationsPreviewLegacy() {
    const previewBody = document.getElementById('prev_recTableBody');
    const findingDesc = document.getElementById('prev_finding_desc');
    const rows = document.querySelectorAll('#recTableBody tr');

    const defaultRecs = [
        { item: '01', remarks: "Visual scratch at OD area \"A\"\nVisual corrosion area spline\nVisual scratch at area surface\nVisual dirty at hole thread area surface", rec: "Cleaning and polish OD area \"A\"\nCleaning area surface\nCleaning and polish area spline\nRetap hole thread area surface" },
        { item: '02', remarks: "Visual corrosion at OD area \"B\"\nVisual corrosion at OD area \"C\"", rec: "Cleaning OD area \"B and C\"" },
        { item: '03', remarks: "Visual scratch at OD area \"D\"\nVisual worn out at area surface (see mark no 1)", rec: "Cleaning and polish OD area \"D\"\nRepair worn out area surface" },
        { item: '04', remarks: "Actual measurement OD area \"F\" in good condition but, Visual pitting and scratch OD area \"F\" (see mark no 1)", rec: "Repair OD area \"F\" to specification" },
        { item: '05', remarks: "Actual measurement OD area \"H\" was undersize\nVisual pitting and scratch at OD area \"H\" (see mark no 1)", rec: "Repair OD area \"H\" to specification" },
        { item: '06', remarks: "Actual measurement OD area \"G\" was undersize\nVisual corrosion at OD area \"G\"\nVisual corrosion area spline\nVisual scratch and corrosion at area surface\nVisual dirty at hole thread area surface", rec: "Repair Cleaning OD area \"G\" to specification\nCleaning and polish area spline\nCleaning and polish area surface\nRetap hole thread area surface" }
    ];

    let userRecs = [];
    rows.forEach((r, idx) => {
        const item = r.querySelector('.rec-item')?.value || (idx < 9 ? `0${idx + 1}` : `${idx + 1}`);
        const remarks = r.querySelector('.rec-remarks')?.value || '';
        const rec = r.querySelector('.rec-rec')?.value || r.querySelector('.rec-recommendation')?.value || '';
        if (remarks.trim() || rec.trim()) {
            userRecs.push({ item, remarks, rec });
        }
    });

    const active = userRecs.length > 0 ? userRecs : defaultRecs;
    if (previewBody) {
        let html = '';
        active.forEach(r => {
            html += `<tr>
                <td style="text-align: center; font-weight: 700; font-family: monospace;">${r.item}</td>
                <td style="white-space: pre-line;">${r.remarks}</td>
                <td style="white-space: pre-line;">${r.rec}</td>
            </tr>`;
        });
        previewBody.innerHTML = html;
    }

    if (findingDesc) {
        findingDesc.innerHTML = '<div><span style="font-weight:700;">&gt;</span> Full image received component OUTPUT SHAFT</div>';
    }
}

function setupRecommendations(recs) {
    const tbody = document.getElementById('recTableBody');
    if (!tbody) return;
    tbody.innerHTML = '';

    const list = recs && recs.length > 0 ? recs : blankDefaultTemplate.recommendations;
    const normalizedRows = [];

    list.forEach((r, idx) => {
        const itemNum = (r.item || (idx < 9 ? `0${idx + 1}` : `${idx + 1}`)).trim();
        const rem = (r.remarks || '').trim();
        const rec = (r.recommendation || r.rec || '').trim();

        if (Array.isArray(r.points) && r.points.length > 0) {
            r.points.forEach((pt, pIdx) => {
                normalizedRows.push({
                    item: itemNum,
                    remarks: pt.remarks || '',
                    recommendation: pt.recommendation || pt.rec || '',
                    isSub: pIdx > 0
                });
            });
        } else {
            const remLines = rem.includes('\n') ? rem.split('\n').map(s => s.trim()).filter(Boolean) : [rem];
            const recLines = rec.includes('\n') ? rec.split('\n').map(s => s.trim()).filter(Boolean) : [rec];
            const max = Math.max(remLines.length, recLines.length, 1);

            for (let i = 0; i < max; i++) {
                normalizedRows.push({
                    item: itemNum,
                    remarks: remLines[i] || '',
                    recommendation: recLines[i] || '',
                    isSub: i > 0
                });
            }
        }
    });

    if (normalizedRows.length === 0) {
        normalizedRows.push({ item: '01', remarks: '', recommendation: '', isSub: false });
    }

    normalizedRows.forEach(row => {
        addRecRow(row.item, row.remarks, row.recommendation, row.isSub);
    });

    syncRecommendationsPreview();
}

function addRecRow(item = '', remarks = '', recommendation = '', isSub = false, insertAfterTr = null) {
    const tbody = document.getElementById('recTableBody');
    if (!tbody) return;

    let formattedItem = item;
    if (!formattedItem) {
        // Dapatkan nomor item terakhir
        const existingItems = Array.from(tbody.querySelectorAll('.rec-item')).map(i => parseInt(i.value, 10)).filter(n => !isNaN(n));
        const nextNum = existingItems.length > 0 ? Math.max(...existingItems) + 1 : 1;
        formattedItem = nextNum < 10 ? `0${nextNum}` : `${nextNum}`;
    }

    const tr = document.createElement('tr');
    tr.setAttribute('data-item', formattedItem);
    tr.setAttribute('data-is-sub', isSub ? 'true' : 'false');

    let itemCellHtml = '';
    if (!isSub) {
        itemCellHtml = `
            <div style="display: flex; align-items: center; justify-content: center; gap: 4px;">
                <input type="text" class="rec-item mono" value="${formattedItem}" style="width: 44px; text-align: center; font-weight: 700;">
                <button type="button" class="btn btn-secondary btn-xs btn-add-sub" onclick="window.addSubRecPoint(this)" title="Tambah baris poin temuan & rekomendasi baru di bawah Item ${formattedItem}" style="font-size: 0.68rem; padding: 2px 5px; white-space: nowrap; font-weight: 600; line-height: 1.2;">
                    + Baris
                </button>
            </div>
        `;
    } else {
        itemCellHtml = `
            <div style="display: flex; align-items: center; justify-content: flex-start; gap: 4px; padding-left: 10px;">
                <span style="color: var(--primary); font-size: 0.85rem; font-weight: 700;">↳</span>
                <input type="text" class="rec-item mono" value="${formattedItem}" style="width: 44px; text-align: center; font-size: 0.78rem; background: #f8fafc; border: 1px dashed #cbd5e1; color: var(--text-muted);" readonly title="Sub-poin dari Item ${formattedItem}">
            </div>
        `;
    }

    tr.innerHTML = `
        <td style="vertical-align: middle; width: 115px; padding: 4px 6px;">${itemCellHtml}</td>
        <td style="vertical-align: middle;"><input type="text" class="rec-remarks" value="${remarks.replace(/"/g, '&quot;')}" placeholder="Temuan visual..."></td>
        <td style="vertical-align: middle;"><input type="text" class="rec-rec" value="${recommendation.replace(/"/g, '&quot;')}" placeholder="Rekomendasi tindakan QC..."></td>
        <td style="text-align: center; vertical-align: middle; width: 45px;">
            <button type="button" class="btn btn-secondary btn-xs" onclick="window.removeRecRow(this)" title="Hapus Baris">
                &times;
            </button>
        </td>
    `;

    if (insertAfterTr && insertAfterTr.nextSibling) {
        tbody.insertBefore(tr, insertAfterTr.nextSibling);
    } else if (insertAfterTr) {
        tbody.appendChild(tr);
    } else {
        tbody.appendChild(tr);
    }

    tr.querySelectorAll('input').forEach(inp => {
        inp.addEventListener('input', () => {
            if (inp.classList.contains('rec-item')) {
                // Update attribute data-item & sub-baris jika ada
                const newItemVal = inp.value.trim();
                tr.setAttribute('data-item', newItemVal);
            }
            syncRecommendationsPreview();
        });
    });

    syncRecommendationsPreview();
    return tr;
}

window.addSubRecPoint = function (btn) {
    const tr = btn.closest('tr');
    if (!tr) return;
    const itemVal = (tr.querySelector('.rec-item')?.value || '01').trim();

    // Temukan baris terakhir dalam grup item ini
    const tbody = document.getElementById('recTableBody');
    const allRows = Array.from(tbody.querySelectorAll('tr'));
    const startIdx = allRows.indexOf(tr);

    let lastTr = tr;
    for (let i = startIdx + 1; i < allRows.length; i++) {
        const row = allRows[i];
        if (row.getAttribute('data-is-sub') === 'true' && (row.querySelector('.rec-item')?.value || '').trim() === itemVal) {
            lastTr = row;
        } else {
            break;
        }
    }

    const newTr = addRecRow(itemVal, '', '', true, lastTr);
    if (newTr) {
        const inpRemarks = newTr.querySelector('.rec-remarks');
        if (inpRemarks) inpRemarks.focus();
    }
};

window.removeRecRow = function (btn) {
    const tr = btn.closest('tr');
    if (!tr) return;

    const tbody = document.getElementById('recTableBody');
    const allRows = Array.from(tbody.querySelectorAll('tr'));
    const curIdx = allRows.indexOf(tr);
    const isSub = tr.getAttribute('data-is-sub') === 'true';
    const itemVal = (tr.querySelector('.rec-item')?.value || '').trim();

    // Jika baris utama yang dihapus, dan ada sub-baris di bawahnya dengan item yang sama,
    // promosikan sub-baris pertama menjadi baris utama
    if (!isSub && curIdx < allRows.length - 1) {
        const nextTr = allRows[curIdx + 1];
        if (nextTr.getAttribute('data-is-sub') === 'true' && (nextTr.querySelector('.rec-item')?.value || '').trim() === itemVal) {
            nextTr.setAttribute('data-is-sub', 'false');
            const itemTd = nextTr.querySelector('td:first-child');
            if (itemTd) {
                itemTd.innerHTML = `
                    <div style="display: flex; align-items: center; justify-content: center; gap: 4px;">
                        <input type="text" class="rec-item mono" value="${itemVal}" style="width: 44px; text-align: center; font-weight: 700;">
                        <button type="button" class="btn btn-secondary btn-xs btn-add-sub" onclick="window.addSubRecPoint(this)" title="Tambah baris poin temuan & rekomendasi baru di bawah Item ${itemVal}" style="font-size: 0.68rem; padding: 2px 5px; white-space: nowrap; font-weight: 600; line-height: 1.2;">
                            + Baris
                        </button>
                    </div>
                `;
            }
        }
    }

    tr.remove();
    syncRecommendationsPreview();
};

function syncRecommendationsPreview() {
    const previewBody = document.getElementById('prev_recTableBody');
    const findingDesc = document.getElementById('prev_finding_desc');
    const rows = document.querySelectorAll('#recTableBody tr');

    // Grouping baris berdasarkan nomor Item
    const groups = [];
    let currentGroup = null;

    rows.forEach((r, idx) => {
        const itemVal = (r.querySelector('.rec-item')?.value || '').trim() || (idx < 9 ? `0${idx + 1}` : `${idx + 1}`);
        const remarks = (r.querySelector('.rec-remarks')?.value || '').trim();
        const recommendation = (r.querySelector('.rec-rec')?.value || '').trim();

        if (!currentGroup || currentGroup.item !== itemVal) {
            currentGroup = { item: itemVal, points: [] };
            groups.push(currentGroup);
        }
        currentGroup.points.push({ remarks, recommendation });
    });

    if (previewBody) {
        previewBody.innerHTML = '';
        if (groups.length === 0) {
            previewBody.innerHTML = '<tr><td colspan="3" class="center" style="color:#94a3b8; padding:8px;">Tidak ada rekomendasi</td></tr>';
        } else {
            groups.forEach(g => {
                const count = g.points.length;
                g.points.forEach((pt, pIdx) => {
                    const ptr = document.createElement('tr');
                    let html = '';
                    if (pIdx === 0) {
                        html += `<td rowspan="${count}" style="text-align: center; font-weight: 700; font-family: 'JetBrains Mono', monospace; vertical-align: middle; border: 1px solid #000; width: 40px; padding: 3px;">${g.item}</td>`;
                    }
                    html += `<td style="border: 1px solid #000; padding: 3px 6px;">${pt.remarks || '-'}</td>`;
                    html += `<td style="border: 1px solid #000; padding: 3px 6px;">${pt.recommendation || '-'}</td>`;
                    ptr.innerHTML = html;
                    previewBody.appendChild(ptr);
                });
            });
        }
    }

    if (findingDesc) {
        if (groups.length === 0) {
            findingDesc.innerHTML = '<span style="color:#9ca3af; font-style:italic;">— Temuan akan muncul dari tab Rekomendasi —</span>';
        } else {
            const listItems = [];
            groups.forEach((g, gIdx) => {
                const validRemarks = g.points.map(p => p.remarks).filter(Boolean);
                if (validRemarks.length > 0) {
                    listItems.push(
                        `<div style="margin-bottom:3px;"><strong>${gIdx + 1}. [Item ${g.item}]</strong> ${validRemarks.join('; ')}</div>`
                    );
                }
            });
            findingDesc.innerHTML = listItems.length > 0 ? listItems.join('') : '<span style="color:#9ca3af; font-style:italic;">— Belum ada temuan visual diisi —</span>';
        }
    }
}

// -------------------------------------------------------------
// Form Data Extraction & Submission
// -------------------------------------------------------------
function extractFormData() {
    let sscId = document.getElementById('g_sscId')?.value || '';
    if (!sscId.trim()) {
        sscId = 'SSC SBY-' + Math.floor(5000 + Math.random() * 4000);
        const sscEl = document.getElementById('g_sscId');
        if (sscEl) sscEl.value = sscId;
    }

    const payload = {
        general: {
            sscId: sscId.trim(),
            reportNo: document.getElementById('g_reportNo')?.value || '',
            date: document.getElementById('g_date')?.value || '',
            customer: document.getElementById('g_customer')?.value || '',
            partName: document.getElementById('g_partName')?.value || 'OUTPUT SHAFT',
            inspectionType: document.getElementById('g_inspectionType')?.value || 'Pre',
            modelUnit: document.getElementById('g_modelUnit')?.value || 'HT051',
            jobNo: document.getElementById('g_jobNo')?.value || '-',
            quoteNo: document.getElementById('g_quoteNo')?.value || '-',
            partNo: document.getElementById('g_partNo')?.value || '-',
            docNo: document.getElementById('g_docNo')?.value || 'FORM-QC-002',
            rev: document.getElementById('g_rev')?.value || '04',
            responsibility: document.getElementById('g_responsibility')?.value || 'QC Inspector',
            effectiveDate: document.getElementById('g_effectiveDate')?.value || '',
            page: document.getElementById('g_page')?.value || '1'
        },
        sections: {
            item01: {
                title: "OD Area \"A\" and OD Spline",
                spec: "Ø79 ± 0.25",
                x: document.getElementById('i1_x')?.value || '',
                y: document.getElementById('i1_y')?.value || '',
                z: document.getElementById('i1_z')?.value || '',
                ar: document.getElementById('i1_ar')?.value || 'A',
                remark1: document.getElementById('i1_remark1')?.value || '',
                lengthResult: document.getElementById('i1_length')?.value || '',
                penetrantRemark: "No indication crack at OD area \"A\" and OD spline after penetrant check"
            },
            item02: {
                title: "OD Area \"B and C\"",
                itemB: {
                    spec: "Ø82 ± 0.25",
                    row1: { x: document.getElementById('i2_b1_x')?.value || '', y: document.getElementById('i2_b1_y')?.value || '', z: document.getElementById('i2_b1_z')?.value || '', ar: document.getElementById('i2_b1_ar')?.value || 'A' },
                    row2: { x: document.getElementById('i2_b2_x')?.value || '', y: document.getElementById('i2_b2_y')?.value || '', z: document.getElementById('i2_b2_z')?.value || '', ar: document.getElementById('i2_b2_ar')?.value || 'A' },
                    row3: { x: document.getElementById('i2_b3_x')?.value || '', y: document.getElementById('i2_b3_y')?.value || '', z: document.getElementById('i2_b3_z')?.value || '', ar: document.getElementById('i2_b3_ar')?.value || 'A' }
                },
                itemC: {
                    spec: "Ø77 ± 0.25",
                    row1: { x: document.getElementById('i2_c1_x')?.value || '', y: document.getElementById('i2_c1_y')?.value || '', z: document.getElementById('i2_c1_z')?.value || '', ar: document.getElementById('i2_c1_ar')?.value || 'A' },
                    row2: { x: document.getElementById('i2_c2_x')?.value || '', y: document.getElementById('i2_c2_y')?.value || '', z: document.getElementById('i2_c2_z')?.value || '', ar: document.getElementById('i2_c2_ar')?.value || 'A' },
                    row3: { x: document.getElementById('i2_c3_x')?.value || '', y: document.getElementById('i2_c3_y')?.value || '', z: document.getElementById('i2_c3_z')?.value || '', ar: document.getElementById('i2_c3_ar')?.value || 'A' }
                },
                remarks: ["Actual measurement OD area \"B and C\" in good condition"],
                penetrantRemark: "No indication crack at OD Area \"B and C\" after penetrant check"
            },
            item03: {
                title: "OD Area \"D\"",
                itemD: {
                    spec: "Ø118 ± 0.5",
                    x: document.getElementById('i3_d_x')?.value || '',
                    y: document.getElementById('i3_d_y')?.value || '',
                    z: document.getElementById('i3_d_z')?.value || '',
                    ar: document.getElementById('i3_d_ar')?.value || 'A'
                },
                distanceE: {
                    spec: "13 ± 0.5",
                    x: document.getElementById('i3_e_x')?.value || '',
                    y: document.getElementById('i3_e_y')?.value || '',
                    z: document.getElementById('i3_e_z')?.value || '',
                    ar: document.getElementById('i3_e_ar')?.value || 'A'
                },
                remarks: ["Actual measurement OD area \"D\" in good condition", "Actual distance area \"E\" in good condition"],
                penetrantRemark: "No indication crack at OD area \"D\" after penetrant check"
            },
            item04: {
                title: "OD Area \"F\"",
                itemF: {
                    spec: "Ø100,01 ± 0,008",
                    row1: { x: document.getElementById('i4_f1_x')?.value || '', y: document.getElementById('i4_f1_y')?.value || '', z: document.getElementById('i4_f1_z')?.value || '', ar: document.getElementById('i4_f1_ar')?.value || 'A' },
                    row2: { x: document.getElementById('i4_f2_x')?.value || '', y: document.getElementById('i4_f2_y')?.value || '', z: document.getElementById('i4_f2_z')?.value || '', ar: document.getElementById('i4_f2_ar')?.value || 'A' },
                    row3: { x: document.getElementById('i4_f3_x')?.value || '', y: document.getElementById('i4_f3_y')?.value || '', z: document.getElementById('i4_f3_z')?.value || '', ar: document.getElementById('i4_f3_ar')?.value || 'A' }
                },
                distanceK: { spec: "N/A", result: document.getElementById('i4_k_res')?.value || '', ar: "-" },
                remarks: ["Actual measurement OD area \"F\" in good condition"],
                penetrantRemark: "No indication crack at area OD area \"F\" after penetrant check"
            },
            item05: {
                title: "OD Area \"H\"",
                itemH: {
                    spec: "Ø94 ± 0,013",
                    row1: { x: document.getElementById('i5_h1_x')?.value || '', y: document.getElementById('i5_h1_y')?.value || '', z: document.getElementById('i5_h1_z')?.value || '', ar: document.getElementById('i5_h1_ar')?.value || 'A' },
                    row2: { x: document.getElementById('i5_h2_x')?.value || '', y: document.getElementById('i5_h2_y')?.value || '', z: document.getElementById('i5_h2_z')?.value || '', ar: document.getElementById('i5_h2_ar')?.value || 'A' },
                    row3: { x: document.getElementById('i5_h3_x')?.value || '', y: document.getElementById('i5_h3_y')?.value || '', z: document.getElementById('i5_h3_z')?.value || '', ar: document.getElementById('i5_h3_ar')?.value || 'A' }
                },
                distanceJ: { spec: "N/A", result: document.getElementById('i5_j_res')?.value || '', ar: "-" },
                remarks: ["Actual measurement OD area \"H\" in good condition"],
                penetrantRemark: "No indication crack at area OD area \"H\" after penetrant check"
            },
            item06: {
                title: "OD Area \"G\" and OD Spline",
                itemG: {
                    spec: "Ø84,2 ± 0,13",
                    x: document.getElementById('i6_x')?.value || '',
                    y: document.getElementById('i6_y')?.value || '',
                    z: document.getElementById('i6_z')?.value || '',
                    ar: document.getElementById('i6_ar')?.value || 'A'
                },
                distanceI: { spec: "N/A", result: document.getElementById('i6_i_res')?.value || '', ar: "-" },
                remarks: ["Actual measurement OD area \"G\" in good condition"],
                penetrantRemark: "No indication crack at area OD spline after penetrant check"
            }
        },
        tir: {
            spec: "Max 0,2",
            datum: "Datum at item \"B and E\"",
            items: [
                { item: "A", d0: document.getElementById('tir_a_0')?.value || '', d90: document.getElementById('tir_a_90')?.value || '', d180: document.getElementById('tir_a_180')?.value || '', d270: document.getElementById('tir_a_270')?.value || '', ar: document.getElementById('tir_a_ar')?.value || 'A' },
                { item: "B", d0: document.getElementById('tir_b_0')?.value || '', d90: document.getElementById('tir_b_90')?.value || '', d180: document.getElementById('tir_b_180')?.value || '', d270: document.getElementById('tir_b_270')?.value || '', ar: document.getElementById('tir_b_ar')?.value || 'A' },
                { item: "C", d0: document.getElementById('tir_c_0')?.value || '', d90: document.getElementById('tir_c_90')?.value || '', d180: document.getElementById('tir_c_180')?.value || '', d270: document.getElementById('tir_c_270')?.value || '', ar: document.getElementById('tir_c_ar')?.value || 'A' },
                { item: "D", d0: document.getElementById('tir_d_0')?.value || '', d90: document.getElementById('tir_d_90')?.value || '', d180: document.getElementById('tir_d_180')?.value || '', d270: document.getElementById('tir_d_270')?.value || '', ar: document.getElementById('tir_d_ar')?.value || 'A' },
                { item: "E", d0: document.getElementById('tir_e_0')?.value || '', d90: document.getElementById('tir_e_90')?.value || '', d180: document.getElementById('tir_e_180')?.value || '', d270: document.getElementById('tir_e_270')?.value || '', ar: document.getElementById('tir_e_ar')?.value || 'A' },
                { item: "F", d0: document.getElementById('tir_f_0')?.value || '', d90: document.getElementById('tir_f_90')?.value || '', d180: document.getElementById('tir_f_180')?.value || '', d270: document.getElementById('tir_f_270')?.value || '', ar: document.getElementById('tir_f_ar')?.value || 'A' }
            ]
        },
        recommendations: [],
        signatures: {
            checkedBy: { name: document.getElementById('sig_checkedName')?.value || '', title: document.getElementById('sig_checkedTitle')?.value || '', date: document.getElementById('sig_checkedDate')?.value || '' },
            createdBy: { name: document.getElementById('sig_createdName')?.value || '', title: document.getElementById('sig_createdTitle')?.value || '', date: document.getElementById('sig_createdDate')?.value || '' },
            reviewedBy: { name: document.getElementById('sig_reviewedName')?.value || '', title: document.getElementById('sig_reviewedTitle')?.value || '', date: document.getElementById('sig_reviewedDate')?.value || '' }
        },
        checker_id: (() => {
            const sel = document.getElementById('sig_checkedName');
            const uid = sel?.selectedOptions?.[0]?.dataset?.userId;
            return uid ? Number(uid) : null;
        })(),
        reviewer_id: (() => {
            const sel = document.getElementById('sig_reviewedName');
            const uid = sel?.selectedOptions?.[0]?.dataset?.userId;
            return uid ? Number(uid) : null;
        })(),
        photos: reportPhotos
    };

    // Extract dynamic recommendations
    document.querySelectorAll('#recTableBody tr').forEach(r => {
        const item = r.querySelector('.rec-item')?.value || '';
        const remarks = r.querySelector('.rec-remarks')?.value || '';
        const recommendation = r.querySelector('.rec-rec')?.value || '';
        if (item || remarks || recommendation) {
            payload.recommendations.push({ item, remarks, recommendation });
        }
    });

    return payload;
}

let isSavingReport = false;

function saveReportData(andDownloadExcel = false, submitAction = null) {
    if (isSavingReport) return;

    let sscEl = document.getElementById('g_sscId');
    if (!sscEl || !sscEl.value.trim()) {
        showToast('Mohon isi SSC ID terlebih dahulu!', 'error');
        if (sscEl) sscEl.focus();
        return;
    }
    const payload = extractFormData();
    if (!payload) return;

    const reportIdInput = document.getElementById('reportId');
    const reportId = reportIdInput?.value;
    const isUpdate = Boolean(reportId);
    const url = isUpdate ? `/reports/${reportId}` : '/reports';
    const method = isUpdate ? 'PUT' : 'POST';

    const resolvedAction = submitAction || (andDownloadExcel
        ? ((!isUpdate || window.qcReportStatus === 'draft') ? 'draft' : 'save')
        : 'submit');
    payload.submit_action = resolvedAction;

    isSavingReport = true;
    const actionBtnIds = [
        'btnTopDraft', 'btnFooterDraft', 'btnTopSave', 'btnFooterSubmit',
        'btnTopExportExcel', 'btnTopResetBlank', 'btnResetBlank',
        'btnMobileMenuTrigger', 'btnMobileDraft', 'btnMobileExportExcel', 'btnMobileResetBlank'
    ];
    const setButtonsBusy = (busy) => {
        actionBtnIds.forEach(id => {
            const btn = document.getElementById(id);
            if (!btn) return;
            btn.disabled = busy;
            btn.classList.toggle('is-loading', busy);
        });
    };
    setButtonsBusy(true);

    const progressMsg = resolvedAction === 'draft'
        ? 'Menyimpan draft laporan...'
        : (isUpdate && window.qcReportStatus !== 'draft' ? 'Menyimpan perubahan laporan...' : 'Mengirim pengajuan laporan...');
    showToast(progressMsg, 'loading');

    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
        .then(async r => {
            const data = await r.json().catch(() => ({}));
            if (!r.ok) {
                throw new Error(data.message || `HTTP ${r.status}`);
            }
            return data;
        })
        .then(data => {
            if (data.success || data.id || data.report) {
                const savedReport = data.report || data;
                const savedId = savedReport.id || reportId;
                if (reportIdInput) reportIdInput.value = savedId;
                currentReportId = savedId;
                if (savedReport.review_status) {
                    window.qcReportStatus = savedReport.review_status;
                }
                if (!isUpdate && savedId && window.history?.replaceState) {
                    window.history.replaceState({}, '', `/reports/${savedId}/edit`);
                }

                showToast(data.message || 'Laporan berhasil disimpan!', 'success');

                if (andDownloadExcel) {
                    setButtonsBusy(false);
                    window.location.href = `/reports/${savedId}/export`;
                } else {
                    setTimeout(() => {
                        window.location.href = '/reports?tab=mine';
                    }, 700);
                }
            } else {
                setButtonsBusy(false);
                showToast('Gagal menyimpan laporan: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(err => {
            console.error('Save error:', err);
            setButtonsBusy(false);
            showToast('Gagal menyimpan: ' + (err.message || 'Terjadi kesalahan koneksi.'), 'error');
        })
        .finally(() => {
            isSavingReport = false;
        });
}

function setupButtonActions() {
    // Export Dropdown Toggle (PDF & Excel)
    const exportWrap = document.getElementById('topExportDropdown');
    const exportTrigger = document.getElementById('btnExportDropdownTrigger');
    const exportMenu = document.getElementById('topExportDropdownMenu');

    if (exportWrap && exportTrigger && exportMenu) {
        const toggleExportDropdown = (open) => {
            const willOpen = open !== undefined ? open : !exportWrap.classList.contains('is-open');
            exportWrap.classList.toggle('is-open', willOpen);
            exportTrigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        };

        exportTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleExportDropdown();
        });

        exportMenu.querySelectorAll('.export-menu-item').forEach(item => {
            item.addEventListener('click', () => {
                toggleExportDropdown(false);
            });
        });

        document.addEventListener('click', (e) => {
            if (!exportWrap.contains(e.target)) {
                toggleExportDropdown(false);
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && exportWrap.classList.contains('is-open')) {
                toggleExportDropdown(false);
            }
        });
    }

    // Mobile Action Dropdown Toggle (More Options [ ••• ])
    const mobileDropdown = document.getElementById('mobileActionDropdown');
    const mobileTrigger = document.getElementById('btnMobileMenuTrigger');
    const mobileMenu = document.getElementById('mobileMenuPopup');

    if (mobileDropdown && mobileTrigger) {
        const toggleMobileMenu = (open) => {
            const willOpen = open !== undefined ? open : !mobileDropdown.classList.contains('is-open');
            mobileDropdown.classList.toggle('is-open', willOpen);
            mobileTrigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        };

        window.closeMobileMenu = () => toggleMobileMenu(false);

        mobileTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMobileMenu();
        });

        if (mobileMenu) {
            mobileMenu.querySelectorAll('.mobile-menu-item').forEach(item => {
                item.addEventListener('click', () => {
                    toggleMobileMenu(false);
                });
            });
        }

        document.addEventListener('click', (e) => {
            if (!mobileDropdown.contains(e.target)) {
                toggleMobileMenu(false);
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && mobileDropdown.classList.contains('is-open')) {
                toggleMobileMenu(false);
            }
        });
    }

    // Draft & Submit buttons (Top bar & Studio Footer)
    document.getElementById('btnTopDraft')?.addEventListener('click', () => saveReportData(false, 'draft'));
    document.getElementById('btnMobileDraft')?.addEventListener('click', () => {
        window.closeMobileMenu?.();
        saveReportData(false, 'draft');
    });
    document.getElementById('btnFooterDraft')?.addEventListener('click', () => saveReportData(false, 'draft'));
    document.getElementById('btnTopSave')?.addEventListener('click', () => saveReportData(false, 'submit'));
    document.getElementById('btnFooterSubmit')?.addEventListener('click', () => saveReportData(false, 'submit'));

    // Save & Download Excel (Top bar - preserves draft if not yet submitted)
    document.getElementById('btnTopExportExcel')?.addEventListener('click', () => saveReportData(true));
    document.getElementById('btnMobileExportExcel')?.addEventListener('click', () => {
        window.closeMobileMenu?.();
        saveReportData(true);
    });

    // Reset blank button (Studio footer & Top action bar)
    const handleResetBlank = async () => {
        const ok = window.qcConfirm
            ? await window.qcConfirm({
                title: 'Kosongkan Form Inspeksi',
                message: 'Kosongkan semua nilai input dan mulai form baru dari awal?',
                confirmText: 'Ya, Kosongkan',
                cancelText: 'Batal',
                variant: 'warning'
            })
            : confirm('Kosongkan semua nilai input dan mulai form baru?');
        if (ok) {
            loadDefaultBlankForm();
        }
    };
    document.getElementById('btnResetBlank')?.addEventListener('click', handleResetBlank);
    document.getElementById('btnTopResetBlank')?.addEventListener('click', handleResetBlank);
    document.getElementById('btnMobileResetBlank')?.addEventListener('click', () => {
        window.closeMobileMenu?.();
        handleResetBlank();
    });

    // Add recommendation row button
    document.getElementById('btnAddRecRow')?.addEventListener('click', () => {
        addRecRow();
    });

    // PDF Export Buttons (Opens clean 6-page official report matching PDF export)
    document.getElementById('btnPrintPreview')?.addEventListener('click', (e) => {
        e.preventDefault();
        window.exportOfficialPdf();
    });
    document.getElementById('btnTopExportPdf')?.addEventListener('click', (e) => {
        e.preventDefault();
        window.exportOfficialPdf();
    });
    document.getElementById('btnMobileExportPdf')?.addEventListener('click', (e) => {
        e.preventDefault();
        window.closeMobileMenu?.();
        window.exportOfficialPdf();
    });

    // Component dropdown change
    document.getElementById('componentSelect')?.addEventListener('change', (e) => {
        const val = e.target.value;
        if (val !== 'output_shaft') {
            if (window.qcAlert) {
                window.qcAlert({
                    title: 'Template Belum Tersedia',
                    message: 'Template Excel untuk komponen ini belum tersedia. Menunggu dari divisi terkait.',
                    variant: 'info'
                });
            } else {
                showToast('Template Excel untuk komponen ini belum tersedia.', 'info');
            }
            e.target.value = 'output_shaft';
        }
    });
}

// -------------------------------------------------------------
// Form Initialization & Populating
// -------------------------------------------------------------
function loadDefaultBlankForm() {
    currentReportId = null;
    document.getElementById('reportId').value = '';

    // Reset inputs
    document.getElementById('g_sscId').value = 'SSC-' + Math.floor(1000 + Math.random() * 9000);
    document.getElementById('g_reportNo').value = '';
    document.getElementById('g_date').value = new Date().toISOString().split('T')[0];
    document.getElementById('g_customer').value = '';
    document.getElementById('g_jobNo').value = '-';
    document.getElementById('g_quoteNo').value = '-';
    document.getElementById('g_partNo').value = '-';

    // Clear dimension values
    const dimInputs = document.querySelectorAll('.dim-input, .tir-input');
    dimInputs.forEach(i => i.value = '');

    // Reset selects to A
    document.querySelectorAll('.status-select').forEach(s => s.value = 'A');

    // Reset photos
    for (const key in reportPhotos) {
        removeSlotPhoto(key, true);
    }

    // Reset recommendations
    setupRecommendations([
        { item: "01", remarks: "", recommendation: "" }
    ]);

    // Reset signatures to defaults (Created by defaults to logged-in account)
    setValue('sig_checkedName', '');
    setValue('sig_checkedTitle', 'QC Inspector');
    setValue('sig_checkedDate', new Date().toISOString().split('T')[0]);

    setValue('sig_createdName', window.qcCurrentUserName || '');
    setValue('sig_createdTitle', 'QC Inspector');
    setValue('sig_createdDate', new Date().toISOString().split('T')[0]);

    setValue('sig_reviewedName', '');
    setValue('sig_reviewedTitle', 'Leader QC');
    setValue('sig_reviewedDate', new Date().toISOString().split('T')[0]);

    if (window.syncSigChips) {
        ['sig_checkedName', 'sig_createdName', 'sig_reviewedName'].forEach(window.syncSigChips);
    }

    updateLivePreview();
    showToast('Form baru telah disiapkan dengan data kosong', 'success');
}

function loadReportForEdit(id) {
    showToast('Memuat data laporan...', 'success');
    fetch(`/reports/${id}`, {
        headers: { 'Accept': 'application/json' }
    })
        .then(r => r.json())
        .then(data => {
            populateFormWithData(data);
            showToast(`Laporan ${data.ssc_id} berhasil dimuat`, 'success');
        })
        .catch(err => {
            console.error('Load report error:', err);
            showToast('Gagal memuat data laporan', 'error');
        });
}

function populateFormWithData(data) {
    if (!data) return;

    // General
    setValue('g_sscId', data.ssc_id);
    setValue('g_reportNo', data.report_no);
    setValue('g_date', data.date ? data.date.split('T')[0] : '');
    setValue('g_customer', data.customer);
    setValue('g_partName', data.part_name || 'OUTPUT SHAFT');
    setValue('g_inspectionType', data.inspection_type || 'Pre');
    setValue('g_modelUnit', data.model_unit || 'HT051');
    setValue('g_jobNo', data.job_no || '-');
    setValue('g_quoteNo', data.quote_no || '-');
    setValue('g_partNo', data.part_no || '-');

    const sec = typeof data.sections === 'string' ? JSON.parse(data.sections) : (data.sections || {});

    // Item 01
    if (sec.item01) {
        setValue('i1_x', sec.item01.x);
        setValue('i1_y', sec.item01.y);
        setValue('i1_z', sec.item01.z);
        setValue('i1_ar', sec.item01.ar || 'A');
        setValue('i1_length', sec.item01.lengthResult);
        setValue('i1_remark1', sec.item01.remark1);
    }

    // Item 02
    if (sec.item02) {
        const b = sec.item02.itemB;
        if (b) {
            setValue('i2_b1_x', b.row1?.x); setValue('i2_b1_y', b.row1?.y); setValue('i2_b1_z', b.row1?.z); setValue('i2_b1_ar', b.row1?.ar || 'A');
            setValue('i2_b2_x', b.row2?.x); setValue('i2_b2_y', b.row2?.y); setValue('i2_b2_z', b.row2?.z); setValue('i2_b2_ar', b.row2?.ar || 'A');
            setValue('i2_b3_x', b.row3?.x); setValue('i2_b3_y', b.row3?.y); setValue('i2_b3_z', b.row3?.z); setValue('i2_b3_ar', b.row3?.ar || 'A');
        }
        const c = sec.item02.itemC;
        if (c) {
            setValue('i2_c1_x', c.row1?.x); setValue('i2_c1_y', c.row1?.y); setValue('i2_c1_z', c.row1?.z); setValue('i2_c1_ar', c.row1?.ar || 'A');
            setValue('i2_c2_x', c.row2?.x); setValue('i2_c2_y', c.row2?.y); setValue('i2_c2_z', c.row2?.z); setValue('i2_c2_ar', c.row2?.ar || 'A');
            setValue('i2_c3_x', c.row3?.x); setValue('i2_c3_y', c.row3?.y); setValue('i2_c3_z', c.row3?.z); setValue('i2_c3_ar', c.row3?.ar || 'A');
        }
    }

    // Item 03
    if (sec.item03) {
        setValue('i3_d_x', sec.item03.itemD?.x); setValue('i3_d_y', sec.item03.itemD?.y); setValue('i3_d_z', sec.item03.itemD?.z); setValue('i3_d_ar', sec.item03.itemD?.ar || 'A');
        setValue('i3_e_x', sec.item03.distanceE?.x); setValue('i3_e_y', sec.item03.distanceE?.y); setValue('i3_e_z', sec.item03.distanceE?.z); setValue('i3_e_ar', sec.item03.distanceE?.ar || 'A');
    }

    // Item 04
    if (sec.item04) {
        const f = sec.item04.itemF;
        if (f) {
            setValue('i4_f1_x', f.row1?.x); setValue('i4_f1_y', f.row1?.y); setValue('i4_f1_z', f.row1?.z); setValue('i4_f1_ar', f.row1?.ar || 'A');
            setValue('i4_f2_x', f.row2?.x); setValue('i4_f2_y', f.row2?.y); setValue('i4_f2_z', f.row2?.z); setValue('i4_f2_ar', f.row2?.ar || 'A');
            setValue('i4_f3_x', f.row3?.x); setValue('i4_f3_y', f.row3?.y); setValue('i4_f3_z', f.row3?.z); setValue('i4_f3_ar', f.row3?.ar || 'A');
        }
        setValue('i4_k_res', sec.item04.distanceK?.result);
    }

    // Item 05
    if (sec.item05) {
        const h = sec.item05.itemH;
        if (h) {
            setValue('i5_h1_x', h.row1?.x); setValue('i5_h1_y', h.row1?.y); setValue('i5_h1_z', h.row1?.z); setValue('i5_h1_ar', h.row1?.ar || 'A');
            setValue('i5_h2_x', h.row2?.x); setValue('i5_h2_y', h.row2?.y); setValue('i5_h2_z', h.row2?.z); setValue('i5_h2_ar', h.row2?.ar || 'A');
            setValue('i5_h3_x', h.row3?.x); setValue('i5_h3_y', h.row3?.y); setValue('i5_h3_z', h.row3?.z); setValue('i5_h3_ar', h.row3?.ar || 'A');
        }
        setValue('i5_j_res', sec.item05.distanceJ?.result);
    }

    // Item 06
    if (sec.item06) {
        setValue('i6_x', sec.item06.itemG?.x); setValue('i6_y', sec.item06.itemG?.y); setValue('i6_z', sec.item06.itemG?.z); setValue('i6_ar', sec.item06.itemG?.ar || 'A');
        setValue('i6_i_res', sec.item06.distanceI?.result);
    }

    // TIR
    const tir = typeof data.tir === 'string' ? JSON.parse(data.tir) : (data.tir || {});
    if (tir.items && Array.isArray(tir.items)) {
        const pts = ['a', 'b', 'c', 'd', 'e', 'f'];
        tir.items.forEach((item, idx) => {
            if (idx < pts.length) {
                const pt = pts[idx];
                setValue(`tir_${pt}_0`, item.d0);
                setValue(`tir_${pt}_90`, item.d90);
                setValue(`tir_${pt}_180`, item.d180);
                setValue(`tir_${pt}_270`, item.d270);
                setValue(`tir_${pt}_ar`, item.ar || 'A');
            }
        });
    }

    // Recommendations
    const recs = typeof data.recommendations === 'string' ? JSON.parse(data.recommendations) : (data.recommendations || []);
    setupRecommendations(recs);

    if (data.review_status) {
        window.qcReportStatus = data.review_status;
    }

    // Signatures
    const sig = typeof data.signatures === 'string' ? JSON.parse(data.signatures) : (data.signatures || {});
    if (sig.checkedBy) {
        setValue('sig_checkedName', sig.checkedBy.name);
        setValue('sig_checkedTitle', sig.checkedBy.title);
        setValue('sig_checkedDate', sig.checkedBy.date);
    }
    if (data.checker_id) {
        const chkSelect = document.getElementById('sig_checkedName');
        const matchedChk = chkSelect ? Array.from(chkSelect.options).find(opt => Number(opt.dataset.userId) === Number(data.checker_id)) : null;
        if (matchedChk) {
            chkSelect.value = matchedChk.value;
        }
    }
    if (sig.createdBy) {
        setValue('sig_createdName', sig.createdBy.name);
        setValue('sig_createdTitle', sig.createdBy.title);
        setValue('sig_createdDate', sig.createdBy.date);
    }
    if (sig.reviewedBy) {
        setValue('sig_reviewedName', sig.reviewedBy.name);
        setValue('sig_reviewedTitle', sig.reviewedBy.title);
        setValue('sig_reviewedDate', sig.reviewedBy.date);
    }
    if (data.reviewer_id) {
        const revSelect = document.getElementById('sig_reviewedName');
        const matchedById = revSelect ? Array.from(revSelect.options).find(opt => Number(opt.dataset.userId) === Number(data.reviewer_id)) : null;
        if (matchedById) {
            revSelect.value = matchedById.value;
        }
    }

    if (window.syncSigChips) {
        ['sig_checkedName', 'sig_createdName', 'sig_reviewedName'].forEach(window.syncSigChips);
    }

    // Photos
    const photos = typeof data.photos === 'string' ? JSON.parse(data.photos) : (data.photos || {});
    for (const key in photos) {
        if (photos[key]) {
            renderSlotPreview(key, photos[key]);
        }
    }

    updateLivePreview();
}

function setValue(id, val) {
    const el = document.getElementById(id);
    if (!el) return;
    const cleanVal = (val !== null && val !== undefined) ? String(val) : '';
    if (el.tagName === 'SELECT' && cleanVal !== '') {
        const matched = Array.from(el.options).find(opt => opt.value.toLowerCase() === cleanVal.trim().toLowerCase());
        if (matched) {
            el.value = matched.value;
            return;
        }
        const opt = document.createElement('option');
        opt.value = cleanVal;
        opt.textContent = cleanVal;
        el.appendChild(opt);
    }
    el.value = cleanVal;
}

// -------------------------------------------------------------
// Toast Notifications
// -------------------------------------------------------------
function showToast(message, type = 'success', customTitle = null) {
    if (typeof window.qcNotify === 'function') {
        return window.qcNotify(message, type, customTitle);
    }

    const container = document.getElementById('toastContainer');
    if (!container) return null;

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `<div class="toast-body"><div class="toast-msg"></div></div>`;
    toast.querySelector('.toast-msg').textContent = message;
    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('toast-leaving');
        setTimeout(() => toast.remove(), 220);
    }, 3500);
    return toast;
}

// -------------------------------------------------------------
// Preview Page Switching (Lembar 1 - 6)
// -------------------------------------------------------------
window.switchPreviewPage = function (page) {
    const sheets = document.querySelectorAll('.preview-sheet');
    const btns = document.querySelectorAll('.p-page-btn');

    btns.forEach(b => b.classList.remove('active'));

    if (page === 'all') {
        sheets.forEach(s => s.classList.add('sheet-visible'));
        const allBtn = Array.from(btns).find(b => b.innerText.includes('Semua'));
        if (allBtn) allBtn.classList.add('active');
    } else {
        sheets.forEach(s => s.classList.remove('sheet-visible'));
        const target = document.getElementById(`prev_sheet_${page}`);
        if (target) target.classList.add('sheet-visible');
        const activeBtn = Array.from(btns).find(b => b.innerText.includes(`Lembar ${page}`));
        if (activeBtn) activeBtn.classList.add('active');
    }
};

// Initial default to Lembar 1
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        if (window.switchPreviewPage) window.switchPreviewPage(1);
    }, 150);
});


// -------------------------------------------------------------
// Official PDF Export (Opens pure 6-page A4 document)
// -------------------------------------------------------------
window.exportOfficialPdf = function () {
    let sscEl = document.getElementById('g_sscId');
    if (!sscEl || !sscEl.value.trim()) {
        showToast('Mohon isi SSC ID terlebih dahulu!', 'error');
        if (sscEl) sscEl.focus();
        return;
    }

    const payload = extractFormData();
    if (!payload) return;

    const reportIdInput = document.getElementById('reportId');
    const existingId = reportIdInput?.value;
    const isUpdate = Boolean(existingId);
    const url = isUpdate ? `/reports/${existingId}` : '/reports';
    const method = isUpdate ? 'PUT' : 'POST';

    // Preserve draft status when exporting/printing before submitting
    payload.submit_action = (!isUpdate || window.qcReportStatus === 'draft') ? 'draft' : 'save';

    showToast('Menyiapkan dokumen resmi 6 lembar (PDF)...', 'loading');

    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
        .then(async r => {
            const data = await r.json().catch(() => ({}));
            if (!r.ok) {
                throw new Error(data.message || `HTTP ${r.status}`);
            }
            return data;
        })
        .then(data => {
            if (data.success || data.report || data.id) {
                const savedReport = data.report || data;
                const savedId = savedReport.id || existingId;
                if (reportIdInput) reportIdInput.value = savedId;
                currentReportId = savedId;
                if (savedReport.review_status) {
                    window.qcReportStatus = savedReport.review_status;
                }
                if (!isUpdate && savedId && window.history?.replaceState) {
                    window.history.replaceState({}, '', `/reports/${savedId}/edit`);
                }

                showToast('Membuka PDF Dokumen Resmi 6 Lembar...', 'success');
                // Open dedicated 6-page A4 print view in new window/tab
                window.open(`/reports/${savedId}/pdf?autoprint=1`, '_blank');
            } else if (existingId) {
                window.open(`/reports/${existingId}/pdf?autoprint=1`, '_blank');
            } else {
                showToast('Gagal memproses dokumen PDF: ' + (data.message || 'Error'), 'error');
            }
        })
        .catch(err => {
            console.error('PDF export error:', err);
            if (existingId) {
                window.open(`/reports/${existingId}/pdf?autoprint=1`, '_blank');
            } else {
                showToast('Terjadi kesalahan saat menyimpan data.', 'error');
            }
        });
};

window.saveReportData = saveReportData;

// -------------------------------------------------------------
// Signature Personel Quick Selection & Chip Sync Helpers
// -------------------------------------------------------------
window.setSignatureName = function (inputId, name) {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.value = name;
    input.dispatchEvent(new Event('input', { bubbles: true }));
    input.dispatchEvent(new Event('change', { bubbles: true }));
    window.syncSigChips(inputId);
};

window.syncSigChips = function (inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const parentBox = input.closest('.sig-box');
    if (!parentBox) return;
    const val = (input.value || '').trim().toLowerCase();
    parentBox.querySelectorAll('.sig-chip').forEach(chip => {
        if (chip.textContent.trim().toLowerCase() === val) {
            chip.classList.add('active');
        } else {
            chip.classList.remove('active');
        }
    });
};

// -------------------------------------------------------------
// Mobile Full-Screen Preview Drawer Controls
// -------------------------------------------------------------
window.openMobilePreview = function () {
    const previewPane = document.querySelector('.studio-preview-pane');
    if (!previewPane) return;
    previewPane.classList.add('mobile-open');
    document.body.style.overflow = 'hidden';

    // Ensure active page is visible
    if (window.switchPreviewPage && !window.currentPreviewPage) {
        window.switchPreviewPage(1);
    }
    if (window.lucide) {
        lucide.createIcons();
    }
};

window.closeMobilePreview = function () {
    const previewPane = document.querySelector('.studio-preview-pane');
    if (!previewPane) return;
    previewPane.classList.remove('mobile-open');
    document.body.style.overflow = '';
};

// Also close on Escape key if open
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const previewPane = document.querySelector('.studio-preview-pane');
        if (previewPane && previewPane.classList.contains('mobile-open')) {
            window.closeMobilePreview();
        }
    }
});
