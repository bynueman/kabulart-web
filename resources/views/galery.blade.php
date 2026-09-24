<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ app()->getLocale() === 'id' ? 'Galeri Seni — Kabul Art Gallery' : 'Gallery — Kabul Art Gallery' }}</title>
  <meta name="description" content="{{ app()->getLocale() === 'id' ? 'Jelajahi koleksi lengkap lukisan batik karya WH. Kabul — gaya klasik, modern, dan tradisional siap dipesan.' : 'Browse the full collection of batik paintings by WH. Kabul — classic, modern, and traditional styles available for purchase.' }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Jost:wght@400;500;600&display=swap">
  <link rel="stylesheet" href="{{ asset('css/gallery-redesign.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" media="print" onload="this.media='all'">
  <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"></noscript>
</head>
<body>

  <!-- NAV -->
  @include('partials.public-nav', ['activeNav' => 'gallery'])

  <main class="kg-page">

    <!-- PAGE HERO -->
    <header class="kg-page-hero">
      <span class="kg-page-hero__eyebrow">{{ __('gallery.eyebrow') }}</span>
      <h1 class="kg-page-hero__title">{{ __('gallery.title') }}</h1>
    </header>

    <!-- ARTWORK GRID -->
    <div class="kg-container">
      <div class="kg-artwork-grid">
        @forelse ($posts as $post)
        <article class="kg-artwork-card">
          <div class="kg-artwork-card__img-wrap">
            <x-picture :image="$post->image" :alt="$post->nama" class="rounded" preset="gallery" :eager="false" />
          </div>
          <div class="kg-artwork-card__body">
            <p class="kg-artwork-card__name">{{ $post->nama }}</p>
            <p class="kg-artwork-card__dim">{{ $post->dimensi }}</p>
            <a href="{{ $post->order_link }}" class="kg-btn-order" target="_blank" rel="noopener noreferrer">{{ __('gallery.order_btn') }}</a>
          </div>
        </article>
        @empty
        <p class="kg-empty">{{ __('gallery.empty') }}</p>
        @endforelse
      </div>
    </div>

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
