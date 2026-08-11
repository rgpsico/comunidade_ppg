<?php

namespace App\Filament\Widgets;

use App\Models\AnalyticsEvent;
use App\Models\Evento;
use App\Models\Profissional;
use App\Models\Projeto;
use App\Models\User;
use App\Models\Vaga;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $sessoesHoje   = AnalyticsEvent::whereDate('created_at', today())
            ->whereNotNull('session_id')
            ->distinct('session_id')
            ->count('session_id');

        $acessosHoje   = AnalyticsEvent::whereDate('created_at', today())
            ->where('type', 'page_view')
            ->count();

        $acessosSemana = AnalyticsEvent::where('created_at', '>=', now()->subDays(7))
            ->where('type', 'page_view')
            ->count();

        return [
            Stat::make('Sessões hoje', $sessoesHoje)
                ->description($acessosHoje . ' page views hoje')
                ->descriptionIcon('heroicon-m-cursor-arrow-rays')
                ->color('success'),

            Stat::make('Acessos (7 dias)', $acessosSemana)
                ->description(
                    AnalyticsEvent::where('created_at', '>=', now()->subDays(7))
                        ->whereNotNull('session_id')->distinct('session_id')->count('session_id')
                    . ' visitantes únicos'
                )
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),

            Stat::make('Usuários', User::count())
                ->description(User::whereDate('created_at', today())->count() . ' novos hoje')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Profissionais', Profissional::count())
                ->description(Profissional::where('verificado', true)->count() . ' verificados')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('warning'),

            Stat::make('Eventos ativos', Evento::where('ativo', true)->where('data_hora', '>=', now())->count())
                ->description(Evento::where('destaque', true)->count() . ' em destaque')
                ->descriptionIcon('heroicon-m-star')
                ->color('danger'),

            Stat::make('Vagas abertas', Vaga::where('ativa', true)->count())
                ->description(Vaga::where('urgente', true)->where('ativa', true)->count() . ' urgentes')
                ->descriptionIcon('heroicon-m-fire')
                ->color('warning'),
        ];
    }
}
