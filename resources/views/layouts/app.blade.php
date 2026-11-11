<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'QCMS') - PT SSC Works</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ file_exists(public_path('css/style.css')) ? filemtime(public_path('css/style.css')) : time() }}">
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
    <button type="button" class="sidebar-edge-tab" id="btnSidebarEdgeTab" title="Buka Sidebar" aria-label="Buka Sidebar">
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
        <button type="button" class="sidebar-collapse-desktop" id="btnSidebarCollapse" aria-label="Ciutkan Menu" title="Ciutkan Menu">
          <i data-lucide="panel-left-close"></i>
        </button>
        <button type="button" class="sidebar-close-mobile" id="btnCloseSidebarMobile" aria-label="Tutup Menu" title="Tutup Menu">
          <i data-lucide="x"></i>
        </button>
      </div>

      <!-- Navigation Links -->
      <nav class="sidebar-nav">
        <div class="nav-section-title">Menu Utama</div>

        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
          <i data-lucide="layout-dashboard"></i>
          <span>Dashboard</span>
        </a>

        <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') || request()->routeIs('inspection.create') ? 'active' : '' }}">
          <i data-lucide="archive"></i>
          <span>Riwayat Laporan</span>
          @if(($pendingApprovalCount ?? 0) > 0)
            <span class="nav-badge-alert" title="{{ $pendingApprovalCount }} laporan menunggu review Anda">{{ $pendingApprovalCount }}</span>
          @endif
        </a>

        <a href="{{ route('templates.index') }}" class="nav-item {{ request()->routeIs('templates.*') || request()->routeIs('parts.*') ? 'active' : '' }}">
          <i data-lucide="file-spreadsheet"></i>
          <span>Template Komponen</span>
        </a>

        @if(auth()->check() && auth()->user()->isAdmin())
          <div class="nav-section-title" style="margin-top: 1.25rem;">Pengaturan Sistem</div>

          <a href="{{ route('settings.users.index') }}" class="nav-item {{ request()->routeIs('settings.users.*') ? 'active' : '' }}">
            <i data-lucide="shield-check"></i>
            <span>Kelola Akun Web</span>
          </a>
        @endif
      </nav>

      <!-- Sidebar User Footer -->
      <div class="sidebar-footer">
        <a href="{{ route('profile.show') }}" class="sidebar-user-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" title="Buka Profil & Pengaturan Akun">
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
          title: 'Akses Log Out',
          message: 'Apakah Anda yakin ingin keluar dari akun QCMS?',
          confirmText: 'Ya',
          cancelText: 'Tidak',
          variant: 'danger',
          icon: 'log-out'
        })">
          @csrf
          <button type="submit" class="sidebar-logout" title="Keluar" aria-label="Keluar">
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

        document.querySelectorAll('.sidebar-nav .nav-item').forEach(item => {
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
