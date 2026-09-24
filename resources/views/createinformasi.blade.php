<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Informasi — Kabul Art Gallery Admin</title>
  <meta name="robots" content="noindex,nofollow">
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<div class="adm-shell">

  @include('partials.admin-sidebar', ['activeNav' => 'informasi'])
  <div class="adm-overlay" id="admOverlay"></div>

  <div class="adm-main">
    <div class="adm-topbar">
      <button class="adm-hamburger" id="admHamburger" aria-label="Toggle sidebar">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
      </button>
      <div><div class="adm-topbar__title">Informasi</div></div>
      <div class="adm-topbar__actions">
        <a href="{{ route('postsinformasi.index') }}" class="adm-btn adm-btn--ghost adm-btn--sm">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
          Kembali
        </a>
        <a href="/" target="_blank" class="adm-btn adm-btn--ghost adm-btn--sm">Lihat Website</a>
        <a href="/logout" class="adm-btn adm-btn--sm" style="background:rgba(155,35,53,0.1);color:#9b2335;border:1px solid rgba(155,35,53,0.2);">Logout</a>
      </div>
    </div>

    <div class="adm-content">

      @if(session('success'))
        <div class="adm-flash adm-flash--success" data-auto-dismiss>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          {{ session('success') }}
        </div>
      @endif
      @if(isset($errors) && $errors->any())
        <div class="adm-flash adm-flash--error" data-auto-dismiss>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          {{ $errors->first() }}
        </div>
      @endif

      <div class="adm-page-header">
        <div>
          <span class="adm-page-header__eyebrow">Manajemen Berita &amp; Artikel</span>
          <h1 class="adm-page-header__title">Tambah Informasi Baru</h1>
        </div>
      </div>

      <div class="adm-form-card">
        <form action="{{ route('postsinformasi.store') }}" method="POST" enctype="multipart/form-data" id="informasiForm">
          @csrf

          {{-- UPLOAD GAMBAR DOKUMENTASI / POSTER --}}
          <div class="adm-form-group">
            <label class="adm-form-label" for="imageInput">Gambar Dokumentasi / Banner <span style="color:var(--clr-danger)">*</span></label>
            <div class="adm-upload-zone" id="uploadZone">
              <input type="file" name="image" id="imageInput" accept="image/jpeg,image/png,image/webp,image/avif" required>
              <svg class="adm-upload-zone__icon" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
              </svg>
              <div class="adm-upload-zone__text">
                <strong>Klik untuk memilih gambar</strong> atau seret file ke sini<br>
                <small style="color:var(--clr-espresso-lt)">Format didukung: JPG, PNG, WEBP, AVIF (Maks. 15MB). Otomatis dioptimasi ke WebP saat disimpan.</small>
              </div>
            </div>

            <div class="adm-form-error" id="previewError" style="display:none;"></div>

            <div class="adm-preview" id="previewWrap">
              <img id="previewImg" class="adm-preview__img" src="" alt="Pratinjau Foto Informasi">
              <div class="adm-preview__info">
                <span class="adm-preview__name" id="previewName"></span>
                <span id="previewSize"></span>
              </div>
            </div>

            @error('image')
              <div class="adm-form-error">{{ $message }}</div>
            @enderror
          </div>

          {{-- DESKRIPSI INFORMASI --}}
          <div class="adm-form-group">
            <label class="adm-form-label" for="deskripsi">Teks / Deskripsi Informasi <span style="color:var(--clr-danger)">*</span></label>
            <textarea name="deskripsi" id="deskripsi" class="adm-form-textarea @error('deskripsi') is-invalid @enderror" placeholder="Tuliskan berita, liputan pameran, atau rilis kegiatan seni..." rows="8" required>{{ old('deskripsi') }}</textarea>
            <div class="adm-form-hint">Dapat memuat informasi kegiatan galeri, pameran seni, workshop, atau pengumuman penting.</div>
            @error('deskripsi')
              <div class="adm-form-error">{{ $message }}</div>
            @enderror
          </div>

          {{-- ACTION BUTTONS --}}
          <div class="adm-form-actions">
            <button type="submit" class="adm-btn adm-btn--primary">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              Simpan &amp; Terbitkan Informasi
            </button>
            <a href="{{ route('postsinformasi.index') }}" class="adm-btn adm-btn--ghost">Batal</a>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>

@include('partials.admin-js')

</body>
</html>