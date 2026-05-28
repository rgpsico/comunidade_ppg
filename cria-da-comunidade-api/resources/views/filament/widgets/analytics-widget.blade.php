<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            📊 Analytics — últimos 30 dias
        </x-slot>

        {{-- Linha: cliques por tipo --}}
        @if($clicksByType->isNotEmpty())
        <div class="grid grid-cols-4 gap-3 mb-6">
            @foreach(['profissional' => ['label'=>'Profissionais','icon'=>'👤'], 'evento' => ['label'=>'Eventos','icon'=>'🎉'], 'projeto' => ['label'=>'Projetos','icon'=>'❤'], 'vaga' => ['label'=>'Vagas','icon'=>'💼']] as $type => $meta)
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-4 text-center">
                <div class="text-2xl mb-1">{{ $meta['icon'] }}</div>
                <div class="text-xl font-bold font-mono">{{ $clicksByType[$type] ?? 0 }}</div>
                <div class="text-xs text-gray-500 mt-1">cliques em {{ $meta['label'] }}</div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Acessos diários (14 dias) --}}
        @if($dailyViews->isNotEmpty())
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-3">Acessos diários (últimos 14 dias)</h4>
            @php $maxViews = $dailyViews->max('views') ?: 1; @endphp
            <div class="flex items-end gap-1.5 h-20">
                @foreach($dailyViews as $day)
                @php $pct = round(($day->views / $maxViews) * 100); @endphp
                <div class="flex-1 flex flex-col items-center gap-1 group" title="{{ $day->day }}: {{ $day->views }} views">
                    <div class="w-full bg-orange-500 dark:bg-orange-400 rounded-t transition-all" style="height: {{ $pct }}%"></div>
                    <span class="text-[9px] text-gray-400 font-mono">{{ \Carbon\Carbon::parse($day->day)->format('d/m') }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Tabelas: Top Profissionais + Top Telas --}}
        <div class="grid grid-cols-2 gap-6">

            {{-- Top Profissionais --}}
            <div>
                <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-3">
                    👤 Profissionais mais acessados
                </h4>
                @if($topPros->isEmpty())
                    <p class="text-sm text-gray-400">Nenhum clique registrado ainda.</p>
                @else
                <div class="space-y-1">
                    @foreach($topPros as $i => $item)
                    <div class="flex items-center gap-3 py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <span class="text-xs font-mono text-gray-400 w-4">{{ $i + 1 }}</span>
                        <span class="flex-1 text-sm truncate">{{ $item->entity_name ?? '—' }}</span>
                        <span class="text-xs font-mono font-semibold bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300 px-2 py-0.5 rounded-full">
                            {{ $item->clicks }}×
                        </span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Top Telas --}}
            <div>
                <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-3">
                    🖥️ Telas mais visitadas
                </h4>
                @if($topScreens->isEmpty())
                    <p class="text-sm text-gray-400">Nenhum acesso registrado ainda.</p>
                @else
                @php $maxScreen = $topScreens->max('views') ?: 1; @endphp
                <div class="space-y-1">
                    @foreach($topScreens as $i => $item)
                    <div class="flex items-center gap-3 py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <span class="text-xs font-mono text-gray-400 w-4">{{ $i + 1 }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm truncate">{{ $item->screen_label }}</div>
                            <div class="mt-0.5 h-1 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500 rounded-full" style="width: {{ round(($item->views / $maxScreen) * 100) }}%"></div>
                            </div>
                        </div>
                        <span class="text-xs font-mono font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 px-2 py-0.5 rounded-full">
                            {{ $item->views }}×
                        </span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <p class="text-xs text-gray-400 mt-4 text-right">
            Atualizado em tempo real · dados dos últimos 30 dias
        </p>
    </x-filament::section>
</x-filament-widgets::widget>
