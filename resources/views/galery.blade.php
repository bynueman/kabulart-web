<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gallery &mdash; Kabul Art Gallery</title>
  <meta name="description" content="Browse the full collection of batik paintings by WH. Kabul — classic, modern, and traditional styles available for purchase.">
  <link rel="stylesheet" href="css/gallery-redesign.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

  <!-- NAV -->
  <nav class="kg-nav" role="navigation" aria-label="Main navigation">
    <a href="/" class="kg-nav__brand">
      <img src="img/logokabul.png" alt="Kabul Art Gallery logo" class="logo-icon">
      <img src="img/Desain_tanpa.png" alt="Kabul Art Gallery" class="logo-text">
    </a>
    <button class="kg-nav__hamburger" id="navToggle" aria-label="Toggle menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
    <div class="kg-nav__links" id="navLinks">
      <a href="/" id="nav-home">Home</a>
      <a href="/profil" id="nav-profil">Profil</a>
      <a href="/informasi" id="nav-informasi">Informasi</a>
      <a href="/gallery" class="kg-active" id="nav-gallery">Gallery</a>
      <a href="/testimoni" id="nav-testimoni">Testimoni</a>
    </div>
  </nav>

  <main class="kg-page">

    <!-- PAGE HERO -->
    <header class="kg-page-hero">
      <span class="kg-page-hero__eyebrow">Koleksi Karya</span>
      <h1 class="kg-page-hero__title">GALLERY</h1>
    </header>

    <!-- ARTWORK GRID -->
    <div class="kg-container">
      <div class="kg-artwork-grid">
        @forelse ($posts as $post)
        <article class="kg-artwork-card">
          <div class="kg-artwork-card__img-wrap">
            <img src="{{ asset('/storage/postsimg/'.$post->image) }}" alt="{{ $post->nama }}" class="rounded">
          </div>
          <div class="kg-artwork-card__body">
            <p class="kg-artwork-card__name">{{ $post->nama }}</p>
            <p class="kg-artwork-card__dim">{{ $post->dimensi }}</p>
            <a href="{{ $post->link }}" class="kg-btn-order">PESAN SEKARANG</a>
          </div>
        </article>
        @empty
        <p class="kg-empty">Data Post belum Tersedia.</p>
        @endforelse
      </div>
    </div>

  </main>

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
