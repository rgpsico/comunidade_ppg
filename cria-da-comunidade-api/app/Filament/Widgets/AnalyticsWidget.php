<?php

namespace App\Filament\Widgets;

use App\Models\AnalyticsEvent;
use Filament\Widgets\Widget;

class AnalyticsWidget extends Widget
{
    protected static string $view = 'filament.widgets.analytics-widget';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Analytics — últimos 30 dias';

    private static array $screenLabels = [
        'inicio'      => 'Início',
        'profissionais' => 'Profissionais',
        'eventos'     => 'Eventos',
        'projetos'    => 'Projetos sociais',
        'vagas'       => 'Vagas',
        'proDetail'   => 'Perfil de profissional',
        'eventDetail' => 'Detalhe de evento',
        'projDetail'  => 'Detalhe de projeto',
        'vagaDetail'  => 'Detalhe de vaga',
        'login'       => 'Login',
        'perfil'      => 'Perfil do usuário',
    ];

    public function getViewData(): array
    {
        $since = now()->subDays(30);

        // Top profissionais clicados
        $topPros = AnalyticsEvent::query()
            ->where('type', 'click')
            ->where('entity_type', 'profissional')
            ->where('created_at', '>=', $since)
            ->selectRaw('entity_id, entity_name, count(*) as clicks')
            ->groupBy('entity_id', 'entity_name')
            ->orderByDesc('clicks')
            ->limit(8)
            ->get();

        // Top telas visitadas
        $topScreens = AnalyticsEvent::query()
            ->where('type', 'page_view')
            ->whereNotNull('screen')
            ->where('created_at', '>=', $since)
            ->selectRaw('screen, count(*) as views')
            ->groupBy('screen')
            ->orderByDesc('views')
            ->limit(8)
            ->get()
            ->map(function ($row) {
                $row->screen_label = self::$screenLabels[$row->screen] ?? $row->screen;
                return $row;
            });

        // Total cliques por tipo de entidade
        $clicksByType = AnalyticsEvent::query()
            ->where('type', 'click')
            ->where('created_at', '>=', $since)
            ->selectRaw('entity_type, count(*) as total')
            ->groupBy('entity_type')
            ->pluck('total', 'entity_type');

        // Acessos por dia (últimos 14 dias)
        $dailyViews = AnalyticsEvent::query()
            ->where('type', 'page_view')
            ->where('created_at', '>=', now()->subDays(14))
            ->selectRaw('DATE(created_at) as day, count(*) as views')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        return compact('topPros', 'topScreens', 'clicksByType', 'dailyViews');
    }
}
