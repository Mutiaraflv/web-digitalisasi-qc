@extends('layouts.app')

@section('title', __('qc.settings.users_title'))
@section('page_title', __('qc.settings.users_page_title'))

@section('top_actions')
  <button type="button" class="btn btn-primary btn-sm" onclick="openAddModal()">
    <i data-lucide="user-plus"></i> {{ __('qc.settings.add_user_btn') }}
  </button>
@endsection

@section('content')
  <div class="page-header">
    <div class="page-title">
      <h1>{{ __('qc.settings.users_heading') }}</h1>
      <p>{{ __('qc.settings.users_subtitle') }}</p>
    </div>
  </div>

  <!-- KPI Stats Grid -->
  <div class="stats-grid">
    <div class="stat-card">
      <p>{{ __('qc.settings.kpi_total') }}</p>
      <h3>{{ $stats['total'] }}</h3>
      <div class="stat-sub">{{ __('qc.settings.kpi_total_sub') }}</div>
    </div>

    <div class="stat-card">
      <p>{{ __('qc.roles.admin') }}</p>
      <h3 style="color: var(--primary-text);">{{ $stats['admin'] }}</h3>
      <div class="stat-sub">{{ __('qc.settings.kpi_admin_sub') }}</div>
    </div>

    <div class="stat-card">
      <p>{{ __('qc.roles.qc_leader') }}</p>
      <h3 style="color: var(--warning);">{{ $stats['qc_leader'] }}</h3>
      <div class="stat-sub">{{ __('qc.settings.kpi_leader_sub') }}</div>
    </div>

    <div class="stat-card">
      <p>{{ __('qc.roles.user') }}</p>
      <h3>{{ $stats['user'] }}</h3>
      <div class="stat-sub">{{ __('qc.settings.kpi_user_sub') }}</div>
    </div>
  </div>

  <!-- Filter & Pencarian -->
  <div class="control-bar">
    <form method="GET" action="{{ route('settings.users.index') }}" style="display: flex; gap: 0.75rem; flex: 1; flex-wrap: wrap; align-items: center;">
      <div class="search-box">
        <i data-lucide="search" class="search-icon"></i>
        <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="{{ __('qc.settings.search_placeholder') }}">
      </div>

      <div class="filter-group">
        <select name="role" class="select-filter" onchange="this.form.submit()">
          <option value="">{{ __('qc.settings.all_roles') }}</option>
          <option value="admin" {{ ($roleFilter ?? '') === 'admin' ? 'selected' : '' }}>{{ __('qc.roles.admin') }}</option>
          <option value="qc_leader" {{ ($roleFilter ?? '') === 'qc_leader' ? 'selected' : '' }}>{{ __('qc.roles.qc_leader') }}</option>
          <option value="user" {{ ($roleFilter ?? '') === 'user' ? 'selected' : '' }}>{{ __('qc.roles.user') }}</option>
        </select>

        @if(!empty($search) || !empty($roleFilter))
          <a href="{{ route('settings.users.index') }}" class="btn btn-secondary btn-sm" title="{{ __('qc.common.reset') }}">
            <i data-lucide="x"></i> {{ __('qc.common.reset') }}
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
            <th style="width: 45px;">{{ __('qc.settings.col_no') }}</th>
            <th>{{ __('qc.settings.col_name') }}</th>
            <th>{{ __('qc.settings.col_email') }}</th>
            <th>{{ __('qc.settings.col_role') }}</th>
            <th>{{ __('qc.settings.col_status') }}</th>
            <th>{{ __('qc.settings.col_created') }}</th>
            <th style="text-align: right; width: 140px;">{{ __('qc.common.actions') }}</th>
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
                  <div class="user-avatar {{ $u->isAdmin() ? 'is-admin' : ($u->isQcLeader() ? 'is-leader' : 'is-inspector') }}" style="width: 30px; height: 30px; font-size: 0.72rem; flex-shrink: 0; overflow: hidden;">
                    @if($u->avatar_url)
                      <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="user-avatar-img">
                    @else
                      {{ $u->initials() }}
                    @endif
                  </div>
                  <div>
                    <strong style="color: var(--text-main); font-size: 0.88rem;">{{ $u->name }}</strong>
                    @if($u->id === auth()->id())
                      <span style="font-size: 0.7rem; color: var(--primary-text); font-weight: 700; margin-left: 4px;">{{ __('qc.common.you') }}</span>
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
                    <i data-lucide="shield-check" style="width: 12px; height: 12px;"></i> {{ __('qc.roles.admin') }}
                  </span>
                @elseif($u->isQcLeader())
                  <span class="badge badge-orange">
                    <i data-lucide="award" style="width: 12px; height: 12px;"></i> {{ __('qc.roles.qc_leader') }}
                  </span>
                @else
                  <span class="badge badge-purple">
                    <i data-lucide="user-check" style="width: 12px; height: 12px;"></i> {{ __('qc.roles.user') }}
                  </span>
                @endif
              </td>
              <td>
                @if($u->id === auth()->id())
                  <span class="badge badge-green">{{ __('qc.settings.status_active') }}</span>
                @else
                  <span class="badge badge-slate">{{ __('qc.settings.status_registered') }}</span>
                @endif
              </td>
              <td style="font-size: 0.78rem; color: var(--text-subtle);">
                {{ $u->created_at ? $u->created_at->format('d M Y, H:i') : '-' }}
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 0.35rem; align-items: center;">
                  <button type="button" 
                          class="btn btn-secondary btn-sm" 
                          style="padding: 0.3rem 0.55rem; font-size: 0.75rem;" 
                          title="{{ __('qc.settings.edit_user_tooltip') }}"
                          onclick="openEditModal({{ json_encode([
                            'id' => $u->id,
                            'name' => $u->name,
                            'email' => $u->email,
                            'role' => $u->role,
                            'is_self' => $u->id === auth()->id(),
                            'avatar_url' => $u->avatar_url,
                            'initials' => $u->initials(),
                          ]) }})">
                    <i data-lucide="edit-3"></i> {{ __('qc.common.edit') }}
                  </button>

                  @if($u->id === auth()->id())
                    <button type="button" 
                            class="btn btn-secondary btn-sm" 
                            style="padding: 0.3rem 0.55rem; font-size: 0.75rem; opacity: 0.45; cursor: not-allowed;" 
                            title="{{ __('qc.settings.self_delete_disabled') }}" 
                            disabled>
                      <i data-lucide="trash-2"></i>
                    </button>
                  @else
                    <button type="button" 
                            class="btn btn-danger btn-sm" 
                            style="padding: 0.3rem 0.55rem; font-size: 0.75rem;" 
                            title="{{ __('qc.settings.delete_user_tooltip') }}"
                            data-id="{{ $u->id }}"
                            data-name="{{ $u->name }}"
                            data-email="{{ $u->email }}"
                            onclick="handleDeleteUserClick(this)">
                      <i data-lucide="trash-2"></i>
                    </button>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align: center; padding: 2.5rem; color: var(--text-subtle);">
                <i data-lucide="users" style="width: 32px; height: 32px; margin: 0 auto 0.5rem; display: block; opacity: 0.4;"></i>
                {{ __('qc.settings.empty_users') }}
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($users->hasPages())
      <div style="padding: 0.85rem 1.15rem; border-top: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center;">
        <span style="font-size: 0.78rem; color: var(--text-subtle);">
          {{ $users->firstItem() }} - {{ $users->lastItem() }} / {{ $users->total() }}
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
        <h2><i data-lucide="user-plus" style="width: 18px; height: 18px; vertical-align: -2px; color: var(--primary-text);"></i> {{ __('qc.settings.add_modal_title') }}</h2>
        <button type="button" class="modal-close" onclick="closeAddModal()">&times;</button>
      </div>
      <form method="POST" action="{{ route('settings.users.store') }}">
        @csrf
        <div class="modal-body" style="display: flex; flex-direction: column; gap: 1rem;">
          <div class="form-group">
            <label for="add_name">{{ __('qc.settings.field_name') }} <span style="color: var(--danger);">*</span></label>
            <input type="text" id="add_name" name="name" value="{{ old('name') }}" required>
            @error('name') <span style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label for="add_email">{{ __('qc.settings.field_email') }} <span style="color: var(--danger);">*</span></label>
            <input type="email" id="add_email" name="email" value="{{ old('email') }}" required>
            @error('email') <span style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label for="add_role">{{ __('qc.settings.field_role') }} <span style="color: var(--danger);">*</span></label>
            <select id="add_role" name="role" required>
              <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>{{ __('qc.roles.user') }}</option>
              <option value="qc_leader" {{ old('role') === 'qc_leader' ? 'selected' : '' }}>{{ __('qc.roles.qc_leader') }}</option>
              <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>{{ __('qc.roles.admin') }}</option>
            </select>
            @error('role') <span style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label for="add_password">{{ __('qc.settings.field_password') }} <span style="color: var(--danger);">*</span></label>
            <input type="password" id="add_password" name="password" minlength="6" required>
            @error('password') <span style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span> @enderror
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" onclick="closeAddModal()">{{ __('qc.common.cancel') }}</button>
          <button type="submit" class="btn btn-primary btn-sm">
            <i data-lucide="check"></i> {{ __('qc.settings.save_new_user') }}
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL 2: Edit Akun -->
  <div class="modal-overlay" id="modalEditUser">
    <div class="modal-dialog" style="max-width: 520px;">
      <div class="modal-header">
        <h2><i data-lucide="user-cog" style="width: 18px; height: 18px; vertical-align: -2px; color: var(--primary-text);"></i> {{ __('qc.settings.edit_modal_title') }}</h2>
        <button type="button" class="modal-close" onclick="closeEditModal()">&times;</button>
      </div>
      <form id="formEditUser" method="POST" action="">
        @csrf
        @method('PUT')
        <div class="modal-body" style="display: flex; flex-direction: column; gap: 1rem;">
          <div class="edit-user-preview-box" style="display: flex; align-items: center; gap: 0.85rem; padding: 0.6rem 0.85rem; border: 1px solid var(--border-subtle); border-radius: 10px;">
            <div id="edit_avatar_preview" class="user-avatar" style="width: 38px; height: 38px; font-size: 0.85rem; flex-shrink: 0; overflow: hidden;"></div>
            <div style="min-width: 0; flex: 1;">
              <strong id="edit_name_preview" style="display: block; font-size: 0.88rem; color: var(--text-main); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"></strong>
              <span id="edit_email_preview" style="font-size: 0.74rem; color: var(--text-subtle); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block;"></span>
            </div>
          </div>

          <div class="form-group">
            <label for="edit_name">{{ __('qc.settings.field_name') }} <span style="color: var(--danger);">*</span></label>
            <input type="text" id="edit_name" name="name" required>
          </div>

          <div class="form-group">
            <label for="edit_email">{{ __('qc.settings.field_email') }} <span style="color: var(--danger);">*</span></label>
            <input type="email" id="edit_email" name="email" required>
          </div>

          <div class="form-group">
            <label for="edit_role">{{ __('qc.settings.field_role') }} <span style="color: var(--danger);">*</span></label>
            <select id="edit_role" name="role" required>
              <option value="user">{{ __('qc.roles.user') }}</option>
              <option value="qc_leader">{{ __('qc.roles.qc_leader') }}</option>
              <option value="admin">{{ __('qc.roles.admin') }}</option>
            </select>
            <span id="edit_role_note" style="font-size: 0.72rem; color: var(--text-subtle); margin-top: 2px;"></span>
          </div>

          <div class="form-group" style="border-top: 1px dashed var(--border-subtle); padding-top: 0.75rem;">
            <label for="edit_password">{{ __('qc.settings.field_new_password') }}</label>
            <input type="password" id="edit_password" name="password" placeholder="{{ __('qc.settings.password_hint') }}" minlength="6">
            <span style="font-size: 0.72rem; color: var(--text-subtle); margin-top: 2px;">
              {{ __('qc.settings.password_note') }}
            </span>
          </div>
        </div>

        <div class="modal-footer" style="display: flex; justify-content: space-between; align-items: center;">
          <div>
            <button type="button" id="btnEditModalDelete" class="btn btn-danger btn-sm" onclick="deleteUserFromModal()" style="display: none;">
              <i data-lucide="trash-2"></i> {{ __('qc.common.delete') }}
            </button>
          </div>
          <div style="display: flex; gap: 0.5rem; align-items: center;">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeEditModal()">{{ __('qc.common.cancel') }}</button>
            <button type="submit" class="btn btn-primary btn-sm">
              <i data-lucide="check"></i> {{ __('qc.common.save_changes') }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Global Delete User Form -->
  <form id="globalDeleteUserForm" method="POST" action="" style="display: none;">
    @csrf
    @method('DELETE')
  </form>
@endsection

@section('scripts')
<script>
  let currentEditUserData = null;

  function handleDeleteUserClick(btn) {
    if (!btn) return;
    const id = btn.getAttribute('data-id');
    const name = btn.getAttribute('data-name');
    const email = btn.getAttribute('data-email');
    confirmDeleteUser(id, name, email);
  }

  async function confirmDeleteUser(id, name, email) {
    const title = @json(__('qc.settings.delete_user_title')) || 'Hapus Akun Pengguna';
    const accountLabel = name ? (email && email !== name ? `${name} (${email})` : name) : (email || '');
    const template = @json(__('qc.settings.delete_user_confirm', ['name' => ':ACCOUNT:']));
    const message = template.replace(':ACCOUNT:', accountLabel);

    const confirmed = await window.qcConfirm({
      title: title,
      message: message,
      confirmText: @json(__('qc.common.yes_delete')),
      cancelText: @json(__('qc.common.cancel')),
      variant: 'danger',
      icon: 'trash-2'
    });

    if (confirmed) {
      const form = document.getElementById('globalDeleteUserForm');
      if (form) {
        const baseAction = @json(route('settings.users.destroy', ':ID:'));
        form.action = baseAction.replace(':ID:', id);
        form.submit();
      }
    }
  }

  function deleteUserFromModal() {
    if (!currentEditUserData || currentEditUserData.is_self) return;
    const { id, name, email } = currentEditUserData;
    closeEditModal();
    confirmDeleteUser(id, name, email);
  }
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

    currentEditUserData = userData;
    form.action = `/settings/users/${userData.id}`;
    document.getElementById('edit_name').value = userData.name;
    document.getElementById('edit_email').value = userData.email;
    document.getElementById('edit_role').value = userData.role;
    document.getElementById('edit_password').value = '';

    const btnDel = document.getElementById('btnEditModalDelete');
    if (btnDel) {
      btnDel.style.display = userData.is_self ? 'none' : 'inline-flex';
    }

    const avatarEl = document.getElementById('edit_avatar_preview');
    if (avatarEl) {
      if (userData.avatar_url) {
        avatarEl.innerHTML = `<img src="${userData.avatar_url}" alt="${userData.name}" class="user-avatar-img">`;
      } else {
        avatarEl.innerHTML = userData.initials || (userData.name ? userData.name.substring(0, 2).toUpperCase() : '-');
      }
    }
    const namePrev = document.getElementById('edit_name_preview');
    if (namePrev) namePrev.textContent = userData.name;
    const emailPrev = document.getElementById('edit_email_preview');
    if (emailPrev) emailPrev.textContent = userData.email;

    const roleNote = document.getElementById('edit_role_note');
    if (userData.is_self) {
      roleNote.textContent = @json(__('qc.settings.self_admin_note'));
      roleNote.style.color = 'var(--warning)';
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

