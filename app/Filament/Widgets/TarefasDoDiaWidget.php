<?php

namespace App\Filament\Widgets;

use App\Models\Afastamento;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class TarefasDoDiaWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1; // Para aparecer no topo

    protected function getCards(): array
    {
        // Consultas para afastamentos
        $afastamentos15Dias = Afastamento::whereDate('termino_previsto_beneficio', today()->addDays(15))->count();
        $afastamentos10Dias = Afastamento::whereDate('termino_previsto_beneficio', today()->addDays(10))->count();
        $notificacoesHoje = Afastamento::whereDate('notificar_shopee_retorno', today())->count();

        return [
            Card::make('Afastamentos em 15 Dias', $afastamentos15Dias)
                ->description('Afastamentos com término previsto para os próximos 15 dias')
                ->color('primary')
                ->icon('heroicon-o-calendar')
                ->url('/admin/afastamentos?filter[termino_previsto_beneficio][date]=' . today()->addDays(15)->toDateString()),

            Card::make('Afastamentos em 10 Dias', $afastamentos10Dias)
                ->description('Afastamentos com término previsto para os próximos 10 dias')
                ->color('warning')
                ->icon('heroicon-o-clock')
                ->url('/admin/afastamentos?filter[termino_previsto_beneficio][date]=' . today()->addDays(10)->toDateString()),

            Card::make('Notificações de Retorno Hoje', $notificacoesHoje)
                ->description('Afastamentos com notificações de retorno para hoje')
                ->color('danger')
                ->icon('heroicon-o-exclamation-circle')
                ->url('/admin/afastamentos?filter[notificar_shopee_retorno][date]=' . today()->toDateString()),
        ];
    }

    protected function getPollingInterval(): ?string
    {
        return '10s';
    }
}
