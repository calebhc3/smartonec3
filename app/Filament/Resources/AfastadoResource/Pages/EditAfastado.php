<?php

namespace App\Filament\Resources\AfastadoResource\Pages;

use App\Filament\Resources\AfastadoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAfastado extends EditRecord
{
    protected static string $resource = AfastadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
