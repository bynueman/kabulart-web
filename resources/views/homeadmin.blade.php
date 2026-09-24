<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — Kabul Art Gallery Admin</title>
  <meta name="robots" content="noindex,nofollow">
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<div class="adm-shell">

  <!-- SIDEBAR -->
  @include('partials.admin-sidebar', ['activeNav' => 'dashboard'])

  <!-- OVERLAY (mobile) -->
  <div class="adm-overlay" id="admOverlay"></div>

  <!-- MAIN -->
  <div class="adm-main">

    <!-- TOP BAR -->
    <div class="adm-topbar">
      <button class="adm-hamburger" id="admHamburger" aria-label="Toggle sidebar">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
      </button>
      <div>
        <div class="adm-topbar__title">Dashboard</div>
      </div>
      <div class="adm-topbar__actions">
        <a href="/" target="_blank" class="adm-btn adm-btn--ghost adm-btn--sm">Lihat Website</a>
        <a href="/logout" class="adm-btn adm-btn--sm" style="background:rgba(155,35,53,0.1);color:#9b2335;border:1px solid rgba(155,35,53,0.2);">Logout</a>
      </div>
    </div>

    <!-- CONTENT -->
    <div class="adm-content">

      <div class="adm-page-header">
        <div>
          <span class="adm-page-header__eyebrow">Kabul Art Gallery</span>
          <h1 class="adm-page-header__title">Selamat Datang</h1>
        </div>
      </div>

      <!-- STATS -->
      <div class="adm-stats">
        <div class="adm-stat">
          <span class="adm-stat__label">Karya Gallery</span>
          <span class="adm-stat__count">{{ $countGalery }}</span>
          <span class="adm-stat__sub">lukisan terdaftar</span>
          <a href="{{ route('postsgalery.index') }}" class="adm-stat__link">Kelola →</a>
        </div>
        <div class="adm-stat">
          <span class="adm-stat__label">Informasi</span>
          <span class="adm-stat__count">{{ $countInformasi }}</span>
          <span class="adm-stat__sub">artikel kegiatan</span>
          <a href="{{ route('postsinformasi.index') }}" class="adm-stat__link">Kelola →</a>
        </div>
        <div class="adm-stat">
          <span class="adm-stat__label">Testimoni</span>
          <span class="adm-stat__count">{{ $countTestimoni }}</span>
          <span class="adm-stat__sub">foto pelanggan</span>
          <a href="{{ route('posttestimoni.index') }}" class="adm-stat__link">Kelola →</a>
        </div>
      </div>

      <!-- QUICK NAV -->
      <div style="margin-bottom:1rem;">
        <span style="font-size:0.68rem;font-weight:600;letter-spacing:0.18em;text-transform:uppercase;color:var(--clr-gold);">Akses Cepat</span>
      </div>
      <div class="adm-quick-nav">
        <a href="{{ route('postsgalery.create') }}" class="adm-quick-card">
          <div class="adm-quick-card__icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="m9 9 2 2 4-4"/><path d="M3 15l5-5 5 5"/><path d="M14 14l3-3 3 3"/></svg>
          </div>
          <div class="adm-quick-card__title">Tambah Karya</div>
          <div class="adm-quick-card__desc">Upload lukisan atau foto karya baru ke galeri.</div>
        </a>
        <a href="{{ route('postsinformasi.create') }}" class="adm-quick-card">
          <div class="adm-quick-card__icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg>
          </div>
          <div class="adm-quick-card__title">Tambah Informasi</div>
          <div class="adm-quick-card__desc">Buat artikel atau pengumuman kegiatan baru.</div>
        </a>
        <a href="{{ route('posttestimoni.create') }}" class="adm-quick-card">
          <div class="adm-quick-card__icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          </div>
          <div class="adm-quick-card__title">Tambah Testimoni</div>
          <div class="adm-quick-card__desc">Upload foto testimoni pelanggan.</div>
        </a>
      </div>

    </div><!-- /adm-content -->
  </div><!-- /adm-main -->
</div><!-- /adm-shell -->

@include('partials.admin-js')
</body>
</html>