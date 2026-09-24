<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Testimoni — Kabul Art Gallery Admin</title>
  <meta name="robots" content="noindex,nofollow">
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<div class="adm-shell">

  @include('partials.admin-sidebar', ['activeNav' => 'testimoni'])
  <div class="adm-overlay" id="admOverlay"></div>

  <div class="adm-main">
    <div class="adm-topbar">
      <button class="adm-hamburger" id="admHamburger" aria-label="Toggle sidebar">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
      </button>
      <div><div class="adm-topbar__title">Testimoni</div></div>
      <div class="adm-topbar__actions">
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
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          {{ $errors->first() }}
        </div>
      @endif

      <div class="adm-page-header">
        <div>
          <span class="adm-page-header__eyebrow">Manajemen</span>
          <h1 class="adm-page-header__title">Foto Testimoni</h1>
        </div>
        <a href="{{ route('posttestimoni.create') }}" class="adm-btn adm-btn--primary" id="btn-tambah-testimoni">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Tambah Foto
        </a>
      </div>

      <div class="adm-table-wrap">
        <table class="adm-table" role="grid">
          <thead>
            <tr>
              <th scope="col">Foto Testimoni</th>
              <th scope="col">Info</th>
              <th scope="col" style="text-align:center;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($posts as $post)
            <tr>
              <td class="adm-td-img" data-label="Foto">
                @php
                  $picData = \App\Services\MediaPipeline::getPictureData($post->image, 'postsimg');
                @endphp
                <img
                  src="{{ $picData['fallback_url'] }}"
                  alt="Foto testimoni pelanggan"
                  class="adm-thumb"
                  onerror="this.onerror=null;this.classList.add('adm-thumb-broken');this.src='{{ asset('img/desain.png') }}';"
                  loading="lazy"
                >
              </td>
              <td data-label="Info" style="color:var(--clr-espresso-lt);font-size:0.85rem;">
                ID: #{{ $post->id }}<br>
                <span style="font-size:0.8rem;opacity:0.7;">{{ $post->created_at ? $post->created_at->format('d M Y') : '—' }}</span>
              </td>
              <td class="adm-td-actions" data-label="Aksi">
                <div style="display:flex;gap:0.4rem;justify-content:center;align-items:center;">
                  <a
                    href="{{ route('posttestimoni.edit', $post->id) }}"
                    class="adm-btn adm-btn--ghost adm-btn--sm"
                    id="edit-testimoni-{{ $post->id }}"
                    aria-label="Edit testimoni {{ $post->id }}"
                  >Edit</a>
                  <button
                    type="button"
                    class="adm-btn adm-btn--danger adm-btn--sm"
                    data-delete-url="{{ route('posttestimoni.destroy', $post->id) }}"
                    id="delete-testimoni-{{ $post->id }}"
                    aria-label="Hapus foto testimoni {{ $post->id }}"
                  >Hapus</button>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="3">
                <div class="adm-empty">Belum ada testimoni. <a href="{{ route('posttestimoni.create') }}" style="color:var(--clr-gold);">Tambahkan sekarang →</a></div>
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