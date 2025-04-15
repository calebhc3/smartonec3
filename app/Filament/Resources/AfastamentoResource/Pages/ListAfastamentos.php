<?php

namespace App\Filament\Resources\AfastamentoResource\Pages;

use App\Filament\Resources\AfastamentoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Widgets\AfastamentosHeaderOverview;

class ListAfastamentos extends ListRecords
{
    protected static string $resource = AfastamentoResource::class;

    protected function getHeaderWidgets(): array
{
    return [
        AfastamentosHeaderOverview::class,
    ];
}
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
