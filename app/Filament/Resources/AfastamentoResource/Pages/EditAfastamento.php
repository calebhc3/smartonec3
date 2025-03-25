<?php

namespace App\Filament\Resources\AfastamentoResource\Pages;

use App\Filament\Resources\AfastamentoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAfastamento extends EditRecord
{
    protected static string $resource = AfastamentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
