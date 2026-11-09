<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Masuk — QCMS PT SSC Works</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap">
  <style>
    :root { color-scheme: light; font-family: Inter, Arial, sans-serif; color: #19241d; }
    * { box-sizing: border-box; }
    body { margin: 0; min-height: 100vh; background: #f4f6f4; display: grid; place-items: center; padding: 24px; }
    .login-shell { width: min(960px, 100%); min-height: 570px; display: grid; grid-template-columns: 1fr 1fr; overflow: hidden; background: #fff; border: 1px solid #e3e8e4; border-radius: 12px; box-shadow: 0 20px 50px rgba(25, 36, 29, .06); }
    .login-brand { display: flex; flex-direction: column; justify-content: space-between; padding: 48px; color: #fff; background: linear-gradient(155deg, #16291f 0%, #223c2e 100%); border-right: 1px solid #2f4b3b; }
    .brand-mark { display: flex; align-items: center; gap: 14px; }
    .brand-mark img { width: 54px; height: 54px; object-fit: contain; background: #fff; padding: 5px; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.2); }
    .brand-name { font-family: 'JetBrains Mono', monospace; font-size: 16px; font-weight: 800; letter-spacing: .08em; color: #fff; }
    .brand-subtitle { margin-top: 3px; color: #74c69d; font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
    .brand-copy { border-left: 3px solid #52b788; padding-left: 16px; margin: 30px 0; }
    .brand-copy h1 { margin: 0 0 12px; font-size: clamp(26px, 3.8vw, 36px); line-height: 1.15; letter-spacing: -.03em; color: #fff; font-weight: 800; }
    .brand-copy p { max-width: 360px; margin: 0; color: #b7cebf; font-size: 13.5px; line-height: 1.65; }
    .brand-foot { color: #829f8c; font-size: 11px; font-family: 'JetBrains Mono', monospace; letter-spacing: .04em; }
    .login-panel { display: flex; align-items: center; padding: 52px; background: #ffffff; }
    .login-form { width: 100%; max-width: 370px; margin: auto; }
    .eyebrow { margin: 0 0 8px; color: #2d6a4f; font-size: 11px; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
    h2 { margin: 0; font-size: 26px; letter-spacing: -.03em; color: #19241d; font-weight: 700; }
    .intro { margin: 10px 0 28px; color: #738278; font-size: 13px; line-height: 1.6; }
    .field { margin-bottom: 18px; }
    label { display: block; margin-bottom: 7px; font-size: 11.5px; font-weight: 700; color: #4a574f; text-transform: uppercase; letter-spacing: .05em; }
    input[type=email], input[type=password] { width: 100%; height: 44px; padding: 0 13px; border: 1px solid #cdd6cf; border-radius: 6px; background: #fff; color: #19241d; font: inherit; font-size: 13.5px; outline: none; transition: border-color .15s, box-shadow .15s; }
    input:focus { border-color: #2d6a4f; box-shadow: 0 0 0 3px rgba(45, 106, 79, .15); }
    .field-error { display: block; margin-top: 6px; color: #be123c; font-size: 11px; }
    .remember { display: flex; align-items: center; gap: 8px; margin: 2px 0 22px; color: #4a574f; font-size: 12px; }
    .remember input { width: 15px; height: 15px; margin: 0; accent-color: #2d6a4f; }
    .submit { width: 100%; height: 44px; border: 0; border-radius: 6px; background: #2d6a4f; color: #fff; font: inherit; font-size: 13px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 14px rgba(45, 106, 79, .22); transition: background-color .15s, transform .1s; }
    .submit:hover { background: #204d39; }
    .submit:active { transform: scale(0.99); }
    .form-notice { margin: 18px 0 0; color: #94a3b8; text-align: center; font-size: 11px; line-height: 1.5; }
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
        <h1>Quality Control<br>Management System</h1>
        <p>Sistem pencatatan inspeksi, laporan kondisi komponen, dan dokumentasi kualitas workshop.</p>
      </div>
      <div class="brand-foot">Akses internal · Quality Control</div>
    </section>

    <section class="login-panel">
      <form class="login-form" method="POST" action="{{ route('login.store') }}">
        @csrf
        <p class="eyebrow">Selamat datang kembali</p>
        <h2>Masuk ke akun</h2>
        <p class="intro">Gunakan email dan kata sandi akun QCMS Anda untuk melanjutkan.</p>

        <div class="field">
          <label for="email">Email</label>
          <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
          @error('email') <span class="field-error">{{ $message }}</span> @enderror
        </div>

        <div class="field">
          <label for="password">Kata sandi</label>
          <input id="password" type="password" name="password" autocomplete="current-password" required>
          @error('password') <span class="field-error">{{ $message }}</span> @enderror
        </div>

        <label class="remember" for="remember">
          <input id="remember" type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
          Ingat saya di perangkat ini
        </label>

        <button class="submit" type="submit">Masuk</button>
        <p class="form-notice">Jika belum memiliki akun, hubungi administrator sistem.</p>
      </form>
    </section>
  </main>
</body>
</html>
