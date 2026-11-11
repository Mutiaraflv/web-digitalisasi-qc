<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="{{ $currentTheme ?? (auth()->user()?->theme ?? session('qc_theme', request()->cookie('qc_theme', 'light'))) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ __('qc.auth.page_title') }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700;800&display=swap">
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
      font-family: Inter, Arial, sans-serif;
      color: #0f172a;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    /* Edge-to-Edge Full Screen (No Card Box, No Outer Padding) */
    body {
      min-height: 100vh;
      width: 100%;
      margin: 0;
      padding: 0;
      background: #ffffff;
      overflow-x: hidden;
      line-height: 1.5;
    }

    .login-shell {
      width: 100%;
      min-height: 100vh;
      display: grid;
      grid-template-columns: 1fr 1fr;
      background: #ffffff;
      border: none;
      border-radius: 0;
      box-shadow: none;
    }

    /* Left Brand Section: Original Composition, Full Height */
    .login-brand {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: clamp(36px, 5vw, 64px);
      color: #ffffff;
      background: linear-gradient(155deg, #090a2a 0%, #1312b6 100%);
      border-right: 1px solid #1e2560;
      min-height: 100vh;
    }

    .brand-mark {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .brand-mark img {
      width: 56px;
      height: 56px;
      object-fit: contain;
      background: transparent;
      padding: 0;
      border: none;
    }

    .brand-name {
      font-family: 'JetBrains Mono', monospace;
      font-size: 17px;
      font-weight: 800;
      letter-spacing: .08em;
      color: #ffffff;
    }

    .brand-subtitle {
      margin-top: 3px;
      color: #a5b4fc;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
    }

    .brand-copy {
      border-left: 3px solid #818cf8;
      padding-left: 20px;
      margin: 40px 0;
      max-width: 480px;
    }

    .brand-copy h1 {
      margin: 0 0 14px;
      font-size: clamp(26px, 3.4vw, 38px);
      line-height: 1.18;
      letter-spacing: -.03em;
      color: #ffffff;
      font-weight: 800;
    }

    .brand-copy p {
      max-width: 420px;
      margin: 0;
      color: #cbd5e1;
      font-size: 14px;
      line-height: 1.65;
    }

    .brand-foot {
      color: #94a3b8;
      font-size: 11.5px;
      font-family: 'JetBrains Mono', monospace;
      letter-spacing: .04em;
    }

    /* Right Panel Section: Original Form on Full-Height Seamless Canvas */
    .login-panel {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: clamp(36px, 5vw, 64px);
      background: #ffffff;
      min-height: 100vh;
    }

    .login-form {
      width: 100%;
      max-width: 380px;
      margin: auto;
    }

    .eyebrow {
      margin: 0 0 8px;
      color: #1312b6;
      font-size: 11.5px;
      font-weight: 800;
      letter-spacing: .14em;
      text-transform: uppercase;
    }

    h2 {
      margin: 0;
      font-size: 28px;
      letter-spacing: -.03em;
      color: #0f172a;
      font-weight: 700;
    }

    .intro {
      margin: 10px 0 28px;
      color: #64748b;
      font-size: 13.5px;
      line-height: 1.6;
    }

    .field {
      margin-bottom: 18px;
    }

    label {
      display: block;
      margin-bottom: 7px;
      font-size: 11.5px;
      font-weight: 700;
      color: #334155;
      text-transform: uppercase;
      letter-spacing: .05em;
    }

    .input-wrap {
      position: relative;
      display: flex;
      align-items: center;
    }

    input[type=email], input[type=password], input[type=text] {
      width: 100%;
      height: 46px;
      padding: 0 14px;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
      background: #ffffff;
      color: #0f172a;
      font: inherit;
      font-size: 13.5px;
      outline: none;
      transition: border-color .15s, box-shadow .15s;
    }

    .input-wrap input {
      padding-right: 42px;
    }

    input:focus {
      border-color: #1312b6;
      box-shadow: 0 0 0 3px rgba(19, 18, 182, .15);
    }

    .field-error {
      display: block;
      margin-top: 6px;
      color: #be123c;
      font-size: 11.5px;
    }

    .btn-toggle-pwd {
      position: absolute;
      right: 10px;
      background: transparent;
      border: none;
      color: #64748b;
      cursor: pointer;
      padding: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 4px;
      transition: color 0.15s ease;
    }

    .btn-toggle-pwd:hover {
      color: #0f172a;
    }

    .btn-toggle-pwd svg {
      width: 17px;
      height: 17px;
    }

    .remember {
      display: flex;
      align-items: center;
      gap: 8px;
      margin: 2px 0 24px;
      color: #475569;
      font-size: 12.5px;
      cursor: pointer;
    }

    .remember input {
      width: 16px;
      height: 16px;
      margin: 0;
      accent-color: #1312b6;
      cursor: pointer;
    }

    .submit {
      width: 100%;
      height: 46px;
      border: 0;
      border-radius: 8px;
      background: #1312b6;
      color: #ffffff;
      font: inherit;
      font-size: 13.5px;
      font-weight: 700;
      cursor: pointer;
      box-shadow: 0 4px 14px rgba(19, 18, 182, .25);
      transition: background-color .15s, transform .1s;
    }

    .submit:hover {
      background: #0e0d9b;
    }

    .submit:active {
      transform: scale(0.99);
    }

    .form-notice {
      margin: 20px 0 0;
      color: #94a3b8;
      text-align: center;
      font-size: 11.5px;
      line-height: 1.5;
    }

    /* Full-Screen Dark Theme */
    [data-theme="dark"]:root {
      color-scheme: dark;
      color: #f1f5f9;
    }

    [data-theme="dark"] body {
      background: #0b1120;
    }

    [data-theme="dark"] .login-shell {
      background: #0b1120;
      border: none;
      box-shadow: none;
    }

    [data-theme="dark"] .login-panel {
      background: #0b1120;
    }

    [data-theme="dark"] .eyebrow {
      color: #a5b4fc;
    }

    [data-theme="dark"] h2 {
      color: #f8fafc;
    }

    [data-theme="dark"] .intro {
      color: #94a3b8;
    }

    [data-theme="dark"] label {
      color: #cbd5e1;
    }

    [data-theme="dark"] input[type=email],
    [data-theme="dark"] input[type=password],
    [data-theme="dark"] input[type=text] {
      background: #0f172a;
      border-color: #334155;
      color: #f8fafc;
    }

    [data-theme="dark"] input:focus {
      border-color: #818cf8;
      box-shadow: 0 0 0 3px rgba(129, 140, 248, .25);
    }

    [data-theme="dark"] .btn-toggle-pwd {
      color: #94a3b8;
    }

    [data-theme="dark"] .btn-toggle-pwd:hover {
      color: #f8fafc;
    }

    [data-theme="dark"] .remember {
      color: #cbd5e1;
    }

    [data-theme="dark"] .submit {
      background: #6366f1;
    }

    [data-theme="dark"] .submit:hover {
      background: #4f46e5;
    }

    [data-theme="dark"] .form-notice {
      color: #94a3b8;
    }

    [data-theme="dark"] .field-error {
      color: #fda4af;
    }

    @media (max-width: 900px) {
      .login-shell {
        grid-template-columns: 1fr;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
      }

      /* Slim, Elegant Top Header on Mobile (~56px) */
      .login-brand {
        min-height: 56px;
        height: 56px;
        padding: 0 1.25rem;
        flex-direction: row;
        align-items: center;
        justify-content: flex-start;
        border-right: none;
        border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        flex-shrink: 0;
      }

      .brand-mark {
        display: flex;
        align-items: center;
        gap: 10px;
      }

      .brand-mark img {
        width: 32px;
        height: 32px;
      }

      .brand-name {
        font-size: 15px;
        line-height: 1.1;
      }

      .brand-subtitle {
        font-size: 10px;
        margin-top: 1px;
      }

      /* Hide verbose copy & desktop footer on mobile to keep header ultra clean */
      .brand-copy,
      .brand-foot {
        display: none !important;
      }

      /* Login Form Immediately Below the Header */
      .login-panel {
        flex: 1;
        min-height: calc(100vh - 56px);
        padding: 32px 20px 40px;
        display: flex;
        align-items: center;
        justify-content: center;
      }

      .login-form {
        max-width: 380px;
        width: 100%;
      }

      h2 {
        font-size: 24px;
      }

      .intro {
        margin-bottom: 22px;
        font-size: 13px;
      }
    }
  </style>
</head>
<body>
  <main class="login-shell">
    <!-- Left Section: Original Content with Clean Full-Height Split -->
    <section class="login-brand" aria-label="QCMS PT SSC Works">
      <div class="brand-mark">
        <img src="{{ asset('images/logo.png') }}" alt="Logo PT SSC Works">
        <div>
          <div class="brand-name">QCMS</div>
          <div class="brand-subtitle">PT SSC Works</div>
        </div>
      </div>
      <div class="brand-copy">
        <h1>{{ __('qc.auth.system_title') }}</h1>
        <p>{{ __('qc.auth.brand_desc') }}</p>
      </div>
      <div class="brand-foot">{{ __('qc.auth.brand_foot') }}</div>
    </section>

    <!-- Right Section: Original Form on Full-Height Canvas (No Card Box) -->
    <section class="login-panel">
      <form class="login-form" method="POST" action="{{ route('login.store') }}">
        @csrf
        <p class="eyebrow">{{ __('qc.auth.welcome_back') }}</p>
        <h2>{{ __('qc.auth.sign_in_title') }}</h2>
        <p class="intro">{{ __('qc.auth.sign_in_desc') }}</p>

        <div class="field">
          <label for="email">{{ __('qc.auth.email') }}</label>
          <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
          @error('email') <span class="field-error">{{ $message }}</span> @enderror
        </div>

        <div class="field">
          <label for="password">{{ __('qc.auth.password') }}</label>
          <div class="input-wrap">
            <input id="password" type="password" name="password" autocomplete="current-password" required>
            <button type="button" class="btn-toggle-pwd" onclick="togglePasswordVisibility()" title="Lihat kata sandi" aria-label="Lihat kata sandi">
              <i data-lucide="eye" id="eyeIcon"></i>
            </button>
          </div>
          @error('password') <span class="field-error">{{ $message }}</span> @enderror
        </div>

        <label class="remember" for="remember">
          <input id="remember" type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
          <span>{{ __('qc.auth.remember') }}</span>
        </label>

        <button class="submit" type="submit">{{ __('qc.auth.sign_in_btn') }}</button>
        <p class="form-notice">{{ __('qc.auth.no_account') }}</p>
      </form>
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
