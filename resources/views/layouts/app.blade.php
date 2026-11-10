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

  <div class="app-shell">
    <!-- Sidebar Navigation -->
    <aside class="app-sidebar" id="appSidebar">
      <!-- Sidebar Header -->
      <div class="sidebar-header">
        <img src="{{ asset('images/logo.png') }}" alt="Logo SSC Works" class="sidebar-logo">
        <div class="sidebar-brand-text">
          <h2>QCMS</h2>
          <p>PT SSC Works</p>
        </div>
      </div>

      <!-- Navigation Links -->
      <nav class="sidebar-nav">
        <div class="nav-section-title">Menu Utama</div>
        
        <a href="{{ route('inspection.create') }}" class="nav-item {{ request()->routeIs('inspection.create') || request()->routeIs('reports.create') || request()->routeIs('reports.edit') ? 'active' : '' }}">
          <i data-lucide="file-plus-2"></i>
          <span>Input Inspeksi Baru</span>
        </a>

        <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.index') && !request()->has('create') ? 'active' : '' }}">
          <i data-lucide="archive"></i>
          <span>Riwayat Laporan</span>
        </a>

        <a href="{{ route('templates.index') }}" class="nav-item {{ request()->routeIs('templates.*') || request()->routeIs('parts.*') ? 'active' : '' }}">
          <i data-lucide="file-spreadsheet"></i>
          <span>Template Komponen</span>
        </a>

        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
          <i data-lucide="layout-dashboard"></i>
          <span>Dashboard Ringkas</span>
        </a>

        <div class="nav-section-title" style="margin-top: 1.25rem;">Modul Pendukung</div>

        <a href="{{ route('customers.index') }}" class="nav-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
          <i data-lucide="building"></i>
          <span>Data Pelanggan</span>
        </a>

        <a href="{{ route('ncrs.index') }}" class="nav-item {{ request()->routeIs('ncrs.*') ? 'active' : '' }}">
          <i data-lucide="alert-octagon"></i>
          <span>Temuan & NCR</span>
        </a>

        <a href="{{ route('calibration.index') }}" class="nav-item {{ request()->routeIs('calibration.*') ? 'active' : '' }}">
          <i data-lucide="ruler"></i>
          <span>Kalibrasi Alat</span>
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
        <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
        <div class="user-info">
          <h4>{{ auth()->user()->name }}</h4>
          <p>{{ auth()->user()->email }} · <span style="color: {{ auth()->user()->isAdmin() ? 'var(--primary)' : 'var(--text-subtle)' }}; font-weight: 700; text-transform: uppercase; font-size: 0.65rem;">{{ auth()->user()->role ?? 'user' }}</span></p>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="sidebar-logout-form">
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
          <button type="button" class="btn-sidebar-toggle" id="btnToggleSidebar" title="Toggle Sidebar">
            <i data-lucide="menu"></i>
          </button>
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
          <div class="toast success" style="margin-bottom: 1rem; position: static;">
            <span>{{ session('success') }}</span>
          </div>
        @endif
        @if(session('error'))
          <div class="toast error" style="margin-bottom: 1rem; position: static;">
            <span>{{ session('error') }}</span>
          </div>
        @endif

        @yield('content')
      </main>
    </div>
  </div>

  <div class="toast-container" id="toastContainer"></div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      if (window.lucide) lucide.createIcons();
      
      const btnToggle = document.getElementById('btnToggleSidebar');
      const sidebar = document.getElementById('appSidebar');
      const backdrop = document.getElementById('sidebarBackdrop');

      function toggleSidebar() {
        if (!sidebar) return;
        const isOpen = sidebar.classList.toggle('show');
        if (backdrop) {
          if (isOpen) backdrop.classList.add('show');
          else backdrop.classList.remove('show');
        }
      }

      function closeSidebar() {
        if (sidebar) sidebar.classList.remove('show');
        if (backdrop) backdrop.classList.remove('show');
      }

      if (btnToggle) {
        btnToggle.addEventListener('click', toggleSidebar);
      }
      if (backdrop) {
        backdrop.addEventListener('click', closeSidebar);
      }

      // Close mobile sidebar when clicking a nav link
      document.querySelectorAll('.sidebar-nav .nav-item').forEach(item => {
        item.addEventListener('click', () => {
          if (window.innerWidth <= 900) closeSidebar();
        });
      });
    });
  </script>

  @yield('scripts')
</body>
</html>
