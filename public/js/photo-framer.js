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
        baseCoverScale: 1.0,
        userZoom: 1.0,
        minZoom: 1.0,
        maxZoom: 3.5,
        panX: 0,
        panY: 0,
        rotation: 0, // 0, 90, 180, 270
        hasUserInteracted: false,

        // Ratio state
        activeRatioMode: 'slot', // 'slot', '4:3', '16:9', '1:1', 'free'
        apertureRatio: 1.0,
        currentApW: 0,
        currentApH: 0,

        // Drag state
        isDragging: false,
        dragStartX: 0,
        dragStartY: 0,
        lastPanX: 0,
        lastPanY: 0,

        // Pinch state
        initialPinchDist: 0,
        initialPinchZoom: 1.0,

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

        ensureElements() {
            if (!this.modal) this.modal = document.getElementById('qcFramerModal');
            if (!this.stage) this.stage = document.getElementById('framerStage');
            if (!this.viewportContainer) this.viewportContainer = document.getElementById('framerViewportContainer');
            if (!this.canvasWrap) this.canvasWrap = document.getElementById('framerCanvasWrap');
            if (!this.img) this.img = document.getElementById('framerSourceImg');
            if (!this.aperture) this.aperture = document.getElementById('framerAperture');
            if (!this.slotBadge) this.slotBadge = document.getElementById('framerSlotBadge');
            if (!this.zoomSlider) this.zoomSlider = document.getElementById('framerZoomSlider');
            if (!this.zoomVal) this.zoomVal = document.getElementById('framerZoomVal');
            if (!this.applyBtn) this.applyBtn = document.getElementById('btnFramerApply');
        },

        init() {
            this.ensureElements();
            if (!this.modal) return;
            this.bindEvents();
        },

        normalizeUrl(url) {
            if (!url || typeof url !== 'string') return '';
            const trimmed = url.trim();
            if (trimmed.startsWith('data:')) return trimmed;

            // Jika URL adalah URL absolut (mis. http://localhost/... atau http://192.168.x.x/...),
            // ekstrak path relatifnya (/reports/photo/.. atau /uploads/.. atau /images/..)
            // agar selalu termuat langsung dari host & port aktif browser di Synology/klien mana pun
            try {
                if (/^https?:\/\//i.test(trimmed)) {
                    const parsed = new URL(trimmed, window.location.origin);
                    if (
                        parsed.pathname.startsWith('/reports/photo') ||
                        parsed.pathname.startsWith('/uploads') ||
                        parsed.pathname.startsWith('/images')
                    ) {
                        return parsed.pathname + parsed.search;
                    }
                }
            } catch (e) {}

            if (
                !trimmed.startsWith('/') &&
                !trimmed.startsWith('http://') &&
                !trimmed.startsWith('https://')
            ) {
                return '/' + trimmed;
            }

            return trimmed;
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
                    self.setUserZoom(val);
                });
            }

            // Zoom In / Out Buttons
            document.getElementById('btnFramerZoomIn')?.addEventListener('click', () => {
                self.setUserZoom((self.userZoom || 1.0) + 0.15);
            });
            document.getElementById('btnFramerZoomOut')?.addEventListener('click', () => {
                self.setUserZoom((self.userZoom || 1.0) - 0.15);
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
                    self.setUserZoom((self.userZoom || 1.0) + delta);
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
                        self.initialPinchZoom = self.userZoom || 1.0;
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
                        self.setUserZoom(self.initialPinchZoom * factor);
                    }
                }, { passive: true });

                this.viewportContainer.addEventListener('touchend', () => {
                    self.endDrag();
                    self.initialPinchDist = 0;
                });
            }

            // Window resize adjustment
            window.addEventListener('resize', () => {
                if (self.isOpen && self.updateApertureGeometry()) {
                    if (!self.hasUserInteracted) {
                        self.fitCenter();
                    } else {
                        self.updateTransformUI();
                    }
                }
            });
        },

        getSlotDefaultRatio(slotKey) {
            if (slotKey === 'drawing_dimension' || slotKey === 'tir_diagram') {
                return 2.5; // banner wide 5:2
            }
            if (slotKey === 'finding_component' || slotKey === 'full_component') {
                return 1.85; // component finding box
            }
            return 1.0; // 1:1 standard square grid photo (matches 110x110 slot preview & print)
        },

        open(slotKey) {
            this.ensureElements();
            this.slotKey = slotKey;

            // Resolve image url from report photos or preview
            let currentUrl = (window.reportPhotos && window.reportPhotos[slotKey]) ||
                document.querySelector(`#view_slot_${slotKey} img`)?.getAttribute('src') ||
                (window.qcOriginalPhotos && window.qcOriginalPhotos[slotKey]) || '';

            currentUrl = this.normalizeUrl(currentUrl);

            if (!currentUrl) {
                const S = window.qcI18n?.studio || {};
                window.qcNotify?.(S.framer_no_photo || 'Unggah foto terlebih dahulu sebelum mengatur posisi.', 'warning');
                return;
            }

            // Store raw uncropped photo permanently if not yet recorded
            window.qcRawPhotos = window.qcRawPhotos || {};
            if (!window.qcRawPhotos[slotKey]) {
                const orig = (window.qcOriginalPhotos && window.qcOriginalPhotos[slotKey])
                    ? this.normalizeUrl(window.qcOriginalPhotos[slotKey])
                    : '';
                window.qcRawPhotos[slotKey] = orig || currentUrl;
            }
            this.sourceUrl = window.qcRawPhotos[slotKey] || currentUrl;

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
            this.userZoom = 1.0;
            this.baseCoverScale = 1.0;
            this.scale = 1.0;
            this.panX = 0;
            this.panY = 0;
            this.rotation = 0;
            this.hasUserInteracted = false;

            // Show modal
            this.isOpen = true;
            if (this.modal) {
                this.modal.classList.add('active');
            }
            document.body.style.overflow = 'hidden';

            // Load Image
            this.loadImage(this.sourceUrl);
        },

        close() {
            if (!this.isOpen) return;
            this.isOpen = false;
            this.hasUserInteracted = false;
            if (this.modal) {
                this.modal.classList.remove('active');
            }
            document.body.style.overflow = '';
        },

        loadImage(src) {
            const self = this;
            const normalized = this.normalizeUrl(src);
            this.sourceUrl = normalized;

            this.ensureElements();
            if (!this.img) return;

            // Hapus atribut crossorigin agar gambar lokal/relatif tidak diblokir kebijakan CORS
            this.img.removeAttribute('crossorigin');
            this.img.style.display = 'block';

            const onReady = () => {
                if (!self.img.naturalWidth || !self.img.naturalHeight) return;

                self.naturalWidth = self.img.naturalWidth;
                self.naturalHeight = self.img.naturalHeight;

                const refresh = () => {
                    if (!self.isOpen) return;
                    if (self.updateApertureGeometry()) {
                        self.fitCenter();
                    }
                };

                // Perbarui ukuran aperture & skala foto secara bertahap saat modal terbuka
                refresh();
                requestAnimationFrame(refresh);
                setTimeout(refresh, 50);
                setTimeout(refresh, 180);

                if (window.lucide) lucide.createIcons();
            };

            this.img.onload = onReady;
            this.img.onerror = () => {
                console.error('[QC Framer] Gagal memuat file foto sumber:', normalized);
                window.qcNotify?.('Foto tidak dapat dimuat dari server.', 'error');
            };

            this.img.src = normalized;
            if (this.img.complete && this.img.naturalWidth) {
                onReady();
            }
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
            if (this.updateApertureGeometry()) {
                this.fitCenter();
            }
        },

        updateApertureGeometry() {
            this.ensureElements();
            if (!this.stage || !this.aperture) return false;

            const stageRect = this.stage.getBoundingClientRect();
            const stageW = stageRect.width || this.stage.offsetWidth || 800;
            const stageH = stageRect.height || this.stage.offsetHeight || 500;

            const padding = 36;
            const maxW = Math.max(120, stageW - (padding * 2));
            const maxH = Math.max(120, stageH - (padding * 2));

            const ratio = this.apertureRatio || 1.0;
            let apW = maxW;
            let apH = apW / ratio;

            if (apH > maxH) {
                apH = maxH;
                apW = apH * ratio;
            }

            apW = Math.round(apW);
            apH = Math.round(apH);

            this.aperture.style.width = `${apW}px`;
            this.aperture.style.height = `${apH}px`;

            if (this.viewportContainer) {
                this.viewportContainer.style.width = `${apW}px`;
                this.viewportContainer.style.height = `${apH}px`;
            }

            this.currentApW = apW;
            this.currentApH = apH;
            return true;
        },

        fitCenter(forcedW, forcedH) {
            this.ensureElements();
            if (!this.naturalWidth || !this.naturalHeight) return;

            const apW = forcedW || this.currentApW || this.aperture?.offsetWidth || this.viewportContainer?.offsetWidth || 400;
            const apH = forcedH || this.currentApH || this.aperture?.offsetHeight || this.viewportContainer?.offsetHeight || 400;

            if (apW <= 0 || apH <= 0) return;

            const isRotated = (this.rotation === 90 || this.rotation === 270);
            const effW = isRotated ? this.naturalHeight : this.naturalWidth;
            const effH = isRotated ? this.naturalWidth : this.naturalHeight;

            if (effW <= 0 || effH <= 0) return;

            // Menghitung skala agar foto menutupi 100% wadah aperture (mode COVER penuh)
            const scaleX = apW / effW;
            const scaleY = apH / effH;
            // Gunakan Math.max dengan toleransi subpiksel 1.002 untuk memastikan tidak ada celah hitam 1px
            const coverScale = Math.max(scaleX, scaleY) * 1.002;

            this.baseCoverScale = coverScale;
            this.userZoom = 1.0;
            this.scale = coverScale;
            this.panX = 0;
            this.panY = 0;

            this.updateTransformUI();
        },

        setUserZoom(newZoom) {
            this.hasUserInteracted = true;
            this.userZoom = Math.max(this.minZoom, Math.min(this.maxZoom, newZoom));
            this.scale = (this.baseCoverScale || 1.0) * this.userZoom;
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
            this.hasUserInteracted = false;
            this.rotation = 0;
            this.fitCenter();
        },

        startDrag(clientX, clientY) {
            this.hasUserInteracted = true;
            this.isDragging = true;
            this.dragStartX = clientX;
            this.dragStartY = clientY;
            this.lastPanX = this.panX;
            this.lastPanY = this.panY;
            this.viewportContainer?.classList.add('is-dragging');
        },

        onDrag(clientX, clientY) {
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

            // Terapkan transformasi yang berpusat pada aperture
            const transform = `translate(${this.panX}px, ${this.panY}px) scale(${this.scale}) rotate(${this.rotation}deg)`;
            this.canvasWrap.style.transform = transform;

            // Sinkronkan slider dan label persentase zoom
            const pct = Math.round((this.userZoom || 1.0) * 100);
            if (this.zoomSlider) this.zoomSlider.value = pct;
            if (this.zoomVal) this.zoomVal.textContent = `${pct}%`;
        },

        // Render framing aktif ke kanvas resolusi tinggi
        exportFramedCanvas() {
            const self = this;
            return new Promise((resolve, reject) => {
                if (!self.naturalWidth || !self.naturalHeight || !self.img?.complete || self.img.naturalWidth === 0) {
                    return reject(new Error('Foto belum selesai dimuat. Silakan tunggu hingga foto muncul.'));
                }

                const apW = self.currentApW || self.aperture?.offsetWidth || self.viewportContainer?.offsetWidth || 400;
                const apH = self.currentApH || self.aperture?.offsetHeight || self.viewportContainer?.offsetHeight || 400;

                if (apW <= 0 || apH <= 0) {
                    return reject(new Error('Ukuran bingkai foto tidak valid.'));
                }

                // Resolusi tinggi: ~1600px untuk slot banner besar, ~1200px untuk grid 2x2
                const targetW = (self.slotKey === 'drawing_dimension' || self.slotKey === 'finding_component' || self.slotKey === 'tir_diagram') ? 1600 : 1200;
                const targetH = Math.round(targetW / (self.apertureRatio || 1.0));

                const canvas = document.createElement('canvas');
                canvas.width = targetW;
                canvas.height = targetH;
                const ctx = canvas.getContext('2d');

                // Latar putih sebagai pengaman mutlak (kanvas tidak akan pernah menghasilkan JPEG hitam kosong)
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, targetW, targetH);

                // High quality image smoothing
                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';

                // Rasio resolusi ekspor terhadap dimensi aperture di layar
                const exportRatio = targetW / apW;

                // Geser titik origin ke tengah kanvas ekspor
                ctx.translate(targetW / 2, targetH / 2);

                // Terapkan pergeseran pan sesuai rasio ekspor
                ctx.translate(self.panX * exportRatio, self.panY * exportRatio);

                // Terapkan rotasi
                ctx.rotate((self.rotation * Math.PI) / 180);

                // Terapkan skala zoom
                ctx.scale(self.scale * exportRatio, self.scale * exportRatio);

                // Gambar foto sumber di titik tengah
                ctx.drawImage(self.img, -self.naturalWidth / 2, -self.naturalHeight / 2);

                canvas.toBlob((blob) => {
                    if (blob && blob.size > 200) {
                        resolve({
                            blob: blob,
                            dataUrl: canvas.toDataURL('image/jpeg', 0.92)
                        });
                    } else {
                        reject(new Error('Gagal mengekspor gambar hasil framing.'));
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
                const filename = `framed_${self.slotKey}_${Date.now()}.jpg`;

                let up;
                if (typeof window.qcUploadPhoto === 'function') {
                    up = await window.qcUploadPhoto(self.slotKey, result.blob, filename, 'framed');
                } else {
                    const formData = new FormData();
                    formData.append('photo', result.blob, filename);
                    formData.append('section', self.slotKey);
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    const resp = await fetch('/reports/upload-photo', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf },
                        body: formData
                    });
                    const data = await resp.json();
                    up = { url: data.url || result.dataUrl, queued: false };
                }

                if (up && up.queued) {
                    if (typeof window.renderSlotPreview === 'function') {
                        window.renderSlotPreview(self.slotKey, result.dataUrl, true);
                    }
                    window.qcNotify?.(S.photo_queued || 'Foto belum terunggah (koneksi bermasalah). Akan diunggah otomatis saat koneksi pulih.', 'error');
                    self.close();
                    return;
                }

                // Reset marker koordinat lama jika ada
                if (window.qcPhotoMarkers && window.qcPhotoMarkers[self.slotKey]) {
                    delete window.qcPhotoMarkers[self.slotKey];
                }

                const finalUrl = up?.url || result.dataUrl;

                if (typeof window.qcApplyUploadedPhoto === 'function') {
                    window.qcApplyUploadedPhoto('framed', self.slotKey, finalUrl);
                } else {
                    if (window.reportPhotos) window.reportPhotos[self.slotKey] = finalUrl;
                    if (typeof window.renderSlotPreview === 'function') {
                        window.renderSlotPreview(self.slotKey, finalUrl);
                    }
                }

                window.qcNotify?.(S.framer_success || 'Posisi dan framing foto berhasil diterapkan!', 'success');
                self.close();
            } catch (err) {
                console.error('[QC Framer] Error applying framing:', err);
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
