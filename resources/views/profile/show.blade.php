@extends('layouts.app')

@section('title', 'Profil Akun')
@section('page_title', 'Profil Akun')

@section('content')
  <div class="profile-container" style="max-width: 760px; margin: 0 auto; padding-bottom: 2rem;">
    <!-- Profile Card: Identitas Utama (Foto Profil, Nama, Jabatan, Tanggal Bergabung) -->
    <div class="profile-hero-card" style="margin-bottom: 1.25rem;">
      <div class="profile-hero-main">
        <div class="profile-hero-avatar-wrap">
          <div class="profile-hero-avatar" id="heroAvatarBox">
            @if($user->avatar_url)
              <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="user-avatar-img" id="heroAvatarImg">
            @else
              <span id="heroAvatarInitials">{{ $user->initials() }}</span>
            @endif
          </div>
          <label for="profile_avatar" class="avatar-edit-badge" title="Pilih Foto Profil Baru">
            <i data-lucide="camera"></i>
          </label>
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
              <span>Bergabung sejak {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</span>
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Card 1: Akses Edit Informasi Profil & Foto -->
    <div class="profile-section-card">
      <div class="profile-card-header">
        <h3>
          <i data-lucide="user-pen"></i>
          <span>Edit Informasi Profil</span>
        </h3>
      </div>
      <div class="profile-card-body">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
          @csrf
          @method('PUT')

          <div class="profile-form-grid">
            <!-- Edit Foto Profil -->
            <div class="form-group" style="border-bottom: 1px dashed var(--border-subtle); padding-bottom: 1rem; margin-bottom: 0.25rem;">
              <label for="profile_avatar">Ubah Foto Profil</label>
              <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <input type="file" id="profile_avatar" name="avatar" accept="image/png, image/jpeg, image/jpg, image/webp" onchange="previewAvatar(this)" style="font-size: 0.82rem; flex: 1; min-width: 200px;">
                @if($user->avatar_url)
                  <button type="button" class="btn btn-secondary btn-sm" onclick="confirmDeleteAvatar()" style="color: var(--danger); border-color: rgba(239, 68, 68, 0.35);" title="Hapus foto dan gunakan inisial">
                    <i data-lucide="trash-2"></i> Hapus Foto
                  </button>
                @endif
              </div>
              <span style="font-size: 0.72rem; color: var(--text-subtle); margin-top: 4px; display: block;">
                Format yang didukung: JPG, PNG, atau WEBP (Maksimal 3 MB).
              </span>
              @error('avatar')
                <span style="color: var(--danger); font-size: 0.76rem; margin-top: 3px; display: block;">{{ $message }}</span>
              @enderror
            </div>

            <!-- Nama & Email -->
            <div class="profile-form-row">
              <div class="form-group">
                <label for="profile_name">Nama Lengkap <span style="color: var(--danger);">*</span></label>
                <input type="text" id="profile_name" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')
                  <span style="color: var(--danger); font-size: 0.76rem; margin-top: 3px; display: block;">{{ $message }}</span>
                @enderror
              </div>

              <div class="form-group">
                <label for="profile_email">Alamat Email <span style="color: var(--danger);">*</span></label>
                <input type="email" id="profile_email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')
                  <span style="color: var(--danger); font-size: 0.76rem; margin-top: 3px; display: block;">{{ $message }}</span>
                @enderror
              </div>
            </div>

            <div class="form-group">
              <label for="profile_role">Jabatan / Peran</label>
              <input type="text" id="profile_role" value="{{ $user->roleLabel() }}" disabled style="background: #f1f5f9; color: #475569; cursor: not-allowed; font-weight: 600;">
              <span style="font-size: 0.72rem; color: var(--text-subtle); margin-top: 3px; display: block;">
                Jabatan akun ditentukan oleh Administrator sistem.
              </span>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 0.25rem;">
              <button type="submit" class="btn btn-primary btn-sm">
                <i data-lucide="check"></i> Simpan Profil
              </button>
            </div>
          </div>
        </form>

        @if($user->avatar_url)
          <form id="formDeleteAvatar" method="POST" action="{{ route('profile.avatar.destroy') }}" style="display: none;">
            @csrf
            @method('DELETE')
          </form>
        @endif
      </div>
    </div>

    <!-- Card 2: Pengaturan Sandi Akun -->
    <div class="profile-section-card" style="margin-bottom: 0;">
      <div class="profile-card-header">
        <h3>
          <i data-lucide="key-round"></i>
          <span>Ganti Kata Sandi</span>
        </h3>
      </div>
      <div class="profile-card-body">
        <form method="POST" action="{{ route('profile.password.update') }}">
          @csrf
          @method('PUT')

          <div class="profile-form-grid">
            <div class="form-group">
              <label for="current_password">Kata Sandi Saat Ini <span style="color: var(--danger);">*</span></label>
              <input type="password" id="current_password" name="current_password" placeholder="Masukkan kata sandi lama Anda" required>
              @error('current_password')
                <span style="color: var(--danger); font-size: 0.76rem; margin-top: 3px; display: block;">{{ $message }}</span>
              @enderror
            </div>

            <div class="profile-form-row">
              <div class="form-group">
                <label for="password">Kata Sandi Baru <span style="color: var(--danger);">*</span></label>
                <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" minlength="6" required>
                @error('password')
                  <span style="color: var(--danger); font-size: 0.76rem; margin-top: 3px; display: block;">{{ $message }}</span>
                @enderror
              </div>

              <div class="form-group">
                <label for="password_confirmation">Konfirmasi Kata Sandi Baru <span style="color: var(--danger);">*</span></label>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi kata sandi baru" minlength="6" required>
              </div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 0.25rem;">
              <button type="submit" class="btn btn-secondary btn-sm" style="font-weight: 600;">
                <i data-lucide="lock"></i> Perbarui Sandi
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
  function previewAvatar(input) {
    if (input.files && input.files[0]) {
      const file = input.files[0];
      if (file.size > 3 * 1024 * 1024) {
        window.qcNotify?.('Ukuran file maksimal 3 MB.', 'warning');
        input.value = '';
        return;
      }

      const reader = new FileReader();
      reader.onload = function(e) {
        const avatarBox = document.getElementById('heroAvatarBox');
        if (avatarBox) {
          avatarBox.innerHTML = `<img src="${e.target.result}" alt="Preview Foto" class="user-avatar-img" id="heroAvatarImg">`;
        }
      };
      reader.readAsDataURL(file);
    }
  }

  async function confirmDeleteAvatar() {
    const confirmed = await window.qcConfirm({
      title: 'Hapus Foto Profil',
      message: 'Apakah Anda yakin ingin menghapus foto profil dan kembali menggunakan inisial nama?',
      confirmText: 'Ya, Hapus',
      cancelText: 'Batal',
      variant: 'danger'
    });
    if (confirmed) {
      document.getElementById('formDeleteAvatar')?.submit();
    }
  }
</script>
@endsection
