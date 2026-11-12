<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="{{ $currentTheme ?? (auth()->user()?->theme ?? session('qc_theme', request()->cookie('qc_theme', 'light'))) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ __('qc.auth.page_title') }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600;700&display=swap">
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
      font-family: 'Inter', 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      text-rendering: optimizeLegibility;
      color: #0f172a;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      -webkit-tap-highlight-color: transparent;
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
      position: relative;
      overflow: hidden;
      width: 100%;
      min-height: 100vh;
      display: grid;
      grid-template-columns: 1fr 1fr;
      background-color: #060722;
      border: none;
      border-radius: 0;
      box-shadow: none;
    }

    /* Blurred Heavy-Equipment QC Inspection Atmospheric Backdrop */
    .login-bg-photo {
      position: absolute;
      inset: 0;
      z-index: 0;
      pointer-events: none;
      background-image: url('{{ asset('images/login-inspection-bg.jpg') }}');
      background-size: cover;
      background-position: 38% center;
      filter: blur(4px) saturate(0.95);
      transform: scale(1.05);
      opacity: 0.9;
    }

    /* Smooth Corporate Blue-to-White Gradient Veil Over Blurred Backdrop */
    .login-bg-scrim {
      position: absolute;
      inset: 0;
      z-index: 1;
      pointer-events: none;
      background-image:
        radial-gradient(ellipse 42% 58% at 30% 22%, rgba(99, 102, 241, 0.16) 0%, rgba(99, 102, 241, 0) 72%),
        linear-gradient(
          96deg,
          rgba(6, 7, 36, 0.86) 0%,
          rgba(10, 14, 78, 0.78) 18%,
          rgba(16, 22, 128, 0.70) 30%,
          rgba(38, 56, 186, 0.64) 37%,
          rgba(98, 124, 226, 0.66) 43%,
          rgba(182, 200, 246, 0.82) 48%,
          rgba(234, 240, 254, 0.92) 53%,
          rgba(255, 255, 255, 0.96) 58%,
          rgba(255, 255, 255, 0.97) 100%
        );
    }

    /* Left Brand Section: Seamless over Unified Atmospheric Canvas */
    .login-brand {
      position: relative;
      z-index: 2;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: clamp(36px, 5vw, 64px);
      color: #ffffff;
      background: transparent;
      border-right: none;
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
      font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;
      font-size: 18px;
      font-weight: 800;
      letter-spacing: .04em;
      color: #ffffff;
    }

    .brand-subtitle {
      margin-top: 3px;
      color: #c7d2fe;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: .10em;
      text-transform: uppercase;
    }

    .brand-copy {
      border-left: 3px solid #818cf8;
      padding-left: 20px;
      margin: 40px 0;
      max-width: 480px;
    }

    .brand-copy h1 {
      font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;
      margin: 0 0 14px;
      font-size: clamp(26px, 3.3vw, 38px);
      line-height: 1.18;
      letter-spacing: -.028em;
      color: #ffffff;
      font-weight: 800;
      text-shadow: 0 2px 14px rgba(4, 6, 28, 0.45);
    }

    .brand-copy p {
      max-width: 420px;
      margin: 0;
      color: #f1f5f9;
      font-size: 14px;
      line-height: 1.65;
      text-shadow: 0 1px 8px rgba(4, 6, 28, 0.35);
    }

    .brand-foot {
      color: #cbd5e1;
      font-size: 11.5px;
      font-family: 'JetBrains Mono', monospace;
      letter-spacing: .04em;
    }

    /* Right Panel Section: Seamless High-Contrast Form over Unified Canvas */
    .login-panel {
      position: relative;
      z-index: 2;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: clamp(36px, 5vw, 64px);
      background: transparent;
      min-height: 100vh;
    }

    .login-form {
      width: 100%;
      max-width: 380px;
      margin: auto;
    }

    .eyebrow {
      font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;
      margin: 0 0 8px;
      color: #1312b6;
      font-size: 11.5px;
      font-weight: 700;
      letter-spacing: .14em;
      text-transform: uppercase;
    }

    h2 {
      font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;
      margin: 0;
      font-size: 29px;
      letter-spacing: -.025em;
      color: #0b1120;
      font-weight: 800;
    }

    .intro {
      margin: 10px 0 28px;
      color: #334155;
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
      color: #1e293b;
      text-transform: uppercase;
      letter-spacing: .06em;
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
      border: 1.5px solid #94a3b8;
      border-radius: 8px;
      background: #ffffff;
      color: #0b1120;
      font: inherit;
      font-size: 14px;
      font-weight: 500;
      outline: none;
      box-shadow: 0 2px 6px rgba(15, 23, 42, 0.05), inset 0 1px 2px rgba(15, 23, 42, 0.03);
      transition: border-color .15s, box-shadow .15s;
    }

    .input-wrap input {
      padding-right: 42px;
    }

    input[type=email]:hover,
    input[type=password]:hover,
    input[type=text]:hover {
      border-color: #64748b;
    }

    input[type=email]:focus,
    input[type=password]:focus,
    input[type=text]:focus {
      border-color: #1312b6;
      box-shadow: 0 0 0 4px rgba(19, 18, 182, .14);
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
      outline: none;
      box-shadow: none;
      color: #64748b;
      cursor: pointer;
      padding: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 6px;
      user-select: none;
      -webkit-user-select: none;
      -webkit-tap-highlight-color: transparent;
      touch-action: manipulation;
      transition: color 0.15s ease;
    }

    .btn-toggle-pwd:hover {
      color: #0f172a;
      background: transparent;
    }

    .btn-toggle-pwd:focus,
    .btn-toggle-pwd:active {
      background: transparent;
      outline: none;
      box-shadow: none;
    }

    .btn-toggle-pwd:focus-visible {
      outline: 2px solid rgba(19, 18, 182, 0.35);
      outline-offset: 2px;
    }

    .btn-toggle-pwd svg {
      width: 17px;
      height: 17px;
    }

    .remember {
      display: flex;
      align-items: center;
      gap: 9px;
      margin: 2px 0 24px;
      color: #1e293b;
      font-size: 12.5px;
      font-weight: 500;
      cursor: pointer;
      user-select: none;
      -webkit-user-select: none;
      -webkit-tap-highlight-color: transparent;
      touch-action: manipulation;
      outline: none;
    }

    .remember input[type=checkbox] {
      appearance: none;
      -webkit-appearance: none;
      width: 17px;
      height: 17px;
      margin: 0;
      border: 1.5px solid #94a3b8;
      border-radius: 5px;
      background: rgba(255, 255, 255, 0.9);
      display: inline-grid;
      place-content: center;
      flex-shrink: 0;
      cursor: pointer;
      outline: none;
      box-shadow: none;
      -webkit-tap-highlight-color: transparent;
      transition: background-color .15s ease, border-color .15s ease;
    }

    .remember input[type=checkbox]::after {
      content: "";
      width: 9px;
      height: 5px;
      border-left: 2px solid #ffffff;
      border-bottom: 2px solid #ffffff;
      transform: rotate(-45deg) scale(0.75);
      margin-top: -1.5px;
      opacity: 0;
      transition: opacity .12s ease, transform .12s ease;
    }

    .remember input[type=checkbox]:checked {
      background: #1312b6;
      border-color: #1312b6;
    }

    .remember input[type=checkbox]:checked::after {
      opacity: 1;
      transform: rotate(-45deg) scale(1);
    }

    .remember input[type=checkbox]:hover,
    .remember input[type=checkbox]:focus,
    .remember input[type=checkbox]:active {
      outline: none;
      box-shadow: none;
    }

    .remember input[type=checkbox]:focus-visible {
      outline: 2px solid rgba(19, 18, 182, 0.35);
      outline-offset: 2px;
    }

    .submit {
      font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;
      width: 100%;
      height: 46px;
      border: 0;
      border-radius: 8px;
      background: #1312b6;
      color: #ffffff;
      font-size: 14px;
      font-weight: 700;
      letter-spacing: .01em;
      cursor: pointer;
      outline: none;
      user-select: none;
      -webkit-user-select: none;
      -webkit-tap-highlight-color: transparent;
      touch-action: manipulation;
      box-shadow: 0 4px 14px rgba(19, 18, 182, .25);
      transition: background-color .15s, transform .1s;
    }

    .submit:focus {
      outline: none;
    }

    .submit:focus-visible {
      outline: 2px solid rgba(19, 18, 182, 0.45);
      outline-offset: 2px;
    }

    .submit:hover {
      background: #0e0d9b;
    }

    .submit:active {
      transform: scale(0.99);
    }

    .form-notice {
      margin: 20px 0 0;
      color: #475569;
      text-align: center;
      font-size: 12px;
      font-weight: 500;
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
      background-color: #05061c;
      border: none;
      box-shadow: none;
    }

    [data-theme="dark"] .login-bg-photo {
      opacity: 0.72;
      filter: blur(4px) saturate(0.88) brightness(0.9);
    }

    [data-theme="dark"] .login-bg-scrim {
      background-image:
        radial-gradient(ellipse 42% 58% at 30% 22%, rgba(99, 102, 241, 0.16) 0%, rgba(99, 102, 241, 0) 72%),
        linear-gradient(
          96deg,
          rgba(4, 6, 24, 0.88) 0%,
          rgba(8, 12, 62, 0.80) 22%,
          rgba(16, 22, 122, 0.72) 34%,
          rgba(22, 30, 114, 0.78) 43%,
          rgba(15, 22, 54, 0.90) 51%,
          rgba(11, 17, 32, 0.95) 57%,
          rgba(11, 17, 32, 0.97) 100%
        );
    }

    [data-theme="dark"] .login-panel {
      background: transparent;
    }

    [data-theme="dark"] .eyebrow {
      color: #a5b4fc;
    }

    [data-theme="dark"] h2 {
      color: #f8fafc;
    }

    [data-theme="dark"] .intro {
      color: #cbd5e1;
    }

    [data-theme="dark"] label {
      color: #e2e8f0;
    }

    [data-theme="dark"] input[type=email],
    [data-theme="dark"] input[type=password],
    [data-theme="dark"] input[type=text] {
      background: rgba(15, 23, 42, 0.96);
      border: 1.5px solid #475569;
      color: #f8fafc;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.35);
    }

    [data-theme="dark"] input[type=email]:hover,
    [data-theme="dark"] input[type=password]:hover,
    [data-theme="dark"] input[type=text]:hover {
      border-color: #64748b;
    }

    [data-theme="dark"] input[type=email]:focus,
    [data-theme="dark"] input[type=password]:focus,
    [data-theme="dark"] input[type=text]:focus {
      border-color: #818cf8;
      box-shadow: 0 0 0 4px rgba(129, 140, 248, .25);
    }

    [data-theme="dark"] .btn-toggle-pwd {
      color: #94a3b8;
    }

    [data-theme="dark"] .btn-toggle-pwd:hover {
      color: #f8fafc;
    }

    [data-theme="dark"] .btn-toggle-pwd:focus-visible {
      outline: 2px solid rgba(129, 140, 248, 0.45);
    }

    [data-theme="dark"] .remember {
      color: #cbd5e1;
    }

    [data-theme="dark"] .remember input[type=checkbox] {
      border-color: #64748b;
      background: rgba(15, 23, 42, 0.85);
    }

    [data-theme="dark"] .remember input[type=checkbox]:checked {
      background: #6366f1;
      border-color: #6366f1;
    }

    [data-theme="dark"] .remember input[type=checkbox]:focus-visible {
      outline: 2px solid rgba(129, 140, 248, 0.45);
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
      html,
      body {
        height: 100%;
        height: 100dvh;
        overflow: hidden;
        overscroll-behavior: none;
        touch-action: manipulation;
      }

      .login-shell {
        position: fixed;
        inset: 0;
        grid-template-columns: 1fr;
        width: 100%;
        height: 100%;
        height: 100dvh;
        min-height: 0;
        max-height: 100dvh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        background: #060722;
      }

      [data-theme="dark"] .login-shell {
        background: #05061c;
      }

      .login-bg-photo {
        background-position: 42% 35%;
        background-size: cover;
        filter: blur(3px) saturate(1.0);
        transform: scale(1.04);
        opacity: 0.94;
      }

      .login-bg-scrim {
        background-image:
          radial-gradient(ellipse 90% 42% at 50% 14%, rgba(37, 68, 224, 0.24) 0%, rgba(37, 68, 224, 0) 75%),
          linear-gradient(
            176deg,
            rgba(6, 7, 36, 0.78) 0%,
            rgba(14, 21, 118, 0.60) 15%,
            rgba(45, 72, 198, 0.44) 24%,
            rgba(210, 224, 254, 0.80) 32%,
            rgba(242, 246, 255, 0.86) 40%,
            rgba(246, 249, 255, 0.85) 58%,
            rgba(255, 255, 255, 0.90) 100%
          );
      }

      [data-theme="dark"] .login-bg-scrim {
        background-image:
          radial-gradient(ellipse 90% 42% at 50% 14%, rgba(67, 56, 202, 0.26) 0%, rgba(67, 56, 202, 0) 75%),
          linear-gradient(
            176deg,
            rgba(4, 6, 24, 0.80) 0%,
            rgba(10, 16, 84, 0.64) 18%,
            rgba(18, 28, 116, 0.68) 32%,
            rgba(13, 19, 44, 0.82) 52%,
            rgba(11, 17, 32, 0.88) 100%
          );
      }

      /* Direct Logo & Title on Mobile (No Header Bar) */
      .login-brand {
        width: 100%;
        max-width: 100%;
        min-height: 0;
        height: auto;
        padding: clamp(16px, 3dvh, 26px) 22px 0;
        flex-direction: row;
        align-items: center;
        justify-content: flex-start;
        background: transparent;
        backdrop-filter: none;
        -webkit-backdrop-filter: none;
        border-right: none;
        border-bottom: none;
        box-shadow: none;
        flex: 0 0 auto;
      }

      .brand-mark {
        display: flex;
        align-items: center;
        gap: 11px;
      }

      .brand-mark img {
        width: 36px;
        height: 36px;
      }

      .brand-name {
        font-size: 16px;
        line-height: 1.1;
        text-shadow: 0 1px 10px rgba(4, 6, 28, 0.55);
      }

      .brand-subtitle {
        font-size: 10.5px;
        margin-top: 2px;
        text-shadow: 0 1px 8px rgba(4, 6, 28, 0.45);
      }

      /* Hide verbose copy & desktop footer on mobile to showcase background & keep form focused */
      .brand-copy,
      .brand-foot {
        display: none !important;
      }

      /* Cardless Non-Scrollable Login Form Over Blurred Heavy-Equipment & Gradient Backdrop */
      .login-panel {
        width: 100%;
        max-width: 100%;
        flex: 1 1 auto;
        min-height: 0;
        height: auto;
        padding: clamp(18px, 6dvh, 64px) 22px clamp(16px, 3.5dvh, 32px);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: transparent;
      }

      [data-theme="dark"] .login-panel {
        background: transparent;
      }

      .login-form {
        max-width: 380px;
        width: 100%;
        margin: auto;
        padding: 0;
        border-radius: 0;
        background: transparent;
        backdrop-filter: none;
        -webkit-backdrop-filter: none;
        border: none;
        box-shadow: none;
      }

      .login-form input[type=email],
      .login-form input[type=password],
      .login-form input[type=text] {
        height: 44px;
        background: rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1.5px solid rgba(51, 65, 85, 0.34);
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06);
      }

      [data-theme="dark"] .login-form {
        background: transparent;
        backdrop-filter: none;
        -webkit-backdrop-filter: none;
        border: none;
        box-shadow: none;
      }

      [data-theme="dark"] .login-form input[type=email],
      [data-theme="dark"] .login-form input[type=password],
      [data-theme="dark"] .login-form input[type=text] {
        height: 44px;
        background: rgba(15, 23, 42, 0.64);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-color: rgba(148, 163, 184, 0.38);
      }

      h2 {
        font-size: 25px;
      }

      .intro {
        margin: 8px 0 18px;
        font-size: 13px;
      }

      .field {
        margin-bottom: 14px;
      }

      .remember {
        margin: 2px 0 18px;
      }

      .submit {
        height: 44px;
      }

      .form-notice {
        margin-top: 14px;
      }
    }

    @media (max-width: 420px) {
      .login-panel {
        padding: clamp(14px, 5dvh, 48px) 20px clamp(14px, 3dvh, 28px);
      }

      .login-form {
        padding: 0;
      }

      h2 {
        font-size: 23px;
      }

      .intro {
        margin: 6px 0 16px;
        font-size: 12.5px;
      }
    }

    @media (max-width: 900px) and (max-height: 700px) {
      .login-bg-scrim {
        background-image:
          radial-gradient(ellipse 90% 36% at 50% 10%, rgba(37, 68, 224, 0.24) 0%, rgba(37, 68, 224, 0) 75%),
          linear-gradient(
            176deg,
            rgba(6, 7, 36, 0.78) 0%,
            rgba(14, 21, 118, 0.58) 10%,
            rgba(45, 72, 198, 0.40) 16%,
            rgba(218, 230, 255, 0.82) 23%,
            rgba(244, 248, 255, 0.88) 31%,
            rgba(255, 255, 255, 0.90) 100%
          );
      }

      .login-brand {
        padding-top: 12px;
      }

      .login-panel {
        padding-top: 18px;
        padding-bottom: 14px;
      }

      .intro {
        margin: 6px 0 12px;
      }

      .field {
        margin-bottom: 11px;
      }

      .remember {
        margin: 0 0 14px;
      }

      .form-notice {
        margin-top: 10px;
      }
    }
  </style>
</head>
<body>
  <main class="login-shell">
    <div class="login-bg-photo" aria-hidden="true"></div>
    <div class="login-bg-scrim" aria-hidden="true"></div>

    <!-- Left Section: Corporate Identity Over Atmospheric Backdrop -->
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
