<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ app()->getLocale() === 'id' ? 'Profil — Kabul Art Gallery' : 'Profile — Kabul Art Gallery' }}</title>
  <meta name="description" content="{{ app()->getLocale() === 'id' ? 'Profil dan sejarah Kabul Art Gallery serta biografi sang maestro batik WH. Kabul (Wiji Hartono), Yogyakarta.' : 'Profile and history of Kabul Art Gallery and biography of batik maestro WH. Kabul (Wiji Hartono), Yogyakarta.' }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Jost:wght@400;500;600&display=swap">
  <link rel="stylesheet" href="{{ asset('css/gallery-redesign.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" media="print" onload="this.media='all'">
  <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"></noscript>
</head>
<body>

  <!-- NAV -->
  @include('partials.public-nav', ['activeNav' => 'profil'])

  <main class="kg-page">

    <!-- PAGE HERO -->
    <header class="kg-page-hero">
      <span class="kg-page-hero__eyebrow">{{ __('profil.eyebrow') }}</span>
      <h1 class="kg-page-hero__title">{{ __('profil.title') }}</h1>
    </header>

    <!-- PROFIL CONTENT -->
    <div class="kg-container">
      <section class="kg-profil">

        <!-- Gallery history -->
        <div class="kg-profil__gallery-intro">
          <div class="kg-profil__gallery-img">
            <picture>
              <source type="image/webp" srcset="{{ asset('img/kbl.webp') }}">
              <img src="{{ asset('img/kbl.jpg') }}" alt="photo kabulgallery" width="600" height="450" loading="lazy" decoding="async">
            </picture>
          </div>
          <div class="kg-profil__gallery-text">
            <h2>{{ __('profil.history_title') }}</h2>
            <p>{{ __('profil.history_desc') }}</p>
          </div>
        </div>

        <hr class="kg-divider">

        <!-- Maestro biography -->
        <span class="kg-section-label">{{ __('profil.artist_label') }}</span>
        <h2 class="kg-section-title">{{ __('profil.artist_title') }}</h2>

        <div class="kg-maestro">
          <div class="kg-maestro__bio">
            <h3 class="kg-maestro__name">{{ __('profil.artist_name') }}</h3>
            <span class="kg-maestro__subtitle">{{ __('profil.artist_subtitle') }}</span>

            @if(app()->getLocale() === 'en')
              {{-- Canonical English Primary --}}
              <p>WH. Kabul was born on January 25, 1950 in Yogyakarta. He creates classic, modern, and traditional batik paintings with a distinctive signature style combining natural and modern pigments.</p>
              <p style="font-style:italic;color:var(--clr-espresso-lt);font-size:0.92rem;line-height:1.7;">
                WH. Kabul lahir pada 25 Januari 1950 di Yogyakarta. Beliau telah menghasilkan karya seni batik lukis bercorak klasik, modern, dan tradisional dengan karakteristik unik tersendiri melalui perpaduan pewarna alami dan sintetis berkualitas tinggi.
              </p>
            @else
              {{-- Indonesian Primary --}}
              <p>WH. Kabul lahir pada 25 Januari 1950 di Yogyakarta. Beliau telah menghasilkan karya seni batik lukis bercorak klasik, modern, dan tradisional dengan karakteristik unik tersendiri melalui perpaduan pewarna alami dan sintetis berkualitas tinggi.</p>
              <p style="font-style:italic;color:var(--clr-espresso-lt);font-size:0.92rem;line-height:1.7;">
                WH. Kabul was born on January 25, 1950 in Yogyakarta. He creates classic, modern, and traditional batik paintings with a distinctive signature style combining natural and modern pigments.
              </p>
            @endif

            <h4 class="kg-maestro__exhib-title">{{ __('profil.exhib_title') }}</h4>
            <ul>
              @if(app()->getLocale() === 'en')
                {{-- Canonical English Exhibitions List --}}
                <li>Exhibited by all of student art academy in Paris, France &mdash; September 1975</li>
                <li>Joined the exhibition in Frankfurt, Germany &mdash; July 1986</li>
                <li>Exhibited at Academy of Paint Art, Bremen &mdash; August 2, 1986</li>
                <li>Exhibited Javanese Batik Art at Gallery of Roa Siahca, Bremen &mdash; August 15, 1986</li>
                <li>Exhibition in Madrid, Spain &mdash; August 1989</li>
                <li>Exhibition at Company Stockholm, Sweden &mdash; July 18, 1990</li>
                <li>Exhibition Batik Art Asiana at Finland &mdash; August 5, 1990</li>
                <li>Joined the exhibition with The Viking Line, Sweden &mdash; August to September 1991</li>
                <li>Yogyakarta Batik Art Exhibition at Bentara Budaya Yogyakarta, Indonesia &mdash; February 10, 1992</li>
                <li>Exhibition Batik Art, Melbourne, Australia &mdash; May 4, 1992</li>
                <li>Java Bali Art Exhibition at Nusa Dua Beach Hotel, Bali, Indonesia &mdash; July 1, 1992</li>
                <li>The Champion of the Batik Festival in Jakarta &mdash; September 1992</li>
                <li>Exhibition Santander, Espa&ntilde;a &mdash; December 16, 1992</li>
                <li>The Best Creation on the Asia Batik Festival in Sri Lanka &mdash; February 1993</li>
                <li>Exhibited Java Bali Batik Art, Indonesia &mdash; July 1, 1995</li>
              @else
                {{-- Indonesian Exhibitions List with Dates & Proper Nouns Preserved --}}
                <li>Pameran bersama seluruh mahasiswa akademi seni di Paris, Prancis &mdash; September 1975</li>
                <li>Mengikuti pameran di Frankfurt, Jerman &mdash; Juli 1986</li>
                <li>Pameran di Academy of Paint Art, Bremen &mdash; 2 Agustus 1986</li>
                <li>Pameran Seni Batik Jawa di Gallery of Roa Siahca, Bremen &mdash; 15 Agustus 1986</li>
                <li>Pameran di Madrid, Spanyol &mdash; Agustus 1989</li>
                <li>Pameran di Company Stockholm, Swedia &mdash; 18 Juli 1990</li>
                <li>Pameran Seni Batik Asiana di Finlandia &mdash; 5 Agustus 1990</li>
                <li>Mengikuti pameran bersama The Viking Line, Swedia &mdash; Agustus hingga September 1991</li>
                <li>Pameran Seni Batik Yogyakarta di Bentara Budaya Yogyakarta, Indonesia &mdash; 10 Februari 1992</li>
                <li>Pameran Seni Batik, Melbourne, Australia &mdash; 4 Mei 1992</li>
                <li>Pameran Seni Jawa Bali di Nusa Dua Beach Hotel, Bali, Indonesia &mdash; 1 Juli 1992</li>
                <li>Juara Festival Batik di Jakarta &mdash; September 1992</li>
                <li>Pameran Santander, Spanyol &mdash; 16 Desember 1992</li>
                <li>Karya Terbaik pada Festival Batik Asia di Sri Lanka &mdash; Februari 1993</li>
                <li>Pameran Seni Batik Jawa Bali, Indonesia &mdash; 1 Juli 1995</li>
              @endif
            </ul>
          </div>
          <div class="kg-maestro__photos">
            <figure class="kg-maestro__photo-item">
              <picture>
                <source type="image/webp" srcset="{{ asset('img/caption.webp') }}">
                <img src="{{ asset('img/caption.jpg') }}" alt="{{ __('profil.caption_painting') }}" width="400" height="548" loading="lazy" decoding="async">
              </picture>
              <figcaption>{{ __('profil.caption_painting') }}</figcaption>
            </figure>
            <figure class="kg-maestro__photo-item">
              <picture>
                <source type="image/webp" srcset="{{ asset('img/profil1.webp') }}">
                <img src="{{ asset('img/profil1.jpeg') }}" alt="{{ __('profil.caption_spain') }}" width="400" height="302" loading="lazy" decoding="async">
              </picture>
              <figcaption>{{ __('profil.caption_spain') }}</figcaption>
            </figure>
          </div>
        </div>

      </section>
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
