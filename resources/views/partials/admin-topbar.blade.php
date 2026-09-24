<div class="adm-topbar">
  <button class="adm-hamburger" id="admHamburger" aria-label="Toggle sidebar">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
      <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
    </svg>
  </button>
  <div>
    <div class="adm-topbar__title">{{ $title ?? __('admin.dashboard') }}</div>
  </div>
  <div class="adm-topbar__actions">
    {{-- ADMIN INTERFACE LANGUAGE SWITCHER --}}
    <div class="adm-lang-switch" role="group" aria-label="{{ app()->getLocale() === 'id' ? 'Bahasa Panel Admin' : 'Admin Panel Language' }}">
      <a
        href="{{ route('locale.switch', 'id') }}"
        class="adm-lang-btn {{ app()->getLocale() === 'id' ? 'is-active' : '' }}"
        aria-label="Bahasa Indonesia"
      >ID</a>
      <span class="adm-lang-divider">|</span>
      <a
        href="{{ route('locale.switch', 'en') }}"
        class="adm-lang-btn {{ app()->getLocale() === 'en' ? 'is-active' : '' }}"
        aria-label="English"
      >EN</a>
    </div>

    @if(!empty($backUrl))
      <a href="{{ $backUrl }}" class="adm-btn adm-btn--ghost adm-btn--sm">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        {{ __('admin.back') }}
      </a>
    @endif

    <a href="/" target="_blank" class="adm-btn adm-btn--ghost adm-btn--sm">{{ __('admin.view_website') }}</a>
    <a href="/logout" class="adm-btn adm-btn--sm" style="background:rgba(155,35,53,0.1);color:#9b2335;border:1px solid rgba(155,35,53,0.2);">{{ __('admin.logout') }}</a>
  </div>
</div>
