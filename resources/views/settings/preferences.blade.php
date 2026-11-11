@extends('layouts.app')

@section('title', __('qc.settings.pref_title'))
@section('page_title', __('qc.settings.pref_page_title'))

@section('content')
  <div class="page-header">
    <div class="page-title">
      <h1>{{ __('qc.settings.pref_heading') }}</h1>
      <p>{{ __('qc.settings.pref_subtitle') }}</p>
    </div>
  </div>

  <form method="POST" action="{{ route('settings.preferences.update') }}" id="preferencesForm">
    @csrf

    <div class="settings-pref-grid">
      <!-- 1. Theme Selector Card -->
      <div class="card">
        <div class="card-header">
          <h3 style="display: flex; align-items: center; gap: 0.5rem;">
            <i data-lucide="sun-moon" style="width: 18px; height: 18px; color: var(--primary-text);"></i>
            {{ __('qc.settings.theme_section_title') }}
          </h3>
        </div>
        <div class="card-body">
          <p class="settings-section-desc">{{ __('qc.settings.theme_section_desc') }}</p>

          <div class="pref-option-list">
            <label class="pref-option-card {{ ($currentTheme ?? 'light') === 'light' ? 'is-selected' : '' }}">
              <input type="radio" name="theme" value="light" {{ ($currentTheme ?? 'light') === 'light' ? 'checked' : '' }} onchange="previewThemeChoice('light')">
              <div class="pref-swatch light-swatch" aria-hidden="true">
                <span class="swatch-bar"></span>
                <span class="swatch-line"></span>
              </div>
              <div class="pref-option-info">
                <strong>{{ __('qc.settings.theme_light_title') }}</strong>
                <span>{{ __('qc.settings.theme_light_desc') }}</span>
              </div>
              <span class="pref-check"><i data-lucide="check-circle-2"></i></span>
            </label>

            <label class="pref-option-card {{ ($currentTheme ?? 'light') === 'dark' ? 'is-selected' : '' }}">
              <input type="radio" name="theme" value="dark" {{ ($currentTheme ?? 'light') === 'dark' ? 'checked' : '' }} onchange="previewThemeChoice('dark')">
              <div class="pref-swatch dark-swatch" aria-hidden="true">
                <span class="swatch-bar"></span>
                <span class="swatch-line"></span>
              </div>
              <div class="pref-option-info">
                <strong>{{ __('qc.settings.theme_dark_title') }}</strong>
                <span>{{ __('qc.settings.theme_dark_desc') }}</span>
              </div>
              <span class="pref-check"><i data-lucide="check-circle-2"></i></span>
            </label>
          </div>
        </div>
      </div>

      <!-- 2. Language Selector Card -->
      <div class="card">
        <div class="card-header">
          <h3 style="display: flex; align-items: center; gap: 0.5rem;">
            <i data-lucide="languages" style="width: 18px; height: 18px; color: var(--primary-text);"></i>
            {{ __('qc.settings.lang_section_title') }}
          </h3>
        </div>
        <div class="card-body">
          <p class="settings-section-desc">{{ __('qc.settings.lang_section_desc') }}</p>

          <div class="pref-option-list">
            <label class="pref-option-card {{ ($currentLocale ?? 'id') === 'id' ? 'is-selected' : '' }}">
              <input type="radio" name="locale" value="id" {{ ($currentLocale ?? 'id') === 'id' ? 'checked' : '' }} onchange="highlightPrefGroup(this)">
              <div class="pref-lang-badge">ID</div>
              <div class="pref-option-info">
                <strong>{{ __('qc.settings.lang_id_title') }}</strong>
                <span>{{ __('qc.settings.lang_id_desc') }}</span>
              </div>
              <span class="pref-check"><i data-lucide="check-circle-2"></i></span>
            </label>

            <label class="pref-option-card {{ ($currentLocale ?? 'id') === 'en' ? 'is-selected' : '' }}">
              <input type="radio" name="locale" value="en" {{ ($currentLocale ?? 'id') === 'en' ? 'checked' : '' }} onchange="highlightPrefGroup(this)">
              <div class="pref-lang-badge">EN</div>
              <div class="pref-option-info">
                <strong>{{ __('qc.settings.lang_en_title') }}</strong>
                <span>{{ __('qc.settings.lang_en_desc') }}</span>
              </div>
              <span class="pref-check"><i data-lucide="check-circle-2"></i></span>
            </label>
          </div>
        </div>
      </div>
    </div>

    <div class="settings-pref-footer">
      <button type="submit" class="btn btn-primary">
        <i data-lucide="check"></i> {{ __('qc.settings.save_preferences') }}
      </button>
    </div>
  </form>
@endsection

@section('scripts')
<script>
  function highlightPrefGroup(radioEl) {
    const list = radioEl.closest('.pref-option-list');
    if (!list) return;
    list.querySelectorAll('.pref-option-card').forEach(card => card.classList.remove('is-selected'));
    radioEl.closest('.pref-option-card')?.classList.add('is-selected');
  }

  function previewThemeChoice(themeVal) {
    const radioEl = document.querySelector(`input[name="theme"][value="${themeVal}"]`);
    if (radioEl) highlightPrefGroup(radioEl);
    document.documentElement.setAttribute('data-theme', themeVal);
    try {
      localStorage.setItem('qc_theme', themeVal);
    } catch (e) {}
  }
</script>
@endsection
