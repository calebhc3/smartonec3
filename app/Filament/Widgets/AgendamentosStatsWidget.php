<?php

namespace App\Filament\Widgets;

use App\Models\Agendamento;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AgendamentosStatsWidget extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $hoje = Carbon::now();

        // Consultas revisadas
        $agendamentosNoPrazo = Agendamento::where('status', 'pendente')
            ->where('data_exame', '>=', $hoje)
            ->count();

        $agendamentosAtrasados = Agendamento::where('status', 'pendente')
            ->where('data_exame', '<', $hoje)
            ->count();

        $totalAgendamentos = Agendamento::count(); // Sem cache para teste

        return [
            Stat::make('Total de Agendamentos', $totalAgendamentos)
                ->description('Número total de agendamentos registrados')
                ->color('primary') // Cor do widget
                ->icon('heroicon-o-calendar'), // Ícone do widget

            Card::make('Pendentes', Agendamento::where('status', 'pendente')->count())
                ->description('Aguardando realização')
                ->color('warning')
                ->icon('heroicon-o-clock')
                ->chart([7, 10, 15, 12, 8]),

            Card::make('Agendados', $agendamentosNoPrazo)
                ->description('Dentro do prazo')
                ->color('primary')
                ->icon('heroicon-o-calendar')
                ->chart([5, 8, 12, 10, 7]),

            Card::make('Concluídos', Agendamento::where('status', 'realizado')->count())
                ->description('Exames finalizados')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->chart([10, 15, 20, 18, 22]),

            Card::make('Atrasados', $agendamentosAtrasados)
                ->description('Fora do prazo')
                ->color('danger')
                ->icon('heroicon-o-exclamation-circle')
                ->chart([2, 3, 1, 4, 2]),
        ];
    }

    protected function getPollingInterval(): ?string
    {
        return '10s';
    }
}