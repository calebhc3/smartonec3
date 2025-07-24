<?php

namespace App\Filament\Widgets;

use OwenIt\Auditing\Models\Audit;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Filament\Tables\Filters\SelectFilter;
use App\Models\User;

class UltimasAtividadesWidget extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Audit::query()
            ->where('auditable_type', 'App\Models\Agendamento')
            ->orderBy('created_at', 'desc')
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('user.name')
                ->label('Responsável')
                ->searchable()
                ->sortable(),

            TextColumn::make('created_at')
                ->label('Data da Modificação')
                ->dateTime('d/m/Y H:i')
                ->sortable(),

            BadgeColumn::make('event')
                ->label('Evento')
                ->formatStateUsing(fn ($state) => $this->translateEvent($state))
                ->colors([
                    'success' => 'created',
                    'info' => 'updated',
                    'danger' => 'deleted',
                ])
                ->sortable(),

            TextColumn::make('old_values')
                ->label('Valores Anteriores')
                ->formatStateUsing(fn ($state) => $this->formatValues($state))
                ->wrap()
                ->sortable(),

            TextColumn::make('new_values')
                ->label('Valores Atuais')
                ->formatStateUsing(fn ($state) => $this->formatValues($state))
                ->wrap()
                ->sortable(),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('user_id')
                ->label('Filtrar por Responsável')
                ->options(
                    User::query()
                        ->whereIn('id', function($query) {
                            $query->select('user_id')
                                ->from('audits')
                                ->whereNotNull('user_id')
                                ->distinct();
                        })
                        ->pluck('name', 'id')
                        ->toArray()
                )
                ->searchable(),
                
            SelectFilter::make('event')
                ->label('Filtrar por Evento')
                ->options([
                    'created' => 'Criado',
                    'updated' => 'Atualizado',
                    'deleted' => 'Deletado',
                ]),
        ];
    }

    private function translateEvent($state): string
    {
        switch (strtolower($state)) {
            case 'created':
                return ucfirst('Criado');
            case 'updated':
                return ucfirst('Atualizado');
            case 'deleted':
                return ucfirst('Deletado');
            default:
                return ucfirst($state);
        }
    }

    private function formatValues($state): string
    {
        if (empty($state) || $state === "null" || $state === "[]") {
            return 'Nenhuma alteração registrada';
        }

        if (is_string($state)) {
            return $state;
        }

        $data = is_array($state) ? $state : json_decode($state, true);

        if (!is_array($data)) {
            return 'Erro ao processar os dados';
        }

        return collect($data)
            ->map(function ($value, $key) {
                if (strtotime($value)) {
                    $formattedDate = Carbon::parse($value)->format('d/m/Y H:i');
                    return "**{$key}**: {$formattedDate}";
                }

                return "**{$key}**: " . (is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value);
            })
            ->implode("\n");
    }

    protected function getTableHeading(): string
    {
        return 'Últimas Atividades Realizadas';
    }
}