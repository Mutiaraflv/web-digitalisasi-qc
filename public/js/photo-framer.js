/**
 * QC Photo Framing, Pan & Zoom Cropper Module
 * Allows QC Inspectors to freely reposition, zoom, rotate, and frame uploaded photos
 * to fit perfectly into report containers (PDF & Printout).
 */
(function (window, document) {
    'use strict';

    window.qcRawPhotos = window.qcRawPhotos || {};

    const Framer = {
        isOpen: false,
        slotKey: null,
        slotLabel: '',
        sourceUrl: '',
        naturalWidth: 0,
        naturalHeight: 0,

        // Transformation state
        scale: 1.0,
        minScale: 0.5,
        maxScale: 3.5,
        panX: 0,
        panY: 0,
        rotation: 0, // 0, 90, 180, 270

        // Ratio state
        activeRatioMode: 'slot', // 'slot', '4:3', '16:9', '1:1', 'free'
        apertureRatio: 1.3333,

        // Drag state
        isDragging: false,
        dragStartX: 0,
        dragStartY: 0,
        lastPanX: 0,
        lastPanY: 0,

        // Pinch state
        initialPinchDist: 0,
        initialPinchScale: 1.0,

        // DOM elements
        modal: null,
        stage: null,
        viewportContainer: null,
        canvasWrap: null,
        img: null,
        aperture: null,
        slotBadge: null,
        zoomSlider: null,
        zoomVal: null,
        applyBtn: null,

        init() {
            this.modal = document.getElementById('qcFramerModal');
            if (!this.modal) return;

            this.stage = document.getElementById('framerStage');
            this.viewportContainer = document.getElementById('framerViewportContainer');
            this.canvasWrap = document.getElementById('framerCanvasWrap');
            this.img = document.getElementById('framerSourceImg');
            this.aperture = document.getElementById('framerAperture');
            this.slotBadge = document.getElementById('framerSlotBadge');
            this.zoomSlider = document.getElementById('framerZoomSlider');
            this.zoomVal = document.getElementById('framerZoomVal');
            this.applyBtn = document.getElementById('btnFramerApply');

            this.bindEvents();
        },

        bindEvents() {
            const self = this;

            // Close buttons
            document.querySelectorAll('[data-framer-close]').forEach(btn => {
                btn.addEventListener('click', () => self.close());
            });

            // ESC key to close
            document.addEventListener('keydown', (e) => {
                if (!self.isOpen) return;
                if (e.key === 'Escape') {
                    self.close();
                } else if (e.key === 'r' && !e.ctrlKey && !e.metaKey) {
                    self.rotateClockwise();
                }
            });

            // Ratio Buttons
            document.querySelectorAll('.framer-ratio-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.framer-ratio-btn').forEach(b => b.classList.remove('is-active'));
                    btn.classList.add('is-active');
                    const mode = btn.dataset.ratio;
                    self.setRatioMode(mode);
                });
            });

            // Zoom Slider
            if (this.zoomSlider) {
                this.zoomSlider.addEventListener('input', (e) => {
                    const val = parseFloat(e.target.value) / 100;
                    self.setScale(val);
                });
            }

            // Zoom In / Out Buttons
            document.getElementById('btnFramerZoomIn')?.addEventListener('click', () => {
                self.setScale(self.scale + 0.15);
            });
            document.getElementById('btnFramerZoomOut')?.addEventListener('click', () => {
                self.setScale(self.scale - 0.15);
            });

            // Rotate Button
            document.getElementById('btnFramerRotate')?.addEventListener('click', () => {
                self.rotateClockwise();
            });

            // Fit Center Button
            document.getElementById('btnFramerFitCenter')?.addEventListener('click', () => {
                self.fitCenter();
            });

            // Reset Button
            document.getElementById('btnFramerReset')?.addEventListener('click', () => {
                self.reset();
            });

            // Apply Button
            if (this.applyBtn) {
                this.applyBtn.addEventListener('click', () => self.applyFraming());
            }

            // Drag / Pan & Wheel on Viewport
            if (this.viewportContainer) {
                // Mouse Drag
                this.viewportContainer.addEventListener('mousedown', (e) => {
                    if (e.button !== 0) return;
                    e.preventDefault();
                    self.startDrag(e.clientX, e.clientY);
                });

                window.addEventListener('mousemove', (e) => {
                    if (!self.isDragging) return;
                    self.onDrag(e.clientX, e.clientY);
                });

                window.addEventListener('mouseup', () => {
                    if (self.isDragging) self.endDrag();
                });

                // Wheel Zoom
                this.viewportContainer.addEventListener('wheel', (e) => {
                    e.preventDefault();
                    const delta = e.deltaY < 0 ? 0.08 : -0.08;
                    self.setScale(self.scale + delta);
                }, { passive: false });

                // Touch support (Drag & Pinch)
                this.viewportContainer.addEventListener('touchstart', (e) => {
                    if (e.touches.length === 1) {
                        const t = e.touches[0];
                        self.startDrag(t.clientX, t.clientY);
                    } else if (e.touches.length === 2) {
                        self.isDragging = false;
                        const dist = Math.hypot(
                            e.touches[0].clientX - e.touches[1].clientX,
                            e.touches[0].clientY - e.touches[1].clientY
                        );
                        self.initialPinchDist = dist;
                        self.initialPinchScale = self.scale;
                    }
                }, { passive: true });

                this.viewportContainer.addEventListener('touchmove', (e) => {
                    if (e.touches.length === 1 && self.isDragging) {
                        const t = e.touches[0];
                        self.onDrag(t.clientX, t.clientY);
                    } else if (e.touches.length === 2 && self.initialPinchDist > 0) {
                        const dist = Math.hypot(
                            e.touches[0].clientX - e.touches[1].clientX,
                            e.touches[0].clientY - e.touches[1].clientY
                        );
                        const factor = dist / self.initialPinchDist;
                        self.setScale(self.initialPinchScale * factor);
                    }
                }, { passive: true });

                this.viewportContainer.addEventListener('touchend', () => {
                    self.endDrag();
                    self.initialPinchDist = 0;
                });
            }

            // Window resize adjustment
            window.addEventListener('resize', () => {
                if (self.isOpen) self.updateApertureGeometry();
            });
        },

        getSlotDefaultRatio(slotKey) {
            if (slotKey === 'drawing_dimension' || slotKey === 'tir_diagram') {
                return 2.5; // banner wide 5:2
            }
            if (slotKey === 'finding_component' || slotKey === 'full_component') {
                return 1.85; // component finding box
            }
            return 1.3333; // 4:3 standard grid photo
        },

        open(slotKey) {
            const self = this;
            this.slotKey = slotKey;

            // Resolve image url from report photos or preview
            const currentUrl = window.reportPhotos?.[slotKey] ||
                document.querySelector(`#view_slot_${slotKey} img`)?.getAttribute('src') ||
                window.qcOriginalPhotos?.[slotKey];

            if (!currentUrl) {
                const S = window.qcI18n?.studio || {};
                window.qcNotify?.(S.framer_no_photo || 'Unggah foto terlebih dahulu sebelum mengatur posisi.', 'warning');
                return;
            }

            // Store raw uncropped photo permanently if not yet recorded
            if (!window.qcRawPhotos[slotKey]) {
                window.qcRawPhotos[slotKey] = window.qcOriginalPhotos[slotKey] || currentUrl;
            }
            this.sourceUrl = window.qcRawPhotos[slotKey];

            // Resolve label
            const labelEl = document.querySelector(`#card_slot_${slotKey} .slot-item-label`) ||
                document.querySelector(`#card_slot_${slotKey}`)?.closest('.area-subgroup')?.querySelector('.area-subgroup-title span');
            this.slotLabel = labelEl ? labelEl.textContent.trim() : slotKey;

            if (this.slotBadge) {
                this.slotBadge.textContent = this.slotLabel;
            }

            // Ratio setup
            this.activeRatioMode = 'slot';
            this.apertureRatio = this.getSlotDefaultRatio(slotKey);
            document.querySelectorAll('.framer-ratio-btn').forEach(b => {
                b.classList.toggle('is-active', b.dataset.ratio === 'slot');
            });

            // Reset transformations
            this.scale = 1.0;
            this.panX = 0;
            this.panY = 0;
            this.rotation = 0;

            // Show modal
            this.isOpen = true;
            this.modal.classList.add('active');
            document.body.style.overflow = 'hidden';

            // Load Image
            this.loadImage(this.sourceUrl);
        },

        close() {
            if (!this.isOpen) return;
            this.isOpen = false;
            this.modal?.classList.remove('active');
            document.body.style.overflow = '';
        },

        loadImage(src) {
            const self = this;
            this.img.onload = () => {
                self.naturalWidth = self.img.naturalWidth;
                self.naturalHeight = self.img.naturalHeight;

                self.updateApertureGeometry();
                self.fitCenter();

                if (window.lucide) lucide.createIcons();
            };
            this.img.src = src;
        },

        setRatioMode(mode) {
            this.activeRatioMode = mode;
            switch (mode) {
                case 'slot':
                    this.apertureRatio = this.getSlotDefaultRatio(this.slotKey);
                    break;
                case '4:3':
                    this.apertureRatio = 4 / 3;
                    break;
                case '16:9':
                    this.apertureRatio = 16 / 9;
                    break;
                case '1:1':
                    this.apertureRatio = 1.0;
                    break;
                case 'free':
                    const isRotated = (this.rotation === 90 || this.rotation === 270);
                    const w = isRotated ? this.naturalHeight : this.naturalWidth;
                    const h = isRotated ? this.naturalWidth : this.naturalHeight;
                    this.apertureRatio = (w > 0 && h > 0) ? (w / h) : 1.3333;
                    break;
                default:
                    this.apertureRatio = 1.3333;
            }
            this.updateApertureGeometry();
            this.fitCenter();
        },

        updateApertureGeometry() {
            if (!this.stage || !this.aperture) return;

            const stageRect = this.stage.getBoundingClientRect();
            const padding = 36;
            const maxW = Math.max(100, stageRect.width - (padding * 2));
            const maxH = Math.max(100, stageRect.height - (padding * 2));

            let apW = maxW;
            let apH = apW / this.apertureRatio;

            if (apH > maxH) {
                apH = maxH;
                apW = apH * this.apertureRatio;
            }

            apW = Math.round(apW);
            apH = Math.round(apH);

            this.aperture.style.width = `${apW}px`;
            this.aperture.style.height = `${apH}px`;

            this.viewportContainer.style.width = `${apW}px`;
            this.viewportContainer.style.height = `${apH}px`;
        },

        fitCenter() {
            if (!this.naturalWidth || !this.naturalHeight || !this.aperture) return;

            const apW = this.aperture.offsetWidth;
            const apH = this.aperture.offsetHeight;

            const isRotated = (this.rotation === 90 || this.rotation === 270);
            const effW = isRotated ? this.naturalHeight : this.naturalWidth;
            const effH = isRotated ? this.naturalWidth : this.naturalHeight;

            // Scale image to cover aperture completely (no black bars by default)
            const coverScale = Math.max(apW / effW, apH / effH);

            this.scale = coverScale;
            this.panX = 0;
            this.panY = 0;

            this.updateTransformUI();
        },

        setScale(newScale) {
            this.scale = Math.max(this.minScale, Math.min(this.maxScale, newScale));
            this.updateTransformUI();
        },

        rotateClockwise() {
            this.rotation = (this.rotation + 90) % 360;
            if (this.activeRatioMode === 'free') {
                this.setRatioMode('free');
            } else {
                this.fitCenter();
            }
        },

        reset() {
            this.rotation = 0;
            this.fitCenter();
        },

        startDrag(clientX, clientY) {
            this.isDragging = true;
            this.dragStartX = clientX;
            this.dragStartY = clientY;
            this.lastPanX = this.panX;
            this.lastPanY = this.panY;
            this.viewportContainer.classList.add('is-dragging');
        },

        onDrag(clientX, clientY) {
            if (!this.isDragging) return;
            const dx = clientX - this.dragStartX;
            const dy = clientY - this.dragStartY;
            this.panX = this.lastPanX + dx;
            this.panY = this.lastPanY + dy;
            this.updateTransformUI();
        },

        endDrag() {
            this.isDragging = false;
            this.viewportContainer?.classList.remove('is-dragging');
        },

        updateTransformUI() {
            if (!this.canvasWrap || !this.img) return;

            // Apply transform to inner image wrapper centered within aperture
            const transform = `translate(${this.panX}px, ${this.panY}px) scale(${this.scale}) rotate(${this.rotation}deg)`;
            this.canvasWrap.style.transform = transform;

            // Update zoom slider and badge
            const pct = Math.round(this.scale * 100);
            if (this.zoomSlider) this.zoomSlider.value = Math.max(100, Math.min(350, pct));
            if (this.zoomVal) this.zoomVal.textContent = `${pct}%`;
        },

        // Render current framing view to an off-screen high-resolution canvas
        exportFramedCanvas() {
            const self = this;
            return new Promise((resolve, reject) => {
                if (!self.naturalWidth || !self.naturalHeight) {
                    return reject(new Error('Image not loaded'));
                }

                const apW = self.aperture.offsetWidth;
                const apH = self.aperture.offsetHeight;

                // High resolution export: target width ~1600px for large slots, ~1200px for grid
                const targetW = (self.slotKey === 'drawing_dimension' || self.slotKey === 'finding_component' || self.slotKey === 'tir_diagram') ? 1600 : 1200;
                const targetH = Math.round(targetW / self.apertureRatio);

                const canvas = document.createElement('canvas');
                canvas.width = targetW;
                canvas.height = targetH;
                const ctx = canvas.getContext('2d');

                // High quality image smoothing
                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';

                // Ratio between export canvas pixels and screen aperture pixels
                const exportRatio = targetW / apW;

                // Move canvas origin to center of viewport
                ctx.translate(targetW / 2, targetH / 2);

                // Apply pan offset scaled to export resolution
                ctx.translate(self.panX * exportRatio, self.panY * exportRatio);

                // Apply zoom scale
                ctx.scale(self.scale * exportRatio, self.scale * exportRatio);

                // Apply rotation
                ctx.rotate((self.rotation * Math.PI) / 180);

                // Draw source image centered
                ctx.drawImage(self.img, -self.naturalWidth / 2, -self.naturalHeight / 2);

                canvas.toBlob((blob) => {
                    if (blob) {
                        resolve({
                            blob: blob,
                            dataUrl: canvas.toDataURL('image/jpeg', 0.92)
                        });
                    } else {
                        reject(new Error('Canvas export blob failed'));
                    }
                }, 'image/jpeg', 0.92);
            });
        },

        async applyFraming() {
            const self = this;
            if (!self.slotKey) return;

            const S = window.qcI18n?.studio || {};

            self.applyBtn.disabled = true;
            window.qcNotify?.(S.framer_saving || 'Menyimpan hasil framing foto...', 'loading');

            try {
                const result = await self.exportFramedCanvas();

                // Upload cropped high-res photo to server
                const formData = new FormData();
                const filename = `framed_${self.slotKey}_${Date.now()}.jpg`;
                formData.append('photo', result.blob, filename);
                formData.append('section', self.slotKey);

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

                const response = await fetch('/reports/upload-photo', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                });

                const data = await response.json();

                const finalUrl = (data.success && data.url) ? data.url : result.dataUrl;

                // Update original photo so future annotations will use this framed photo
                window.qcOriginalPhotos[self.slotKey] = finalUrl;

                if (typeof window.renderSlotPreview === 'function') {
                    window.renderSlotPreview(self.slotKey, finalUrl);
                }
                if (typeof window.markFormEdited === 'function') {
                    window.markFormEdited();
                }

                window.qcNotify?.(S.framer_success || 'Posisi dan framing foto berhasil diterapkan!', 'success');
                self.close();
            } catch (err) {
                console.error('Framing save error:', err);
                window.qcNotify?.('Gagal menyimpan framing: ' + (err.message || 'Error'), 'error');
            } finally {
                self.applyBtn.disabled = false;
            }
        }
    };

    // Global helpers
    window.QcPhotoFramer = Framer;
    window.openPhotoFramingModal = function (slotKey) {
        Framer.open(slotKey);
    };
    window.closePhotoFramingModal = function () {
        Framer.close();
    };

    // Auto-init
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => Framer.init());
    } else {
        Framer.init();
    }

})(window, document);
