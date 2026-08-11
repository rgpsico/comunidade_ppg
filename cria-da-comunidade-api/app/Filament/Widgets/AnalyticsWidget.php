<?php

namespace App\Filament\Widgets;

use App\Models\AnalyticsEvent;
use Filament\Widgets\Widget;

class AnalyticsWidget extends Widget
{
    protected string $view = 'filament.widgets.analytics-widget';

    protected static ?int $sort = 2;
    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    private static array $screenLabels = [
        'inicio'        => 'Início',
        'profissionais' => 'Profissionais',
        'eventos'       => 'Eventos',
        'projetos'      => 'Projetos sociais',
        'vagas'         => 'Vagas',
        'lojas'         => 'Lojas',
        'artigos'       => 'Artigos',
        'curriculos'    => 'Currículos',
        'proDetail'     => 'Perfil de profissional',
        'eventDetail'   => 'Detalhe de evento',
        'projDetail'    => 'Detalhe de projeto',
        'vagaDetail'    => 'Detalhe de vaga',
        'login'         => 'Login',
        'perfil'        => 'Perfil do usuário',
    ];

    public function getViewData(): array
    {
        $since30 = now()->subDays(30);
        $since14 = now()->subDays(14);

        // ── KPIs ──────────────────────────────────────────────────────────────
        $totalViews = AnalyticsEvent::where('type', 'page_view')
            ->where('created_at', '>=', $since30)->count();

        $totalSessions = AnalyticsEvent::where('created_at', '>=', $since30)
            ->distinct('session_id')->count('session_id');

        $totalClicks = AnalyticsEvent::where('type', 'click')
            ->where('created_at', '>=', $since30)->count();

        // ── Top profissionais clicados ─────────────────────────────────────────
        $topPros = AnalyticsEvent::query()
            ->where('type', 'click')
            ->where('entity_type', 'profissional')
            ->where('created_at', '>=', $since30)
            ->selectRaw('entity_id, entity_name, count(*) as clicks')
            ->groupBy('entity_id', 'entity_name')
            ->orderByDesc('clicks')
            ->limit(8)
            ->get();

        // ── Top telas visitadas ────────────────────────────────────────────────
        $topScreens = AnalyticsEvent::query()
            ->where('type', 'page_view')
            ->whereNotNull('screen')
            ->where('created_at', '>=', $since30)
            ->selectRaw('screen, count(*) as views')
            ->groupBy('screen')
            ->orderByDesc('views')
            ->limit(8)
            ->get()
            ->map(function ($row) {
                $row->screen_label = self::$screenLabels[$row->screen] ?? ucfirst($row->screen);
                return $row;
            });

        // ── Cliques por tipo de entidade ───────────────────────────────────────
        $clicksByType = AnalyticsEvent::query()
            ->where('type', 'click')
            ->where('created_at', '>=', $since30)
            ->selectRaw('entity_type, count(*) as total')
            ->groupBy('entity_type')
            ->pluck('total', 'entity_type');

        // ── Acessos por dia (últimos 14 dias) ─────────────────────────────────
        $dailyViews = AnalyticsEvent::query()
            ->where('type', 'page_view')
            ->where('created_at', '>=', $since14)
            ->selectRaw('DATE(created_at) as day, count(*) as views')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        return compact(
            'totalViews', 'totalSessions', 'totalClicks',
            'topPros', 'topScreens', 'clicksByType', 'dailyViews'
        );
    }
}
