<aside class="adm-sidebar" id="admSidebar">
  <div class="adm-sidebar__brand">
    <img src="{{ asset('img/logokabul.png') }}" alt="Kabul Art Gallery logo">
    <div>
      <div class="adm-sidebar__brand-text">Kabul Art Gallery</div>
      <span class="adm-sidebar__brand-sub">Admin Panel</span>
    </div>
  </div>

  <nav class="adm-sidebar__nav" role="navigation" aria-label="Admin navigation">
    <a href="/homeadmin" class="{{ ($activeNav ?? '') === 'dashboard' ? 'adm-active' : '' }}">
      <svg class="adm-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      Dashboard
    </a>
    <a href="{{ route('postsgalery.index') }}" class="{{ ($activeNav ?? '') === 'gallery' ? 'adm-active' : '' }}">
      <svg class="adm-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
      Gallery
    </a>
    <a href="{{ route('postsinformasi.index') }}" class="{{ ($activeNav ?? '') === 'informasi' ? 'adm-active' : '' }}">
      <svg class="adm-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      Informasi
    </a>
    <a href="{{ route('posttestimoni.index') }}" class="{{ ($activeNav ?? '') === 'testimoni' ? 'adm-active' : '' }}">
      <svg class="adm-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      Testimoni
    </a>

    <div class="adm-sidebar__divider"></div>

    <a href="/" target="_blank">
      <svg class="adm-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      Lihat Website
    </a>
  </nav>

  <div class="adm-sidebar__footer">
    <a href="/logout">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      Logout
    </a>
  </div>
</aside>
