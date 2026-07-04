<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $ogTitle }}</title>

  {{-- Open Graph --}}
  <meta property="og:type"        content="website">
  <meta property="og:url"         content="{{ request()->url() }}">
  <meta property="og:title"       content="{{ $ogTitle }}">
  <meta property="og:description" content="{{ $ogDesc }}">
  <meta property="og:image"       content="{{ $ogImage }}">
  <meta property="og:image:width"  content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:locale"      content="pt_BR">
  <meta property="og:site_name"   content="Cria da Comunidade">

  {{-- Twitter / X Card --}}
  <meta name="twitter:card"        content="summary_large_image">
  <meta name="twitter:title"       content="{{ $ogTitle }}">
  <meta name="twitter:description" content="{{ $ogDesc }}">
  <meta name="twitter:image"       content="{{ $ogImage }}">

  <meta name="description" content="{{ $ogDesc }}">

  <meta http-equiv="refresh" content="0; url={{ $redirectUrl }}">
</head>
<body style="margin:0;background:#0f0e0c;color:#f5f0e8;font-family:sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;text-align:center;">
  <div>
    <p style="font-size:14px;color:#888;margin-bottom:8px;">Redirecionando…</p>
    <p style="font-size:18px;font-weight:700;">{{ $evento->titulo }}</p>
    <p style="font-size:13px;color:#aaa;margin-top:4px;">{{ $evento->categoria }} · {{ $evento->local }}</p>
    <a href="{{ $redirectUrl }}" style="display:inline-block;margin-top:16px;padding:10px 20px;background:#FF5E1A;color:#fff;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;">
      Ver evento →
    </a>
  </div>
  <script>window.location.replace("{{ $redirectUrl }}");</script>
</body>
</html>
