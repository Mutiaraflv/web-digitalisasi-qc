@extends('layouts.app')

@section('title', 'Kelola Akun Pengguna')
@section('page_title', 'Pengaturan / Kelola Akun')

@section('top_actions')
  <button type="button" class="btn btn-primary btn-sm" onclick="openAddModal()">
    <i data-lucide="user-plus"></i> Tambah Akun Baru
  </button>
@endsection

@section('content')
  <div class="page-header">
    <div class="page-title">
      <h1>Pengaturan Akun Pengguna</h1>
      <p>Kelola hak akses personil workshop, kredensial inspektor mutu, dan izin tingkat administrator.</p>
    </div>
  </div>

  <!-- KPI Stats Grid -->
  <div class="stats-grid">
    <div class="stat-card">
      <p>Total Akun Terdaftar</p>
      <h3>{{ $stats['total'] }}</h3>
      <div class="stat-sub">Seluruh personil sistem</div>
    </div>

    <div class="stat-card">
      <p>Administrator</p>
      <h3 style="color: var(--primary);">{{ $stats['admin'] }}</h3>
      <div class="stat-sub">Pengelola sistem & akun</div>
    </div>

    <div class="stat-card">
      <p>QC Leader</p>
      <h3 style="color: #d97706;">{{ $stats['qc_leader'] }}</h3>
      <div class="stat-sub">Peninjau & approval QC</div>
    </div>

    <div class="stat-card">
      <p>Inspector QC</p>
      <h3>{{ $stats['user'] }}</h3>
      <div class="stat-sub">Pencatatan & pengajuan QC</div>
    </div>
  </div>

  <!-- Filter & Pencarian -->
  <div class="control-bar">
    <form method="GET" action="{{ route('settings.users.index') }}" style="display: flex; gap: 0.75rem; flex: 1; flex-wrap: wrap; align-items: center;">
      <div class="search-box">
        <i data-lucide="search" class="search-icon"></i>
        <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Cari nama atau email pengguna...">
      </div>

      <div class="filter-group">
        <select name="role" class="select-filter" onchange="this.form.submit()">
          <option value="">Semua Role</option>
          <option value="admin" {{ ($roleFilter ?? '') === 'admin' ? 'selected' : '' }}>Administrator</option>
          <option value="qc_leader" {{ ($roleFilter ?? '') === 'qc_leader' ? 'selected' : '' }}>QC Leader</option>
          <option value="user" {{ ($roleFilter ?? '') === 'user' ? 'selected' : '' }}>Inspector QC</option>
        </select>

        @if(!empty($search) || !empty($roleFilter))
          <a href="{{ route('settings.users.index') }}" class="btn btn-secondary btn-sm" title="Reset Filter">
            <i data-lucide="x"></i> Reset
          </a>
        @endif
      </div>
    </form>
  </div>

  <!-- Tabel Data Akun -->
  <div class="table-card">
    <div class="table-responsive">
      <table class="qc-table">
        <thead>
          <tr>
            <th style="width: 45px;">No.</th>
            <th>Nama Personil</th>
            <th>Alamat Email</th>
            <th>Role</th>
            <th>Status Akun</th>
            <th>Tanggal Registrasi</th>
            <th style="text-align: right; width: 140px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $index => $u)
            <tr>
              <td class="mono" style="color: var(--text-subtle); font-size: 0.78rem;">
                {{ $users->firstItem() + $index }}
              </td>
              <td>
                <div style="display: flex; align-items: center; gap: 0.65rem;">
                  <div class="user-avatar" style="width: 28px; height: 28px; font-size: 0.72rem; flex-shrink: 0; background: {{ $u->isAdmin() ? 'var(--primary-light)' : ($u->isQcLeader() ? '#fff7ed' : 'var(--graphite-light)') }}; color: {{ $u->isAdmin() ? 'var(--primary)' : ($u->isQcLeader() ? '#d97706' : 'var(--graphite)') }}; border-color: {{ $u->isAdmin() ? 'var(--primary-border)' : ($u->isQcLeader() ? '#fed7aa' : 'var(--border-strong)') }};">
                    {{ strtoupper(substr($u->name, 0, 2)) }}
                  </div>
                  <div>
                    <strong style="color: var(--text-main); font-size: 0.88rem;">{{ $u->name }}</strong>
                    @if($u->id === auth()->id())
                      <span style="font-size: 0.7rem; color: var(--primary); font-weight: 700; margin-left: 4px;">(Anda)</span>
                    @endif
                  </div>
                </div>
              </td>
              <td class="mono" style="font-size: 0.82rem; color: var(--text-muted);">
                {{ $u->email }}
              </td>
              <td>
                @if($u->isAdmin())
                  <span class="badge badge-blue">
                    <i data-lucide="shield-check" style="width: 12px; height: 12px;"></i> Administrator
                  </span>
                @elseif($u->isQcLeader())
                  <span class="badge badge-orange">
                    <i data-lucide="award" style="width: 12px; height: 12px;"></i> QC Leader
                  </span>
                @else
                  <span class="badge badge-purple">
                    <i data-lucide="user-check" style="width: 12px; height: 12px;"></i> Inspector QC
                  </span>
                @endif
              </td>
              <td>
                @if($u->id === auth()->id())
                  <span class="badge badge-green">Sedang Aktif</span>
                @else
                  <span class="badge" style="background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;">Terdaftar</span>
                @endif
              </td>
              <td style="font-size: 0.78rem; color: var(--text-subtle);">
                {{ $u->created_at ? $u->created_at->format('d M Y, H:i') : '-' }}
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 0.35rem; align-items: center;">
                  <!-- Tombol Edit -->
                  <button type="button" 
                          class="btn btn-secondary btn-sm" 
                          style="padding: 0.3rem 0.55rem; font-size: 0.75rem;" 
                          title="Ubah Data Akun"
                          onclick="openEditModal({{ json_encode([
                            'id' => $u->id,
                            'name' => $u->name,
                            'email' => $u->email,
                            'role' => $u->role,
                            'is_self' => $u->id === auth()->id()
                          ]) }})">
                    <i data-lucide="edit-3"></i> Edit
                  </button>

                  <!-- Tombol Hapus -->
                  @if($u->id === auth()->id())
                    <button type="button" 
                            class="btn btn-secondary btn-sm" 
                            style="padding: 0.3rem 0.55rem; font-size: 0.75rem; opacity: 0.45; cursor: not-allowed;" 
                            title="Tidak dapat menghapus akun Anda sendiri" 
                            disabled>
                      <i data-lucide="trash-2"></i>
                    </button>
                  @else
                    <form method="POST" action="{{ route('settings.users.destroy', $u) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun \'{{ $u->name }}\'? Tindakan ini tidak dapat dibatalkan.');" style="display: inline;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" 
                              class="btn btn-danger btn-sm" 
                              style="padding: 0.3rem 0.55rem; font-size: 0.75rem;" 
                              title="Hapus Akun">
                        <i data-lucide="trash-2"></i>
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align: center; padding: 2.5rem; color: var(--text-subtle);">
                <i data-lucide="users" style="width: 32px; height: 32px; margin: 0 auto 0.5rem; display: block; opacity: 0.4;"></i>
                Tidak ada akun pengguna yang sesuai dengan kriteria pencarian.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($users->hasPages())
      <div style="padding: 0.85rem 1.15rem; border-top: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center;">
        <span style="font-size: 0.78rem; color: var(--text-subtle);">
          Menampilkan {{ $users->firstItem() }} - {{ $users->lastItem() }} dari total {{ $users->total() }} akun
        </span>
        <div>
          {{ $users->links() }}
        </div>
      </div>
    @endif
  </div>

  <!-- MODAL 1: Tambah Akun Baru -->
  <div class="modal-overlay" id="modalAddUser">
    <div class="modal-dialog" style="max-width: 520px;">
      <div class="modal-header">
        <h2><i data-lucide="user-plus" style="width: 18px; height: 18px; vertical-align: -2px; color: var(--primary);"></i> Tambah Akun Personil Baru</h2>
        <button type="button" class="modal-close" onclick="closeAddModal()">&times;</button>
      </div>
      <form method="POST" action="{{ route('settings.users.store') }}">
        @csrf
        <div class="modal-body" style="display: flex; flex-direction: column; gap: 1rem;">
          <div class="form-group">
            <label for="add_name">Nama Lengkap Personil <span style="color: var(--danger);">*</span></label>
            <input type="text" id="add_name" name="name" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" required>
            @error('name') <span style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label for="add_email">Alamat Email <span style="color: var(--danger);">*</span></label>
            <input type="email" id="add_email" name="email" value="{{ old('email') }}" placeholder="Contoh: budi@sscworks.com" required>
            @error('email') <span style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label for="add_role">Role <span style="color: var(--danger);">*</span></label>
            <select id="add_role" name="role" required>
              <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>Inspector QC</option>
              <option value="qc_leader" {{ old('role') === 'qc_leader' ? 'selected' : '' }}>QC Leader</option>
              <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
            </select>
            @error('role') <span style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label for="add_password">Kata Sandi Awal <span style="color: var(--danger);">*</span></label>
            <input type="password" id="add_password" name="password" placeholder="Minimal 6 karakter" minlength="6" required>
            @error('password') <span style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span> @enderror
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" onclick="closeAddModal()">Batal</button>
          <button type="submit" class="btn btn-primary btn-sm">
            <i data-lucide="check"></i> Simpan Akun Baru
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL 2: Edit Akun -->
  <div class="modal-overlay" id="modalEditUser">
    <div class="modal-dialog" style="max-width: 520px;">
      <div class="modal-header">
        <h2><i data-lucide="user-cog" style="width: 18px; height: 18px; vertical-align: -2px; color: var(--primary);"></i> Ubah Data Akun Pengguna</h2>
        <button type="button" class="modal-close" onclick="closeEditModal()">&times;</button>
      </div>
      <form id="formEditUser" method="POST" action="">
        @csrf
        @method('PUT')
        <div class="modal-body" style="display: flex; flex-direction: column; gap: 1rem;">
          <div class="form-group">
            <label for="edit_name">Nama Lengkap Personil <span style="color: var(--danger);">*</span></label>
            <input type="text" id="edit_name" name="name" required>
          </div>

          <div class="form-group">
            <label for="edit_email">Alamat Email <span style="color: var(--danger);">*</span></label>
            <input type="email" id="edit_email" name="email" required>
          </div>

          <div class="form-group">
            <label for="edit_role">Role <span style="color: var(--danger);">*</span></label>
            <select id="edit_role" name="role" required>
              <option value="user">Inspector QC</option>
              <option value="qc_leader">QC Leader</option>
              <option value="admin">Administrator</option>
            </select>
            <span id="edit_role_note" style="font-size: 0.72rem; color: var(--text-subtle); margin-top: 2px;"></span>
          </div>

          <div class="form-group" style="border-top: 1px dashed var(--border-subtle); padding-top: 0.75rem;">
            <label for="edit_password">Kata Sandi Baru (Opsional)</label>
            <input type="password" id="edit_password" name="password" placeholder="Kosongkan jika tidak ingin mengubah kata sandi" minlength="6">
            <span style="font-size: 0.72rem; color: var(--text-subtle); margin-top: 2px;">
              Isi hanya jika Anda ingin mereset atau mengganti kata sandi pengguna ini.
            </span>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" onclick="closeEditModal()">Batal</button>
          <button type="submit" class="btn btn-primary btn-sm">
            <i data-lucide="check"></i> Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection

@section('scripts')
<script>
  function openAddModal() {
    const modal = document.getElementById('modalAddUser');
    if (modal) {
      modal.classList.add('active');
      document.getElementById('add_name').focus();
    }
  }

  function closeAddModal() {
    const modal = document.getElementById('modalAddUser');
    if (modal) modal.classList.remove('active');
  }

  function openEditModal(userData) {
    const modal = document.getElementById('modalEditUser');
    const form = document.getElementById('formEditUser');
    if (!modal || !form) return;

    form.action = `/settings/users/${userData.id}`;
    document.getElementById('edit_name').value = userData.name;
    document.getElementById('edit_email').value = userData.email;
    document.getElementById('edit_role').value = userData.role;
    document.getElementById('edit_password').value = '';

    const roleNote = document.getElementById('edit_role_note');
    const roleSelect = document.getElementById('edit_role');

    if (userData.is_self) {
      roleNote.textContent = 'Perhatian: Anda sedang mengedit akun Anda sendiri. Role harus tetap Administrator.';
      roleNote.style.color = '#d97706';
    } else {
      roleNote.textContent = '';
    }

    modal.classList.add('active');
    document.getElementById('edit_name').focus();
    if (window.lucide) lucide.createIcons();
  }

  function closeEditModal() {
    const modal = document.getElementById('modalEditUser');
    if (modal) modal.classList.remove('active');
  }

  // Close modals when clicking outside
  document.addEventListener('click', function(e) {
    const addModal = document.getElementById('modalAddUser');
    const editModal = document.getElementById('modalEditUser');
    if (e.target === addModal) closeAddModal();
    if (e.target === editModal) closeEditModal();
  });

  // ESC key to close modals
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeAddModal();
      closeEditModal();
    }
  });

  // Re-open Add Modal if there are validation errors
  @if($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
      openAddModal();
    });
  @endif
</script>
@endsection

