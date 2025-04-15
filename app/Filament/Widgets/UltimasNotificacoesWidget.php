<?php

namespace App\Filament\Widgets;

use App\Models\Afastamento;
use Filament\Widgets\Widget;

class UltimasNotificacoesWidget extends Widget
{
    protected static string $view = 'filament.widgets.ultimas-notificacoes-widget';
    protected static ?int $sort = 2;

    protected function getViewData(): array
    {
        return [
            'ultimas_notificacoes' => Afastamento::latest()->limit(10)->get(),
        ];
    }
}
