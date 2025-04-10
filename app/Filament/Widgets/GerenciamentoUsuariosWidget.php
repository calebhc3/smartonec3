<?php

namespace App\Filament\Widgets;

use App\Models\Agendamento;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class GerenciamentoUsuariosWidget extends TableWidget
{
    protected int | string | array $columnSpan = 'full'; // Ocupa toda a largura da página

    protected function getTableQuery(): Builder
    {
        return Agendamento::query()
            ->whereRaw('DATEDIFF(data_devolutiva, data_solicitacao) > 1') // Filtra registros com diferença maior que 1 dia
            ->orderBy('data_solicitacao', 'desc'); // Ordena por data de solicitação
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('user.name')->label('Solicitante'), // Nome do usuário que fez o registro
            TextColumn::make('data_solicitacao')->label('Data da Solicitação')->date(), // Data da solicitação
            TextColumn::make('data_devolutiva')->label('Data da Devolutiva')->date(), // Data da devolutiva
            TextColumn::make('dias_espera') // Coluna calculada para mostrar a diferença em dias
                ->label('Dias de Espera')
                ->getStateUsing(function ($record) {
                    return $record->data_devolutiva->diffInDays($record->data_solicitacao);
                }),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('solicitante') // Filtro por solicitante
                ->relationship('user', 'name') // Relacionamento com o model User
                ->label('Solicitante'),
        ];
    }

    protected function getTableHeading(): string
    {
        return 'Gerenciamento de Usuários (Produtividade)';
    }
}