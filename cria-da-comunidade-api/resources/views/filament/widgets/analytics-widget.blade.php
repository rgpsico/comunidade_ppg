<x-filament-widgets::widget>
    <x-filament::section>

        {{-- ── Cabeçalho principal ── --}}
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-base font-bold text-gray-900 dark:text-white">📊 Analytics da Plataforma</h2>
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 px-2.5 py-1 rounded-full">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                Ao vivo · 30 dias
            </span>
        </div>

        {{-- ── KPIs ── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">

            <div class="rounded-xl border border-indigo-100 dark:border-indigo-900/40 bg-indigo-50 dark:bg-indigo-950/30 p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-lg">🌐</span>
                    <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider">Sessões únicas</p>
                </div>
                <p class="text-3xl font-black font-mono text-indigo-700 dark:text-indigo-200 leading-none">{{ number_format($totalSessions) }}</p>
                <p class="text-[11px] text-indigo-400 mt-1">visitantes distintos</p>
            </div>

            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/40 bg-emerald-50 dark:bg-emerald-950/30 p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-lg">👁</span>
                    <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Page views</p>
                </div>
                <p class="text-3xl font-black font-mono text-emerald-700 dark:text-emerald-200 leading-none">{{ number_format($totalViews) }}</p>
                <p class="text-[11px] text-emerald-500 mt-1">telas abertas</p>
            </div>

            <div class="rounded-xl border border-orange-100 dark:border-orange-900/40 bg-orange-50 dark:bg-orange-950/30 p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-lg">🖱️</span>
                    <p class="text-[10px] font-bold text-orange-500 uppercase tracking-wider">Cliques totais</p>
                </div>
                <p class="text-3xl font-black font-mono text-orange-700 dark:text-orange-200 leading-none">{{ number_format($totalClicks) }}</p>
                <p class="text-[11px] text-orange-400 mt-1">interações</p>
            </div>

            <div class="rounded-xl border border-purple-100 dark:border-purple-900/40 bg-purple-50 dark:bg-purple-950/30 p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-lg">🏆</span>
                    <p class="text-[10px] font-bold text-purple-500 uppercase tracking-wider">Pro do mês</p>
                </div>
                @if($topPros->isNotEmpty())
                    <p class="text-sm font-black text-purple-700 dark:text-purple-200 leading-tight line-clamp-2">{{ $topPros->first()->entity_name }}</p>
                    <p class="text-[11px] text-purple-400 mt-1">{{ $topPros->first()->clicks }} cliques</p>
                @else
                    <p class="text-3xl font-black font-mono text-purple-200 dark:text-purple-700 leading-none">—</p>
                    <p class="text-[11px] text-purple-400 mt-1">sem dados ainda</p>
                @endif
            </div>

        </div>

        {{-- ── Card: Gráfico de barras ── --}}
        @if($dailyViews->isNotEmpty())
        <div class="rounded-xl border border-gray-200 dark:border-gray-700/60 bg-white dark:bg-gray-800/40 mb-4">
            <div class="flex items-center gap-2 px-5 py-3 border-b border-gray-100 dark:border-gray-700/50">
                <span class="text-sm">📈</span>
                <p class="text-xs font-bold text-gray-700 dark:text-gray-300">Acessos diários — últimos 14 dias</p>
            </div>
            <div class="px-5 py-4">
                @php
                    $maxViews = $dailyViews->max('views') ?: 1;
                    $chartH   = 80;
                @endphp
                <div style="display: flex; align-items: flex-end; gap: 6px; height: {{ $chartH + 32 }}px;">
                    @foreach($dailyViews as $day)
                    @php $barH = max(4, (int) round(($day->views / $maxViews) * $chartH)); @endphp
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: {{ $chartH + 32 }}px;">
                        <span style="font-size: 9px; font-weight: 800; color: #ea580c; line-height: 1; margin-bottom: 3px;">{{ $day->views }}</span>
                        <div style="width: 100%; height: {{ $barH }}px; background: linear-gradient(to top, #c2410c, #fb923c); border-radius: 4px 4px 0 0;" title="{{ \Carbon\Carbon::parse($day->day)->format('d/m') }}: {{ $day->views }} acessos"></div>
                        <span style="font-size: 9px; color: #9ca3af; margin-top: 5px; white-space: nowrap;">{{ \Carbon\Carbon::parse($day->day)->format('d/m') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- ── Cards: Telas + Profissionais ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">

            {{-- Card Telas --}}
            <div class="rounded-xl border border-gray-200 dark:border-gray-700/60 bg-white dark:bg-gray-800/40 overflow-hidden">
                <div class="flex items-center gap-2 px-4 py-3 border-b border-gray-100 dark:border-gray-700/50 bg-gray-50 dark:bg-gray-800/60">
                    <span class="text-sm">🖥️</span>
                    <p class="text-xs font-bold text-gray-700 dark:text-gray-300">Telas mais visitadas</p>
                    <span class="ml-auto text-[10px] font-semibold text-gray-400 font-mono uppercase">views</span>
                </div>
                @if($topScreens->isEmpty())
                    <p class="text-xs text-gray-400 py-8 text-center">Nenhum acesso ainda</p>
                @else
                @php $maxScreen = $topScreens->max('views') ?: 1; @endphp
                <div class="divide-y divide-gray-50 dark:divide-gray-700/40">
                    @foreach($topScreens as $i => $item)
                    <div class="flex items-center gap-3 px-4 py-2.5">
                        <span class="text-[10px] font-bold font-mono w-4 text-right shrink-0 {{ $i === 0 ? 'text-blue-500' : 'text-gray-300 dark:text-gray-600' }}">{{ $i + 1 }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-200 truncate">{{ $item->screen_label }}</span>
                                <span class="text-[11px] font-black font-mono text-blue-600 dark:text-blue-400 shrink-0">{{ $item->views }}</span>
                            </div>
                            <div class="h-1 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-400 rounded-full" style="width: {{ round(($item->views / $maxScreen) * 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Card Profissionais --}}
            <div class="rounded-xl border border-gray-200 dark:border-gray-700/60 bg-white dark:bg-gray-800/40 overflow-hidden">
                <div class="flex items-center gap-2 px-4 py-3 border-b border-gray-100 dark:border-gray-700/50 bg-gray-50 dark:bg-gray-800/60">
                    <span class="text-sm">👤</span>
                    <p class="text-xs font-bold text-gray-700 dark:text-gray-300">Profissionais mais clicados</p>
                    <span class="ml-auto text-[10px] font-semibold text-gray-400 font-mono uppercase">cliques</span>
                </div>
                @if($topPros->isEmpty())
                    <p class="text-xs text-gray-400 py-8 text-center">Nenhum clique ainda</p>
                @else
                @php $maxPro = $topPros->max('clicks') ?: 1; @endphp
                <div class="divide-y divide-gray-50 dark:divide-gray-700/40">
                    @foreach($topPros as $i => $item)
                    <div class="flex items-center gap-3 px-4 py-2.5">
                        <span class="text-[10px] font-bold font-mono w-4 text-right shrink-0 {{ $i === 0 ? 'text-orange-500' : 'text-gray-300 dark:text-gray-600' }}">{{ $i + 1 }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-200 truncate">{{ $item->entity_name ?? '—' }}</span>
                                <span class="text-[11px] font-black font-mono text-orange-600 dark:text-orange-400 shrink-0">{{ $item->clicks }}</span>
                            </div>
                            <div class="h-1 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-orange-400 rounded-full" style="width: {{ round(($item->clicks / $maxPro) * 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- ── Card: Cliques por categoria ── --}}
        <div class="rounded-xl border border-gray-200 dark:border-gray-700/60 bg-white dark:bg-gray-800/40 overflow-hidden">
            <div class="flex items-center gap-2 px-4 py-3 border-b border-gray-100 dark:border-gray-700/50 bg-gray-50 dark:bg-gray-800/60">
                <span class="text-sm">🖱️</span>
                <p class="text-xs font-bold text-gray-700 dark:text-gray-300">Cliques por categoria</p>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 divide-x divide-y lg:divide-y-0 divide-gray-100 dark:divide-gray-700/40">
                @foreach([
                    ['profissional', '👤', 'Profissionais', 'text-orange-700 dark:text-orange-300', 'text-orange-400'],
                    ['evento',       '🎉', 'Eventos',       'text-purple-700 dark:text-purple-300', 'text-purple-400'],
                    ['projeto',      '❤',  'Projetos',      'text-red-700 dark:text-red-300',       'text-red-400'],
                    ['vaga',         '💼', 'Vagas',         'text-blue-700 dark:text-blue-300',     'text-blue-400'],
                ] as [$type, $icon, $label, $numCls, $lblCls])
                @php $n = $clicksByType[$type] ?? 0; @endphp
                <div class="flex items-center gap-3 px-5 py-4">
                    <span class="text-2xl leading-none shrink-0">{{ $icon }}</span>
                    <div>
                        <p class="text-xl font-black font-mono {{ $numCls }} leading-none">{{ $n }}</p>
                        <p class="text-[10px] font-semibold {{ $lblCls }} mt-0.5">{{ $label }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
