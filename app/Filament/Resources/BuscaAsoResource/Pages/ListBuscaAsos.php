<?php

namespace App\Filament\Resources\BuscaAsoResource\Pages;

use App\Filament\Resources\BuscaAsoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBuscaAsos extends ListRecords
{
    protected static string $resource = BuscaAsoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
