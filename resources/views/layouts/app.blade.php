c                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      <!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'QCMS') - PT SSC Works</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <script src="https://unpkg.com/lucide@latest"></script>
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
      </nav>

      <!-- Sidebar User Footer -->
      <div class="sidebar-footer">
        <div class="user-avatar">QC</div>
        <div class="user-info">
          <h4>Tim QC Workshop</h4>
          <p>PT SSC Works</p>
        </div>
      </div>
    </aside>

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
      if (btnToggle && sidebar) {
        btnToggle.addEventListener('click', () => {
          sidebar.classList.toggle('show');
        });
      }
    });
  </script>

  @yield('scripts')
</body>
</html>
