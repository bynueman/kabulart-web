<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ __('admin.add_testimoni_title') }} — Kabul Art Gallery Admin</title>
  <meta name="robots" content="noindex,nofollow">
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<div class="adm-shell">

  @include('partials.admin-sidebar', ['activeNav' => 'testimoni'])
  <div class="adm-overlay" id="admOverlay"></div>

  <div class="adm-main">
    @include('partials.admin-topbar', ['title' => __('admin.testimoni'), 'backUrl' => route('posttestimoni.index')])

    <div class="adm-content">

      @if(session('success'))
        <div class="adm-flash adm-flash--success" data-auto-dismiss>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          {{ session('success') }}
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
          <span class="adm-page-header__eyebrow">{{ __('admin.testimoni') }}</span>
          <h1 class="adm-page-header__title">{{ __('admin.add_testimoni_title') }}</h1>
        </div>
      </div>

      <div class="adm-form-card">
        <form action="{{ route('posttestimoni.store') }}" method="POST" enctype="multipart/form-data" id="testimoniForm">
          @csrf

          {{-- UPLOAD TANGKAPAN LAYAR TESTIMONI --}}
          <div class="adm-form-group">
            <label class="adm-form-label" for="imageInput">{{ __('admin.testimoni_label') }} <span style="color:var(--clr-danger)">*</span></label>
            <div class="adm-upload-zone" id="uploadZone">
              <input type="file" name="image" id="imageInput" accept="image/jpeg,image/png,image/webp,image/avif" required>
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
              <img id="previewImg" class="adm-preview__img" src="" alt="Pratinjau Foto Testimoni">
              <div class="adm-preview__info">
                <span class="adm-preview__name" id="previewName"></span>
                <span id="previewSize"></span>
              </div>
            </div>

            @error('image')
              <div class="adm-form-error">{{ $message }}</div>
            @enderror
          </div>

          {{-- ACTION BUTTONS --}}
          <div class="adm-form-actions">
            <button type="submit" class="adm-btn adm-btn--primary">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              {{ __('admin.save_testimoni_btn') }}
            </button>
            <a href="{{ route('posttestimoni.index') }}" class="adm-btn adm-btn--ghost">{{ __('admin.cancel') }}</a>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>

@include('partials.admin-js')
</body>
</html>