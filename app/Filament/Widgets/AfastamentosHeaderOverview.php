<?php

namespace App\Filament\Widgets;

use App\Models\Afastamento;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class AfastamentosHeaderOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Total de Afastamentos', Afastamento::count()),

            Card::make('Afastados', Afastamento::where('status_atual', 'Afastado')->count())
                ->color('danger'),

                Card::make('Liberados', Afastamento::whereIn('status_atual', [
                    'liberado_ao_retorno',
                    'desligado',
                    'liberado_com_termo',
                    'liberado_com_restricao',
                ])->count())
                ->color('success'),
        ];
    }
}
