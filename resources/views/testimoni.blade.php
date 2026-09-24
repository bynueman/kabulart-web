<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ app()->getLocale() === 'id' ? 'Testimoni Pelanggan — Kabul Art Gallery' : 'Testimonials — Kabul Art Gallery' }}</title>
  <meta name="description" content="{{ app()->getLocale() === 'id' ? 'Lihat testimoni dan kesan dari pelanggan Kabul Art Gallery Yogyakarta.' : 'See testimonials and impressions from customers of Kabul Art Gallery Yogyakarta.' }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Jost:wght@400;500;600&display=swap">
  <link rel="stylesheet" href="{{ asset('css/gallery-redesign.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" media="print" onload="this.media='all'">
  <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"></noscript>
</head>
<body>

  <!-- NAV -->
  @include('partials.public-nav', ['activeNav' => 'testimoni'])

  <main class="kg-page">

    <!-- PAGE HERO -->
    <header class="kg-page-hero">
      <span class="kg-page-hero__eyebrow">{{ __('testimoni.eyebrow') }}</span>
      <h1 class="kg-page-hero__title">{{ __('testimoni.title') }}</h1>
    </header>

    <!-- TESTIMONI GRID -->
    <div class="kg-container">
      <div class="kg-testimoni-grid">
        @forelse ($posts as $post)
        <div class="kg-testimoni-item">
          <x-picture :image="$post->image" alt="{{ __('testimoni.photo_alt') }}" class="rounded" preset="testimoni" :eager="false" />
        </div>
        @empty
        <p class="kg-empty">{{ __('testimoni.empty') }}</p>
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
