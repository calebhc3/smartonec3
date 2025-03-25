<?php

namespace App\Filament\Resources\AfastamentoResource\Pages;

use App\Filament\Resources\AfastamentoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAfastamentos extends ListRecords
{
    protected static string $resource = AfastamentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
