<?php

namespace App\Filament\Widgets;

use App\Models\Afastamento;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Carbon;

class AfastamentosHeaderOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Total de Afastamentos', Afastamento::count()),

            Card::make('Afastados', Afastamento::where('status_atual', 'Afastado')->count())
                ->color('danger'),

                Card::make('Liberados ao retorno', Afastamento::whereIn('status_atual', [
                    'liberado_ao_retorno',
                ])->count()),

                Card::make('Desligados', Afastamento::whereIn('status_atual', [
                    'desligado',
                ])->count()),

                Card::make('Liberados com termo', Afastamento::whereIn('status_atual', [
                    'liberado_com_termo',
                ])->count()),

                Card::make('Liberados com restrição', Afastamento::whereIn('status_atual', [
                    'liberado_com_restricao',
                ])->count()),
        ];
    }
}
