<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="{{ $currentTheme ?? (auth()->user()?->theme ?? session('qc_theme', 'light')) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'QCMS') - PT SSC Works</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ file_exists(public_path('css/style.css')) ? filemtime(public_path('css/style.css')) : time() }}">
  <!-- Inline theme initialization preventing white flash on dark mode load -->
  <script>
    (function () {
      try {
        var savedTheme = @json($currentTheme ?? (auth()->user()?->theme ?? session('qc_theme', 'light')));
        if (savedTheme) {
          document.documentElement.setAttribute('data-theme', savedTheme);
        }
      } catch (e) {}
    })();
  </script>
  <script src="{{ asset('js/lucide.min.js') }}"></script>
  @yield('styles')
</head>
<body>

  <div class="app-shell" id="appShell">
    <script>
      (function () {
        try {
          if (window.innerWidth > 900 && localStorage.getItem('qc_sidebar_collapsed') === '1') {
            document.getElementById('appShell')?.classList.add('sidebar-collapsed');
          }
        } catch (e) {}
      })();
    </script>
    <!-- Floating Sidebar Edge Tab (Linear/Notion Style) -->
    <button type="button" class="sidebar-edge-tab" id="btnSidebarEdgeTab" title="{{ __('qc.nav.open_sidebar') }}" aria-label="{{ __('qc.nav.open_sidebar') }}">
      <i data-lucide="panel-left-open"></i>
    </button>

    <!-- Sidebar Navigation -->
    <aside class="app-sidebar" id="appSidebar">
      <!-- Sidebar Header -->
      <div class="sidebar-header">
        <img src="{{ asset('images/logo.png') }}" alt="Logo SSC Works" class="sidebar-logo">
        <div class="sidebar-brand-text">
          <h2>QCMS</h2>
          <p>PT SSC Works</p>
        </div>
        <button type="button" class="sidebar-collapse-desktop" id="btnSidebarCollapse" aria-label="{{ __('qc.nav.collapse_menu') }}" title="{{ __('qc.nav.collapse_menu') }}">
          <i data-lucide="panel-left-close"></i>
        </button>
        <button type="button" class="sidebar-close-mobile" id="btnCloseSidebarMobile" aria-label="{{ __('qc.nav.close_menu') }}" title="{{ __('qc.nav.close_menu') }}">
          <i data-lucide="x"></i>
        </button>
      </div>

      <!-- Navigation Links -->
      <nav class="sidebar-nav">
        <div class="nav-section-title">{{ __('qc.nav.main_menu') }}</div>

        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
          <i data-lucide="layout-dashboard"></i>
          <span>{{ __('qc.nav.dashboard') }}</span>
        </a>

        <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') || request()->routeIs('inspection.create') ? 'active' : '' }}">
          <i data-lucide="archive"></i>
          <span>{{ __('qc.nav.reports') }}</span>
          @if(($pendingApprovalCount ?? 0) > 0)
            <span class="nav-badge-alert" title="{{ __('qc.nav.pending_tooltip', ['count' => $pendingApprovalCount]) }}">{{ $pendingApprovalCount }}</span>
          @endif
        </a>

        <a href="{{ route('templates.index') }}" class="nav-item {{ request()->routeIs('templates.*') || request()->routeIs('parts.*') ? 'active' : '' }}">
          <i data-lucide="file-spreadsheet"></i>
          <span>{{ __('qc.nav.templates') }}</span>
        </a>

        <!-- Pengaturan Sistem Nested Submenu -->
        <div class="nav-group open" id="navSettingsGroup" style="margin-top: 0.35rem;">
          <button type="button"
                  class="nav-item nav-group-toggle {{ request()->routeIs('settings.*') ? 'active' : '' }}"
                  id="btnNavSettingsToggle"
                  aria-expanded="true">
            <i data-lucide="settings"></i>
            <span>{{ __('qc.nav.system_settings') }}</span>
            <i data-lucide="chevron-down" class="nav-chevron"></i>
          </button>

          <div class="nav-submenu" id="navSettingsSubmenu">
            <a href="{{ route('settings.preferences') }}" class="nav-subitem {{ request()->routeIs('settings.index') || request()->routeIs('settings.preferences') ? 'active' : '' }}">
              <i data-lucide="sliders-horizontal"></i>
              <span>{{ __('qc.nav.appearance_language') }}</span>
            </a>

            @if(auth()->check() && auth()->user()->isAdmin())
              <a href="{{ route('settings.users.index') }}" class="nav-subitem {{ request()->routeIs('settings.users.*') ? 'active' : '' }}">
                <i data-lucide="shield-check"></i>
                <span>{{ __('qc.nav.manage_accounts') }}</span>
              </a>
            @endif
          </div>
        </div>
        @if(!request()->routeIs('settings.*'))
          <script>
            (function () {
              try {
                if (localStorage.getItem('qc_settings_group_closed') === '1') {
                  var g = document.getElementById('navSettingsGroup');
                  var b = document.getElementById('btnNavSettingsToggle');
                  if (g) g.classList.remove('open');
                  if (b) b.setAttribute('aria-expanded', 'false');
                }
              } catch (e) {}
            })();
          </script>
        @endif
      </nav>

      <!-- Sidebar User Footer -->
      <div class="sidebar-footer">
        <a href="{{ route('profile.show') }}" class="sidebar-user-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" title="{{ __('qc.nav.profile_tooltip') }}">
          <div class="user-avatar">
            @if(auth()->user()->avatar_url)
              <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="user-avatar-img">
            @else
              {{ auth()->user()->initials() }}
            @endif
          </div>
          <div class="user-info">
            <h4>{{ auth()->user()->name }}</h4>
            <p>{{ auth()->user()->email }} · <span style="color: {{ auth()->user()->isAdmin() ? 'var(--primary)' : 'var(--text-subtle)' }}; font-weight: 700; text-transform: uppercase; font-size: 0.65rem;">{{ auth()->user()->roleLabel() }}</span></p>
          </div>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="sidebar-logout-form" onsubmit="return window.qcConfirmForm(event, this, {
          title: '{{ __('qc.nav.logout_confirm_title') }}',
          message: '{{ __('qc.nav.logout_confirm_msg') }}',
          confirmText: '{{ __('qc.nav.yes') }}',
          cancelText: '{{ __('qc.nav.no') }}',
          variant: 'danger',
          icon: 'log-out'
        })">
          @csrf
          <button type="submit" class="sidebar-logout" title="{{ __('qc.nav.logout') }}" aria-label="{{ __('qc.nav.logout') }}">
            <i data-lucide="log-out"></i>
          </button>
        </form>
      </div>
    </aside>

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Main Content Area -->
    <div class="app-main">
      <!-- Top App Bar -->
      <header class="top-navbar">
        <div class="top-nav-left">
          @hasSection('top_back_link')
            <a href="@yield('top_back_link')" class="btn-nav-back-icon" title="@yield('top_back_title', 'Kembali ke Riwayat')">
              <i data-lucide="arrow-left"></i>
            </a>
          @endif

          <div class="breadcrumbs">
            <span>QCMS</span>
            <span>/</span>
            <span class="current">@yield('page_title', 'Dashboard')</span>
          </div>
        </div>

        <div class="top-nav-right">
          @yield('top_actions')
        </div>
      </header>

      <!-- Page Content -->
      <main class="page-container">
        @if(session('success'))
          <div class="qc-flash-alert success" role="status">
            <div class="qc-flash-icon"><i data-lucide="check-circle-2"></i></div>
            <div class="qc-flash-body">
              <strong>Berhasil</strong>
              <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="qc-flash-close" onclick="this.closest('.qc-flash-alert').remove()" aria-label="Tutup">&times;</button>
          </div>
        @endif
        @if(session('error'))
          <div class="qc-flash-alert error" role="alert">
            <div class="qc-flash-icon"><i data-lucide="alert-circle"></i></div>
            <div class="qc-flash-body">
              <strong>Perhatian</strong>
              <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="qc-flash-close" onclick="this.closest('.qc-flash-alert').remove()" aria-label="Tutup">&times;</button>
          </div>
        @endif

        @yield('content')
      </main>
    </div>
  </div>

  <!-- Global Floating Toast Notifications -->
  <div class="toast-container" id="toastContainer" aria-live="polite"></div>

  <!-- Global Professional Confirmation & Info Modal -->
  <div class="qc-dialog-overlay" id="qcDialogOverlay" aria-hidden="true">
    <div class="qc-dialog-box" role="dialog" aria-modal="true" aria-labelledby="qcDialogTitle">
      <div class="qc-dialog-icon-wrap" id="qcDialogIconWrap">
        <i data-lucide="help-circle" id="qcDialogIcon"></i>
      </div>
      <h3 class="qc-dialog-title" id="qcDialogTitle">Konfirmasi</h3>
      <p class="qc-dialog-message" id="qcDialogMessage"></p>
      <div class="qc-dialog-actions">
        <button type="button" class="btn btn-secondary btn-sm" id="qcDialogCancel">Batal</button>
        <button type="button" class="btn btn-primary btn-sm" id="qcDialogConfirm">Lanjutkan</button>
      </div>
    </div>
  </div>

  <script>
    (function () {
      function escapeHtml(str) {
        return String(str ?? '')
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;');
      }

      const toastMeta = {
        success: { title: 'Berhasil', icon: 'check-circle-2' },
        error: { title: 'Gagal', icon: 'alert-circle' },
        warning: { title: 'Perhatian', icon: 'alert-triangle' },
        info: { title: 'Informasi', icon: 'info' },
        loading: { title: 'Memproses', icon: 'loader-2' },
      };

      window.qcNotify = function (message, type = 'success', customTitle = null) {
        const container = document.getElementById('toastContainer');
        if (!container) return null;

        const kind = toastMeta[type] ? type : 'info';
        const meta = toastMeta[kind];
        const title = customTitle || meta.title;
        const duration = kind === 'error' ? 5000 : (kind === 'loading' ? 0 : 3800);

        // Replace existing loading toast smoothly
        if (kind !== 'loading') {
          container.querySelectorAll('.toast.loading').forEach(el => el.remove());
        }

        const toast = document.createElement('div');
        toast.className = `toast ${kind}`;
        toast.innerHTML = `
          <div class="toast-icon"><i data-lucide="${meta.icon}"></i></div>
          <div class="toast-body">
            <div class="toast-title">${escapeHtml(title)}</div>
            <div class="toast-msg">${escapeHtml(message)}</div>
          </div>
          <button type="button" class="toast-close" aria-label="Tutup">&times;</button>
          ${duration > 0 ? `<div class="toast-progress" style="animation-duration: ${duration}ms;"></div>` : ''}
        `;

        const dismiss = () => {
          toast.classList.add('toast-leaving');
          setTimeout(() => toast.remove(), 220);
        };

        toast.querySelector('.toast-close')?.addEventListener('click', dismiss);
        container.appendChild(toast);
        if (window.lucide) lucide.createIcons({ root: toast });

        if (duration > 0) {
          setTimeout(dismiss, duration);
        }
        return toast;
      };

      window.showToast = function (message, type = 'success', customTitle = null) {
        return window.qcNotify(message, type, customTitle);
      };

      /**
       * Client-Side Image Compression & Optimization Engine (WebP / JPEG)
       * Automatically scales resolution and converts high-res camera photos to lightweight WebP/JPEG.
       */
      window.qcCompressImage = function (file, options = {}) {
        return new Promise((resolve, reject) => {
          if (!file || !(file instanceof Blob)) {
            return reject(new Error('File tidak valid'));
          }
          if (file.type === 'image/svg+xml') {
            return resolve({
              file,
              blob: file,
              dataUrl: null,
              originalSize: file.size,
              compressedSize: file.size,
              ratio: 0,
              format: file.type
            });
          }

          const maxWidth = options.maxWidth || 1600;
          const maxHeight = options.maxHeight || 1600;
          const quality = typeof options.quality === 'number' ? options.quality : 0.82;
          const targetFormat = options.format || 'image/webp';

          const reader = new FileReader();
          reader.onerror = () => reject(new Error('Gagal membaca file gambar'));
          reader.onload = (ev) => {
            const img = new Image();
            img.onerror = () => reject(new Error('Format file gambar tidak dapat dibuka'));
            img.onload = () => {
              let w = img.naturalWidth || img.width;
              let h = img.naturalHeight || img.height;

              // Proportional scaling
              if (w > maxWidth || h > maxHeight) {
                const scale = Math.min(maxWidth / w, maxHeight / h);
                w = Math.max(1, Math.round(w * scale));
                h = Math.max(1, Math.round(h * scale));
              }

              const canvas = document.createElement('canvas');
              canvas.width = w;
              canvas.height = h;
              const ctx = canvas.getContext('2d');
              if (!ctx) return reject(new Error('Canvas tidak didukung'));

              // Clear background with white (protects transparent PNGs converting to JPEG/WebP)
              ctx.fillStyle = '#FFFFFF';
              ctx.fillRect(0, 0, w, h);
              ctx.drawImage(img, 0, 0, w, h);

              const exportBlob = (fmt) => {
                canvas.toBlob((blob) => {
                  if (!blob) {
                    if (fmt !== 'image/jpeg') {
                      exportBlob('image/jpeg');
                    } else {
                      reject(new Error('Gagal mengompres gambar'));
                    }
                    return;
                  }

                  // Fallback if browser returns image/png instead of webp
                  if (fmt === 'image/webp' && blob.type === 'image/png') {
                    exportBlob('image/jpeg');
                    return;
                  }

                  const finalFormat = blob.type;
                  const ext = finalFormat === 'image/webp' ? '.webp' : (finalFormat === 'image/png' ? '.png' : '.jpg');
                  const rawName = (file.name || 'image').replace(/\.[^/.]+$/, '');
                  const newFileName = `${rawName}${ext}`;

                  const compressedFile = new File([blob], newFileName, {
                    type: finalFormat,
                    lastModified: Date.now()
                  });

                  const dataUrl = canvas.toDataURL(finalFormat, quality);

                  resolve({
                    file: compressedFile,
                    blob: blob,
                    dataUrl: dataUrl,
                    originalSize: file.size,
                    compressedSize: blob.size,
                    ratio: Math.round((1 - (blob.size / Math.max(1, file.size))) * 100),
                    format: finalFormat,
                    width: w,
                    height: h
                  });
                }, fmt, quality);
              };

              exportBlob(targetFormat);
            };
            img.src = ev.target.result;
          };
          reader.readAsDataURL(file);
        });
      };

      let dialogResolver = null;
      function closeQcDialog(result) {
        const overlay = document.getElementById('qcDialogOverlay');
        if (!overlay) return;
        overlay.classList.remove('active');
        overlay.setAttribute('aria-hidden', 'true');
        if (dialogResolver) {
          const resolve = dialogResolver;
          dialogResolver = null;
          resolve(result);
        }
      }

      function openQcDialog({ title, message, confirmText = 'Lanjutkan', cancelText = 'Batal', variant = 'primary', icon = null, showCancel = true }) {
        const overlay = document.getElementById('qcDialogOverlay');
        const titleEl = document.getElementById('qcDialogTitle');
        const msgEl = document.getElementById('qcDialogMessage');
        const confirmBtn = document.getElementById('qcDialogConfirm');
        const cancelBtn = document.getElementById('qcDialogCancel');
        const iconWrap = document.getElementById('qcDialogIconWrap');

        if (!overlay || !confirmBtn || !cancelBtn) {
          return Promise.resolve(showCancel ? window.confirm(message) : true);
        }

        const iconMap = {
          danger: 'trash-2',
          warning: 'alert-triangle',
          success: 'check-circle-2',
          info: 'info',
          primary: 'help-circle',
        };
        const btnClassMap = {
          danger: 'btn-danger-solid',
          warning: 'btn-primary',
          success: 'btn-success-solid',
          info: 'btn-primary',
          primary: 'btn-primary',
        };

        titleEl.textContent = title || 'Konfirmasi';
        msgEl.textContent = message || '';
        confirmBtn.textContent = confirmText;
        cancelBtn.textContent = cancelText;
        cancelBtn.style.display = showCancel ? '' : 'none';

        confirmBtn.className = `btn btn-sm ${btnClassMap[variant] || 'btn-primary'}`;
        iconWrap.className = `qc-dialog-icon-wrap variant-${variant}`;
        const chosenIcon = icon || iconMap[variant] || 'help-circle';
        iconWrap.innerHTML = `<i data-lucide="${chosenIcon}"></i>`;
        if (window.lucide) lucide.createIcons({ root: iconWrap });

        overlay.classList.add('active');
        overlay.setAttribute('aria-hidden', 'false');
        setTimeout(() => confirmBtn.focus(), 50);

        return new Promise((resolve) => {
          dialogResolver = resolve;
        });
      }

      window.qcConfirm = function (options) {
        return openQcDialog({
          title: options?.title || 'Konfirmasi Tindakan',
          message: options?.message || '',
          confirmText: options?.confirmText || 'Ya, Lanjutkan',
          cancelText: options?.cancelText || 'Batal',
          variant: options?.variant || 'primary',
          icon: options?.icon || null,
          showCancel: true,
        });
      };

      window.qcAlert = function (options) {
        const opts = typeof options === 'string' ? { message: options } : (options || {});
        return openQcDialog({
          title: opts.title || 'Informasi',
          message: opts.message || '',
          confirmText: opts.buttonText || 'Mengerti',
          variant: opts.variant || 'info',
          showCancel: false,
        });
      };

      window.qcConfirmForm = function (event, formEl, options) {
        if (formEl.dataset.confirmed === '1') {
          formEl.dataset.confirmed = '';
          return true;
        }
        event.preventDefault();
        window.qcConfirm(options).then((ok) => {
          if (ok) {
            formEl.dataset.confirmed = '1';
            formEl.submit();
          }
        });
        return false;
      };

      document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();

        const appShell = document.getElementById('appShell');
        const btnCollapse = document.getElementById('btnSidebarCollapse');
        const btnEdgeTab = document.getElementById('btnSidebarEdgeTab');
        const btnCloseMobile = document.getElementById('btnCloseSidebarMobile');
        const sidebar = document.getElementById('appSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');

        function syncToggleButtonState() {
          const isMobile = window.innerWidth <= 900;
          const isOpen = isMobile
            ? Boolean(sidebar?.classList.contains('show'))
            : !Boolean(appShell?.classList.contains('sidebar-collapsed'));
          if (btnCollapse) {
            btnCollapse.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
          }
          if (btnEdgeTab) {
            btnEdgeTab.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
          }
        }

        function openMobileSidebar() {
          if (!sidebar) return;
          sidebar.classList.add('show');
          backdrop?.classList.add('show');
          document.body.style.overflow = 'hidden';
          syncToggleButtonState();
        }

        function closeMobileSidebar() {
          if (sidebar) sidebar.classList.remove('show');
          if (backdrop) backdrop.classList.remove('show');
          document.body.style.overflow = '';
          syncToggleButtonState();
        }

        function toggleSidebar() {
          if (window.innerWidth <= 900) {
            const isOpen = sidebar?.classList.contains('show');
            if (isOpen) closeMobileSidebar();
            else openMobileSidebar();
            return;
          }

          if (appShell) {
            const isCollapsed = appShell.classList.toggle('sidebar-collapsed');
            try {
              localStorage.setItem('qc_sidebar_collapsed', isCollapsed ? '1' : '0');
            } catch (e) {}
            syncToggleButtonState();
            setTimeout(() => window.dispatchEvent(new Event('resize')), 260);
          }
        }

        // Sidebar Pengaturan Sistem submenu accordion
        const navSettingsGroup = document.getElementById('navSettingsGroup');
        const btnNavSettingsToggle = document.getElementById('btnNavSettingsToggle');
        if (btnNavSettingsToggle && navSettingsGroup) {
          btnNavSettingsToggle.addEventListener('click', () => {
            const isOpen = navSettingsGroup.classList.toggle('open');
            btnNavSettingsToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            try {
              localStorage.setItem('qc_settings_group_closed', isOpen ? '0' : '1');
            } catch (e) {}
          });
        }

        btnCollapse?.addEventListener('click', toggleSidebar);
        btnEdgeTab?.addEventListener('click', toggleSidebar);
        btnCloseMobile?.addEventListener('click', closeMobileSidebar);
        backdrop?.addEventListener('click', closeMobileSidebar);

        window.addEventListener('resize', () => {
          if (window.innerWidth > 900) {
            closeMobileSidebar();
            try {
              const saved = localStorage.getItem('qc_sidebar_collapsed') === '1';
              appShell?.classList.toggle('sidebar-collapsed', saved);
            } catch (e) {}
          } else {
            appShell?.classList.remove('sidebar-collapsed');
          }
          syncToggleButtonState();
        });

        document.querySelectorAll('.sidebar-nav a.nav-item, .sidebar-nav a.nav-subitem').forEach(item => {
          item.addEventListener('click', () => {
            if (window.innerWidth <= 900) closeMobileSidebar();
          });
        });

        syncToggleButtonState();

        // Dialog button handlers
        document.getElementById('qcDialogConfirm')?.addEventListener('click', () => closeQcDialog(true));
        document.getElementById('qcDialogCancel')?.addEventListener('click', () => closeQcDialog(false));
        document.getElementById('qcDialogOverlay')?.addEventListener('click', (e) => {
          if (e.target.id === 'qcDialogOverlay') closeQcDialog(false);
        });
        document.addEventListener('keydown', (e) => {
          const overlay = document.getElementById('qcDialogOverlay');
          if (e.key === 'Escape' && overlay?.classList.contains('active')) {
            closeQcDialog(false);
          } else if (e.key === 'Escape' && sidebar?.classList.contains('show')) {
            closeMobileSidebar();
          }
        });

        // Auto-hide flash alert after 6 seconds smoothly
        document.querySelectorAll('.qc-flash-alert').forEach(banner => {
          setTimeout(() => {
            banner.style.opacity = '0';
            banner.style.transform = 'translateY(-6px)';
            setTimeout(() => banner.remove(), 260);
          }, 6000);
        });
      });
    })();
  </script>

  @yield('scripts')
</body>
</html>
