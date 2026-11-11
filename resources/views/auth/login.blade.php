<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="{{ $currentTheme ?? request()->cookie('qc_theme', 'light') }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ __('qc.auth.page_title') }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap">
  <style>
    :root { color-scheme: light; font-family: Inter, Arial, sans-serif; color: #0f172a; }
    * { box-sizing: border-box; }
    body { margin: 0; min-height: 100vh; background: #f7f9fc; display: grid; place-items: center; padding: 24px; }
    .login-shell { width: min(960px, 100%); min-height: 570px; display: grid; grid-template-columns: 1fr 1fr; overflow: hidden; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 20px 50px rgba(15, 23, 42, .08); }
    .login-brand { display: flex; flex-direction: column; justify-content: space-between; padding: 48px; color: #fff; background: linear-gradient(155deg, #090a2a 0%, #1312b6 100%); border-right: 1px solid #1e2560; }
    .brand-mark { display: flex; align-items: center; gap: 14px; }
    .brand-mark img { width: 56px; height: 56px; object-fit: contain; background: transparent; padding: 0; border: none; }
    .brand-name { font-family: 'JetBrains Mono', monospace; font-size: 16px; font-weight: 800; letter-spacing: .08em; color: #fff; }
    .brand-subtitle { margin-top: 3px; color: #a5b4fc; font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
    .brand-copy { border-left: 3px solid #818cf8; padding-left: 16px; margin: 30px 0; }
    .brand-copy h1 { margin: 0 0 12px; font-size: clamp(26px, 3.8vw, 36px); line-height: 1.15; letter-spacing: -.03em; color: #fff; font-weight: 800; }
    .brand-copy p { max-width: 360px; margin: 0; color: #cbd5e1; font-size: 13.5px; line-height: 1.65; }
    .brand-foot { color: #94a3b8; font-size: 11px; font-family: 'JetBrains Mono', monospace; letter-spacing: .04em; }
    .login-panel { display: flex; align-items: center; padding: 52px; background: #ffffff; }
    .login-form { width: 100%; max-width: 370px; margin: auto; }
    .eyebrow { margin: 0 0 8px; color: #1312b6; font-size: 11px; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
    h2 { margin: 0; font-size: 26px; letter-spacing: -.03em; color: #0f172a; font-weight: 700; }
    .intro { margin: 10px 0 28px; color: #64748b; font-size: 13px; line-height: 1.6; }
    .field { margin-bottom: 18px; }
    label { display: block; margin-bottom: 7px; font-size: 11.5px; font-weight: 700; color: #334155; text-transform: uppercase; letter-spacing: .05em; }
    input[type=email], input[type=password] { width: 100%; height: 44px; padding: 0 13px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #0f172a; font: inherit; font-size: 13.5px; outline: none; transition: border-color .15s, box-shadow .15s; }
    input:focus { border-color: #1312b6; box-shadow: 0 0 0 3px rgba(19, 18, 182, .15); }
    .field-error { display: block; margin-top: 6px; color: #be123c; font-size: 11px; }
    .remember { display: flex; align-items: center; gap: 8px; margin: 2px 0 22px; color: #475569; font-size: 12px; }
    .remember input { width: 15px; height: 15px; margin: 0; accent-color: #1312b6; }
    .submit { width: 100%; height: 44px; border: 0; border-radius: 6px; background: #1312b6; color: #fff; font: inherit; font-size: 13px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 14px rgba(19, 18, 182, .25); transition: background-color .15s, transform .1s; }
    .submit:hover { background: #0e0d9b; }
    .submit:active { transform: scale(0.99); }
    .form-notice { margin: 18px 0 0; color: #94a3b8; text-align: center; font-size: 11px; line-height: 1.5; }
    [data-theme="dark"]:root { color-scheme: dark; color: #f1f5f9; }
    [data-theme="dark"] body { background: #0b1120; }
    [data-theme="dark"] .login-shell { background: #111827; border-color: #1e293b; box-shadow: 0 20px 50px rgba(0, 0, 0, .5); }
    [data-theme="dark"] .login-panel { background: #111827; }
    [data-theme="dark"] .eyebrow { color: #a5b4fc; }
    [data-theme="dark"] h2 { color: #f8fafc; }
    [data-theme="dark"] .intro { color: #94a3b8; }
    [data-theme="dark"] label { color: #cbd5e1; }
    [data-theme="dark"] input[type=email], [data-theme="dark"] input[type=password] { background: #0f172a; border-color: #334155; color: #f8fafc; }
    [data-theme="dark"] input:focus { border-color: #818cf8; box-shadow: 0 0 0 3px rgba(129, 140, 248, .25); }
    [data-theme="dark"] .remember { color: #cbd5e1; }
    [data-theme="dark"] .submit { background: #6366f1; }
    [data-theme="dark"] .submit:hover { background: #4f46e5; }
    [data-theme="dark"] .form-notice { color: #94a3b8; }
    [data-theme="dark"] .field-error { color: #fda4af; }
    @media (max-width: 720px) {
      body { padding: 14px; }
      .login-shell { max-width: 460px; min-height: 0; grid-template-columns: 1fr; border-radius: 12px; }
      .login-brand { min-height: 200px; padding: 25px; }
      .brand-copy h1 { font-size: 24px; }
      .brand-copy p, .brand-foot { display: none; }
      .login-panel { padding: 30px 25px 34px; }
      .intro { margin-bottom: 24px; }
    }
  </style>
</head>
<body>
  <main class="login-shell">
    <section class="login-brand" aria-label="QCMS PT SSC Works">
      <div class="brand-mark">
        <img src="{{ asset('images/logo.png') }}" alt="Logo PT SSC Works">
        <div><div class="brand-name">QCMS</div><div class="brand-subtitle">PT SSC Works</div></div>
      </div>
      <div class="brand-copy">
        <h1>{{ __('qc.auth.system_title') }}</h1>
        <p>{{ __('qc.auth.brand_desc') }}</p>
      </div>
      <div class="brand-foot">{{ __('qc.auth.brand_foot') }}</div>
    </section>

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
          <input id="password" type="password" name="password" autocomplete="current-password" required>
          @error('password') <span class="field-error">{{ $message }}</span> @enderror
        </div>

        <label class="remember" for="remember">
          <input id="remember" type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
          {{ __('qc.auth.remember') }}
        </label>

        <button class="submit" type="submit">{{ __('qc.auth.sign_in_btn') }}</button>
        <p class="form-notice">{{ __('qc.auth.no_account') }}</p>
      </form>
    </section>
  </main>
</body>
</html>
