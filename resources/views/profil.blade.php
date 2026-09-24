<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil &mdash; Kabul Art Gallery</title>
  <meta name="description" content="Profil dan sejarah Kabul Art Gallery serta biografi sang maestro batik WH. Kabul (Wiji Hartono), Yogyakarta.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Jost:wght@400;500;600&display=swap">
  <link rel="stylesheet" href="css/gallery-redesign.css">
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
      <a href="/profil" class="kg-active" id="nav-profil">Profil</a>
      <a href="/informasi" id="nav-informasi">Informasi</a>
      <a href="/gallery" id="nav-gallery">Gallery</a>
      <a href="/testimoni" id="nav-testimoni">Testimoni</a>
    </div>
  </nav>

  <main class="kg-page">

    <!-- PAGE HERO -->
    <header class="kg-page-hero">
      <span class="kg-page-hero__eyebrow">Tentang Kami</span>
      <h1 class="kg-page-hero__title">PROFIL</h1>
    </header>

    <!-- PROFIL CONTENT -->
    <div class="kg-container">
      <section class="kg-profil">

        <!-- Gallery history -->
        <div class="kg-profil__gallery-intro">
          <div class="kg-profil__gallery-img">
            <picture>
              <source type="image/webp" srcset="img/kbl.webp">
              <img src="img/kbl.jpg" alt="photo kabulgallery" width="600" height="450" loading="lazy" decoding="async">
            </picture>
          </div>
          <div class="kg-profil__gallery-text">
            <h2>History Kabul Gallery</h2>
            <p>Kabul Art Gallery is a company that offers paintings of batik in Yogyakarta. The majority of its customers are foreigners from a number of different countries including Germany.
              Foreign tourists would most likely prefer to buy batik in the form of paintings.</p>
          </div>
        </div>

        <hr class="kg-divider">

        <!-- Maestro biography -->
        <span class="kg-section-label">Sang Seniman</span>
        <h2 class="kg-section-title">Autobiografi The Maestro</h2>

        <div class="kg-maestro">
          <div class="kg-maestro__bio">
            <strong>Autobiografi The Maestro <br>
              Wiji Hartono / Kabul
            </strong>
            <p>WH. Kabul he`s born Januari 25, 1950 in Yogyakarta, he has made the classic,
              modern, and traditional batik painting by his own characteristic, his styles
              combination by naturak and chemical.</p>
            <p>These are the exhibitions he has ever made :</p>
            <ul>
              <li>Exhibitioned by all of student art academy in Paris, France, September 1975</li>
              <li>Jained the exhibition in Frankfrut Germany, July 1986.</li>
              <li>August 2, 1986, Exhibitioned Academy of Paint Art at Bremen</li>
              <li>August 15, 1986, Exhibitioned Javanis Batik Art at Gallery of Roa Siahca Bremen.</li>
              <li>Used to have exhibition in Madrid Spain on August 1989.</li>
              <li>July 18, 1990 Company Stocholm, Sweden.</li>
              <li>August 5, 1990 Exhibition Batik Art Asiane at Vinland.</li>
              <li>Joined the exhibition with The Viking Line Sweden during August to September 1991.</li>
              <li>February 10, 1992, Yogyakarta Batik Art Exhibition at Bentara Budaya Yogya Indonesia.</li>
              <li>May 4, 1992, Exhibition Batik Art Melbourne Australia</li>
              <li>July 1, 1992, Java Bali Art Exhibition at Nusa Dua Beach Hotel Bali Indonesia.</li>
              <li>The Champion of the Batik Festival in Jakarta, September 1992</li>
              <li>December 16, 1992 SANTANDER ESPANA</li>
              <li>The Best Creation on the Asia Batik Festival in Srilanka, February 1993.</li>
              <li>July 1, 1995, Exhibitioned Java Bali Batik Art at Indonesia</li>
            </ul>
          </div>
          <div class="kg-maestro__photos">
            <picture>
              <source type="image/webp" srcset="img/caption.webp">
              <img src="img/caption.jpg" alt="profile img" width="400" height="548" loading="lazy" decoding="async">
            </picture>
            <picture>
              <source type="image/webp" srcset="img/profil1.webp">
              <img src="img/profil1.jpeg" alt="profile img" width="400" height="302" loading="lazy" decoding="async">
            </picture>
          </div>
        </div>

      </section>
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
