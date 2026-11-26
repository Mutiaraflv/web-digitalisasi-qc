@extends('layouts.app')

@section('title', __('qc.profile.title'))
@section('page_title', __('qc.profile.title'))

@section('content')
  <div class="profile-container" style="max-width: 760px; margin: 0 auto; padding-bottom: 2rem;">
    <!-- Profile Card: Identitas Utama (Foto Profil, Nama, Jabatan, Tanggal Bergabung) -->
    <div class="profile-hero-card" style="margin-bottom: 1.25rem;">
      <div class="profile-hero-main">
        <div class="profile-hero-avatar-wrap">
          <div class="profile-hero-avatar" id="heroAvatarBox" onclick="handleAvatarClick()" title="{{ __('qc.profile.pick_photo') }}">
            @if($user->avatar_url)
              <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="user-avatar-img" id="heroAvatarImg">
            @else
              <span id="heroAvatarInitials">{{ $user->initials() }}</span>
            @endif
          </div>

          <!-- Squircle Camera Badge Button -->
          <button type="button"
                  class="avatar-camera-badge"
                  id="btnAvatarBadge"
                  onclick="handleAvatarBadgeClick(event)"
                  title="{{ __('qc.profile.pick_photo') }}"
                  aria-label="{{ __('qc.profile.pick_photo') }}">
            <i data-lucide="camera"></i>
          </button>

          @if($user->avatar_url)
            <!-- Mini Action Menu if Photo Exists -->
            <div class="avatar-badge-menu" id="avatarBadgeMenu">
              <button type="button" class="avatar-badge-menu-item" onclick="openAvatarPicker(event)">
                <i data-lucide="image-up"></i>
                <span>{{ __('qc.profile.change_photo') }}</span>
              </button>
              <button type="button" class="avatar-badge-menu-item item-danger" onclick="triggerDeleteAvatar(event)">
                <i data-lucide="trash-2"></i>
                <span>{{ __('qc.profile.remove_photo') }}</span>
              </button>
            </div>
          @endif
        </div>

        <div class="profile-hero-info">
          <h2 style="margin-bottom: 0.3rem;">
            <span>{{ $user->name }}</span>
            <span class="badge {{ $user->isAdmin() ? 'badge-blue' : ($user->isQcLeader() ? 'badge-purple' : 'badge-slate') }}">
              <i data-lucide="{{ $user->isAdmin() ? 'shield-check' : 'user-check' }}" style="width: 12px; height: 12px; vertical-align: -1px; margin-right: 3px;"></i>
              {{ $user->roleLabel() }}
            </span>
          </h2>
          <div class="profile-hero-meta">
            <span class="profile-meta-item">
              <i data-lucide="mail"></i>
              <span>{{ $user->email }}</span>
            </span>
            <span class="profile-meta-item">
              <i data-lucide="calendar"></i>
              <span>{{ __('qc.profile.joined_since', ['date' => $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-']) }}</span>
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Hidden Form for Direct Avatar Upload -->
    <form id="avatarUploadForm" method="POST" action="{{ route('profile.avatar.upload') }}" enctype="multipart/form-data" style="display: none;">
      @csrf
      <input type="file" id="avatarFileInput" name="avatar" accept="image/png, image/jpeg, image/jpg, image/webp" onchange="handleDirectAvatarUpload(this)">
    </form>

    <!-- Hidden Form for Avatar Deletion -->
    @if($user->avatar_url)
      <form id="formDeleteAvatar" method="POST" action="{{ route('profile.avatar.destroy') }}" style="display: none;">
        @csrf
        @method('DELETE')
      </form>
    @endif

    <!-- Card 1: Akses Edit Informasi Profil (Nama & Email) -->
    <div class="profile-section-card">
      <div class="profile-card-header">
        <h3>
          <i data-lucide="user-pen"></i>
          <span>{{ __('qc.profile.edit_info') }}</span>
        </h3>
      </div>
      <div class="profile-card-body">
        <form method="POST" action="{{ route('profile.update') }}">
          @csrf
          @method('PUT')

          <div class="profile-form-grid">
            <div class="profile-form-row">
              <div class="form-group">
                <label for="profile_name">{{ __('qc.profile.full_name') }} <span style="color: var(--danger);">*</span></label>
                <input type="text" id="profile_name" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')
                  <span style="color: var(--danger); font-size: 0.76rem; margin-top: 3px; display: block;">{{ $message }}</span>
                @enderror
              </div>

              <div class="form-group">
                <label for="profile_email">{{ __('qc.profile.email') }} <span style="color: var(--danger);">*</span></label>
                <input type="email" id="profile_email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')
                  <span style="color: var(--danger); font-size: 0.76rem; margin-top: 3px; display: block;">{{ $message }}</span>
                @enderror
              </div>
            </div>

            <div class="form-group">
              <label for="profile_role">{{ __('qc.profile.role_label') }}</label>
              <input type="text" id="profile_role" class="input-readonly" value="{{ $user->roleLabel() }}" disabled>
              <span style="font-size: 0.72rem; color: var(--text-subtle); margin-top: 3px; display: block;">
                {{ __('qc.profile.role_hint') }}
              </span>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 0.25rem;">
              <button type="submit" class="btn btn-primary btn-sm">
                <i data-lucide="check"></i> {{ __('qc.profile.save_profile') }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Card Tanda Tangan Digital Saya -->
    <div class="profile-section-card" id="cardProfileSignature">
      <div class="profile-card-header">
        <h3>
          <i data-lucide="pen-tool"></i>
          <span>{{ __('qc.profile.signature_section') }}</span>
        </h3>
      </div>
      <div class="profile-card-body">
        @if($user->signature_url)
          <!-- Tampilan Tanda Tangan Aktif -->
          <div class="sig-profile-display-card" id="sigProfileActiveView">
            <div class="sig-profile-preview-box">
              <img src="{{ $user->signature_url }}" alt="Tanda Tangan {{ $user->name }}" class="sig-profile-img">
            </div>
            <div class="sig-profile-meta">
              <div>
                <span class="badge badge-green">
                  <i data-lucide="check-circle-2" style="width: 12px; height: 12px;"></i>
                  {{ __('qc.profile.signature_active') }}
                </span>
              </div>
              <p class="sig-profile-hint">{{ __('qc.profile.signature_hint') }}</p>
              <div class="sig-profile-actions">
                <button type="button" class="btn btn-secondary btn-sm" onclick="toggleSignatureEditor(true)">
                  <i data-lucide="edit-3"></i> {{ __('qc.profile.signature_change') }}
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmDeleteSignature()">
                  <i data-lucide="trash-2"></i> {{ __('qc.profile.signature_delete') }}
                </button>
              </div>
            </div>
          </div>
        @endif

        <!-- Editor Input / Ubah Tanda Tangan -->
        <div id="sigEditorContainer" style="{{ ($user->signature_url && !$errors->has('signature_file')) ? 'display: none; margin-top: 1rem;' : 'margin-top: 1rem;' }}">
          <div class="sig-tab-pills">
            <button type="button" class="sig-tab-pill {{ $errors->has('signature_file') ? '' : 'active' }}" id="tabBtnDraw" onclick="switchSigTab('draw')">
              <i data-lucide="pen-line"></i> {{ __('qc.profile.tab_draw') }}
            </button>
            <button type="button" class="sig-tab-pill {{ $errors->has('signature_file') ? 'active' : '' }}" id="tabBtnUpload" onclick="switchSigTab('upload')">
              <i data-lucide="upload-cloud"></i> {{ __('qc.profile.tab_upload') }}
            </button>
          </div>

          <!-- TAB 1: Goreskan Tanda Tangan di Kanvas -->
          <div id="sigTabDraw" class="sig-tab-pane" style="{{ $errors->has('signature_file') ? 'display: none;' : '' }}">
            <div class="sig-profile-canvas-wrap" id="profileCanvasWrap">
              <canvas id="profileSigCanvas"></canvas>
              <div class="sig-profile-placeholder" id="profileSigPlaceholder">
                <i data-lucide="pen" style="width: 20px; height: 20px; display: block; margin: 0 auto 4px auto; opacity: 0.7;"></i>
                {{ __('qc.profile.draw_ph') }}
              </div>
            </div>
            <div class="sig-profile-canvas-tools">
              <button type="button" class="btn-sig-clear" onclick="clearProfileSigCanvas()">
                <i data-lucide="rotate-ccw"></i> {{ __('qc.profile.draw_clear') }}
              </button>
            </div>
            <form id="formSaveDrawSignature" method="POST" action="{{ route('profile.signature.upload') }}">
              @csrf
              <input type="hidden" name="signature_data" id="profileSigDataInput">
              <div class="sig-profile-submit-row">
                @if($user->signature_url)
                  <button type="button" class="btn btn-secondary btn-sm" onclick="toggleSignatureEditor(false)">
                    {{ __('qc.common.cancel') }}
                  </button>
                @endif
                <button type="button" class="btn btn-primary btn-sm" onclick="submitProfileDrawSignature()">
                  <i data-lucide="check"></i> {{ __('qc.profile.draw_save') }}
                </button>
              </div>
            </form>
          </div>

          <!-- TAB 2: Unggah Berkas Gambar TTD -->
          <div id="sigTabUpload" class="sig-tab-pane" style="{{ $errors->has('signature_file') ? 'display: block;' : 'display: none;' }}">
            <form method="POST" action="{{ route('profile.signature.upload') }}" enctype="multipart/form-data">
              @csrf
              <div class="sig-upload-box" id="sigUploadDropzone" onclick="document.getElementById('profileSigFileInput').click()">
                <i data-lucide="image-up" class="sig-upload-icon"></i>
                <div class="sig-upload-text">{{ __('qc.profile.upload_drag') }}</div>
                <div class="sig-upload-subtext">{{ __('qc.profile.upload_hint') }}</div>
                <input type="file" id="profileSigFileInput" name="signature_file" accept="image/png, image/jpeg, image/jpg, image/webp" style="display: none;" onchange="previewUploadSig(this)">
              </div>
              @error('signature_file')
                <div style="color: var(--danger, #ef4444); font-size: 0.8rem; margin-top: 6px; text-align: center; font-weight: 500;">
                  <i data-lucide="alert-circle" style="width: 13px; height: 13px; vertical-align: -2px; display: inline-block;"></i> {{ $message }}
                </div>
              @enderror
              <div id="uploadPreviewWrap" style="display: none; margin-top: 10px; text-align: center;">
                <img id="uploadPreviewImg" style="max-height: 70px; max-width: 180px; object-fit: contain; border: 1px dashed var(--border); border-radius: 6px; padding: 4px; background: #fff;">
                <div id="uploadFileName" style="font-size: 11px; color: var(--text-subtle); margin-top: 4px;"></div>
              </div>
              <div class="sig-profile-submit-row">
                @if($user->signature_url)
                  <button type="button" class="btn btn-secondary btn-sm" onclick="toggleSignatureEditor(false)">
                    {{ __('qc.common.cancel') }}
                  </button>
                @endif
                <button type="submit" class="btn btn-primary btn-sm" id="btnSubmitUploadSig" disabled>
                  <i data-lucide="upload"></i> {{ __('qc.profile.upload_btn') }}
                </button>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>

    <!-- Hidden Form for Signature Deletion -->
    @if($user->signature_url)
      <form id="formDeleteSignature" method="POST" action="{{ route('profile.signature.destroy') }}" style="display: none;">
        @csrf
        @method('DELETE')
      </form>
    @endif

    <!-- Card 2: Pengaturan Sandi Akun -->
    <div class="profile-section-card">
      <div class="profile-card-header">
        <h3>
          <i data-lucide="key-round"></i>
          <span>{{ __('qc.profile.change_password') }}</span>
        </h3>
      </div>
      <div class="profile-card-body">
        <form method="POST" action="{{ route('profile.password.update') }}">
          @csrf
          @method('PUT')

          <div class="profile-form-grid">
            <div class="form-group">
              <label for="current_password">{{ __('qc.profile.current_password') }} <span style="color: var(--danger);">*</span></label>
              <input type="password" id="current_password" name="current_password" placeholder="{{ __('qc.profile.current_password_ph') }}" required>
              @error('current_password')
                <span style="color: var(--danger); font-size: 0.76rem; margin-top: 3px; display: block;">{{ $message }}</span>
              @enderror
            </div>

            <div class="profile-form-row">
              <div class="form-group">
                <label for="password">{{ __('qc.profile.new_password') }} <span style="color: var(--danger);">*</span></label>
                <input type="password" id="password" name="password" placeholder="{{ __('qc.profile.new_password_ph') }}" minlength="6" required>
                @error('password')
                  <span style="color: var(--danger); font-size: 0.76rem; margin-top: 3px; display: block;">{{ $message }}</span>
                @enderror
              </div>

              <div class="form-group">
                <label for="password_confirmation">{{ __('qc.profile.confirm_password') }} <span style="color: var(--danger);">*</span></label>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="{{ __('qc.profile.confirm_password_ph') }}" minlength="6" required>
              </div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 0.25rem;">
              <button type="submit" class="btn btn-secondary btn-sm" style="font-weight: 600;">
                <i data-lucide="lock"></i> {{ __('qc.profile.save_password') }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Card 3: Login dengan Kunci Perangkat (Passkey) -->
    @php
      $passkeysReady = \App\Models\Passkey::tableExists();
      $passkeys = $passkeysReady ? $user->passkeys : collect();
    @endphp
    <div class="profile-section-card" id="faceLoginCard" style="margin-bottom: 0;">
      <div class="profile-card-header">
        <h3>
          <i data-lucide="fingerprint"></i>
          <span>{{ __('qc.passkey.title') }}</span>
        </h3>
      </div>
      <div class="profile-card-body">
        <p class="pk-desc">{{ __('qc.passkey.desc') }}</p>

        @if(! $passkeysReady)
          <div class="pk-alert">
            <i data-lucide="info"></i>
            <span>{{ __('qc.passkey.err_not_migrated') }}</span>
          </div>
        @else
          <ul class="pk-list">
            @forelse($passkeys as $pk)
              <li class="pk-item">
                <span class="pk-icon">
                  <i data-lucide="{{ preg_match('/HP|iPhone|Android|Samsung/i', $pk->name) ? 'smartphone' : 'laptop' }}"></i>
                </span>
                <div class="pk-info">
                  <strong>{{ $pk->name }}</strong>
                  <span>
                    {{ __('qc.passkey.added_on', ['date' => $pk->created_at?->translatedFormat('d M Y')]) }}
                    ·
                    {{ $pk->last_used_at
                        ? __('qc.passkey.last_used', ['date' => $pk->last_used_at->translatedFormat('d M Y, H:i')])
                        : __('qc.passkey.never_used') }}
                  </span>
                </div>
                <form method="POST" action="{{ route('passkeys.destroy', $pk) }}" class="pk-remove-form">
                  @csrf
                  @method('DELETE')
                  <button type="button" class="btn btn-secondary btn-sm pk-remove-btn" data-name="{{ $pk->name }}" title="{{ __('qc.passkey.remove') }}">
                    <i data-lucide="trash-2"></i>
                    <span class="pk-remove-label">{{ __('qc.passkey.remove') }}</span>
                  </button>
                </form>
              </li>
            @empty
              <li class="pk-empty">{{ __('qc.passkey.empty') }}</li>
            @endforelse
          </ul>

          <div class="pk-register" id="pkRegisterBox">
            <div class="form-group" style="margin: 0; flex: 1; min-width: 0;">
              <label for="pkDeviceName">{{ __('qc.passkey.device_name') }}</label>
              <input type="text" id="pkDeviceName" maxlength="60" placeholder="{{ __('qc.passkey.device_name_ph') }}">
            </div>
            <button type="button" class="btn btn-primary btn-sm" id="btnPkRegister">
              <i data-lucide="fingerprint"></i>
              <span>{{ __('qc.passkey.register_btn') }}</span>
            </button>
          </div>
          <div class="pk-alert" id="pkUnsupported" hidden>
            <i data-lucide="info"></i>
            <span id="pkUnsupportedText"></span>
          </div>
          <p class="pk-note">{{ __('qc.passkey.tunnel_note') }}</p>
        @endif
      </div>
    </div>
  </div>
@endsection

@section('styles')
<style>
  .pk-desc { font-size: 0.82rem; color: var(--text-subtle); line-height: 1.6; margin: 0 0 1rem; }
  .pk-list { list-style: none; margin: 0 0 1rem; padding: 0; display: flex; flex-direction: column; gap: 0.5rem; }
  .pk-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 0.75rem; border: 1px solid var(--border, rgba(148,163,184,.35)); border-radius: 10px; }
  .pk-icon { width: 36px; height: 36px; flex-shrink: 0; display: grid; place-items: center; border-radius: 9px; background: rgba(99,102,241,.12); color: var(--primary, #4f46e5); }
  .pk-icon svg { width: 18px; height: 18px; }
  .pk-info { flex: 1; min-width: 0; display: flex; flex-direction: column; }
  .pk-info strong { font-size: 0.86rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .pk-info span { font-size: 0.72rem; color: var(--text-subtle); }
  .pk-remove-form { margin: 0; }
  .pk-remove-btn { color: var(--danger, #dc2626); }
  .pk-empty { font-size: 0.8rem; color: var(--text-subtle); padding: 0.75rem; border: 1px dashed var(--border, rgba(148,163,184,.45)); border-radius: 10px; text-align: center; }
  .pk-register { display: flex; align-items: flex-end; gap: 0.75rem; flex-wrap: wrap; }
  .pk-register .btn { white-space: nowrap; }
  .pk-alert { display: flex; gap: 0.5rem; align-items: flex-start; margin-top: 0.75rem; padding: 0.65rem 0.75rem; border-radius: 10px; font-size: 0.78rem; line-height: 1.5; background: rgba(245,158,11,.12); color: inherit; }
  .pk-alert svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 2px; }
  .pk-note { font-size: 0.72rem; color: var(--text-subtle); margin: 0.75rem 0 0; }
  @media (max-width: 560px) {
    .pk-remove-label { display: none; }
    .pk-register .btn { width: 100%; justify-content: center; }
  }
</style>
@endsection

@section('scripts')
<script src="{{ asset('js/passkey.js') }}?v={{ file_exists(public_path('js/passkey.js')) ? filemtime(public_path('js/passkey.js')) : 1 }}"></script>
<script>
  // ---------- Login dengan Wajah (Passkey) ----------
  (function () {
    const btn = document.getElementById('btnPkRegister');
    const nameInput = document.getElementById('pkDeviceName');
    const regBox = document.getElementById('pkRegisterBox');
    const unsupported = document.getElementById('pkUnsupported');
    const unsupportedText = document.getElementById('pkUnsupportedText');
    const text = {
      unsupported: @json(__('qc.passkey.unsupported')),
      insecure: @json(__('qc.passkey.insecure')),
      registering: @json(__('qc.passkey.registering')),
      registered: @json(__('qc.passkey.registered')),
      cancelled: @json(__('qc.passkey.cancelled')),
      duplicate: @json(__('qc.passkey.err_duplicate')),
      generic: @json(__('qc.passkey.err_generic')),
      removeTitle: @json(__('qc.passkey.remove_title')),
      removeMsg: @json(__('qc.passkey.remove_msg')),
      removeBtn: @json(__('qc.passkey.remove')),
      cancel: @json(__('qc.common.cancel'))
    };

    // Hapus perangkat (dengan konfirmasi)
    document.querySelectorAll('.pk-remove-btn').forEach((b) => {
      b.addEventListener('click', async () => {
        const msg = text.removeMsg.replace(':name', b.dataset.name || '');
        const ok = typeof window.qcConfirm === 'function'
          ? await window.qcConfirm({ title: text.removeTitle, message: msg, confirmText: text.removeBtn, cancelText: text.cancel, variant: 'danger' })
          : window.confirm(msg);
        if (ok) b.closest('form')?.submit();
      });
    });

    if (!btn || !window.QcPasskey) return;

    if (!QcPasskey.isSupported() || !QcPasskey.isSecure()) {
      regBox.style.display = 'none';
      unsupportedText.textContent = QcPasskey.isSupported() ? text.insecure : text.unsupported;
      unsupported.hidden = false;
      return;
    }

    if (nameInput && !nameInput.value) nameInput.value = QcPasskey.guessDeviceName();

    btn.addEventListener('click', async () => {
      btn.disabled = true;
      window.qcNotify?.(text.registering, 'loading');
      try {
        await QcPasskey.register({
          optionsUrl: @json(route('passkeys.options')),
          storeUrl: @json(route('passkeys.store')),
          name: (nameInput?.value || '').trim()
        });
        window.qcNotify?.(text.registered, 'success');
        setTimeout(() => window.location.reload(), 900);
        return;
      } catch (err) {
        let msg = text.generic;
        if (QcPasskey.isCancelled(err)) msg = text.cancelled;
        else if (err?.name === 'InvalidStateError') msg = text.duplicate;
        else if (err?.name === 'UnsupportedError') msg = text.unsupported;
        else if (err?.fromServer) msg = err.message;
        else console.warn('[Passkey]', err);
        window.qcNotify?.(msg, 'error');
      }
      btn.disabled = false;
    });
  })();

  const hasAvatar = {{ $user->avatar_url ? 'true' : 'false' }};

  function handleAvatarBadgeClick(event) {
    event?.stopPropagation();
    if (hasAvatar) {
      const menu = document.getElementById('avatarBadgeMenu');
      if (menu) {
        menu.classList.toggle('show');
        if (window.lucide) lucide.createIcons();
      }
    } else {
      document.getElementById('avatarFileInput')?.click();
    }
  }

  function handleAvatarClick() {
    if (hasAvatar) {
      const menu = document.getElementById('avatarBadgeMenu');
      if (menu) {
        menu.classList.toggle('show');
        if (window.lucide) lucide.createIcons();
      }
    } else {
      document.getElementById('avatarFileInput')?.click();
    }
  }

  function openAvatarPicker(event) {
    event?.stopPropagation();
    document.getElementById('avatarBadgeMenu')?.classList.remove('show');
    document.getElementById('avatarFileInput')?.click();
  }

  function triggerDeleteAvatar(event) {
    event?.stopPropagation();
    document.getElementById('avatarBadgeMenu')?.classList.remove('show');
    confirmDeleteAvatar();
  }

  // Close menu on click outside
  document.addEventListener('click', (e) => {
    const menu = document.getElementById('avatarBadgeMenu');
    const badge = document.getElementById('btnAvatarBadge');
    const box = document.getElementById('heroAvatarBox');
    if (menu && menu.classList.contains('show')) {
      if (!menu.contains(e.target) && !badge?.contains(e.target) && !box?.contains(e.target)) {
        menu.classList.remove('show');
      }
    }
  });

  async function handleDirectAvatarUpload(input) {
    if (!input.files || !input.files[0]) return;
    const rawFile = input.files[0];
    const form = document.getElementById('avatarUploadForm');
    if (!form) return;

    window.qcNotify?.('Mengompres dan memproses foto profil...', 'loading');

    let fileToUpload = rawFile;
    try {
      if (typeof window.qcCompressImage === 'function') {
        const result = await window.qcCompressImage(rawFile, {
          maxWidth: 512,
          maxHeight: 512,
          quality: 0.85,
          format: 'image/webp'
        });
        fileToUpload = result.file;
        console.log(`[Avatar Compress] ${rawFile.name} (${(rawFile.size / 1024).toFixed(1)} KB) -> ${(result.compressedSize / 1024).toFixed(1)} KB [${result.format}]`);
      }
    } catch (e) {
      console.warn('Kompresi avatar di sisi klien gagal, menggunakan file asli:', e);
    }

    if (fileToUpload.size > 3 * 1024 * 1024) {
      window.qcNotify?.('{{ __("qc.profile.photo_too_large") }}', 'warning');
      input.value = '';
      return;
    }

    try {
      const dt = new DataTransfer();
      dt.items.add(fileToUpload);
      input.files = dt.files;
      form.submit();
    } catch (dtErr) {
      // Fallback submit via FormData
      const formData = new FormData(form);
      formData.set('avatar', fileToUpload);
      fetch(form.action, {
        method: 'POST',
        body: formData
      }).then(() => {
        window.location.reload();
      }).catch(() => {
        form.submit();
      });
    }
  }

  async function confirmDeleteAvatar() {
    const confirmed = await window.qcConfirm({
      title: '{{ __("qc.profile.delete_avatar_title") }}',
      message: '{{ __("qc.profile.delete_avatar_msg") }}',
      confirmText: '{{ __("qc.common.yes_delete") }}',
      cancelText: '{{ __("qc.common.cancel") }}',
      variant: 'danger'
    });
    if (confirmed) {
      document.getElementById('formDeleteAvatar')?.submit();
    }
  }

  // Profile Signature Logic
  let pSigCanvas = null;
  let pSigCtx = null;
  let pSigIsDrawing = false;
  let pSigHasDrawn = false;
  let pSigLastX = 0;
  let pSigLastY = 0;

  function toggleSignatureEditor(show) {
    const activeView = document.getElementById('sigProfileActiveView');
    const editor = document.getElementById('sigEditorContainer');
    if (activeView) activeView.style.display = show ? 'none' : 'flex';
    if (editor) {
      editor.style.display = show ? 'block' : 'none';
      if (show) {
        switchSigTab('draw');
      }
    }
    if (window.lucide) lucide.createIcons();
  }

  function switchSigTab(tab) {
    const tabDraw = document.getElementById('sigTabDraw');
    const tabUpload = document.getElementById('sigTabUpload');
    const btnDraw = document.getElementById('tabBtnDraw');
    const btnUpload = document.getElementById('tabBtnUpload');

    if (tab === 'draw') {
      if (tabDraw) tabDraw.style.display = 'block';
      if (tabUpload) tabUpload.style.display = 'none';
      btnDraw?.classList.add('active');
      btnUpload?.classList.remove('active');
      requestAnimationFrame(() => {
        initProfileCanvas();
      });
    } else {
      if (tabDraw) tabDraw.style.display = 'none';
      if (tabUpload) tabUpload.style.display = 'block';
      btnDraw?.classList.remove('active');
      btnUpload?.classList.add('active');
    }
    if (window.lucide) lucide.createIcons();
  }

  function initProfileCanvas() {
    pSigCanvas = document.getElementById('profileSigCanvas');
    const wrap = document.getElementById('profileCanvasWrap');
    if (!pSigCanvas || !wrap) return;

    pSigCtx = pSigCanvas.getContext('2d');
    const dpr = window.devicePixelRatio || 1;
    const rect = wrap.getBoundingClientRect();
    const displayW = Math.max(300, rect.width || 500);
    const displayH = 150;

    pSigCanvas.style.width = displayW + 'px';
    pSigCanvas.style.height = displayH + 'px';
    pSigCanvas.width = Math.round(displayW * dpr);
    pSigCanvas.height = Math.round(displayH * dpr);

    pSigCtx.scale(dpr, dpr);
    pSigCtx.strokeStyle = '#0f2b5c';
    pSigCtx.lineWidth = 2.4;
    pSigCtx.lineCap = 'round';
    pSigCtx.lineJoin = 'round';
  }

  function clearProfileSigCanvas() {
    if (!pSigCanvas || !pSigCtx) return;
    const dpr = window.devicePixelRatio || 1;
    pSigCtx.clearRect(0, 0, pSigCanvas.width / dpr, pSigCanvas.height / dpr);
    pSigHasDrawn = false;
    document.getElementById('profileCanvasWrap')?.classList.remove('has-drawn');
  }

  function trimCanvas(origCanvas, pad = 6) {
    const origCtx = origCanvas.getContext('2d');
    const width = origCanvas.width;
    const height = origCanvas.height;
    if (!width || !height) return origCanvas;

    try {
      const imgData = origCtx.getImageData(0, 0, width, height);
      const data = imgData.data;
      let minX = width, minY = height, maxX = -1, maxY = -1;

      for (let y = 0; y < height; y++) {
        for (let x = 0; x < width; x++) {
          const alpha = data[((y * width + x) * 4) + 3];
          if (alpha > 10) {
            if (x < minX) minX = x;
            if (x > maxX) maxX = x;
            if (y < minY) minY = y;
            if (y > maxY) maxY = y;
          }
        }
      }

      if (maxX === -1) return origCanvas;

      const pX = Math.max(0, minX - pad);
      const pY = Math.max(0, minY - pad);
      const pW = Math.min(width - 1, maxX + pad) - pX + 1;
      const pH = Math.min(height - 1, maxY + pad) - pY + 1;

      const trimmed = document.createElement('canvas');
      trimmed.width = pW;
      trimmed.height = pH;
      const tCtx = trimmed.getContext('2d');
      tCtx.drawImage(origCanvas, pX, pY, pW, pH, 0, 0, pW, pH);
      return trimmed;
    } catch (e) {
      return origCanvas;
    }
  }

  function submitProfileDrawSignature() {
    if (!pSigHasDrawn || !pSigCanvas) {
      window.qcNotify?.('Silakan goreskan tanda tangan Anda terlebih dahulu.', 'warning');
      return;
    }
    const trimmed = trimCanvas(pSigCanvas, 6);
    const dataUrl = trimmed.toDataURL('image/png');
    document.getElementById('profileSigDataInput').value = dataUrl;
    document.getElementById('formSaveDrawSignature').submit();
  }

  function previewUploadSig(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    const previewWrap = document.getElementById('uploadPreviewWrap');
    const previewImg = document.getElementById('uploadPreviewImg');
    const fileName = document.getElementById('uploadFileName');
    const submitBtn = document.getElementById('btnSubmitUploadSig');

    if (previewImg && previewWrap) {
      previewImg.src = URL.createObjectURL(file);
      previewWrap.style.display = 'block';
      if (fileName) fileName.textContent = `${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
      if (submitBtn) submitBtn.disabled = false;
    }
  }

  async function confirmDeleteSignature() {
    const confirmed = await window.qcConfirm({
      title: '{{ __("qc.profile.signature_delete_title") }}',
      message: '{{ __("qc.profile.signature_delete_msg") }}',
      confirmText: '{{ __("qc.common.yes_delete") }}',
      cancelText: '{{ __("qc.common.cancel") }}',
      variant: 'danger'
    });
    if (confirmed) {
      document.getElementById('formDeleteSignature')?.submit();
    }
  }

  // Bind mouse and touch events for profile canvas
  document.addEventListener('DOMContentLoaded', () => {
    const pCanvas = document.getElementById('profileSigCanvas');
    const pWrap = document.getElementById('profileCanvasWrap');
    if (!pCanvas) return;

    const getPos = (e) => {
      const rect = pCanvas.getBoundingClientRect();
      if (e.touches && e.touches.length > 0) {
        return { x: e.touches[0].clientX - rect.left, y: e.touches[0].clientY - rect.top };
      }
      return { x: e.clientX - rect.left, y: e.clientY - rect.top };
    };

    const startDraw = (e) => {
      if (!pSigCtx) initProfileCanvas();
      pSigIsDrawing = true;
      const pos = getPos(e);
      pSigLastX = pos.x;
      pSigLastY = pos.y;
      pWrap?.classList.add('is-drawing');
    };

    const drawing = (e) => {
      if (!pSigIsDrawing || !pSigCtx) return;
      const pos = getPos(e);
      pSigCtx.beginPath();
      pSigCtx.moveTo(pSigLastX, pSigLastY);
      pSigCtx.lineTo(pos.x, pos.y);
      pSigCtx.stroke();
      pSigLastX = pos.x;
      pSigLastY = pos.y;
      if (!pSigHasDrawn) {
        pSigHasDrawn = true;
        pWrap?.classList.add('has-drawn');
      }
    };

    const stopDraw = () => {
      if (!pSigIsDrawing) return;
      pSigIsDrawing = false;
      pWrap?.classList.remove('is-drawing');
    };

    pCanvas.addEventListener('mousedown', startDraw);
    pCanvas.addEventListener('mousemove', drawing);
    window.addEventListener('mouseup', stopDraw);
    pCanvas.addEventListener('mouseleave', stopDraw);

    pCanvas.addEventListener('touchstart', (e) => { e.preventDefault(); startDraw(e); }, { passive: false });
    pCanvas.addEventListener('touchmove', (e) => { e.preventDefault(); drawing(e); }, { passive: false });
    pCanvas.addEventListener('touchend', (e) => { e.preventDefault(); stopDraw(); }, { passive: false });
    pCanvas.addEventListener('touchcancel', (e) => { e.preventDefault(); stopDraw(); }, { passive: false });

    // Drag & Drop listener for signature upload
    const dropzone = document.getElementById('sigUploadDropzone');
    const fileInput = document.getElementById('profileSigFileInput');
    if (dropzone && fileInput) {
      ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
          e.preventDefault();
          e.stopPropagation();
          dropzone.style.borderColor = 'var(--primary, #2563eb)';
          dropzone.style.background = 'rgba(37, 99, 235, 0.05)';
        }, false);
      });
      ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
          e.preventDefault();
          e.stopPropagation();
          dropzone.style.borderColor = '';
          dropzone.style.background = '';
        }, false);
      });
      dropzone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        if (dt && dt.files && dt.files.length > 0) {
          fileInput.files = dt.files;
          previewUploadSig(fileInput);
        }
      }, false);
    }

    @if(!$user->signature_url)
      setTimeout(() => initProfileCanvas(), 100);
    @endif
  });
</script>
@endsection
