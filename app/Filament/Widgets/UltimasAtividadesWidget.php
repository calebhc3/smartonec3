<?php

namespace App\Filament\Widgets;

use OwenIt\Auditing\Models\Audit;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class UltimasAtividadesWidget extends TableWidget
{
    protected int | string | array $columnSpan = 'full'; // Ocupa toda a largura da página

    /**
     * Define a query utilizada para popular a tabela
     */
    protected function getTableQuery(): Builder
    {
        return Audit::query()
            ->where('auditable_type', 'App\Models\Agendamento')
            ->whereRaw('MOD(id, 2) = 1') // Filtra para exibir apenas registros com id ímpar
            ->orderBy('created_at', 'desc')
            ->limit(10);
    }

    /**
     * Define as colunas da tabela
     */
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

    /**
     * Traduz o evento diretamente no código
     */
    private function translateEvent($state): string
    {
        // Traduz os eventos e coloca a primeira letra maiúscula
        switch (strtolower($state)) {
            case 'created':
                return ucfirst('Criado');
            case 'updated':
                return ucfirst('Atualizado');
            case 'deleted':
                return ucfirst('Deletado');
            default:
                return ucfirst($state); // Para outros eventos não conhecidos
        }
    }

    /**
     * Formata os valores antigos e novos para exibição mais legível
     */
    private function formatValues($state): string
    {
        if (empty($state) || $state === "null" || $state === "[]") {
            return 'Nenhuma alteração registrada';
        }

        // Teste: Exibe os valores brutos antes da conversão
        if (is_string($state)) {
            return $state; // Se for uma string, exibe diretamente
        }

        // Converter JSON em array se necessário
        $data = is_array($state) ? $state : json_decode($state, true);

        // Caso a conversão falhe, mostrar o erro
        if (!is_array($data)) {
            return 'Erro ao processar os dados';
        }

        // Agora lidamos com datas, strings ou outros tipos específicos
        return collect($data)
            ->map(function ($value, $key) {
                if (strtotime($value)) {
                    // Se for uma data, formate-a corretamente
                    $formattedDate = Carbon::parse($value)->format('d/m/Y H:i');
                    return "**{$key}**: {$formattedDate}";
                }

                // Para valores simples ou outros tipos, exibe normalmente
                return "**{$key}**: " . (is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value);
            })
            ->implode("\n");
    }

    /**
     * Define o título da tabela
     */
    protected function getTableHeading(): string
    {
        return 'Últimas Atividades Realizadas';
    }
}
