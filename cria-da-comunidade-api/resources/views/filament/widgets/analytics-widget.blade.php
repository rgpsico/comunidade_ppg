<x-filament-widgets::widget>
    <x-filament::section padding="none">
    <div style="background:#111827; border-radius:12px; padding:24px; color:#f9fafb; font-family:system-ui,sans-serif;">

        {{-- ── 4 KPIs ── --}}
        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:20px;">

            <div style="background:#1e2433; border:1px solid rgba(255,255,255,0.07); border-radius:12px; padding:16px; display:flex; align-items:center; gap:14px;">
                <div style="width:44px; height:44px; background:#f97316; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="20" height="20" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
                </div>
                <div>
                    <p style="font-size:11px; color:#9ca3af; margin:0 0 4px; font-weight:500;">Visitas totais</p>
                    <p style="font-size:26px; font-weight:900; margin:0; line-height:1; font-family:monospace;">{{ number_format($totalViews) }}</p>
                </div>
            </div>

            <div style="background:#1e2433; border:1px solid rgba(255,255,255,0.07); border-radius:12px; padding:16px; display:flex; align-items:center; gap:14px;">
                <div style="width:44px; height:44px; background:#f97316; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="20" height="20" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                </div>
                <div>
                    <p style="font-size:11px; color:#9ca3af; margin:0 0 4px; font-weight:500;">Telas acessadas</p>
                    <p style="font-size:26px; font-weight:900; margin:0; line-height:1; font-family:monospace;">{{ $topScreens->count() }}</p>
                </div>
            </div>

            <div style="background:#1e2433; border:1px solid rgba(255,255,255,0.07); border-radius:12px; padding:16px; display:flex; align-items:center; gap:14px;">
                <div style="width:44px; height:44px; background:#f97316; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="20" height="20" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 10-16 0"/></svg>
                </div>
                <div>
                    <p style="font-size:11px; color:#9ca3af; margin:0 0 4px; font-weight:500;">Profissionais clicados</p>
                    <p style="font-size:26px; font-weight:900; margin:0; line-height:1; font-family:monospace;">{{ $clicksByType['profissional'] ?? 0 }}</p>
                </div>
            </div>

            <div style="background:#1e2433; border:1px solid rgba(255,255,255,0.07); border-radius:12px; padding:16px; display:flex; align-items:center; gap:14px;">
                <div style="width:44px; height:44px; background:#f97316; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="20" height="20" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                </div>
                <div>
                    <p style="font-size:11px; color:#9ca3af; margin:0 0 4px; font-weight:500;">Categorias</p>
                    <p style="font-size:26px; font-weight:900; margin:0; line-height:1; font-family:monospace;">{{ $clicksByType->filter(fn($v) => $v > 0)->count() }}</p>
                </div>
            </div>

        </div>

        {{-- ── Gráfico: Visitas por dia ── --}}
        @if($dailyViews->isNotEmpty())
        @php
            $maxV   = $dailyViews->max('views') ?: 1;
            $chartH = 120;
            $steps  = [0, round($maxV * 0.25), round($maxV * 0.5), round($maxV * 0.75), $maxV];
        @endphp
        <div style="background:#1e2433; border:1px solid rgba(255,255,255,0.07); border-radius:12px; padding:20px; margin-bottom:20px;">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:20px;">
                <svg width="16" height="16" fill="#f97316" viewBox="0 0 24 24"><rect x="2" y="12" width="4" height="10"/><rect x="10" y="7" width="4" height="15"/><rect x="18" y="3" width="4" height="19"/></svg>
                <p style="font-size:13px; font-weight:700; margin:0;">Visitas por dia</p>
            </div>

            <div style="position:relative; padding-left:28px;">
                {{-- Gridlines --}}
                <div style="position:absolute; inset:0 0 20px 28px; display:flex; flex-direction:column; justify-content:space-between; pointer-events:none;">
                    @foreach(array_reverse($steps) as $step)
                    <div style="display:flex; align-items:center; gap:6px;">
                        <span style="font-size:9px; color:#4b5563; width:22px; text-align:right; margin-left:-28px;">{{ $step }}</span>
                        <div style="flex:1; height:1px; background:rgba(255,255,255,0.05);"></div>
                    </div>
                    @endforeach
                </div>

                {{-- Barras --}}
                <div style="display:flex; align-items:flex-end; gap:8px; height:{{ $chartH + 24 }}px; padding-bottom:24px; position:relative; z-index:1;">
                    @foreach($dailyViews as $day)
                    @php $barH = max(4, (int) round(($day->views / $maxV) * $chartH)); @endphp
                    <div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:flex-end; height:100%;">
                        <span style="font-size:10px; font-weight:800; color:#f9fafb; margin-bottom:4px; line-height:1;">{{ $day->views }}</span>
                        <div style="width:100%; height:{{ $barH }}px; background:#f97316; border-radius:4px 4px 0 0;" title="{{ \Carbon\Carbon::parse($day->day)->format('d/m') }}: {{ $day->views }}"></div>
                    </div>
                    @endforeach
                </div>

                {{-- Labels datas --}}
                <div style="display:flex; gap:8px; padding-left:0; margin-top:4px;">
                    @foreach($dailyViews as $day)
                    <div style="flex:1; text-align:center;">
                        <span style="font-size:9px; color:#6b7280; white-space:nowrap;">{{ \Carbon\Carbon::parse($day->day)->format('d/m') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- ── Rankings: Telas + Profissionais ── --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">

            {{-- Telas --}}
            <div style="background:#1e2433; border:1px solid rgba(255,255,255,0.07); border-radius:12px; overflow:hidden;">
                <div style="display:flex; align-items:center; gap:8px; padding:16px 20px; border-bottom:1px solid rgba(255,255,255,0.06);">
                    <svg width="16" height="16" fill="none" stroke="#f97316" stroke-width="2" viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                    <p style="font-size:13px; font-weight:700; margin:0;">Telas mais visitadas</p>
                </div>
                @php $maxSc = $topScreens->take(5)->max('views') ?: 1; @endphp
                @foreach($topScreens->take(5) as $i => $item)
                <div style="display:flex; align-items:center; gap:12px; padding:10px 20px; border-bottom:1px solid rgba(255,255,255,0.04);">
                    <span style="font-size:11px; font-weight:700; color:{{ $i === 0 ? '#f97316' : '#4b5563' }}; width:16px; text-align:center; flex-shrink:0;">{{ $i + 1 }}</span>
                    <span style="font-size:12px; color:#e5e7eb; width:90px; flex-shrink:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $item->screen_label }}</span>
                    <div style="flex:1; height:6px; background:rgba(255,255,255,0.08); border-radius:99px; overflow:hidden;">
                        <div style="height:100%; width:{{ round(($item->views / $maxSc) * 100) }}%; background:#f97316; border-radius:99px;"></div>
                    </div>
                    <span style="font-size:12px; font-weight:800; color:#f97316; font-family:monospace; width:28px; text-align:right; flex-shrink:0;">{{ $item->views }}</span>
                </div>
                @endforeach
                <div style="padding:12px 20px; text-align:center;">
                    <span style="font-size:12px; color:#f97316; font-weight:600; cursor:default;">Ver ranking completo &rsaquo;</span>
                </div>
            </div>

            {{-- Profissionais --}}
            <div style="background:#1e2433; border:1px solid rgba(255,255,255,0.07); border-radius:12px; overflow:hidden;">
                <div style="display:flex; align-items:center; gap:8px; padding:16px 20px; border-bottom:1px solid rgba(255,255,255,0.06);">
                    <svg width="16" height="16" fill="none" stroke="#f97316" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 10-16 0"/></svg>
                    <p style="font-size:13px; font-weight:700; margin:0;">Profissionais mais clicados</p>
                </div>
                @if($topPros->isEmpty())
                    <p style="font-size:12px; color:#6b7280; padding:24px; text-align:center;">Nenhum clique ainda</p>
                @else
                @php $maxPr = $topPros->take(5)->max('clicks') ?: 1; @endphp
                @foreach($topPros->take(5) as $i => $item)
                <div style="display:flex; align-items:center; gap:12px; padding:10px 20px; border-bottom:1px solid rgba(255,255,255,0.04);">
                    <span style="font-size:11px; font-weight:700; color:{{ $i === 0 ? '#f97316' : '#4b5563' }}; width:16px; text-align:center; flex-shrink:0;">{{ $i + 1 }}</span>
                    <span style="font-size:12px; color:#e5e7eb; width:110px; flex-shrink:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $item->entity_name ?? '—' }}</span>
                    <div style="flex:1; height:6px; background:rgba(255,255,255,0.08); border-radius:99px; overflow:hidden;">
                        <div style="height:100%; width:{{ round(($item->clicks / $maxPr) * 100) }}%; background:#f97316; border-radius:99px;"></div>
                    </div>
                    <span style="font-size:12px; font-weight:800; color:#f97316; font-family:monospace; width:20px; text-align:right; flex-shrink:0;">{{ $item->clicks }}</span>
                </div>
                @endforeach
                @endif
                <div style="padding:12px 20px; text-align:center;">
                    <span style="font-size:12px; color:#f97316; font-weight:600; cursor:default;">Ver ranking completo &rsaquo;</span>
                </div>
            </div>
        </div>

        {{-- ── Cliques por categoria (barras horizontais) ── --}}
        <div style="background:#1e2433; border:1px solid rgba(255,255,255,0.07); border-radius:12px; overflow:hidden;">
            <div style="display:flex; align-items:center; gap:8px; padding:16px 20px; border-bottom:1px solid rgba(255,255,255,0.06);">
                <svg width="16" height="16" fill="none" stroke="#f97316" stroke-width="2" viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                <p style="font-size:13px; font-weight:700; margin:0;">Cliques por categoria</p>
            </div>
            @php $totalV = $totalViews ?: 1; @endphp
            <div style="padding:16px 20px; display:flex; flex-direction:column; gap:10px;">
                @foreach($topScreens as $item)
                @php $pct = round(($item->views / $totalV) * 100, 1); @endphp
                <div style="display:flex; align-items:center; gap:12px;">
                    <span style="font-size:12px; color:#d1d5db; width:110px; flex-shrink:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $item->screen_label }}</span>
                    <div style="flex:1; height:8px; background:rgba(255,255,255,0.06); border-radius:99px; overflow:hidden;">
                        <div style="height:100%; width:{{ round(($item->views / ($topScreens->max('views') ?: 1)) * 100) }}%; background:#f97316; border-radius:99px;"></div>
                    </div>
                    <span style="font-size:11px; font-weight:800; color:#f9fafb; font-family:monospace; width:36px; text-align:right; flex-shrink:0;">{{ $item->views }}</span>
                    <span style="font-size:11px; color:#6b7280; width:44px; flex-shrink:0;">({{ $pct }}%)</span>
                </div>
                @endforeach
            </div>
        </div>

    </div>
    </x-filament::section>
</x-filament-widgets::widget>
