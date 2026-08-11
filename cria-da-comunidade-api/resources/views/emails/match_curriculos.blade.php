<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Currículos compatíveis — {{ $vaga->titulo }}</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { background: #f4f4f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #18181b; }
    .wrapper { max-width: 600px; margin: 32px auto; }
    .card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
    .header { background: linear-gradient(135deg, #0f0e0c 0%, #1a1816 100%); padding: 32px 36px; }
    .logo-row { display: flex; align-items: center; gap: 10px; margin-bottom: 24px; }
    .logo-mark { width: 32px; height: 32px; background: #FF5E1A; border-radius: 8px; transform: rotate(-4deg); display: inline-block; }
    .logo-text { color: #f5f0e8; font-size: 16px; font-weight: 800; letter-spacing: -0.02em; }
    .badge { display: inline-block; background: rgba(43,217,107,0.15); border: 1px solid rgba(43,217,107,0.3); color: #2BD96B; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; padding: 4px 10px; border-radius: 999px; margin-bottom: 12px; }
    .header h1 { color: #f5f0e8; font-size: 22px; font-weight: 800; letter-spacing: -0.03em; line-height: 1.2; margin-bottom: 4px; }
    .header .subtitle { color: #888; font-size: 14px; margin-top: 6px; }
    .body { padding: 32px 36px; }
    .summary { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 16px 20px; margin-bottom: 28px; display: flex; align-items: center; gap: 12px; }
    .summary-count { font-size: 32px; font-weight: 900; color: #16a34a; line-height: 1; }
    .summary-text { font-size: 14px; color: #166534; line-height: 1.5; }
    .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #71717a; margin-bottom: 14px; }
    .curriculo-card { background: #f9f9f9; border: 1px solid #e4e4e7; border-radius: 12px; padding: 20px; margin-bottom: 12px; }
    .curriculo-name { font-size: 17px; font-weight: 800; letter-spacing: -0.02em; margin-bottom: 10px; color: #18181b; }
    .info-row { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #52525b; margin-bottom: 6px; }
    .info-label { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: #a1a1aa; width: 80px; flex-shrink: 0; }
    .tags { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; }
    .tag { background: rgba(255,94,26,0.08); color: #FF5E1A; font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 999px; }
    .pdf-btn { display: inline-block; background: #18181b; color: #fff !important; font-size: 12px; font-weight: 600; padding: 7px 14px; border-radius: 7px; text-decoration: none; margin-top: 12px; }
    .vaga-box { background: #f4f4f5; border-radius: 10px; padding: 16px 20px; margin-bottom: 28px; }
    .vaga-title { font-size: 15px; font-weight: 700; margin-bottom: 4px; }
    .vaga-meta { font-size: 12px; color: #71717a; display: flex; gap: 12px; flex-wrap: wrap; }
    .footer { background: #f4f4f5; padding: 20px 36px; text-align: center; }
    .footer p { font-size: 12px; color: #a1a1aa; line-height: 1.6; }
    .footer a { color: #71717a; }
  </style>
</head>
<body>
<div class="wrapper">
  <div class="card">

    <div class="header">
      <div class="logo-row">
        <span class="logo-mark"></span>
        <span class="logo-text">Cria da Comunidade</span>
      </div>
      <div class="badge">🎯 Match de Currículos</div>
      <h1>{{ $vaga->titulo }}</h1>
      <div class="subtitle">{{ $vaga->empresa }}{{ $vaga->local ? ' · ' . $vaga->local : '' }}</div>
    </div>

    <div class="body">

      <div class="summary">
        <div class="summary-count">{{ $curriculos->count() }}</div>
        <div class="summary-text">
          <strong>currículo(s) compatível(is)</strong> encontrado(s) no banco da comunidade para esta vaga.<br>
          Entre em contato para conhecer os candidatos.
        </div>
      </div>

      <div class="section-title">Sobre a vaga</div>
      <div class="vaga-box">
        <div class="vaga-title">{{ $vaga->titulo }}</div>
        <div class="vaga-meta">
          <span>{{ $vaga->tipo }}</span>
          @if($vaga->local)<span>📍 {{ $vaga->local }}</span>@endif
          @if($vaga->salario)<span>💰 {{ $vaga->salario }}</span>@endif
        </div>
      </div>

      <div class="section-title">Candidatos compatíveis</div>

      @foreach($curriculos as $c)
      <div class="curriculo-card">
        <div class="curriculo-name">{{ $c->nome }}</div>
        <div class="info-row">
          <span class="info-label">E-mail</span>
          <span>{{ $c->email }}</span>
        </div>
        @if($c->telefone)
        <div class="info-row">
          <span class="info-label">Telefone</span>
          <span>{{ $c->telefone }}</span>
        </div>
        @endif
        <div class="info-row">
          <span class="info-label">Área</span>
          <span>{{ $c->area_atuacao }}</span>
        </div>
        @if($c->cidade)
        <div class="info-row">
          <span class="info-label">Cidade</span>
          <span>{{ $c->cidade }}</span>
        </div>
        @endif
        <div class="info-row">
          <span class="info-label">Disponível</span>
          <span>{{ $c->disponibilidade }}</span>
        </div>
        @if($c->habilidades && count($c->habilidades))
        <div class="tags">
          @foreach($c->habilidades as $h)
          <span class="tag">{{ $h }}</span>
          @endforeach
        </div>
        @endif
        @if($c->pdf_url)
        <a href="{{ $c->pdf_url }}" class="pdf-btn" target="_blank">📄 Ver currículo em PDF →</a>
        @endif
      </div>
      @endforeach

    </div>

    <div class="footer">
      <p>
        Este e-mail foi enviado automaticamente pelo portal
        <a href="https://ppg.comunidadeppg.com.br">Cria da Comunidade</a>.<br>
        Os currículos listados fazem parte do banco de talentos da comunidade.
      </p>
    </div>

  </div>
</div>
</body>
</html>
