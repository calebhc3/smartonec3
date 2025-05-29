<?php

namespace App\Filament\Widgets;

use App\Models\Agendamento;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Carbon\Carbon;

class AgendamentosStatsWidget extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $hoje = Carbon::now();
        $inicioPeriodo = Carbon::now()->subDays(30);
        $ontem = Carbon::yesterday()->toDateString(); // Obtém a data de ontem no formato correto

        // Consulta geral de agendamentos no período

        // Filtra os agendamentos para calcular a situação
        $agendamentos = Agendamento::whereBetween('data_exame', [$inicioPeriodo, $hoje])->get();

        // Calcula exames atrasados e em prazo
        $examesAtrasados = $agendamentos->filter(function ($agendamento) {
            return $this->getSituacaoAtrasado($agendamento) === 'Atrasado';
        })->count();

        $examesEmPrazo = $agendamentos->filter(function ($agendamento) {
            return $this->getSituacaoAtrasado($agendamento) === 'No Prazo';
        })->count();

        // Outras métricas
        $examesEnvioAsoPendente = Agendamento::where('status', 'ASO ok')->count();
        
        $aguardandoRealizacao = Agendamento::where('status', 'agendado')->count();

        $examesPendentes = Agendamento::whereDate('data_exame', $ontem)
        ->whereNotIn('status', ['ASO ok', 'ASO enviado'])
        ->count();

        $asosTerceiraFalta = Agendamento::where('nao_compareceu_count', 3)->count();

        $examesReagendamento = Agendamento::where('status', 'nao compareceu')->count();

        return [
            Card::make('Total de Agendamentos', Agendamento::count())
            ->description('Agendamentos feitos')
            ->color('primary')
            ->icon('heroicon-o-calendar')
            ->url('/admin/agendamentos'),
        
        Card::make('Exames Atrasados', $examesAtrasados)
            ->description('Exames fora do prazo')
            ->color('danger')
            ->icon('heroicon-o-exclamation-circle')
            ->chart([2, 3, 1, 4, 2])
            ->url('/admin/agendamentos?tableFilters[situacao][value]=Atrasado'),
        
        Card::make('Exames em Prazo', $examesEmPrazo)
            ->description('Exames dentro do prazo')
            ->color('success')
            ->icon('heroicon-o-check-circle')
            ->chart([5, 8, 12, 10, 7])
            ->url('/admin/agendamentos?tableFilters[situacao][value]=No Prazo'),
        
        Card::make('Envio de ASO Pendente', $examesEnvioAsoPendente)
            ->description('Aguardando envio de ASO')
            ->color('warning')
            ->icon('heroicon-o-document')
            ->chart([3, 5, 7, 6, 4])
            ->url('/admin/agendamentos?tableFilters[status][value]=ASO ok'),
        
        Card::make('Aguardando Realização', $aguardandoRealizacao)
            ->description('Exames agendados')
            ->color('info')
            ->icon('heroicon-o-clock')
            ->chart([4, 6, 9, 5, 8])
            ->url('/admin/agendamentos?tableFilters[status][value]=agendado&tableFilters[situacao][value]=No Prazo'),
        
        Card::make('Exames para Reagendamento', $examesReagendamento)
            ->description('Exames cancelados ou agendados com data anterior')
            ->color('danger')
            ->icon('heroicon-o-arrow-path')
            ->chart([3, 5, 7, 6, 4]) // Customize o gráfico conforme necessário
            ->url('/admin/agendamentos?tableFilters[status][value]=Nao Compareceu&tableFilters[data_exame][before]=today'), 

        Card::make('ASOs na Terceira Falta', $asosTerceiraFalta)
            ->description('ASOs com três faltas')
            ->color('danger')
            ->icon('heroicon-o-exclamation-triangle')
            ->chart([2, 3, 1, 4, 5])
            ->url('/admin/agendamentos?tableFilters[status][value]=não+compareceu&tableFilters[nao_compareceu_count][value]=3'),
    Card::make('Exames registrados hoje', Agendamento::whereDate('created_at', Carbon::today())->count())
        ->description('Registros de exames feitos hoje')
        ->color('primary')
        ->icon('heroicon-o-calendar')
        ->chart([1, 2, 3, 4, 5])
        ->url('/admin/agendamentos?tableFilters[data_registro][data_registro]=' . Carbon::today()->toDateString())

        ];
    }

    /**
     * Calcula a situação do agendamento.
     */
    private static function getSituacaoAtrasado($record): string
    {
        $dataExame = Carbon::parse($record->data_exame);
        $hoje = Carbon::today();
    
        // Definição do prazo conforme o SLA
        $prazo = match ($record->sla) {
            'clinico' => 1,
            'clinico_complementar' => 3,
            'clinico_acidos' => 10,
            default => 0,
        };
    
        // Verifica se o exame está atrasado
        if (in_array($record->status, ['agendado', 'não compareceu']) && $dataExame->isBefore($hoje)) {
            return 'Atrasado';
        }
    
        return 'No Prazo';
    }

    protected function getPollingInterval(): ?string
    {
        return '10s';
    }
}