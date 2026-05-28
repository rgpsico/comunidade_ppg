<?php

namespace App\Filament\Widgets;

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

    protected function getStats(): array
    {
        return [
            Stat::make('Usuários', User::count())
                ->description(User::whereDate('created_at', today())->count() . ' novos hoje')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Profissionais', Profissional::count())
                ->description(Profissional::where('verificado', true)->count() . ' verificados')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('warning'),

            Stat::make('Eventos ativos', Evento::where('ativo', true)->where('data_hora', '>=', now())->count())
                ->description(Evento::where('destaque', true)->count() . ' em destaque')
                ->descriptionIcon('heroicon-m-star')
                ->color('danger'),

            Stat::make('Projetos sociais', Projeto::where('ativo', true)->count())
                ->description('R$ ' . number_format(Projeto::sum('arrecadado'), 0, ',', '.') . ' arrecadados')
                ->descriptionIcon('heroicon-m-heart')
                ->color('info'),

            Stat::make('Vagas abertas', Vaga::where('ativa', true)->count())
                ->description(Vaga::where('urgente', true)->where('ativa', true)->count() . ' urgentes')
                ->descriptionIcon('heroicon-m-fire')
                ->color('warning'),
        ];
    }
}
