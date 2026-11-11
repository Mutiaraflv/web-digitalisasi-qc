<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="{{ $currentTheme ?? (auth()->user()?->theme ?? session('qc_theme', request()->cookie('qc_theme', 'light'))) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ __('qc.auth.page_title') }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap">
  <!-- Inline theme detection preventing white flash -->
  <script>
    (function () {
      try {
        var theme = @json($currentTheme ?? (auth()->user()?->theme ?? session('qc_theme', request()->cookie('qc_theme', 'light'))));
        if (theme) {
          document.documentElement.setAttribute('data-theme', theme);
        }
      } catch (e) {}
    })();
  </script>
  <script src="{{ asset('js/lucide.min.js') }}"></script>
  <style>
    :root {
      color-scheme: light;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      --bg-page: #ffffff;
      --bg-panel: #ffffff;
      --text-main: #0f172a;
      --text-muted: #475569;
      --text-subtle: #64748b;
      --border-color: #e2e8f0;
      --input-bg: #ffffff;
      --input-border: #cbd5e1;
      --primary: #1312b6;
      --primary-hover: #0e0d9b;
      --primary-focus-ring: rgba(19, 18, 182, 0.15);
      --danger: #dc2626;
      --danger-bg: #fef2f2;
      --danger-border: #fecaca;
    }

    [data-theme="dark"]:root {
      color-scheme: dark;
      --bg-page: #0b1120;
      --bg-panel: #0b1120;
      --text-main: #f8fafc;
      --text-muted: #cbd5e1;
      --text-subtle: #94a3b8;
      --border-color: #1e293b;
      --input-bg: #0f172a;
      --input-border: #334155;
      --primary: #4f46e5;
      --primary-hover: #4338ca;
      --primary-focus-ring: rgba(99, 102, 241, 0.25);
      --danger: #f87171;
      --danger-bg: rgba(239, 68, 68, 0.15);
      --danger-border: rgba(248, 113, 113, 0.35);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      min-height: 100vh;
      width: 100%;
      background: var(--bg-page);
      color: var(--text-main);
      overflow-x: hidden;
      line-height: 1.5;
    }

    /* Full-Screen Split Shell (Edge-to-Edge 50/50, No Cards) */
    .login-shell {
      display: grid;
      grid-template-columns: 1fr 1fr;
      min-height: 100vh;
      width: 100%;
    }

    /* Left Column: Full-Height Brand Hero */
    .login-brand {
      position: relative;
      background: radial-gradient(110% 120% at 20% 15%, #181ba8 0%, #0c1048 50%, #05071a 100%);
      color: #ffffff;
      padding: clamp(36px, 5vw, 64px);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      overflow: hidden;
      border-right: 1px solid rgba(255, 255, 255, 0.08);
    }

    /* Subtle Technical Grid Background Overlay */
    .login-brand::before {
      content: '';
      position: absolute;
      inset: 0;
      background-image: 
        radial-gradient(circle at 15% 25%, rgba(99, 102, 241, 0.25) 0%, transparent 40%),
        radial-gradient(circle at 85% 75%, rgba(19, 18, 182, 0.3) 0%, transparent 45%),
        linear-gradient(to right, rgba(255, 255, 255, 0.035) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(255, 255, 255, 0.035) 1px, transparent 1px);
      background-size: auto, auto, 48px 48px, 48px 48px;
      pointer-events: none;
    }

    .brand-content {
      position: relative;
      z-index: 2;
      display: flex;
      flex-direction: column;
      height: 100%;
      justify-content: space-between;
    }

    .brand-top-dummy {
      height: 24px;
    }

    /* Minimalist Center Showcase */
    .brand-center-showcase {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      margin: auto 0;
      padding: 2rem 1.5rem;
    }

    .brand-big-logo {
      width: 92px;
      height: 92px;
      object-fit: contain;
      filter: drop-shadow(0 10px 25px rgba(0, 0, 0, 0.4));
      margin-bottom: 1.5rem;
      transition: transform 0.3s ease;
    }

    .brand-big-logo:hover {
      transform: scale(1.05);
    }

    .brand-headline {
      font-family: 'JetBrains Mono', monospace;
      font-size: 2.35rem;
      font-weight: 800;
      letter-spacing: 0.08em;
      color: #ffffff;
      line-height: 1.1;
      margin-bottom: 0.35rem;
    }

    .brand-company-sub {
      font-size: 0.95rem;
      font-weight: 700;
      letter-spacing: 0.16em;
      text-transform: uppercase;
      color: #a5b4fc;
      margin-bottom: 1.25rem;
    }

    .brand-slogan-line {
      font-size: 0.96rem;
      color: #cbd5e1;
      font-weight: 400;
      max-width: 380px;
      line-height: 1.6;
      border-top: 1px solid rgba(255, 255, 255, 0.14);
      padding-top: 1.1rem;
      letter-spacing: 0.01em;
    }

    /* Brand Footer */
    .brand-footer {
      font-family: 'JetBrains Mono', monospace;
      font-size: 0.74rem;
      color: #94a3b8;
      letter-spacing: 0.03em;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
      padding-top: 1.25rem;
    }

    /* Right Column: Full-Height Clean Form Panel */
    .login-panel {
      position: relative;
      background: var(--bg-panel);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: clamp(32px, 5vw, 64px);
      min-height: 100vh;
    }

    .login-form-container {
      width: 100%;
      max-width: 410px;
      margin: 0 auto;
    }

    /* Mobile-only Header */
    .mobile-brand-header {
      display: none;
      align-items: center;
      gap: 12px;
      margin-bottom: 2rem;
    }

    .mobile-brand-header img {
      width: 44px;
      height: 44px;
      object-fit: contain;
    }

    .mobile-brand-header h3 {
      font-family: 'JetBrains Mono', monospace;
      font-size: 1.15rem;
      font-weight: 800;
      color: var(--text-main);
    }

    .mobile-brand-header p {
      font-size: 0.72rem;
      color: var(--text-subtle);
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.06em;
    }

    /* Form Heading */
    .form-eyebrow {
      font-size: 0.76rem;
      font-weight: 700;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: var(--primary);
      margin-bottom: 0.4rem;
    }

    .form-title {
      font-size: 1.75rem;
      font-weight: 800;
      letter-spacing: -0.03em;
      color: var(--text-main);
      margin-bottom: 0.45rem;
    }

    .form-subtitle {
      font-size: 0.88rem;
      color: var(--text-subtle);
      margin-bottom: 2rem;
      line-height: 1.5;
    }

    /* Form Fields */
    .form-field {
      margin-bottom: 1.25rem;
    }

    .form-label {
      display: block;
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--text-main);
      margin-bottom: 0.45rem;
    }

    .input-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }

    .input-icon-lead {
      position: absolute;
      left: 14px;
      width: 17px;
      height: 17px;
      color: var(--text-subtle);
      pointer-events: none;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .input-control {
      width: 100%;
      height: 46px;
      padding: 0 14px 0 42px;
      border: 1.5px solid var(--input-border);
      border-radius: 10px;
      background: var(--input-bg);
      color: var(--text-main);
      font-family: inherit;
      font-size: 0.88rem;
      outline: none;
      transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
    }

    .input-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px var(--primary-focus-ring);
    }

    .input-control.is-invalid {
      border-color: var(--danger);
      background-color: var(--danger-bg);
    }

    .input-icon-btn {
      position: absolute;
      right: 10px;
      width: 32px;
      height: 32px;
      background: transparent;
      border: none;
      color: var(--text-subtle);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      border-radius: 6px;
      transition: color 0.15s ease;
      padding: 0;
    }

    .input-icon-btn:hover {
      color: var(--text-main);
    }

    .input-icon-btn svg {
      width: 17px;
      height: 17px;
    }

    .field-error-msg {
      display: flex;
      align-items: center;
      gap: 5px;
      margin-top: 0.4rem;
      color: var(--danger);
      font-size: 0.78rem;
      font-weight: 500;
    }

    .field-error-msg svg {
      width: 13px;
      height: 13px;
      flex-shrink: 0;
    }

    /* Remember Checkbox */
    .form-options-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin: 0.5rem 0 1.6rem;
    }

    .checkbox-label {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 0.82rem;
      color: var(--text-muted);
      cursor: pointer;
      user-select: none;
    }

    .checkbox-label input[type="checkbox"] {
      width: 16px;
      height: 16px;
      border-radius: 4px;
      border: 1.5px solid var(--input-border);
      accent-color: var(--primary);
      cursor: pointer;
    }

    /* Submit Button */
    .btn-submit-login {
      width: 100%;
      height: 48px;
      border: none;
      border-radius: 10px;
      background: var(--primary);
      color: #ffffff;
      font-family: inherit;
      font-size: 0.92rem;
      font-weight: 700;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      box-shadow: 0 4px 14px rgba(19, 18, 182, 0.25);
      transition: background-color 0.16s ease, transform 0.1s ease, box-shadow 0.16s ease;
    }

    .btn-submit-login:hover {
      background: var(--primary-hover);
      box-shadow: 0 6px 20px rgba(19, 18, 182, 0.35);
    }

    .btn-submit-login:active {
      transform: scale(0.99);
    }

    .btn-submit-login svg {
      width: 17px;
      height: 17px;
      transition: transform 0.15s ease;
    }

    .btn-submit-login:hover svg {
      transform: translateX(3px);
    }

    /* Footer Notice */
    .login-footer-notice {
      margin-top: 1.75rem;
      text-align: center;
      font-size: 0.78rem;
      color: var(--text-subtle);
      line-height: 1.5;
    }

    /* Responsive Breakpoints */
    @media (max-width: 900px) {
      .login-shell {
        grid-template-columns: 1fr;
      }

      .login-brand {
        display: none;
      }

      .mobile-brand-header {
        display: flex;
      }

      .login-panel {
        padding: 40px 24px;
        align-items: center;
      }

      .form-title {
        font-size: 1.55rem;
      }
    }
  </style>
</head>
<body>
  <main class="login-shell">
    <!-- Left Column: Full-Height Brand Showcase (Minimalist Center) -->
    <section class="login-brand" aria-label="QCMS PT SSC Works">
      <div class="brand-content">
        <!-- Top Spacer for balanced vertical centering -->
        <div class="brand-top-dummy"></div>

        <!-- Center: Big Logo, QCMS Title, and Single-line Slogan -->
        <div class="brand-center-showcase">
          <img src="{{ asset('images/logo.png') }}" alt="Logo PT SSC Works" class="brand-big-logo">
          <h1 class="brand-headline">QCMS</h1>
          <div class="brand-company-sub">PT SSC Works</div>
          <p class="brand-slogan-line">Quality Control Management System</p>
        </div>

        <!-- Bottom: Version & Security Notice -->
        <div class="brand-footer">
          <span>QCMS v2.0 • PT SSC Works</span>
          <span>{{ __('qc.auth.brand_foot') }}</span>
        </div>
      </div>
    </section>

    <!-- Right Column: Full-Height Clean Form Panel (No Card Border / Shadow) -->
    <section class="login-panel">
      <div class="login-form-container">
        <!-- Mobile Logo Header (shown on mobile screens) -->
        <div class="mobile-brand-header">
          <img src="{{ asset('images/logo.png') }}" alt="Logo PT SSC Works">
          <div>
            <h3>QCMS</h3>
            <p>PT SSC Works</p>
          </div>
        </div>

        <!-- Form Heading -->
        <div class="form-eyebrow">{{ __('qc.auth.welcome_back') }}</div>
        <h2 class="form-title">{{ __('qc.auth.sign_in_title') }}</h2>
        <p class="form-subtitle">{{ __('qc.auth.sign_in_desc') }}</p>

        <!-- Form Element -->
        <form method="POST" action="{{ route('login.store') }}" autocomplete="on">
          @csrf

          <!-- Email Field -->
          <div class="form-field">
            <label for="email" class="form-label">{{ __('qc.auth.email') }}</label>
            <div class="input-wrapper">
              <span class="input-icon-lead">
                <i data-lucide="mail"></i>
              </span>
              <input id="email"
                     type="email"
                     name="email"
                     class="input-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                     value="{{ old('email') }}"
                     placeholder="contoh: user@sscworks.com"
                     autocomplete="username"
                     required
                     autofocus>
            </div>
            @error('email')
              <div class="field-error-msg">
                <i data-lucide="alert-circle"></i>
                <span>{{ $message }}</span>
              </div>
            @enderror
          </div>

          <!-- Password Field -->
          <div class="form-field">
            <label for="password" class="form-label">{{ __('qc.auth.password') }}</label>
            <div class="input-wrapper">
              <span class="input-icon-lead">
                <i data-lucide="lock"></i>
              </span>
              <input id="password"
                     type="password"
                     name="password"
                     class="input-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                     placeholder="Masukkan kata sandi akun"
                     autocomplete="current-password"
                     required>
              <button type="button"
                      class="input-icon-btn"
                      id="btnTogglePassword"
                      onclick="togglePasswordVisibility()"
                      aria-label="Lihat kata sandi"
                      title="Lihat kata sandi">
                <i data-lucide="eye" id="eyeIcon"></i>
              </button>
            </div>
            @error('password')
              <div class="field-error-msg">
                <i data-lucide="alert-circle"></i>
                <span>{{ $message }}</span>
              </div>
            @enderror
          </div>

          <!-- Remember Me Checkbox -->
          <div class="form-options-row">
            <label class="checkbox-label" for="remember">
              <input id="remember" type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
              <span>{{ __('qc.auth.remember') }}</span>
            </label>
          </div>

          <!-- Submit Button -->
          <button class="btn-submit-login" type="submit">
            <span>{{ __('qc.auth.sign_in_btn') }}</span>
            <i data-lucide="arrow-right"></i>
          </button>

          <!-- Help / Registration Notice -->
          <p class="login-footer-notice">
            {{ __('qc.auth.no_account') }}
          </p>
        </form>
      </div>
    </section>
  </main>

  <script>
    if (window.lucide) {
      lucide.createIcons();
    }

    function togglePasswordVisibility() {
      const pwd = document.getElementById('password');
      const eyeIcon = document.getElementById('eyeIcon');
      if (!pwd) return;

      if (pwd.type === 'password') {
        pwd.type = 'text';
        if (eyeIcon) eyeIcon.setAttribute('data-lucide', 'eye-off');
      } else {
        pwd.type = 'password';
        if (eyeIcon) eyeIcon.setAttribute('data-lucide', 'eye');
      }

      if (window.lucide) {
        lucide.createIcons();
      }
    }
  </script>
</body>
</html>
