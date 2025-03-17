<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnidadeResource\Pages;
use App\Models\Unidade;
use App\Models\Empresa;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;

class UnidadeResource extends Resource
{
    protected static ?string $model = Unidade::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Gestão'; // Agrupa os itens

    protected static ?int $navigationSort = 3; // Define a posição no menu

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                // Campo para o Nome da Unidade
                TextInput::make('nome')
                    ->label('Nome da Unidade')
                    ->required()
                    ->maxLength(255),

                // Campo para o Telefone
                TextInput::make('telefone')
                    ->label('Telefone')
                    ->tel()
                    ->required()
                    ->mask('(99) 99999-9999'),
                // Seleção da Empresa a qual a unidade pertence
                Select::make('empresa_id')
                    ->label('Empresa')
                    ->required()
                    ->options(Empresa::all()->pluck('nome', 'id'))
                    ->searchable(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                // Colunas para exibir informações sobre as Unidades
                Tables\Columns\TextColumn::make('nome')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('telefone')->sortable(),
                Tables\Columns\TextColumn::make('email')->sortable(),
                Tables\Columns\TextColumn::make('endereco')->limit(50),
                Tables\Columns\TextColumn::make('empresa.nome')->label('Empresa')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnidades::route('/'),
            'create' => Pages\CreateUnidade::route('/create'),
            'edit' => Pages\EditUnidade::route('/{record}/edit'),
        ];
    }
}
