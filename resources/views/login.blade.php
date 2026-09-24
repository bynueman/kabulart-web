<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login — Kabul Art Gallery</title>
  <meta name="robots" content="noindex,nofollow">
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
  <div class="adm-login-page">
    <div class="adm-login-card">

      <div class="adm-login-brand">
        <img src="{{ asset('img/logokabul.png') }}" alt="Kabul Art Gallery logo">
        <div>
          <div class="adm-login-brand__title">Kabul Art Gallery</div>
          <div class="adm-login-brand__sub">Admin Panel</div>
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

        <button type="submit" class="adm-login-submit" id="login-submit">Masuk ke Dashboard</button>
      </form>

    </div>
  </div>
</body>
</html>