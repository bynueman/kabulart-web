<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ app()->getLocale() === 'id' ? 'Informasi — Kabul Art Gallery' : 'Information — Kabul Art Gallery' }}</title>
  <meta name="description" content="{{ app()->getLocale() === 'id' ? 'Kegiatan dan informasi terkini dari Kabul Art Gallery, Yogyakarta.' : 'Latest activities and news from Kabul Art Gallery, Yogyakarta.' }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Jost:wght@400;500;600&display=swap">
  <link rel="stylesheet" href="{{ asset('css/gallery-redesign.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" media="print" onload="this.media='all'">
  <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"></noscript>
</head>
<body>

  <!-- NAV -->
  @include('partials.public-nav', ['activeNav' => 'informasi'])

  <main class="kg-page">

    <!-- PAGE HERO -->
    <header class="kg-page-hero">
      <span class="kg-page-hero__eyebrow">{{ __('info.eyebrow') }}</span>
      <h1 class="kg-page-hero__title">{{ __('info.title') }}</h1>
    </header>

    <!-- INFO LIST -->
    <div class="kg-container">
      <div class="kg-info-list">
        @forelse ($posts as $post)
        <article class="kg-info-card">
          <div class="kg-info-card__img">
            <x-picture :image="$post->image" alt="{{ __('admin.informasi') }}" class="rounded" preset="info" :eager="false" />
          </div>
          <div class="kg-info-card__body">
            <span class="kg-info-card__label">{{ __('info.desc_label') }}</span>
            <p class="kg-info-card__desc">{{ $post->deskripsi }}</p>
          </div>
        </article>
        @empty
        <p class="kg-empty">{{ __('info.empty') }}</p>
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
