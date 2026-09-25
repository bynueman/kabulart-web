<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ __('admin.edit_gallery_title') }} — Kabul Art Gallery Admin</title>
  <meta name="robots" content="noindex,nofollow">
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<div class="adm-shell">

  @include('partials.admin-sidebar', ['activeNav' => 'gallery'])
  <div class="adm-overlay" id="admOverlay"></div>

  <div class="adm-main">
    @include('partials.admin-topbar', ['title' => __('admin.gallery'), 'backUrl' => route('postsgalery.index')])

    <div class="adm-content">

      @if(session('success'))
        <div class="adm-flash adm-flash--success" data-auto-dismiss>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          {{ session('success') }}
        </div>
      @endif
      @if(session('warning'))
        <div class="adm-flash adm-flash--warning" data-auto-dismiss style="background:rgba(184,147,42,0.12);color:var(--clr-gold);border:1px solid rgba(184,147,42,0.3);">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          {{ session('warning') }}
        </div>
      @endif
      @if(isset($errors) && $errors->any())
        <div class="adm-flash adm-flash--error" data-auto-dismiss>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          {{ $errors->first() }}
        </div>
      @endif

      <div class="adm-page-header">
        <div>
          <span class="adm-page-header__eyebrow">{{ __('admin.gallery') }}</span>
          <h1 class="adm-page-header__title">{{ __('admin.edit_gallery_title') }}: {{ $post->nama }}</h1>
        </div>
      </div>

      <div class="adm-form-card">
        <form action="{{ route('postsgalery.update', $post->id) }}" method="POST" enctype="multipart/form-data" id="galleryEditForm">
          @csrf
          @method('PUT')

          {{-- FOTO SAAT INI & GANTI FOTO --}}
          <div class="adm-form-group">
            <label class="adm-form-label">{{ __('admin.photo_current') }}</label>
            @php
              $picData = \App\Services\MediaPipeline::getPictureData($post->image, 'postsimg');
            @endphp
            <div style="display:flex;align-items:center;gap:1.25rem;margin-bottom:1rem;background:var(--clr-cream);padding:0.75rem 1rem;border-radius:var(--radius-md);border:1px solid var(--clr-border);">
              <img src="{{ $picData['fallback_url'] }}" alt="{{ $post->nama }}" style="width:72px;height:72px;object-fit:cover;border-radius:var(--radius-sm);border:1px solid var(--clr-border);" onerror="this.onerror=null;this.src='{{ asset('img/desain.png') }}';">
              <div>
                <div style="font-weight:600;font-size:0.88rem;color:var(--clr-espresso);">{{ $post->image }}</div>
                <div style="font-size:0.78rem;color:var(--clr-espresso-lt);">{{ __('admin.leave_empty_hint') }}</div>
              </div>
            </div>

            <label class="adm-form-label" for="imageInput">{{ __('admin.photo_replace') }}</label>
            <div class="adm-upload-zone" id="uploadZone">
              <input type="file" name="image" id="imageInput" accept="image/jpeg,image/png,image/webp,image/avif">
              <svg class="adm-upload-zone__icon" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
              </svg>
              <div class="adm-upload-zone__text">
                <strong>{{ __('admin.upload_drag') }}</strong><br>
                <small style="color:var(--clr-espresso-lt)">{{ __('admin.upload_hint') }}</small>
              </div>
            </div>

            <div class="adm-form-error" id="previewError" style="display:none;"></div>

            <div class="adm-preview" id="previewWrap">
              <img id="previewImg" class="adm-preview__img" src="" alt="Pratinjau Foto Baru">
              <div class="adm-preview__info">
                <span class="adm-preview__name" id="previewName"></span>
                <span id="previewSize"></span>
              </div>
            </div>

            @error('image')
              <div class="adm-form-error">{{ $message }}</div>
            @enderror
          </div>

          {{-- BILINGUAL NAMA KARYA BOX --}}
          <div class="adm-bilingual-box">
            <div class="adm-bilingual-header">
              <div>
                <strong style="font-size:0.88rem;color:var(--clr-espresso);">{{ __('admin.name_label') }} (Bilingual)</strong>
                <div style="font-size:0.75rem;color:var(--clr-espresso-lt);">{{ __('admin.bilingual_subtitle') }}</div>
              </div>
              <div>
                <span class="adm-bilingual-badge adm-bilingual-badge--{{ $post->translation_manual ? 'manual' : 'auto' }}">
                  {{ $post->translation_manual ? __('admin.manual_translated') : __('admin.auto_translated') }}
                </span>
                <span style="font-size:0.74rem;color:var(--clr-espresso-lt);margin-left:0.4rem;">
                  {{ __('admin.source_lang') }}: <strong>{{ strtoupper($post->translation_source ?? 'id') }}</strong>
                </span>
              </div>
            </div>

            <div class="adm-bilingual-grid">
              <div>
                <label class="adm-form-label" for="nama_id">{{ __('admin.label_id') }} <span style="color:var(--clr-danger)">*</span></label>
                <input
                  type="text"
                  name="nama_id"
                  id="nama_id"
                  class="adm-form-input @error('nama_id') is-invalid @enderror"
                  value="{{ old('nama_id', $post->getRawOriginal('nama_id') ?: $post->getRawOriginal('nama')) }}"
                  required
                >
                @error('nama_id')
                  <div class="adm-form-error">{{ $message }}</div>
                @enderror
              </div>
              <div>
                <label class="adm-form-label" for="nama_en">{{ __('admin.label_en') }} <span style="color:var(--clr-danger)">*</span></label>
                <input
                  type="text"
                  name="nama_en"
                  id="nama_en"
                  class="adm-form-input @error('nama_en') is-invalid @enderror"
                  value="{{ old('nama_en', $post->getRawOriginal('nama_en') ?: $post->getRawOriginal('nama')) }}"
                  required
                >
                @error('nama_en')
                  <div class="adm-form-error">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>

          {{-- DIMENSI BILINGUAL --}}
          <div class="adm-form-group">
            <div class="adm-bilingual-grid">
              <div>
                <label class="adm-form-label" for="dimensi_id">{{ __('admin.dim_label') }} (ID) <span style="color:var(--clr-danger)">*</span></label>
                <input type="text" name="dimensi_id" id="dimensi_id" class="adm-form-input @error('dimensi_id') is-invalid @enderror" value="{{ old('dimensi_id', $post->getRawOriginal('dimensi_id') ?: $post->getRawOriginal('dimensi')) }}" required>
              </div>
              <div>
                <label class="adm-form-label" for="dimensi_en">{{ __('admin.dim_label') }} (EN)</label>
                <input type="text" name="dimensi_en" id="dimensi_en" class="adm-form-input @error('dimensi_en') is-invalid @enderror" value="{{ old('dimensi_en', $post->getRawOriginal('dimensi_en') ?: $post->getRawOriginal('dimensi')) }}">
              </div>
            </div>
            <div class="adm-form-hint">{{ __('admin.dim_hint') }}</div>
            @error('dimensi_id')
              <div class="adm-form-error">{{ $message }}</div>
            @enderror
          </div>

          {{-- LINK PEMBELIAN / DETAIL --}}
          <div class="adm-form-group">
            <label class="adm-form-label" for="link">{{ __('admin.link_label') }} <span style="color:var(--clr-danger)">*</span></label>
            <input type="text" name="link" id="link" class="adm-form-input @error('link') is-invalid @enderror" value="{{ old('link', $post->getRawOriginal('link')) }}" placeholder="Contoh: https://wa.me/628123456789 atau https://tokopedia.com/..." required>
            <div class="adm-form-hint">{{ __('admin.link_hint') }}</div>
            @if(!empty($post->link))
              <div style="margin-top:0.35rem;">
                <a href="{{ $post->order_link }}" target="_blank" rel="noopener noreferrer" style="font-size:0.8rem;color:var(--clr-terracotta);text-decoration:underline;">
                  Tes Link Pesanan di Tab Baru &nearr;
                </a>
              </div>
            @endif
            @error('link')
              <div class="adm-form-error">{{ $message }}</div>
            @enderror
          </div>

          {{-- ACTION BUTTONS --}}
          <div class="adm-form-actions">
            <button type="submit" class="adm-btn adm-btn--primary">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              {{ __('admin.update_gallery_btn') }}
            </button>
            <a href="{{ route('postsgalery.index') }}" class="adm-btn adm-btn--ghost">{{ __('admin.cancel') }}</a>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>

@include('partials.admin-js')
</body>
</html>
