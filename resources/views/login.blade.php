<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ __('login.title') }} — Kabul Art Gallery</title>
  <meta name="robots" content="noindex,nofollow">
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
  <div class="adm-login-page">
    <div class="adm-login-card">

      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
        <div class="adm-login-brand" style="margin-bottom:0;">
          <img src="{{ asset('img/logokabul.png') }}" alt="Kabul Art Gallery logo">
          <div>
            <div class="adm-login-brand__title">Kabul Art Gallery</div>
            <div class="adm-login-brand__sub">Admin Panel</div>
          </div>
        </div>
        <div class="adm-lang-switch" role="group" aria-label="Language">
          <a href="{{ route('locale.switch', 'id') }}" class="adm-lang-btn {{ app()->getLocale() === 'id' ? 'is-active' : '' }}">ID</a>
          <span class="adm-lang-divider">|</span>
          <a href="{{ route('locale.switch', 'en') }}" class="adm-lang-btn {{ app()->getLocale() === 'en' ? 'is-active' : '' }}">EN</a>
        </div>
      </div>

      <div class="adm-login-divider"></div>

      <form action="{{ route('login.action') }}" method="post" autocomplete="off">
        @csrf

        <div class="adm-form-group">
          <label class="adm-form-label" for="email">Email</label>
          <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="admin@example.com"
            required
            autocomplete="username"
            class="adm-form-input @error('email') is-invalid @enderror"
          >
          @error('email')
            <div class="adm-form-error">{{ $message }}</div>
          @enderror
        </div>

        <div class="adm-form-group">
          <label class="adm-form-label" for="password">Password</label>
          <input
            type="password"
            id="password"
            name="password"
            placeholder="••••••••"
            required
            autocomplete="current-password"
            class="adm-form-input @error('password') is-invalid @enderror"
          >
          @error('password')
            <div class="adm-form-error">{{ $message }}</div>
          @enderror
        </div>

        <button type="submit" class="adm-login-submit" id="login-submit">{{ __('login.submit') }}</button>
      </form>

    </div>
  </div>
</body>
</html>