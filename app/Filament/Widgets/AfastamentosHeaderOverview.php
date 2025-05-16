<?php

namespace App\Filament\Widgets;

use App\Models\Afastamento;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Carbon;
use Livewire\Component;

class AfastamentosHeaderOverview extends BaseWidget
{
    public ?string $dataInicial = null;
    public ?string $dataFinal = null;
    
    protected $listeners = ['filtrosAtualizados' => 'atualizarFiltros'];
    
    public function atualizarFiltros($payload)
    {
        $this->dataInicial = $payload['dataInicial'] ?? null;
        $this->dataFinal = $payload['dataFinal'] ?? null;
    }

protected function getCards(): array
{
    $query = Afastamento::query();

    if ($this->dataInicial) {
        $query->whereDate('data_psc', '>=', $this->dataInicial);
    }

    if ($this->dataFinal) {
        $query->whereDate('data_psc', '<=', $this->dataFinal);
    }

    return [
        Card::make('Total de Afastamentos', $query->count()),
        Card::make('Afastados', $query->clone()->where('status_atual', 'afastado')->count())->color('danger'),
        Card::make('Liberados ao retorno', $query->clone()->where('status_atual', 'liberado ao retorno')->count()),
        Card::make('Desligados', $query->clone()->where('status_atual', 'desligado')->count()),
        Card::make('Liberados com termo', $query->clone()->where('status_atual', 'liberado com o termo')->count()),
        Card::make('Liberados com restrição', $query->clone()->where('status_atual', 'liberado com restricao')->count()),
    ];
}

}
