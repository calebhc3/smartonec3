<?php

namespace App\Filament\Resources\BuscaAsoResource\Pages;

use App\Filament\Resources\BuscaAsoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBuscaAso extends EditRecord
{
    protected static string $resource = BuscaAsoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
