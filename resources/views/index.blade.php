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

  <!-- FOOTER -->
  <footer class="kg-footer" role="contentinfo">
    <div class="kg-footer__inner">
      <div class="kg-footer__contact">
        <ul>
          <li><p>Jl. Timoho No.313C, Baciro,<br>Kec. Gondokusuman, Kota Yogyakarta,<br>Daerah Istimewa Yogyakarta 55165</p></li>
          <li><a href="https://instagram.com/kabul.artgallery?igshid=NzZhOTFlYzFmZQ=="><i class="fa fa-instagram"></i> kabulArtGallery</a></li>
          <li><a href="https://wa.me/6282223242071"><i class="fa fa-whatsapp"></i>  0822 2324 2071</a></li>
          <li><a href="mailto:kabulartmedia@gmail.com"><i class="fa fa-envelope"></i> kabulartmedia@gmail.com</a></li>
        </ul>
      </div>
      <div class="kg-footer__map">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d247.0621290628184!2d110.39335760967451!3d-7.79045193156205!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a59d99d82e89b%3A0x5920e1b9f67c96a2!2zS2FidWwgQXJ0IEdhbGxlcnkg6qaP6qan6qa46qat6qeA6qaE6qaC6qag6qeA6qaS6qat6qeA6qat6qa66qaC6qaq6qeA!5e0!3m2!1sid!2sid!4v1692693287671!5m2!1sid!2sid" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" width="300" height="200" title="Kabul Art Gallery Location"></iframe>
      </div>
    </div>
    <p class="kg-footer__copy">Kabul Art Gallery &mdash; Yogyakarta</p>
  </footer>

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
