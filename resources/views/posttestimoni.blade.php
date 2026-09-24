<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ __('admin.testimoni') }} — Kabul Art Gallery Admin</title>
  <meta name="robots" content="noindex,nofollow">
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<div class="adm-shell">

  @include('partials.admin-sidebar', ['activeNav' => 'testimoni'])
  <div class="adm-overlay" id="admOverlay"></div>

  <div class="adm-main">
    @include('partials.admin-topbar', ['title' => __('admin.testimoni')])

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
          <span class="adm-page-header__eyebrow">Manajemen</span>
          <h1 class="adm-page-header__title">{{ __('admin.stat_testimoni') }}</h1>
        </div>
        <a href="{{ route('posttestimoni.create') }}" class="adm-btn adm-btn--primary" id="btn-tambah-testimoni">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          {{ __('admin.add_testimoni') }}
        </a>
      </div>

      <div class="adm-table-wrap">
        <table class="adm-table" role="grid">
          <thead>
            <tr>
              <th scope="col">{{ __('admin.testimoni_label') }}</th>
              <th scope="col">{{ __('admin.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($posts as $post)
            <tr>
              <td class="adm-td-img" data-label="{{ __('admin.photo') }}">
                @php
                  $picData = \App\Services\MediaPipeline::getPictureData($post->image, 'postsimg');
                @endphp
                <img
                  src="{{ $picData['fallback_url'] }}"
                  alt="{{ __('testimoni.photo_alt') }}"
                  class="adm-thumb"
                  onerror="this.onerror=null;this.classList.add('adm-thumb-broken');this.src='{{ asset('img/desain.png') }}';"
                  loading="lazy"
                >
              </td>
              <td class="adm-td-actions" data-label="{{ __('admin.actions') }}">
                <div style="display:flex;gap:0.4rem;justify-content:center;align-items:center;">
                  <a
                    href="{{ route('posttestimoni.edit', $post->id) }}"
                    class="adm-btn adm-btn--ghost adm-btn--sm"
                    id="edit-testimoni-{{ $post->id }}"
                    aria-label="{{ __('admin.edit') }} {{ $post->id }}"
                  >{{ __('admin.edit') }}</a>
                  <button
                    type="button"
                    class="adm-btn adm-btn--danger adm-btn--sm"
                    data-delete-url="{{ route('posttestimoni.destroy', $post->id) }}"
                    id="delete-testimoni-{{ $post->id }}"
                    aria-label="{{ __('admin.delete') }} {{ $post->id }}"
                  >{{ __('admin.delete') }}</button>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="2">
                <div class="adm-empty">{{ __('admin.empty_testimoni') }}</div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

@include('partials.admin-delete-modal')
@include('partials.admin-js')
</body>
</html>