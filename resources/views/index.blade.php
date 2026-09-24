<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kabul Art Gallery — Home</title>
  <meta name="description" content="Kabul Art Gallery offers authentic batik paintings from Yogyakarta. Explore our curated collection of classic, modern, and traditional batik art.">
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
      <a href="/" class="kg-active" id="nav-home">Home</a>
      <a href="/profil" id="nav-profil">Profil</a>
      <a href="/informasi" id="nav-informasi">Informasi</a>
      <a href="/gallery" id="nav-gallery">Gallery</a>
      <a href="/testimoni" id="nav-testimoni">Testimoni</a>
    </div>
  </nav>

  <main class="kg-page">

    <!-- HERO / BRAND HEADER -->
    <section class="kg-home-hero">
      <div class="kg-home-hero__inner">
        <div class="kg-home-hero__logos">
          <a href="/">
            <picture>
              <source type="image/webp" srcset="img/logokabul.webp">
              <img src="img/logokabul.png" alt="Kabul logo" class="logo-icon" width="44" height="44" loading="eager">
            </picture>
          </a>
          <a href="/">
            <picture>
              <source type="image/webp" srcset="img/Desain_tanpa.webp">
              <img src="img/Desain_tanpa.png" alt="Kabul Art Gallery" class="logo-text" width="135" height="38" loading="eager">
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
              <source type="image/webp" srcset="img/kbl.webp">
              <img src="img/kbl.jpg" alt="photo kabulgallery" width="600" height="450" loading="lazy" decoding="async">
            </picture>
          </div>
          <div class="kg-about__body">
            <h2>History Kabul Gallery</h2>
            <p>Kabul Art Gallery is a company that offers paintings of batik in Yogyakarta. The majority of its customers are foreigners from a number of different countries including Germany.
              Foreign tourists would most likely prefer to buy batik in the form of paintings.</p>
          </div>
        </div>
      </div>
    </section>

    <hr class="kg-divider">

    <!-- INFORMASI PREVIEW -->
    <section class="kg-container">
      <span class="kg-section-label">Kegiatan</span>
      <h2 class="kg-section-title">Informasi Terkini</h2>
      <div class="kg-info-list">
        @forelse ($posts1 as $post)
        <article class="kg-info-card">
          <div class="kg-info-card__img">
            <x-picture :image="$post->image" alt="informasi" preset="info" :eager="false" />
          </div>
          <div class="kg-info-card__body">
            <span class="kg-info-card__label">Deskripsi</span>
            <p class="kg-info-card__desc">{{ $post->deskripsi }}</p>
          </div>
        </article>
        @empty
        <p class="kg-empty">Belum ada informasi tersedia.</p>
        @endforelse
      </div>
      <div class="kg-btn-wrap">
        <a href="/informasi" class="kg-btn-more">SELENGKAPNYA &rarr;</a>
      </div>
    </section>

    <hr class="kg-divider">

    <!-- GALLERY PREVIEW -->
    <section class="kg-container">
      <span class="kg-section-label">Koleksi</span>
      <h2 class="kg-section-title">Karya Pilihan</h2>
      <div class="kg-artwork-grid">
        @forelse ($posts2 as $post)
        <article class="kg-artwork-card">
          <div class="kg-artwork-card__img-wrap">
            <x-picture :image="$post->image" :alt="$post->nama" preset="gallery" :eager="false" />
          </div>
          <div class="kg-artwork-card__body">
            <p class="kg-artwork-card__name">{{ $post->nama }}</p>
            <p class="kg-artwork-card__dim">{{ $post->dimensi }}</p>
            <a href="{{ $post->link }}" class="kg-btn-order">PESAN SEKARANG</a>
          </div>
        </article>
        @empty
        <p class="kg-empty">Belum ada karya tersedia.</p>
        @endforelse
      </div>
      <div class="kg-btn-wrap">
        <a href="/gallery" class="kg-btn-more">SELENGKAPNYA &rarr;</a>
      </div>
    </section>

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
