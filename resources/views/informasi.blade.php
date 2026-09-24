<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Informasi &mdash; Kabul Art Gallery</title>
  <meta name="description" content="Kegiatan dan informasi terkini dari Kabul Art Gallery, Yogyakarta.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Jost:wght@400;500;600&display=swap">
  <link rel="stylesheet" href="css/gallery-redesign.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" media="print" onload="this.media='all'">
  <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"></noscript>
</head>
<body>

  <!-- NAV -->
  <nav class="kg-nav" role="navigation" aria-label="Main navigation">
    <a href="/" class="kg-nav__brand">
      <picture>
        <source type="image/webp" srcset="img/logokabul.webp">
        <img src="img/logokabul.png" alt="Kabul Art Gallery logo" class="logo-icon" width="44" height="44" loading="eager" fetchpriority="high">
      </picture>
      <picture>
        <source type="image/webp" srcset="img/Desain_tanpa.webp">
        <img src="img/Desain_tanpa.png" alt="Kabul Art Gallery" class="logo-text" width="135" height="38" loading="eager" fetchpriority="high">
      </picture>
    </a>
    <button class="kg-nav__hamburger" id="navToggle" aria-label="Toggle menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
    <div class="kg-nav__links" id="navLinks">
      <a href="/" id="nav-home">Home</a>
      <a href="/profil" id="nav-profil">Profil</a>
      <a href="/informasi" class="kg-active" id="nav-informasi">Informasi</a>
      <a href="/gallery" id="nav-gallery">Gallery</a>
      <a href="/testimoni" id="nav-testimoni">Testimoni</a>
    </div>
  </nav>

  <main class="kg-page">

    <!-- PAGE HERO -->
    <header class="kg-page-hero">
      <span class="kg-page-hero__eyebrow">Kabar Terkini</span>
      <h1 class="kg-page-hero__title">Kegiatan</h1>
    </header>

    <!-- INFO LIST -->
    <div class="kg-container">
      <div class="kg-info-list">
        @forelse ($posts as $post)
        <article class="kg-info-card">
          <div class="kg-info-card__img">
            <x-picture :image="$post->image" alt="informasi" class="rounded" preset="info" :eager="false" />
          </div>
          <div class="kg-info-card__body">
            <span class="kg-info-card__label">Deskripsi</span>
            <p class="kg-info-card__desc">{{ $post->deskripsi }}</p>
          </div>
        </article>
        @empty
        <p class="kg-empty">Data Post belum Tersedia.</p>
        @endforelse
      </div>
    </div>

  </main>

  @include('partials.public-footer')

  <script>
    var toggle = document.getElementById('navToggle');
    var links  = document.getElementById('navLinks');
    toggle.addEventListener('click', function() {
      var open = links.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', open);
    });
  </script>
</body>
</html>
