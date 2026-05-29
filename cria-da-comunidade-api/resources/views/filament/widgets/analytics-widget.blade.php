<x-filament-widgets::widget>
    <x-filament::section>

        {{-- ══════════════════════════════════════════════
             CABEÇALHO
        ══════════════════════════════════════════════ --}}
        <div class="flex items-start justify-between mb-8">
            <div>
                <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                    📊 Analytics da Plataforma
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Resumo dos últimos 30 dias
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 px-3 py-1.5 rounded-full">
                <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                Tempo real
            </span>
        </div>

        {{-- ══════════════════════════════════════════════
             KPIs — 4 cartões
        ══════════════════════════════════════════════ --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

            {{-- Sessões únicas --}}
            <div class="rounded-2xl border border-indigo-100 dark:border-indigo-900/40 bg-gradient-to-br from-indigo-50 to-white dark:from-indigo-950/40 dark:to-gray-800/40 p-5">
                <p class="text-2xl mb-3">🌐</p>
                <p class="text-3xl font-black font-mono text-indigo-700 dark:text-indigo-300 leading-none">
                    {{ number_format($totalSessions) }}
                </p>
                <p class="text-xs font-bold text-indigo-600 dark:text-indigo-400 mt-2">Sessões únicas</p>
                <p class="text-xs text-gray-400 mt-0.5">visitantes distintos</p>
            </div>

            {{-- Page Views --}}
            <div class="rounded-2xl border border-emerald-100 dark:border-emerald-900/40 bg-gradient-to-br from-emerald-50 to-white dark:from-emerald-950/40 dark:to-gray-800/40 p-5">
                <p class="text-2xl mb-3">👁</p>
                <p class="text-3xl font-black font-mono text-emerald-700 dark:text-emerald-300 leading-none">
                    {{ number_format($totalViews) }}
                </p>
                <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-2">Page views</p>
                <p class="text-xs text-gray-400 mt-0.5">telas abertas</p>
            </div>

            {{-- Total cliques --}}
            <div class="rounded-2xl border border-orange-100 dark:border-orange-900/40 bg-gradient-to-br from-orange-50 to-white dark:from-orange-950/40 dark:to-gray-800/40 p-5">
                <p class="text-2xl mb-3">🖱️</p>
                <p class="text-3xl font-black font-mono text-orange-700 dark:text-orange-300 leading-none">
                    {{ number_format($totalClicks) }}
                </p>
                <p class="text-xs font-bold text-orange-600 dark:text-orange-400 mt-2">Cliques</p>
                <p class="text-xs text-gray-400 mt-0.5">interações totais</p>
            </div>

            {{-- Profissional top --}}
            <div class="rounded-2xl border border-purple-100 dark:border-purple-900/40 bg-gradient-to-br from-purple-50 to-white dark:from-purple-950/40 dark:to-gray-800/40 p-5">
                <p class="text-2xl mb-3">🏆</p>
                @if($topPros->isNotEmpty())
                    <p class="text-sm font-black text-purple-700 dark:text-purple-300 leading-tight line-clamp-2">
                        {{ $topPros->first()->entity_name }}
                    </p>
                    <p class="text-xs font-bold text-purple-600 dark:text-purple-400 mt-2">Pro mais clicado</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $topPros->first()->clicks }} cliques</p>
                @else
                    <p class="text-2xl font-black font-mono text-purple-700 dark:text-purple-300 leading-none">—</p>
                    <p class="text-xs font-bold text-purple-600 dark:text-purple-400 mt-2">Pro mais clicado</p>
                    <p class="text-xs text-gray-400 mt-0.5">sem dados ainda</p>
                @endif
            </div>
        </div>

        {{-- ══════════════════════════════════════════════
             CLIQUES POR TIPO — faixa horizontal
        ══════════════════════════════════════════════ --}}
        <div class="flex flex-wrap items-center gap-3 p-4 mb-8 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700/60">
            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 mr-1 shrink-0">
                Cliques por categoria:
            </span>
            @foreach([
                'profissional' => ['label' => 'Profissionais', 'icon' => '👤', 'bg' => 'bg-orange-100 dark:bg-orange-900/40', 'text' => 'text-orange-700 dark:text-orange-300'],
                'evento'       => ['label' => 'Eventos',       'icon' => '🎉', 'bg' => 'bg-purple-100 dark:bg-purple-900/40', 'text' => 'text-purple-700 dark:text-purple-300'],
                'projeto'      => ['label' => 'Projetos',      'icon' => '❤',  'bg' => 'bg-red-100 dark:bg-red-900/40',    'text' => 'text-red-700 dark:text-red-300'],
                'vaga'         => ['label' => 'Vagas',         'icon' => '💼', 'bg' => 'bg-blue-100 dark:bg-blue-900/40',  'text' => 'text-blue-700 dark:text-blue-300'],
            ] as $type => $meta)
            @php $count = $clicksByType[$type] ?? 0; @endphp
            <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 shadow-sm">
                <span class="text-base leading-none">{{ $meta['icon'] }}</span>
                <span class="text-base font-black font-mono {{ $meta['text'] }}">{{ $count }}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $meta['label'] }}</span>
            </div>
            @endforeach
        </div>

        {{-- ══════════════════════════════════════════════
             GRÁFICO DE BARRAS — acessos diários
        ══════════════════════════════════════════════ --}}
        @if($dailyViews->isNotEmpty())
        <div class="p-5 mb-8 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700/60">
            <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-5">
                📈 Acessos diários — últimos 14 dias
            </h3>
            @php
                $maxViews = $dailyViews->max('views') ?: 1;
                $barMaxPx = 120; // altura máxima da barra em px
            @endphp
            <div class="flex items-end gap-1.5" style="height: {{ $barMaxPx + 32 }}px">
                @foreach($dailyViews as $day)
                @php
                    $barPx = max(3, (int) round(($day->views / $maxViews) * $barMaxPx));
                @endphp
                <div class="flex-1 flex flex-col items-center" style="height: {{ $barMaxPx + 32 }}px; justify-content: flex-end;">
                    {{-- Valor acima da barra --}}
                    <span class="text-[9px] font-bold font-mono text-orange-500 dark:text-orange-400 mb-1"
                          style="visibility: {{ $day->views > 0 ? 'visible' : 'hidden' }}">
                        {{ $day->views }}
                    </span>
                    {{-- Barra --}}
                    <div
                        class="w-full rounded-t-md bg-orange-400 dark:bg-orange-500 hover:bg-orange-500 dark:hover:bg-orange-400 transition-colors cursor-default"
                        style="height: {{ $barPx }}px"
                        title="{{ \Carbon\Carbon::parse($day->day)->translatedFormat('d/m') }}: {{ $day->views }} acessos"
                    ></div>
                    {{-- Label data --}}
                    <span class="text-[9px] font-mono text-gray-400 dark:text-gray-500 mt-1.5 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($day->day)->format('d/m') }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ══════════════════════════════════════════════
             TABELAS — Telas + Profissionais
        ══════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- Telas mais visitadas --}}
            <div class="rounded-2xl border border-gray-100 dark:border-gray-700/60 bg-white dark:bg-gray-800/40 p-5">
                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-5 flex items-center gap-2">
                    <span class="text-base">🖥️</span>
                    <span>Telas mais visitadas</span>
                    @if($topScreens->isNotEmpty())
                    <span class="ml-auto text-xs font-medium text-gray-400 font-mono">views</span>
                    @endif
                </h3>
                @if($topScreens->isEmpty())
                    <div class="flex flex-col items-center py-8 gap-2 text-gray-400">
                        <span class="text-3xl">📭</span>
                        <span class="text-sm">Nenhum acesso ainda</span>
                    </div>
                @else
                @php $maxScreen = $topScreens->max('views') ?: 1; @endphp
                <div class="space-y-4">
                    @foreach($topScreens as $i => $item)
                    <div class="flex items-center gap-3">
                        {{-- Posição --}}
                        <span class="w-6 h-6 flex items-center justify-center rounded-lg
                            {{ $i === 0 ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-500' }}
                            text-[10px] font-bold font-mono shrink-0">
                            {{ $i + 1 }}
                        </span>
                        {{-- Nome + barra --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline justify-between gap-2 mb-1.5">
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                                    {{ $item->screen_label }}
                                </span>
                                <span class="text-xs font-bold font-mono text-blue-600 dark:text-blue-400 shrink-0">
                                    {{ $item->views }}
                                </span>
                            </div>
                            <div class="h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500 dark:bg-blue-400 rounded-full"
                                     style="width: {{ round(($item->views / $maxScreen) * 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Profissionais mais clicados --}}
            <div class="rounded-2xl border border-gray-100 dark:border-gray-700/60 bg-white dark:bg-gray-800/40 p-5">
                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-5 flex items-center gap-2">
                    <span class="text-base">👤</span>
                    <span>Profissionais mais clicados</span>
                    @if($topPros->isNotEmpty())
                    <span class="ml-auto text-xs font-medium text-gray-400 font-mono">cliques</span>
                    @endif
                </h3>
                @if($topPros->isEmpty())
                    <div class="flex flex-col items-center py-8 gap-2 text-gray-400">
                        <span class="text-3xl">📭</span>
                        <span class="text-sm">Nenhum clique ainda</span>
                    </div>
                @else
                @php $maxPro = $topPros->max('clicks') ?: 1; @endphp
                <div class="space-y-4">
                    @foreach($topPros as $i => $item)
                    <div class="flex items-center gap-3">
                        {{-- Posição --}}
                        <span class="w-6 h-6 flex items-center justify-center rounded-lg
                            {{ $i === 0 ? 'bg-orange-100 dark:bg-orange-900/50 text-orange-700 dark:text-orange-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-500' }}
                            text-[10px] font-bold font-mono shrink-0">
                            {{ $i + 1 }}
                        </span>
                        {{-- Nome + barra --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline justify-between gap-2 mb-1.5">
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                                    {{ $item->entity_name ?? '—' }}
                                </span>
                                <span class="text-xs font-bold font-mono text-orange-600 dark:text-orange-400 shrink-0">
                                    {{ $item->clicks }}
                                </span>
                            </div>
                            <div class="h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-orange-500 dark:bg-orange-400 rounded-full"
                                     style="width: {{ round(($item->clicks / $maxPro) * 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- ══════════════════════════════════════════════
             RODAPÉ
        ══════════════════════════════════════════════ --}}
        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100 dark:border-gray-700/60">
            <span class="text-xs text-gray-400 dark:text-gray-500">
                Dados coletados nos últimos 30 dias
            </span>
            <span class="inline-flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500">
                <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                Atualizado em tempo real
            </span>
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
