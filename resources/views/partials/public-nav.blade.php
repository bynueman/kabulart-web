<!-- NAV -->
<nav class="kg-nav" role="navigation" aria-label="{{ app()->getLocale() === 'id' ? 'Navigasi Utama' : 'Main navigation' }}">
  <a href="/" class="kg-nav__brand">
    <picture>
      <source type="image/webp" srcset="{{ asset('img/logokabul.webp') }}">
      <img src="{{ asset('img/logokabul.png') }}" alt="Kabul Art Gallery logo" class="logo-icon" width="44" height="44" loading="eager" fetchpriority="high">
    </picture>
    <picture>
      <source type="image/webp" srcset="{{ asset('img/Desain_tanpa.webp') }}">
      <img src="{{ asset('img/Desain_tanpa.png') }}" alt="Kabul Art Gallery" class="logo-text" width="135" height="38" loading="eager" fetchpriority="high">
    </picture>
  </a>

  <div class="kg-lang-switch" role="group" aria-label="{{ app()->getLocale() === 'id' ? 'Pilih Bahasa' : 'Choose Language' }}">
    <a
      href="{{ route('locale.switch', 'id') }}"
      class="kg-lang-btn {{ app()->getLocale() === 'id' ? 'is-active' : '' }}"
      aria-label="Bahasa Indonesia"
      @if(app()->getLocale() === 'id') aria-current="true" @endif
    >ID</a>
    <span class="kg-lang-divider">|</span>
    <a
      href="{{ route('locale.switch', 'en') }}"
      class="kg-lang-btn {{ app()->getLocale() === 'en' ? 'is-active' : '' }}"
      aria-label="English"
      @if(app()->getLocale() === 'en') aria-current="true" @endif
    >EN</a>
  </div>

  <button class="kg-nav__hamburger" id="navToggle" aria-label="{{ app()->getLocale() === 'id' ? 'Buka menu navigasi' : 'Toggle navigation menu' }}" aria-expanded="false">
    <span></span><span></span><span></span>
  </button>

  <div class="kg-nav__links" id="navLinks">
    <a href="/" class="{{ ($activeNav ?? '') === 'home' ? 'kg-active' : '' }}" id="nav-home">{{ __('nav.home') }}</a>
    <a href="/profil" class="{{ ($activeNav ?? '') === 'profil' ? 'kg-active' : '' }}" id="nav-profil">{{ __('nav.profil') }}</a>
    <a href="/informasi" class="{{ ($activeNav ?? '') === 'informasi' ? 'kg-active' : '' }}" id="nav-informasi">{{ __('nav.informasi') }}</a>
    <a href="/gallery" class="{{ ($activeNav ?? '') === 'gallery' ? 'kg-active' : '' }}" id="nav-gallery">{{ __('nav.gallery') }}</a>
    <a href="/testimoni" class="{{ ($activeNav ?? '') === 'testimoni' ? 'kg-active' : '' }}" id="nav-testimoni">{{ __('nav.testimoni') }}</a>
  </div>
</nav>
