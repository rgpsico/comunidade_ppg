<x-filament-widgets::widget>
    <x-filament::section>

        {{-- ── Cabeçalho ── --}}
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
            <div>
                <h2 style="font-size:15px; font-weight:800; margin:0; color:inherit;">📊 Analytics da Plataforma</h2>
                <p style="font-size:11px; color:#9ca3af; margin:3px 0 0;">Últimos 30 dias</p>
            </div>
            <span style="display:inline-flex; align-items:center; gap:6px; font-size:11px; font-weight:700; color:#10b981; background:#f0fdf4; border:1px solid #bbf7d0; padding:4px 10px; border-radius:99px;">
                <span style="width:6px; height:6px; background:#10b981; border-radius:50%; animation:pulse 1.5s infinite;"></span>
                Ao vivo
            </span>
        </div>

        {{-- ── KPIs ── --}}
        <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:12px; margin-bottom:16px;">

            <div style="border-radius:12px; border:1px solid #c7d2fe; background:#eef2ff; padding:16px;">
                <p style="font-size:10px; font-weight:700; color:#6366f1; text-transform:uppercase; letter-spacing:.06em; margin:0 0 8px;">🌐 Sessões únicas</p>
                <p style="font-size:28px; font-weight:900; font-family:monospace; color:#3730a3; line-height:1; margin:0;">{{ number_format($totalSessions) }}</p>
                <p style="font-size:11px; color:#a5b4fc; margin:4px 0 0;">visitantes distintos</p>
            </div>

            <div style="border-radius:12px; border:1px solid #a7f3d0; background:#ecfdf5; padding:16px;">
                <p style="font-size:10px; font-weight:700; color:#059669; text-transform:uppercase; letter-spacing:.06em; margin:0 0 8px;">👁 Page views</p>
                <p style="font-size:28px; font-weight:900; font-family:monospace; color:#065f46; line-height:1; margin:0;">{{ number_format($totalViews) }}</p>
                <p style="font-size:11px; color:#6ee7b7; margin:4px 0 0;">telas abertas</p>
            </div>

            <div style="border-radius:12px; border:1px solid #fed7aa; background:#fff7ed; padding:16px;">
                <p style="font-size:10px; font-weight:700; color:#ea580c; text-transform:uppercase; letter-spacing:.06em; margin:0 0 8px;">🖱️ Cliques totais</p>
                <p style="font-size:28px; font-weight:900; font-family:monospace; color:#9a3412; line-height:1; margin:0;">{{ number_format($totalClicks) }}</p>
                <p style="font-size:11px; color:#fdba74; margin:4px 0 0;">interações</p>
            </div>

            <div style="border-radius:12px; border:1px solid #e9d5ff; background:#faf5ff; padding:16px;">
                <p style="font-size:10px; font-weight:700; color:#9333ea; text-transform:uppercase; letter-spacing:.06em; margin:0 0 8px;">🏆 Pro do mês</p>
                @if($topPros->isNotEmpty())
                    <p style="font-size:13px; font-weight:900; color:#581c87; line-height:1.2; margin:0;">{{ $topPros->first()->entity_name }}</p>
                    <p style="font-size:11px; color:#c4b5fd; margin:4px 0 0;">{{ $topPros->first()->clicks }} cliques</p>
                @else
                    <p style="font-size:28px; font-weight:900; font-family:monospace; color:#d8b4fe; line-height:1; margin:0;">—</p>
                    <p style="font-size:11px; color:#c4b5fd; margin:4px 0 0;">sem dados ainda</p>
                @endif
            </div>

        </div>

        {{-- ── Card: Gráfico de barras ── --}}
        @if($dailyViews->isNotEmpty())
        <div style="border-radius:12px; border:1px solid #e5e7eb; background:#fff; margin-bottom:16px; overflow:hidden;">
            <div style="display:flex; align-items:center; gap:8px; padding:12px 16px; border-bottom:1px solid #f3f4f6; background:#f9fafb;">
                <span style="font-size:13px;">📈</span>
                <p style="font-size:12px; font-weight:700; margin:0; color:#374151;">Acessos diários — últimos 14 dias</p>
            </div>
            <div style="padding:16px 20px;">
                @php
                    $maxViews = $dailyViews->max('views') ?: 1;
                    $chartH   = 80;
                @endphp
                <div style="display:flex; align-items:flex-end; gap:6px; height:{{ $chartH + 32 }}px;">
                    @foreach($dailyViews as $day)
                    @php $barH = max(4, (int) round(($day->views / $maxViews) * $chartH)); @endphp
                    <div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:flex-end; height:{{ $chartH + 32 }}px;">
                        <span style="font-size:9px; font-weight:800; color:#ea580c; line-height:1; margin-bottom:3px;">{{ $day->views }}</span>
                        <div style="width:100%; height:{{ $barH }}px; background:linear-gradient(to top,#c2410c,#fb923c); border-radius:4px 4px 0 0;" title="{{ \Carbon\Carbon::parse($day->day)->format('d/m') }}: {{ $day->views }} acessos"></div>
                        <span style="font-size:9px; color:#9ca3af; margin-top:5px; white-space:nowrap;">{{ \Carbon\Carbon::parse($day->day)->format('d/m') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- ── Cards: Telas + Profissionais ── --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">

            {{-- Card Telas --}}
            <div style="border-radius:12px; border:1px solid #e5e7eb; background:#fff; overflow:hidden;">
                <div style="display:flex; align-items:center; gap:8px; padding:12px 16px; border-bottom:1px solid #f3f4f6; background:#f9fafb;">
                    <span style="font-size:13px;">🖥️</span>
                    <p style="font-size:12px; font-weight:700; margin:0; color:#374151;">Telas mais visitadas</p>
                    <span style="margin-left:auto; font-size:10px; font-weight:700; color:#9ca3af;">VIEWS</span>
                </div>
                @if($topScreens->isEmpty())
                    <p style="font-size:12px; color:#9ca3af; padding:24px; text-align:center;">Nenhum acesso ainda</p>
                @else
                @php $maxScreen = $topScreens->max('views') ?: 1; @endphp
                @foreach($topScreens as $i => $item)
                <div style="display:flex; align-items:center; gap:10px; padding:10px 16px; border-top:{{ $i > 0 ? '1px solid #f3f4f6' : 'none' }};">
                    <span style="font-size:10px; font-weight:900; font-family:monospace; width:14px; text-align:right; flex-shrink:0; color:{{ $i === 0 ? '#3b82f6' : '#d1d5db' }};">{{ $i + 1 }}</span>
                    <div style="flex:1; min-width:0;">
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:4px;">
                            <span style="font-size:12px; font-weight:600; color:#1f2937; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $item->screen_label }}</span>
                            <span style="font-size:11px; font-weight:900; font-family:monospace; color:#2563eb; flex-shrink:0;">{{ $item->views }}</span>
                        </div>
                        <div style="height:4px; background:#f3f4f6; border-radius:99px; overflow:hidden;">
                            <div style="height:100%; width:{{ round(($item->views / $maxScreen) * 100) }}%; background:#60a5fa; border-radius:99px;"></div>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
            </div>

            {{-- Card Profissionais --}}
            <div style="border-radius:12px; border:1px solid #e5e7eb; background:#fff; overflow:hidden;">
                <div style="display:flex; align-items:center; gap:8px; padding:12px 16px; border-bottom:1px solid #f3f4f6; background:#f9fafb;">
                    <span style="font-size:13px;">👤</span>
                    <p style="font-size:12px; font-weight:700; margin:0; color:#374151;">Profissionais mais clicados</p>
                    <span style="margin-left:auto; font-size:10px; font-weight:700; color:#9ca3af;">CLIQUES</span>
                </div>
                @if($topPros->isEmpty())
                    <p style="font-size:12px; color:#9ca3af; padding:24px; text-align:center;">Nenhum clique ainda</p>
                @else
                @php $maxPro = $topPros->max('clicks') ?: 1; @endphp
                @foreach($topPros as $i => $item)
                <div style="display:flex; align-items:center; gap:10px; padding:10px 16px; border-top:{{ $i > 0 ? '1px solid #f3f4f6' : 'none' }};">
                    <span style="font-size:10px; font-weight:900; font-family:monospace; width:14px; text-align:right; flex-shrink:0; color:{{ $i === 0 ? '#f97316' : '#d1d5db' }};">{{ $i + 1 }}</span>
                    <div style="flex:1; min-width:0;">
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:4px;">
                            <span style="font-size:12px; font-weight:600; color:#1f2937; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $item->entity_name ?? '—' }}</span>
                            <span style="font-size:11px; font-weight:900; font-family:monospace; color:#ea580c; flex-shrink:0;">{{ $item->clicks }}</span>
                        </div>
                        <div style="height:4px; background:#f3f4f6; border-radius:99px; overflow:hidden;">
                            <div style="height:100%; width:{{ round(($item->clicks / $maxPro) * 100) }}%; background:#fb923c; border-radius:99px;"></div>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </div>

        {{-- ── Card: Cliques por categoria ── --}}
        <div style="border-radius:12px; border:1px solid #e5e7eb; background:#fff; overflow:hidden;">
            <div style="display:flex; align-items:center; gap:8px; padding:12px 16px; border-bottom:1px solid #f3f4f6; background:#f9fafb;">
                <span style="font-size:13px;">🖱️</span>
                <p style="font-size:12px; font-weight:700; margin:0; color:#374151;">Cliques por categoria</p>
            </div>
            <div style="display:grid; grid-template-columns:repeat(4,1fr);">
                @foreach([
                    ['profissional', '👤', 'Profissionais', '#ea580c', '#fff7ed', '#fed7aa'],
                    ['evento',       '🎉', 'Eventos',       '#9333ea', '#faf5ff', '#e9d5ff'],
                    ['projeto',      '❤',  'Projetos',      '#dc2626', '#fef2f2', '#fecaca'],
                    ['vaga',         '💼', 'Vagas',         '#2563eb', '#eff6ff', '#bfdbfe'],
                ] as [$i, [$type, $icon, $label, $color, $bg, $border]])
                @php $n = $clicksByType[$type] ?? 0; @endphp
                <div style="display:flex; align-items:center; gap:12px; padding:16px 20px; border-left:{{ $i > 0 ? '1px solid #f3f4f6' : 'none' }}; background:{{ $bg }};">
                    <span style="font-size:22px; line-height:1;">{{ $icon }}</span>
                    <div>
                        <p style="font-size:22px; font-weight:900; font-family:monospace; color:{{ $color }}; line-height:1; margin:0;">{{ $n }}</p>
                        <p style="font-size:10px; font-weight:700; color:{{ $color }}; opacity:.7; margin:3px 0 0; text-transform:uppercase; letter-spacing:.04em;">{{ $label }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
