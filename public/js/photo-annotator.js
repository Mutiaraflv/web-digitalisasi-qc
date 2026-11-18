/**
 * QC Photo Defect Callout / Marker Annotator Module
 * Allows QC Inspectors to place numbered callout stickers and direction arrows
 * directly onto uploaded inspection photos.
 */
(function (window, document) {
    'use strict';

    // Store original clean photos (before any markers are drawn)
    window.qcOriginalPhotos = window.qcOriginalPhotos || {};
    // Store metadata of markers per slot
    window.qcPhotoMarkers = window.qcPhotoMarkers || {};

    const Annotator = {
        isOpen: false,
        slotKey: null,
        slotLabel: '',
        currentImageUrl: '',
        originalImageUrl: '',
        naturalWidth: 0,
        naturalHeight: 0,

        // Markers array: [{ id, number, xPercent, yPercent, direction, size, color }]
        markers: [],
        activeMarkerId: null,
        nextNumber: 1,
        activeDirection: 'down', // 'down', 'up', 'left', 'right', 'down-left', 'down-right', 'up-left', 'up-right', 'none'
        activeSize: 'md',       // 'sm', 'md', 'lg'
        activeColor: '#dc2626',  // Red default QC
        history: [],

        // Dragging state
        isDragging: false,
        dragMarkerId: null,
        dragStartPos: { x: 0, y: 0 },
        hasMoved: false,

        // DOM elements
        modal: null,
        stage: null,
        imgWrap: null,
        img: null,
        svg: null,
        slotBadge: null,
        numInput: null,
        saveBtn: null,

        init() {
            this.modal = document.getElementById('qcAnnotatorModal');
            if (!this.modal) return;

            this.stage = document.getElementById('annotatorStage');
            this.imgWrap = document.getElementById('annotatorImageWrap');
            this.img = document.getElementById('annotatorImage');
            this.svg = document.getElementById('annotatorSvgOverlay');
            this.slotBadge = document.getElementById('annotatorSlotBadge');
            this.numInput = document.getElementById('annotatorNumInput');
            this.saveBtn = document.getElementById('btnAnnotatorSave');

            this.bindEvents();
        },

        bindEvents() {
            const self = this;

            // Close button
            document.querySelectorAll('[data-annotator-close]').forEach(btn => {
                btn.addEventListener('click', () => self.close());
            });

            // ESC key to close
            document.addEventListener('keydown', (e) => {
                if (!self.isOpen) return;
                if (e.key === 'Escape') {
                    self.close();
                } else if ((e.key === 'Delete' || e.key === 'Backspace') && self.activeMarkerId && document.activeElement !== self.numInput) {
                    e.preventDefault();
                    self.deleteMarker(self.activeMarkerId);
                } else if ((e.ctrlKey || e.metaKey) && e.key === 'z') {
                    e.preventDefault();
                    self.undo();
                }
            });

            // Direction Buttons
            document.querySelectorAll('.annotator-dir-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const dir = btn.dataset.dir;
                    self.setDirection(dir);
                });
            });

            // Size Buttons
            document.querySelectorAll('[data-annotator-size]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const size = btn.dataset.annotatorSize;
                    self.setSize(size);
                });
            });

            // Number Controls
            const btnMinus = document.getElementById('btnAnnotatorNumMinus');
            const btnPlus = document.getElementById('btnAnnotatorNumPlus');

            btnMinus?.addEventListener('click', () => {
                let n = parseInt(self.numInput?.value || self.nextNumber, 10);
                if (n > 1) self.setNumber(n - 1);
            });

            btnPlus?.addEventListener('click', () => {
                let n = parseInt(self.numInput?.value || self.nextNumber, 10);
                self.setNumber(n + 1);
            });

            self.numInput?.addEventListener('change', () => {
                let n = parseInt(self.numInput.value, 10);
                if (isNaN(n) || n < 1) n = 1;
                self.setNumber(n);
            });

            // Undo Button
            document.getElementById('btnAnnotatorUndo')?.addEventListener('click', () => {
                self.undo();
            });

            // Clear All Button
            document.getElementById('btnAnnotatorClear')?.addEventListener('click', () => {
                self.clearAll();
            });

            // Reset to Original Button
            document.getElementById('btnAnnotatorResetOriginal')?.addEventListener('click', () => {
                self.resetOriginal();
            });

            // Save & Apply Button
            self.saveBtn?.addEventListener('click', () => {
                self.save();
            });

            // SVG Canvas Interaction: Click to add or select
            self.svg?.addEventListener('pointerdown', (e) => {
                self.handlePointerDown(e);
            });

            window.addEventListener('pointermove', (e) => {
                if (self.isOpen && self.isDragging) {
                    self.handlePointerMove(e);
                }
            });

            window.addEventListener('pointerup', (e) => {
                if (self.isOpen && self.isDragging) {
                    self.handlePointerUp(e);
                }
            });
        },

        open(slotKey) {
            this.slotKey = slotKey;

            // Get current photo from reportPhotos or DOM
            let currentUrl = (window.reportPhotos && window.reportPhotos[slotKey]) || '';
            if (!currentUrl) {
                const domImg = document.querySelector(`#view_slot_${slotKey} img`);
                if (domImg) currentUrl = domImg.src;
            }

            if (!currentUrl) {
                const S = window.qcI18n?.studio || {};
                window.qcNotify?.(S.annotator_no_photo || 'Unggah atau pilih foto terlebih dahulu sebelum memberi marker.', 'warning');
                return;
            }

            this.currentImageUrl = currentUrl;

            // Ensure original image is saved
            if (!window.qcOriginalPhotos[slotKey]) {
                window.qcOriginalPhotos[slotKey] = currentUrl;
            }
            this.originalImageUrl = window.qcOriginalPhotos[slotKey];

            // Restore saved markers if available
            if (window.qcPhotoMarkers[slotKey] && Array.isArray(window.qcPhotoMarkers[slotKey])) {
                this.markers = JSON.parse(JSON.stringify(window.qcPhotoMarkers[slotKey]));
            } else {
                this.markers = [];
            }

            this.history = [];
            this.activeMarkerId = null;
            this.calculateNextNumber();

            // Set slot title / label
            const labelEl = document.querySelector(`#card_slot_${slotKey} .slot-item-label`) ||
                            document.querySelector(`#card_slot_${slotKey}`)?.closest('.area-subgroup')?.querySelector('.area-subgroup-title span');
            this.slotLabel = labelEl ? labelEl.textContent.trim() : slotKey;

            if (this.slotBadge) {
                this.slotBadge.textContent = this.slotLabel;
            }

            this.updateToolbarUI();

            // Show modal
            this.isOpen = true;
            this.modal.classList.add('active');
            document.body.style.overflow = 'hidden';

            // Load Image
            this.loadImage(this.originalImageUrl || this.currentImageUrl);
        },

        close() {
            if (!this.isOpen) return;
            this.isOpen = false;
            this.modal?.classList.remove('active');
            document.body.style.overflow = '';
        },

        loadImage(src) {
            const self = this;
            self.img.onload = () => {
                self.naturalWidth = self.img.naturalWidth;
                self.naturalHeight = self.img.naturalHeight;

                // Set SVG viewBox to match natural image size 1:1
                self.svg.setAttribute('viewBox', `0 0 ${self.naturalWidth} ${self.naturalHeight}`);
                self.renderSvgMarkers();

                if (window.lucide) lucide.createIcons();
            };
            self.img.src = src;
        },

        calculateNextNumber() {
            if (this.markers.length === 0) {
                this.nextNumber = 1;
            } else {
                const nums = this.markers.map(m => parseInt(m.number, 10)).filter(n => !isNaN(n));
                const max = nums.length > 0 ? Math.max(...nums) : 0;
                this.nextNumber = max + 1;
            }
            if (this.numInput) this.numInput.value = this.nextNumber;
        },

        pushHistory() {
            this.history.push(JSON.stringify(this.markers));
            if (this.history.length > 30) this.history.shift();
        },

        undo() {
            if (this.history.length === 0) {
                if (this.markers.length > 0) {
                    this.pushHistory();
                    this.markers.pop();
                    this.activeMarkerId = null;
                    this.calculateNextNumber();
                    this.renderSvgMarkers();
                }
                return;
            }
            const prevState = this.history.pop();
            this.markers = JSON.parse(prevState);
            this.activeMarkerId = null;
            this.calculateNextNumber();
            this.renderSvgMarkers();
        },

        clearAll() {
            if (this.markers.length === 0) return;
            const S = window.qcI18n?.studio || {};
            const ok = confirm(S.annotator_confirm_clear || 'Hapus semua marker yang sudah ditempel pada foto ini?');
            if (ok) {
                this.pushHistory();
                this.markers = [];
                this.activeMarkerId = null;
                this.calculateNextNumber();
                this.renderSvgMarkers();
            }
        },

        resetOriginal() {
            const S = window.qcI18n?.studio || {};
            const ok = confirm(S.annotator_confirm_reset || 'Kembalikan foto ke kondisi asli tanpa marker?');
            if (ok) {
                this.pushHistory();
                this.markers = [];
                this.activeMarkerId = null;
                this.calculateNextNumber();
                this.renderSvgMarkers();
                this.loadImage(this.originalImageUrl);
            }
        },

        setDirection(dir) {
            this.activeDirection = dir;
            if (this.activeMarkerId) {
                const marker = this.markers.find(m => m.id === this.activeMarkerId);
                if (marker) {
                    this.pushHistory();
                    marker.direction = dir;
                    this.renderSvgMarkers();
                }
            }
            this.updateToolbarUI();
        },

        setSize(size) {
            this.activeSize = size;
            if (this.activeMarkerId) {
                const marker = this.markers.find(m => m.id === this.activeMarkerId);
                if (marker) {
                    this.pushHistory();
                    marker.size = size;
                    this.renderSvgMarkers();
                }
            }
            this.updateToolbarUI();
        },

        setNumber(num) {
            this.nextNumber = num;
            if (this.numInput) this.numInput.value = num;

            if (this.activeMarkerId) {
                const marker = this.markers.find(m => m.id === this.activeMarkerId);
                if (marker) {
                    this.pushHistory();
                    marker.number = num;
                    this.renderSvgMarkers();
                }
            }
        },

        deleteMarker(id) {
            this.pushHistory();
            this.markers = this.markers.filter(m => m.id !== id);
            if (this.activeMarkerId === id) this.activeMarkerId = null;
            this.calculateNextNumber();
            this.renderSvgMarkers();
        },

        updateToolbarUI() {
            // Update direction buttons active state
            document.querySelectorAll('.annotator-dir-btn').forEach(btn => {
                if (btn.dataset.dir === this.activeDirection) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });

            // Update size buttons active state
            document.querySelectorAll('[data-annotator-size]').forEach(btn => {
                if (btn.dataset.annotatorSize === this.activeSize) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });

            if (this.numInput) {
                this.numInput.value = this.nextNumber;
            }
        },

        getPointerSvgCoords(e) {
            const rect = this.svg.getBoundingClientRect();
            const clientX = e.clientX;
            const clientY = e.clientY;

            // Calculate coordinate in SVG viewBox space
            const xPercent = ((clientX - rect.left) / rect.width) * 100;
            const yPercent = ((clientY - rect.top) / rect.height) * 100;

            const clampedX = Math.max(0, Math.min(100, xPercent));
            const clampedY = Math.max(0, Math.min(100, yPercent));

            return {
                xPercent: clampedX,
                yPercent: clampedY,
                svgX: (clampedX / 100) * this.naturalWidth,
                svgY: (clampedY / 100) * this.naturalHeight
            };
        },

        handlePointerDown(e) {
            // Check if clicked directly on a marker or marker delete button
            const markerGroup = e.target.closest('.annotator-marker-group');
            const delBtn = e.target.closest('.marker-del-btn');

            if (delBtn) {
                e.stopPropagation();
                const markerId = delBtn.dataset.markerId;
                if (markerId) this.deleteMarker(markerId);
                return;
            }

            if (markerGroup) {
                // Select marker and prepare drag
                e.stopPropagation();
                const markerId = markerGroup.dataset.markerId;
                this.activeMarkerId = markerId;

                const marker = this.markers.find(m => m.id === markerId);
                if (marker) {
                    this.activeDirection = marker.direction || 'down';
                    this.activeSize = marker.size || 'md';
                    this.nextNumber = marker.number;
                    this.updateToolbarUI();
                }

                this.isDragging = true;
                this.dragMarkerId = markerId;
                this.dragStartPos = this.getPointerSvgCoords(e);
                this.hasMoved = false;

                this.renderSvgMarkers();
                return;
            }

            // Clicked on empty space: Add new marker!
            const coords = this.getPointerSvgCoords(e);
            this.pushHistory();

            const newMarker = {
                id: 'm_' + Date.now() + '_' + Math.random().toString(36).substr(2, 4),
                number: this.nextNumber,
                xPercent: coords.xPercent,
                yPercent: coords.yPercent,
                direction: this.activeDirection,
                size: this.activeSize,
                color: this.activeColor
            };

            this.markers.push(newMarker);
            this.activeMarkerId = newMarker.id;
            this.calculateNextNumber();
            this.renderSvgMarkers();
        },

        handlePointerMove(e) {
            if (!this.isDragging || !this.dragMarkerId) return;
            this.hasMoved = true;

            const coords = this.getPointerSvgCoords(e);
            const marker = this.markers.find(m => m.id === this.dragMarkerId);
            if (marker) {
                marker.xPercent = coords.xPercent;
                marker.yPercent = coords.yPercent;
                this.renderSvgMarkers();
            }
        },

        handlePointerUp(e) {
            if (this.isDragging) {
                if (this.hasMoved) {
                    this.pushHistory();
                }
                this.isDragging = false;
                this.dragMarkerId = null;
            }
        },

        // Render markers onto SVG overlay in DOM
        renderSvgMarkers() {
            if (!this.svg || !this.naturalWidth || !this.naturalHeight) return;

            const baseDim = Math.min(this.naturalWidth, this.naturalHeight);

            // Defs for drop shadow if not exists
            let defs = this.svg.querySelector('defs');
            if (!defs) {
                defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
                defs.innerHTML = `
                    <filter id="qcMarkerShadow" x="-30%" y="-30%" width="160%" height="160%">
                        <feDropShadow dx="2" dy="3" stdDeviation="3.5" flood-color="#000000" flood-opacity="0.38"/>
                    </filter>
                    <filter id="qcHaloGlow" x="-50%" y="-50%" width="200%" height="200%">
                        <feDropShadow dx="0" dy="0" stdDeviation="5" flood-color="#3b82f6" flood-opacity="0.7"/>
                    </filter>
                `;
                this.svg.appendChild(defs);
            }

            // Remove existing markers
            const existingGroups = this.svg.querySelectorAll('.annotator-marker-group');
            existingGroups.forEach(g => g.remove());

            this.markers.forEach(marker => {
                const targetX = (marker.xPercent / 100) * this.naturalWidth;
                const targetY = (marker.yPercent / 100) * this.naturalHeight;

                let sizeMultiplier = 1;
                if (marker.size === 'sm') sizeMultiplier = 0.72;
                else if (marker.size === 'lg') sizeMultiplier = 1.38;

                const radius = Math.max(16, baseDim * 0.038) * sizeMultiplier;
                const arrowLength = Math.max(20, baseDim * 0.045) * sizeMultiplier;
                const lineWidth = Math.max(3.2, radius * 0.16);
                const headSize = Math.max(11, radius * 0.58);
                const primaryColor = marker.color || '#dc2626';

                // Direction offsets
                let dirX = 0, dirY = -1; // default 'down'
                let hasArrow = marker.direction !== 'none';

                switch (marker.direction) {
                    case 'down': dirX = 0; dirY = -1; break;
                    case 'up': dirX = 0; dirY = 1; break;
                    case 'left': dirX = 1; dirY = 0; break;
                    case 'right': dirX = -1; dirY = 0; break;
                    case 'down-left': dirX = 0.707; dirY = -0.707; break;
                    case 'down-right': dirX = -0.707; dirY = -0.707; break;
                    case 'up-left': dirX = 0.707; dirY = 0.707; break;
                    case 'up-right': dirX = -0.707; dirY = 0.707; break;
                    default: hasArrow = false; break;
                }

                let circleX = targetX;
                let circleY = targetY;

                let arrowSvg = '';
                if (hasArrow) {
                    const totalDist = radius + arrowLength;
                    circleX = targetX + dirX * totalDist;
                    circleY = targetY + dirY * totalDist;

                    const arrowStartX = circleX - dirX * radius;
                    const arrowStartY = circleY - dirY * radius;

                    // Compute arrowhead points
                    const angle = Math.atan2(targetY - arrowStartY, targetX - arrowStartX);
                    const arrowAngle = Math.PI / 6;

                    const p1x = targetX - headSize * Math.cos(angle - arrowAngle);
                    const p1y = targetY - headSize * Math.sin(angle - arrowAngle);
                    const pMidX = targetX - (headSize * 0.6) * Math.cos(angle);
                    const pMidY = targetY - (headSize * 0.6) * Math.sin(angle);
                    const p2x = targetX - headSize * Math.cos(angle + arrowAngle);
                    const p2y = targetY - headSize * Math.sin(angle + arrowAngle);

                    arrowSvg = `
                        <line x1="${arrowStartX}" y1="${arrowStartY}" x2="${pMidX}" y2="${pMidY}" 
                              stroke="${primaryColor}" stroke-width="${lineWidth}" stroke-linecap="round" />
                        <polygon points="${targetX},${targetY} ${p1x},${p1y} ${pMidX},${pMidY} ${p2x},${p2y}" 
                                 fill="${primaryColor}" />
                    `;
                }

                const isActive = (marker.id === this.activeMarkerId);
                const activeRing = isActive ? `
                    <circle cx="${circleX}" cy="${circleY}" r="${radius + 7}" 
                            fill="none" stroke="#3b82f6" stroke-width="3" stroke-dasharray="6,4" />
                ` : '';

                // Delete 'x' badge on top right of the circular badge
                const delX = circleX + radius * 0.72;
                const delY = circleY - radius * 0.72;
                const delR = Math.max(10, radius * 0.4);

                const delBtnSvg = `
                    <g class="marker-del-btn" data-marker-id="${marker.id}">
                        <circle cx="${delX}" cy="${delY}" r="${delR}" fill="#ef4444" stroke="#ffffff" stroke-width="1.8" />
                        <text x="${delX}" y="${delY + 1}" text-anchor="middle" dominant-baseline="central" 
                              fill="#ffffff" font-size="${delR * 1.3}" font-weight="900" font-family="sans-serif">&times;</text>
                    </g>
                `;

                const fontSize = Math.round(radius * 1.25);

                const group = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                group.setAttribute('class', `annotator-marker-group ${isActive ? 'is-active' : ''}`);
                group.setAttribute('data-marker-id', marker.id);
                group.innerHTML = `
                    ${arrowSvg}
                    ${activeRing}
                    <circle cx="${circleX}" cy="${circleY}" r="${radius}" 
                            fill="#ffffff" stroke="${primaryColor}" stroke-width="${lineWidth}" 
                            filter="url(#qcMarkerShadow)" />
                    <text x="${circleX}" y="${circleY + (fontSize * 0.05)}" 
                          text-anchor="middle" dominant-baseline="central" 
                          font-size="${fontSize}" font-weight="800" 
                          font-family="'Plus Jakarta Sans', 'Inter', -apple-system, sans-serif" 
                          fill="#111827">${marker.number}</text>
                    ${delBtnSvg}
                `;

                this.svg.appendChild(group);
            });
        },

        // Render an individual transparent PNG marker badge for movable Excel overlay
        renderSingleMarker(marker) {
            let sizeMultiplier = 1;
            if (marker.size === 'sm') sizeMultiplier = 0.72;
            else if (marker.size === 'lg') sizeMultiplier = 1.38;

            const radius = Math.round(20 * sizeMultiplier);
            const arrowLength = Math.round(24 * sizeMultiplier);
            const lineWidth = Math.max(3.2, radius * 0.16);
            const headSize = Math.max(11, radius * 0.58);
            const primaryColor = marker.color || '#dc2626';

            let dirX = 0, dirY = -1;
            let hasArrow = marker.direction !== 'none';
            switch (marker.direction) {
                case 'down': dirX = 0; dirY = -1; break;
                case 'up': dirX = 0; dirY = 1; break;
                case 'left': dirX = 1; dirY = 0; break;
                case 'right': dirX = -1; dirY = 0; break;
                case 'down-left': dirX = 0.707; dirY = -0.707; break;
                case 'down-right': dirX = -0.707; dirY = -0.707; break;
                case 'up-left': dirX = 0.707; dirY = 0.707; break;
                case 'up-right': dirX = -0.707; dirY = 0.707; break;
                default: hasArrow = false; break;
            }

            const pad = 8;
            const totalDist = radius + arrowLength;
            const dim = Math.round((radius + totalDist + pad) * 2);
            const cx = dim / 2;
            const cy = dim / 2;

            const tipX = cx;
            const tipY = cy;
            const circleX = hasArrow ? (tipX + dirX * totalDist) : tipX;
            const circleY = hasArrow ? (tipY + dirY * totalDist) : tipY;

            const minX = Math.floor(Math.min(tipX - headSize, circleX - radius - lineWidth) - 2);
            const maxX = Math.ceil(Math.max(tipX + headSize, circleX + radius + lineWidth) + 2);
            const minY = Math.floor(Math.min(tipY - headSize, circleY - radius - lineWidth) - 2);
            const maxY = Math.ceil(Math.max(tipY + headSize, circleY + radius + lineWidth) + 2);
            const cropW = Math.max(10, maxX - minX);
            const cropH = Math.max(10, maxY - minY);

            const canvas = document.createElement('canvas');
            canvas.width = cropW;
            canvas.height = cropH;
            const ctx = canvas.getContext('2d');

            const cCircleX = circleX - minX;
            const cCircleY = circleY - minY;
            const cTipX = tipX - minX;
            const cTipY = tipY - minY;

            if (hasArrow) {
                const arrowStartX = cCircleX - dirX * radius;
                const arrowStartY = cCircleY - dirY * radius;

                ctx.save();
                ctx.strokeStyle = primaryColor;
                ctx.fillStyle = primaryColor;
                ctx.lineWidth = lineWidth;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';

                ctx.beginPath();
                ctx.moveTo(arrowStartX, arrowStartY);
                ctx.lineTo(cTipX, cTipY);
                ctx.stroke();

                const angle = Math.atan2(cTipY - arrowStartY, cTipX - arrowStartX);
                const arrowAngle = Math.PI / 6;

                ctx.beginPath();
                ctx.moveTo(cTipX, cTipY);
                ctx.lineTo(cTipX - headSize * Math.cos(angle - arrowAngle), cTipY - headSize * Math.sin(angle - arrowAngle));
                ctx.lineTo(cTipX - (headSize * 0.6) * Math.cos(angle), cTipY - (headSize * 0.6) * Math.sin(angle));
                ctx.lineTo(cTipX - headSize * Math.cos(angle + arrowAngle), cTipY - headSize * Math.sin(angle + arrowAngle));
                ctx.closePath();
                ctx.fill();
                ctx.restore();
            }

            // Draw circular badge
            ctx.save();
            ctx.beginPath();
            ctx.arc(cCircleX, cCircleY, radius, 0, 2 * Math.PI);
            ctx.fillStyle = '#ffffff';
            ctx.fill();

            ctx.strokeStyle = primaryColor;
            ctx.lineWidth = lineWidth;
            ctx.stroke();

            ctx.fillStyle = '#111827';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            const fontSize = Math.round(radius * 1.25);
            ctx.font = `800 ${fontSize}px "Plus Jakarta Sans", "Inter", -apple-system, sans-serif`;
            ctx.fillText(String(marker.number || '1'), cCircleX, cCircleY + (fontSize * 0.05));
            ctx.restore();

            return {
                dataUrl: canvas.toDataURL('image/png'),
                width: cropW,
                height: cropH,
                tipOffsetX: cTipX,
                tipOffsetY: cTipY
            };
        },

        // Render markers onto high-resolution Canvas and export
        exportToCanvas() {
            const self = this;
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.crossOrigin = 'anonymous';

                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    canvas.width = img.naturalWidth;
                    canvas.height = img.naturalHeight;
                    const ctx = canvas.getContext('2d');

                    // 1. Draw base photo
                    ctx.drawImage(img, 0, 0);

                    // 2. Draw each marker
                    const baseDim = Math.min(canvas.width, canvas.height);

                    self.markers.forEach(marker => {
                        const targetX = (marker.xPercent / 100) * canvas.width;
                        const targetY = (marker.yPercent / 100) * canvas.height;

                        let sizeMultiplier = 1;
                        if (marker.size === 'sm') sizeMultiplier = 0.72;
                        else if (marker.size === 'lg') sizeMultiplier = 1.38;

                        const radius = Math.max(16, baseDim * 0.038) * sizeMultiplier;
                        const arrowLength = Math.max(20, baseDim * 0.045) * sizeMultiplier;
                        const lineWidth = Math.max(3.2, radius * 0.16);
                        const headSize = Math.max(11, radius * 0.58);
                        const primaryColor = marker.color || '#dc2626';

                        let dirX = 0, dirY = -1;
                        let hasArrow = marker.direction !== 'none';

                        switch (marker.direction) {
                            case 'down': dirX = 0; dirY = -1; break;
                            case 'up': dirX = 0; dirY = 1; break;
                            case 'left': dirX = 1; dirY = 0; break;
                            case 'right': dirX = -1; dirY = 0; break;
                            case 'down-left': dirX = 0.707; dirY = -0.707; break;
                            case 'down-right': dirX = -0.707; dirY = -0.707; break;
                            case 'up-left': dirX = 0.707; dirY = 0.707; break;
                            case 'up-right': dirX = -0.707; dirY = 0.707; break;
                            default: hasArrow = false; break;
                        }

                        let circleX = targetX;
                        let circleY = targetY;

                        if (hasArrow) {
                            const totalDist = radius + arrowLength;
                            circleX = targetX + dirX * totalDist;
                            circleY = targetY + dirY * totalDist;

                            const arrowStartX = circleX - dirX * radius;
                            const arrowStartY = circleY - dirY * radius;

                            ctx.save();
                            ctx.strokeStyle = primaryColor;
                            ctx.fillStyle = primaryColor;
                            ctx.lineWidth = lineWidth;
                            ctx.lineCap = 'round';
                            ctx.lineJoin = 'round';

                            // Draw stem
                            ctx.beginPath();
                            ctx.moveTo(arrowStartX, arrowStartY);
                            ctx.lineTo(targetX, targetY);
                            ctx.stroke();

                            // Draw arrowhead
                            const angle = Math.atan2(targetY - arrowStartY, targetX - arrowStartX);
                            const arrowAngle = Math.PI / 6;

                            ctx.beginPath();
                            ctx.moveTo(targetX, targetY);
                            ctx.lineTo(
                                targetX - headSize * Math.cos(angle - arrowAngle),
                                targetY - headSize * Math.sin(angle - arrowAngle)
                            );
                            ctx.lineTo(
                                targetX - (headSize * 0.6) * Math.cos(angle),
                                targetY - (headSize * 0.6) * Math.sin(angle)
                            );
                            ctx.lineTo(
                                targetX - headSize * Math.cos(angle + arrowAngle),
                                targetY - headSize * Math.sin(angle + arrowAngle)
                            );
                            ctx.closePath();
                            ctx.fill();
                            ctx.restore();
                        }

                        // Draw circular badge with shadow
                        ctx.save();
                        ctx.shadowColor = 'rgba(0, 0, 0, 0.4)';
                        ctx.shadowBlur = Math.max(5, radius * 0.22);
                        ctx.shadowOffsetX = 2;
                        ctx.shadowOffsetY = 3;

                        // Fill white
                        ctx.beginPath();
                        ctx.arc(circleX, circleY, radius, 0, 2 * Math.PI);
                        ctx.fillStyle = '#ffffff';
                        ctx.fill();

                        // Stroke red
                        ctx.shadowColor = 'transparent';
                        ctx.strokeStyle = primaryColor;
                        ctx.lineWidth = lineWidth;
                        ctx.stroke();

                        // Draw number text
                        ctx.fillStyle = '#111827';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        const fontSize = Math.round(radius * 1.25);
                        ctx.font = `800 ${fontSize}px "Plus Jakarta Sans", "Inter", -apple-system, sans-serif`;
                        ctx.fillText(String(marker.number || '1'), circleX, circleY + (fontSize * 0.05));

                        ctx.restore();
                    });

                    // Export blob & dataUrl
                    canvas.toBlob((blob) => {
                        if (blob) {
                            resolve({
                                blob: blob,
                                dataUrl: canvas.toDataURL('image/jpeg', 0.92)
                            });
                        } else {
                            reject(new Error('Canvas toBlob failed'));
                        }
                    }, 'image/jpeg', 0.92);
                };

                img.onerror = (err) => reject(err);
                img.src = self.originalImageUrl || self.currentImageUrl;
            });
        },

        // Save annotated photo and update report slot
        async save() {
            const self = this;
            if (!self.slotKey) return;

            const S = window.qcI18n?.studio || {};

            // If no markers added and we're on original photo, just close
            if (self.markers.length === 0) {
                // If user cleared markers, restore clean original photo
                window.qcPhotoMarkers[self.slotKey] = [];
                if (self.originalImageUrl) {
                    if (typeof window.renderSlotPreview === 'function') {
                        window.renderSlotPreview(self.slotKey, self.originalImageUrl);
                        if (typeof window.markFormEdited === 'function') window.markFormEdited();
                    }
                }
                self.close();
                return;
            }

            self.saveBtn.disabled = true;
            window.qcNotify?.(S.annotator_saving || 'Menyimpan foto bertanda...', 'loading');

            try {
                // Pre-render individual transparent marker badges for Excel export
                self.markers.forEach(m => {
                    try {
                        m.rendered = self.renderSingleMarker(m);
                    } catch (e) {
                        console.warn('Marker pre-render failed:', e);
                    }
                });

                const result = await self.exportToCanvas();

                // Save marker metadata in memory/cache
                window.qcPhotoMarkers[self.slotKey] = JSON.parse(JSON.stringify(self.markers));

                // Upload to server
                const formData = new FormData();
                const filename = `annotated_${self.slotKey}_${Date.now()}.jpg`;
                formData.append('photo', result.blob, filename);
                formData.append('section', self.slotKey);

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

                const response = await fetch('/reports/upload-photo', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success && data.url) {
                    if (typeof window.renderSlotPreview === 'function') {
                        window.renderSlotPreview(self.slotKey, data.url);
                    }
                    if (typeof window.markFormEdited === 'function') {
                        window.markFormEdited();
                    }
                    window.qcNotify?.(S.annotator_saved_success || 'Marker temuan berhasil diterapkan ke foto!', 'success');
                    self.close();
                } else {
                    // Fallback offline dataUrl
                    if (typeof window.renderSlotPreview === 'function') {
                        window.renderSlotPreview(self.slotKey, result.dataUrl);
                    }
                    if (typeof window.markFormEdited === 'function') {
                        window.markFormEdited();
                    }
                    window.qcNotify?.('Marker diterapkan secara lokal pada draf ini', 'success');
                    self.close();
                }
            } catch (err) {
                console.error('Save annotated photo error:', err);
                window.qcNotify?.('Gagal menyimpan foto: ' + (err.message || 'Error'), 'error');
            } finally {
                self.saveBtn.disabled = false;
            }
        }
    };

    // Expose global helper
    window.QcPhotoAnnotator = Annotator;
    window.openPhotoAnnotationModal = function (slotKey) {
        Annotator.open(slotKey);
    };
    window.closePhotoAnnotationModal = function () {
        Annotator.close();
    };

    // Auto-init on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => Annotator.init());
    } else {
        Annotator.init();
    }

})(window, document);
