<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmpresaResource\Pages;
use App\Models\Empresa;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class EmpresaResource extends Resource
{
    protected static ?string $model = Empresa::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'Gestão'; // Agrupa os itens

    protected static ?int $navigationSort = 2; // Define a posição no menu


    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('nome')
                    ->label('Nome da Empresa')
                    ->required()
                    ->maxLength(255),
                TextInput::make('cnpj')
                    ->label('CNPJ')
                    ->required()
                    ->maxLength(18)
                    ->mask('99.999.999/9999-99')
                    ->helperText('Digite o CNPJ no formato 99.999.999/9999-99'),

                TextInput::make('telefone')
                    ->label('Telefone')
                    ->tel()
                    ->required()
                    ->mask('(99) 99999-9999'),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('cnpj')->sortable(),
                Tables\Columns\TextColumn::make('telefone')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('delete')
                    ->action(function (array $records) {
                        foreach ($records as $record) {
                            $record->delete();
                        }
                    })
                    ->requiresConfirmation(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmpresas::route('/'),
            'create' => Pages\CreateEmpresa::route('/create'),
            'edit' => Pages\EditEmpresa::route('/{record}/edit'),
        ];
    }
}
