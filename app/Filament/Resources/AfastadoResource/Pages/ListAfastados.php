<?php

namespace App\Filament\Resources\AfastadoResource\Pages;

use App\Filament\Resources\AfastadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAfastados extends ListRecords
{
    protected static string $resource = AfastadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
