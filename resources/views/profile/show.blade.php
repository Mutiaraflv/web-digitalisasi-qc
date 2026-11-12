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

    <!-- Card 2: Pengaturan Sandi Akun -->
    <div class="profile-section-card" style="margin-bottom: 0;">
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
  </div>
@endsection

@section('scripts')
<script>
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
</script>
@endsection
