<?php

namespace App\Filament\Widgets;

use OwenIt\Auditing\Models\Audit;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ExclusoesRegistrosWidget extends TableWidget
{
    protected int | string | array $columnSpan = 'full'; // Ocupa toda a largura da página

    /**
     * Define a query utilizada para popular a tabela
     */
    protected function getTableQuery(): Builder
    {
        return Audit::query()
            ->where('auditable_type', 'App\Models\Agendamento') // Filtro para o modelo de agendamento
            ->where('event', 'deleted') // Apenas eventos de exclusão
            ->orderBy('created_at', 'desc') // Ordena pela data de exclusão
            ->limit(10); // Limita os resultados a 10
    }

    /**
     * Define as colunas da tabela
     */
    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('user.name')
                ->label('Responsável pela Exclusão')
                ->searchable()
                ->sortable(),

            TextColumn::make('created_at')
                ->label('Data da Exclusão')
                ->dateTime('d/m/Y H:i')
                ->sortable(),

            BadgeColumn::make('event')
                ->label('Evento')
                ->formatStateUsing(fn ($state) => $this->translateEvent($state))
                ->colors([
                    'danger' => 'deleted',
                ])
                ->sortable(),

            TextColumn::make('old_values')
                ->label('Detalhes do Agendamento')
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
        switch (strtolower($state)) {
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
    
        // Detectar se os dados são um JSON válido
        $decoded = json_decode($state, true);
        
        if (is_array($decoded)) {
            $data = $decoded; // Se for JSON, usamos normalmente
        } else {
            // Caso contrário, assumimos que está separado por vírgulas e convertemos em array
            $data = explode(', ', $state);
        }
    
        // Definir rótulos para exibição correta
        $camposDesejados = [
            'empresa' => 'Empresa',
            'nome_funcionario' => 'Funcionário',
            'status' => 'Status',
            'data_exame' => 'Data do Exame',
        ];
    
        // Verifica se os dados são indexados (array simples) ou associativos
        if (array_keys($data) === range(0, count($data) - 1)) {
            // Se for um array indexado, assumimos a ordem fixa:
            [$nomeFuncionario, $status, $dataExame, $ultimaAtualizacao] = array_pad($data, 4, null);
    
            // Formata as datas corretamente
            if (!empty($dataExame) && strtotime($dataExame)) {
                $dataExame = Carbon::parse($dataExame)->format('d/m/Y');
            }
    
            return "$nomeFuncionario\n / $status\n / Data do exame: $dataExame";
        }
    
        // Se for array associativo (JSON), formatamos normalmente
        return collect($camposDesejados)
            ->map(fn ($label, $key) => isset($data[$key]) ? "**{$label}**: {$data[$key]}" : null)
            ->filter()
            ->implode("\n") ?: 'Nenhuma alteração relevante registrada';
    }
    

    /**
     * Define o título da tabela
     */
    protected function getTableHeading(): string
    {
        return 'Exclusões de Registros';
    }
}
