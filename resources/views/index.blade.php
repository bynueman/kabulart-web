<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ app()->getLocale() === 'id' ? 'Kabul Art Gallery — Beranda' : 'Kabul Art Gallery — Home' }}</title>
  <meta name="description" content="{{ app()->getLocale() === 'id' ? 'Kabul Art Gallery menawarkan lukisan batik autentik dari Yogyakarta. Jelajahi koleksi lukisan batik klasik, modern, dan tradisional.' : 'Kabul Art Gallery offers authentic batik paintings from Yogyakarta. Explore our curated collection of classic, modern, and traditional batik art.' }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Jost:wght@400;500;600&display=swap">
  <link rel="stylesheet" href="{{ asset('css/gallery-redesign.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" media="print" onload="this.media='all'">
  <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"></noscript>
</head>
<body>

  <!-- NAV -->
  @include('partials.public-nav', ['activeNav' => 'home'])

  <main class="kg-page">

    <!-- HERO / BRAND HEADER -->
    <section class="kg-home-hero">
      <div class="kg-home-hero__inner">
        <div class="kg-home-hero__logos">
          <a href="/">
            <picture>
              <source type="image/webp" srcset="{{ asset('img/logokabul.webp') }}">
              <img src="{{ asset('img/logokabul.png') }}" alt="Kabul logo" class="logo-icon" width="44" height="44" loading="eager">
            </picture>
          </a>
          <a href="/">
            <picture>
              <source type="image/webp" srcset="{{ asset('img/Desain_tanpa.webp') }}">
              <img src="{{ asset('img/Desain_tanpa.png') }}" alt="Kabul Art Gallery" class="logo-text" width="135" height="38" loading="eager">
            </picture>
          </a>
        </div>
      </div>
    </section>

    <!-- ABOUT / HISTORY -->
    <section class="kg-about">
      <div class="kg-container">
        <div class="kg-about__inner">
          <div class="kg-about__photo">
            <picture>
              <source type="image/webp" srcset="{{ asset('img/kbl.webp') }}">
              <img src="{{ asset('img/kbl.jpg') }}" alt="photo kabulgallery" width="600" height="450" loading="lazy" decoding="async">
            </picture>
          </div>
          <div class="kg-about__body">
            <h2>{{ __('home.history_title') }}</h2>
            <p>{{ __('home.history_desc') }}</p>
          </div>
        </div>
      </div>
    </section>

    <hr class="kg-divider">

    <!-- INFORMASI PREVIEW -->
    <section class="kg-container">
      <span class="kg-section-label">{{ __('home.activities_label') }}</span>
      <h2 class="kg-section-title">{{ __('home.activities_title') }}</h2>
      <div class="kg-info-list">
        @forelse ($posts1 as $post)
        <article class="kg-info-card">
          <div class="kg-info-card__img">
            <x-picture :image="$post->image" alt="{{ __('admin.informasi') }}" preset="info" :eager="false" />
          </div>
          <div class="kg-info-card__body">
            <span class="kg-info-card__label">{{ __('info.desc_label') }}</span>
            <p class="kg-info-card__desc">{{ $post->deskripsi }}</p>
          </div>
        </article>
        @empty
        <p class="kg-empty">{{ __('home.empty_info') }}</p>
        @endforelse
      </div>
      <div class="kg-btn-wrap">
        <a href="/informasi" class="kg-btn-more">{{ __('home.view_more') }}</a>
      </div>
    </section>

    <hr class="kg-divider">

    <!-- GALLERY PREVIEW -->
    <section class="kg-container">
      <span class="kg-section-label">{{ __('home.collection_label') }}</span>
      <h2 class="kg-section-title">{{ __('home.collection_title') }}</h2>
      <div class="kg-artwork-grid">
        @forelse ($posts2 as $post)
        <article class="kg-artwork-card">
          <div class="kg-artwork-card__img-wrap">
            <x-picture :image="$post->image" :alt="$post->nama" preset="gallery" :eager="false" />
          </div>
          <div class="kg-artwork-card__body">
            <p class="kg-artwork-card__name">{{ $post->nama }}</p>
            <p class="kg-artwork-card__dim">{{ $post->dimensi }}</p>
            <a href="{{ $post->order_link }}" class="kg-btn-order" target="_blank" rel="noopener noreferrer">{{ __('home.order_now') }}</a>
          </div>
        </article>
        @empty
        <p class="kg-empty">{{ __('home.empty_gallery') }}</p>
        @endforelse
      </div>
      <div class="kg-btn-wrap">
        <a href="/gallery" class="kg-btn-more">{{ __('home.view_more') }}</a>
      </div>
    </section>

  </main>

  @include('partials.public-footer')

  <script>
    var toggle = document.getElementById('navToggle');
    var links  = document.getElementById('navLinks');
    if (toggle && links) {
      toggle.addEventListener('click', function() {
        var open = links.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', open);
      });
    }
  </script>
</body>
</html>
