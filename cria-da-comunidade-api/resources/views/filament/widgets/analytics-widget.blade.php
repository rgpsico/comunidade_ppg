<x-filament-widgets::widget>
    <x-filament::section>

        {{-- ── Cabeçalho ── --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white">
                    📊 Analytics da Plataforma
                </h2>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                    Últimos 30 dias · atualiza em tempo real
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 px-2.5 py-1 rounded-full">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                Ao vivo
            </span>
        </div>

        {{-- ── KPIs ── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">

            <div class="rounded-xl bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/40 p-4">
                <p class="text-[11px] font-semibold text-indigo-500 dark:text-indigo-400 mb-1">🌐 Sessões únicas</p>
                <p class="text-2xl font-black font-mono text-indigo-700 dark:text-indigo-200">{{ number_format($totalSessions) }}</p>
                <p class="text-[11px] text-indigo-400 dark:text-indigo-500 mt-0.5">visitantes distintos</p>
            </div>

            <div class="rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/40 p-4">
                <p class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 mb-1">👁 Page views</p>
                <p class="text-2xl font-black font-mono text-emerald-700 dark:text-emerald-200">{{ number_format($totalViews) }}</p>
                <p class="text-[11px] text-emerald-400 dark:text-emerald-500 mt-0.5">telas abertas</p>
            </div>

            <div class="rounded-xl bg-orange-50 dark:bg-orange-950/30 border border-orange-100 dark:border-orange-900/40 p-4">
                <p class="text-[11px] font-semibold text-orange-600 dark:text-orange-400 mb-1">🖱️ Cliques</p>
                <p class="text-2xl font-black font-mono text-orange-700 dark:text-orange-200">{{ number_format($totalClicks) }}</p>
                <p class="text-[11px] text-orange-400 dark:text-orange-500 mt-0.5">interações totais</p>
            </div>

            <div class="rounded-xl bg-purple-50 dark:bg-purple-950/30 border border-purple-100 dark:border-purple-900/40 p-4">
                <p class="text-[11px] font-semibold text-purple-600 dark:text-purple-400 mb-1">🏆 Pro do mês</p>
                @if($topPros->isNotEmpty())
                    <p class="text-sm font-black text-purple-700 dark:text-purple-200 leading-tight line-clamp-2">{{ $topPros->first()->entity_name }}</p>
                    <p class="text-[11px] text-purple-400 dark:text-purple-500 mt-0.5">{{ $topPros->first()->clicks }} cliques</p>
                @else
                    <p class="text-2xl font-black font-mono text-purple-300 dark:text-purple-600">—</p>
                    <p class="text-[11px] text-purple-400 dark:text-purple-500 mt-0.5">sem dados ainda</p>
                @endif
            </div>
        </div>

        {{-- ── Gráfico diário + Cliques por tipo ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

            {{-- Gráfico de barras --}}
            @if($dailyViews->isNotEmpty())
            <div class="lg:col-span-2 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700/50 p-4">
                <p class="text-[11px] font-bold text-gray-500 dark:text-gray-400 mb-4 uppercase tracking-wider">📈 Acessos diários</p>
                @php
                    $maxViews = $dailyViews->max('views') ?: 1;
                    $chartH   = 72;
                @endphp
                <div style="display: flex; align-items: flex-end; gap: 4px; height: {{ $chartH + 28 }}px;">
                    @foreach($dailyViews as $day)
                    @php $barH = max(3, (int) round(($day->views / $maxViews) * $chartH)); @endphp
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: {{ $chartH + 28 }}px;">
                        <span style="font-size: 8px; font-weight: 700; color: #f97316; line-height: 1; margin-bottom: 2px;">{{ $day->views }}</span>
                        <div style="width: 100%; height: {{ $barH }}px; background: #fb923c; border-radius: 3px 3px 0 0;" title="{{ \Carbon\Carbon::parse($day->day)->format('d/m') }}: {{ $day->views }} acessos"></div>
                        <span style="font-size: 8px; color: #9ca3af; margin-top: 4px; white-space: nowrap;">{{ \Carbon\Carbon::parse($day->day)->format('d/m') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Cliques por tipo --}}
            <div class="rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700/50 p-4">
                <p class="text-[11px] font-bold text-gray-500 dark:text-gray-400 mb-4 uppercase tracking-wider">🖱️ Por categoria</p>
                @php
                    $catItems = [
                        'profissional' => ['👤', 'Profissionais', 'text-orange-600 dark:text-orange-400'],
                        'evento'       => ['🎉', 'Eventos',       'text-purple-600 dark:text-purple-400'],
                        'projeto'      => ['❤',  'Projetos',      'text-red-600 dark:text-red-400'],
                        'vaga'         => ['💼', 'Vagas',         'text-blue-600 dark:text-blue-400'],
                    ];
                    $catMax = max(array_map(fn($t) => $clicksByType[$t] ?? 0, array_keys($catItems))) ?: 1;
                @endphp
                <div class="space-y-3">
                    @foreach($catItems as $type => [$icon, $label, $cls])
                    @php $n = $clicksByType[$type] ?? 0; @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs text-gray-600 dark:text-gray-300">{{ $icon }} {{ $label }}</span>
                            <span class="text-xs font-black font-mono {{ $cls }}">{{ $n }}</span>
                        </div>
                        <div class="h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full rounded-full {{ str_starts_with($cls, 'text-orange') ? 'bg-orange-400' : (str_starts_with($cls, 'text-purple') ? 'bg-purple-400' : (str_starts_with($cls, 'text-red') ? 'bg-red-400' : 'bg-blue-400')) }}"
                                 style="width: {{ $catMax > 0 ? round(($n / $catMax) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Tabelas: Telas + Profissionais ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

            {{-- Telas mais visitadas --}}
            <div class="rounded-xl bg-white dark:bg-gray-800/40 border border-gray-100 dark:border-gray-700/50 p-4">
                <p class="text-[11px] font-bold text-gray-500 dark:text-gray-400 mb-4 uppercase tracking-wider">🖥️ Telas mais visitadas</p>
                @if($topScreens->isEmpty())
                    <p class="text-xs text-gray-400 py-6 text-center">Nenhum acesso ainda</p>
                @else
                @php $maxScreen = $topScreens->max('views') ?: 1; @endphp
                <div class="space-y-3">
                    @foreach($topScreens as $i => $item)
                    <div class="flex items-center gap-2.5">
                        <span class="w-5 text-[10px] font-bold font-mono text-right shrink-0 {{ $i === 0 ? 'text-blue-500' : 'text-gray-300 dark:text-gray-600' }}">{{ $i + 1 }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline justify-between gap-2 mb-1">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-200 truncate">{{ $item->screen_label }}</span>
                                <span class="text-[11px] font-black font-mono text-blue-600 dark:text-blue-400 shrink-0">{{ $item->views }}</span>
                            </div>
                            <div class="h-1 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-400 dark:bg-blue-500 rounded-full"
                                     style="width: {{ round(($item->views / $maxScreen) * 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Profissionais mais clicados --}}
            <div class="rounded-xl bg-white dark:bg-gray-800/40 border border-gray-100 dark:border-gray-700/50 p-4">
                <p class="text-[11px] font-bold text-gray-500 dark:text-gray-400 mb-4 uppercase tracking-wider">👤 Profissionais mais clicados</p>
                @if($topPros->isEmpty())
                    <p class="text-xs text-gray-400 py-6 text-center">Nenhum clique ainda</p>
                @else
                @php $maxPro = $topPros->max('clicks') ?: 1; @endphp
                <div class="space-y-3">
                    @foreach($topPros as $i => $item)
                    <div class="flex items-center gap-2.5">
                        <span class="w-5 text-[10px] font-bold font-mono text-right shrink-0 {{ $i === 0 ? 'text-orange-500' : 'text-gray-300 dark:text-gray-600' }}">{{ $i + 1 }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline justify-between gap-2 mb-1">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-200 truncate">{{ $item->entity_name ?? '—' }}</span>
                                <span class="text-[11px] font-black font-mono text-orange-600 dark:text-orange-400 shrink-0">{{ $item->clicks }}</span>
                            </div>
                            <div class="h-1 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-orange-400 dark:bg-orange-500 rounded-full"
                                     style="width: {{ round(($item->clicks / $maxPro) * 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
