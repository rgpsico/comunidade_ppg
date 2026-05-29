<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Candidatura: {{ $vaga->titulo }}</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { background: #f4f4f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #18181b; }
    .wrapper { max-width: 600px; margin: 32px auto; }
    .card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
    .header { background: linear-gradient(135deg, #0f0e0c 0%, #1a1816 100%); padding: 32px 36px; }
    .logo-row { display: flex; align-items: center; gap: 10px; margin-bottom: 24px; }
    .logo-mark { width: 32px; height: 32px; background: #FF5E1A; border-radius: 8px; transform: rotate(-4deg); display: inline-block; position: relative; }
    .logo-text { color: #f5f0e8; font-size: 16px; font-weight: 800; letter-spacing: -0.02em; }
    .badge { display: inline-block; background: rgba(255,94,26,0.2); border: 1px solid rgba(255,94,26,0.3); color: #FF5E1A; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; padding: 4px 10px; border-radius: 999px; margin-bottom: 12px; }
    .header h1 { color: #f5f0e8; font-size: 24px; font-weight: 800; letter-spacing: -0.03em; line-height: 1.2; margin-bottom: 4px; }
    .header .company { color: #888; font-size: 14px; margin-top: 6px; }
    .body { padding: 32px 36px; }
    .section-title { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #71717a; margin-bottom: 14px; }
    .candidate-card { background: #f9f9f9; border: 1px solid #e4e4e7; border-radius: 12px; padding: 20px; margin-bottom: 24px; }
    .candidate-name { font-size: 20px; font-weight: 800; letter-spacing: -0.02em; margin-bottom: 12px; }
    .info-row { display: flex; align-items: center; gap: 8px; font-size: 14px; color: #3f3f46; margin-bottom: 8px; }
    .info-icon { width: 20px; height: 20px; background: #e4e4e7; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 11px; flex-shrink: 0; }
    .message-box { background: #fff7ed; border: 1px solid #fed7aa; border-left: 3px solid #FF5E1A; border-radius: 0 10px 10px 0; padding: 16px; margin-bottom: 24px; }
    .message-box p { font-size: 14px; color: #431407; line-height: 1.6; }
    .vaga-box { background: #f4f4f5; border-radius: 10px; padding: 16px 20px; margin-bottom: 24px; }
    .vaga-box .vaga-title { font-size: 15px; font-weight: 700; margin-bottom: 4px; }
    .vaga-meta { font-size: 12px; color: #71717a; display: flex; gap: 12px; flex-wrap: wrap; }
    .btn-reply { display: inline-block; background: #FF5E1A; color: #fff !important; font-size: 14px; font-weight: 700; padding: 12px 24px; border-radius: 10px; text-decoration: none; margin-bottom: 8px; }
    .footer { background: #f4f4f5; padding: 20px 36px; text-align: center; }
    .footer p { font-size: 12px; color: #a1a1aa; line-height: 1.6; }
    .footer a { color: #71717a; }
  </style>
</head>
<body>
<div class="wrapper">
  <div class="card">

    {{-- Header --}}
    <div class="header">
      <div class="logo-row">
        <span class="logo-mark"></span>
        <span class="logo-text">Cria da Comunidade</span>
      </div>
      <div class="badge">Nova Candidatura</div>
      <h1>{{ $vaga->titulo }}</h1>
      <div class="company">{{ $vaga->empresa }}{{ $vaga->local ? ' · ' . $vaga->local : '' }}</div>
    </div>

    {{-- Body --}}
    <div class="body">

      <div class="section-title">Candidato</div>
      <div class="candidate-card">
        <div class="candidate-name">{{ $candidato->name }}</div>
        <div class="info-row">
          <div class="info-icon">✉</div>
          <span>{{ $candidato->email }}</span>
        </div>
        @if($candidato->whatsapp)
        <div class="info-row">
          <div class="info-icon">📱</div>
          <span>{{ $candidato->whatsapp }}</span>
        </div>
        @endif
      </div>

      <div class="section-title">Mensagem do candidato</div>
      <div class="message-box">
        <p>
          Olá! Vi a vaga de <strong>{{ $vaga->titulo }}</strong> no portal
          <strong>Cria da Comunidade</strong> e tenho interesse em me candidatar.
          Por favor, entre em contato para mais informações.
        </p>
      </div>

      <div class="section-title">Sobre a vaga</div>
      <div class="vaga-box">
        <div class="vaga-title">{{ $vaga->titulo }}</div>
        <div class="vaga-meta">
          <span>{{ $vaga->tipo }}</span>
          @if($vaga->local)<span>📍 {{ $vaga->local }}</span>@endif
          @if($vaga->salario)<span>💰 {{ $vaga->salario }}{{ $vaga->salario_periodo ? '/' . $vaga->salario_periodo : '' }}</span>@endif
        </div>
      </div>

      <a href="mailto:{{ $candidato->email }}" class="btn-reply">
        Responder ao candidato →
      </a>

    </div>

    {{-- Footer --}}
    <div class="footer">
      <p>
        Este e-mail foi enviado automaticamente pelo portal
        <a href="https://ppg.comunidadeppg.com.br">Cria da Comunidade</a>.<br>
        Para responder diretamente ao candidato, clique em "Responder ao candidato"<br>
        ou envie para <a href="mailto:{{ $candidato->email }}">{{ $candidato->email }}</a>.
      </p>
    </div>

  </div>
</div>
</body>
</html>
