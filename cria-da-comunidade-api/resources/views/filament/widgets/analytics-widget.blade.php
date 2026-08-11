<x-filament-widgets::widget>
    <x-filament::section>

        {{-- ── Cabeçalho ── --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white">Analytics</h2>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Últimos 30 dias</p>
            </div>
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 px-2.5 py-1 rounded-full">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                Ao vivo
            </span>
        </div>

        {{-- ── KPIs ── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-8">

            <div class="rounded-xl bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/40 p-4">
                <p class="text-[10px] font-bold text-indigo-400 dark:text-indigo-500 uppercase tracking-widest mb-2">Sessões únicas</p>
                <p class="text-3xl font-black font-mono text-indigo-700 dark:text-indigo-200 leading-none">{{ number_format($totalSessions) }}</p>
                <p class="text-[11px] text-indigo-400 dark:text-indigo-600 mt-1.5">visitantes distintos</p>
            </div>

            <div class="rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/40 p-4">
                <p class="text-[10px] font-bold text-emerald-500 dark:text-emerald-500 uppercase tracking-widest mb-2">Page views</p>
                <p class="text-3xl font-black font-mono text-emerald-700 dark:text-emerald-200 leading-none">{{ number_format($totalViews) }}</p>
                <p class="text-[11px] text-emerald-400 dark:text-emerald-600 mt-1.5">telas abertas</p>
            </div>

            <div class="rounded-xl bg-orange-50 dark:bg-orange-950/30 border border-orange-100 dark:border-orange-900/40 p-4">
                <p class="text-[10px] font-bold text-orange-500 dark:text-orange-500 uppercase tracking-widest mb-2">Cliques</p>
                <p class="text-3xl font-black font-mono text-orange-700 dark:text-orange-200 leading-none">{{ number_format($totalClicks) }}</p>
                <p class="text-[11px] text-orange-400 dark:text-orange-600 mt-1.5">interações totais</p>
            </div>

            <div class="rounded-xl bg-purple-50 dark:bg-purple-950/30 border border-purple-100 dark:border-purple-900/40 p-4">
                <p class="text-[10px] font-bold text-purple-500 dark:text-purple-500 uppercase tracking-widest mb-2">Pro mais clicado</p>
                @if($topPros->isNotEmpty())
                    <p class="text-sm font-black text-purple-700 dark:text-purple-200 leading-tight line-clamp-2">{{ $topPros->first()->entity_name }}</p>
                    <p class="text-[11px] text-purple-400 dark:text-purple-600 mt-1.5">{{ $topPros->first()->clicks }} cliques</p>
                @else
                    <p class="text-3xl font-black font-mono text-purple-200 dark:text-purple-700 leading-none">—</p>
                    <p class="text-[11px] text-purple-400 dark:text-purple-600 mt-1.5">sem dados ainda</p>
                @endif
            </div>

        </div>

        {{-- ── Gráfico de barras (largura total) ── --}}
        @if($dailyViews->isNotEmpty())
        <div class="mb-8">
            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-4">📈 Acessos por dia — últimos 14 dias</p>
            <div class="rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700/50 p-5">
                @php
                    $maxViews = $dailyViews->max('views') ?: 1;
                    $chartH   = 80;
                @endphp
                <div style="display: flex; align-items: flex-end; gap: 6px; height: {{ $chartH + 32 }}px;">
                    @foreach($dailyViews as $day)
                    @php $barH = max(4, (int) round(($day->views / $maxViews) * $chartH)); @endphp
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: {{ $chartH + 32 }}px;">
                        <span style="font-size: 9px; font-weight: 800; color: #ea580c; line-height: 1; margin-bottom: 3px;">{{ $day->views }}</span>
                        <div style="width: 100%; height: {{ $barH }}px; background: linear-gradient(to top, #ea580c, #fb923c); border-radius: 4px 4px 0 0;" title="{{ \Carbon\Carbon::parse($day->day)->format('d/m') }}: {{ $day->views }} acessos"></div>
                        <span style="font-size: 9px; color: #9ca3af; margin-top: 5px; white-space: nowrap;">{{ \Carbon\Carbon::parse($day->day)->format('d/m') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- ── Telas + Profissionais ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-8">

            {{-- Telas mais visitadas --}}
            <div>
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-3">🖥️ Telas mais visitadas</p>
                <div class="rounded-xl bg-white dark:bg-gray-800/40 border border-gray-100 dark:border-gray-700/50 divide-y divide-gray-50 dark:divide-gray-700/50 overflow-hidden">
                    @if($topScreens->isEmpty())
                        <p class="text-xs text-gray-400 py-6 text-center">Nenhum acesso ainda</p>
                    @else
                    @php $maxScreen = $topScreens->max('views') ?: 1; @endphp
                    @foreach($topScreens as $i => $item)
                    <div class="flex items-center gap-3 px-4 py-3">
                        <span class="text-[10px] font-bold font-mono w-4 text-right shrink-0 {{ $i === 0 ? 'text-blue-500' : 'text-gray-300 dark:text-gray-600' }}">{{ $i + 1 }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-200 truncate">{{ $item->screen_label }}</span>
                                <span class="text-[11px] font-black font-mono text-blue-600 dark:text-blue-400 shrink-0">{{ $item->views }}</span>
                            </div>
                            <div class="h-1 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-400 dark:bg-blue-500 rounded-full transition-all"
                                     style="width: {{ round(($item->views / $maxScreen) * 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>

            {{-- Profissionais mais clicados --}}
            <div>
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-3">👤 Profissionais mais clicados</p>
                <div class="rounded-xl bg-white dark:bg-gray-800/40 border border-gray-100 dark:border-gray-700/50 divide-y divide-gray-50 dark:divide-gray-700/50 overflow-hidden">
                    @if($topPros->isEmpty())
                        <p class="text-xs text-gray-400 py-6 text-center">Nenhum clique ainda</p>
                    @else
                    @php $maxPro = $topPros->max('clicks') ?: 1; @endphp
                    @foreach($topPros as $i => $item)
                    <div class="flex items-center gap-3 px-4 py-3">
                        <span class="text-[10px] font-bold font-mono w-4 text-right shrink-0 {{ $i === 0 ? 'text-orange-500' : 'text-gray-300 dark:text-gray-600' }}">{{ $i + 1 }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-200 truncate">{{ $item->entity_name ?? '—' }}</span>
                                <span class="text-[11px] font-black font-mono text-orange-600 dark:text-orange-400 shrink-0">{{ $item->clicks }}</span>
                            </div>
                            <div class="h-1 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-orange-400 dark:bg-orange-500 rounded-full transition-all"
                                     style="width: {{ round(($item->clicks / $maxPro) * 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Cliques por categoria (rodapé simples) ── --}}
        <div>
            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-3">🖱️ Cliques por categoria</p>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach([
                    ['profissional', '👤', 'Profissionais', 'bg-orange-50 dark:bg-orange-950/20 border-orange-100 dark:border-orange-900/30 text-orange-700 dark:text-orange-300'],
                    ['evento',       '🎉', 'Eventos',       'bg-purple-50 dark:bg-purple-950/20 border-purple-100 dark:border-purple-900/30 text-purple-700 dark:text-purple-300'],
                    ['projeto',      '❤',  'Projetos',      'bg-red-50 dark:bg-red-950/20 border-red-100 dark:border-red-900/30 text-red-700 dark:text-red-300'],
                    ['vaga',         '💼', 'Vagas',         'bg-blue-50 dark:bg-blue-950/20 border-blue-100 dark:border-blue-900/30 text-blue-700 dark:text-blue-300'],
                ] as [$type, $icon, $label, $style])
                @php $n = $clicksByType[$type] ?? 0; @endphp
                <div class="rounded-xl border {{ $style }} p-3 flex items-center gap-3">
                    <span class="text-xl leading-none">{{ $icon }}</span>
                    <div>
                        <p class="text-lg font-black font-mono leading-none">{{ $n }}</p>
                        <p class="text-[10px] font-semibold opacity-70 mt-0.5">{{ $label }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
