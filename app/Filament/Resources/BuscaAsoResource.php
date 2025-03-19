<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BuscaAsoResource\Pages;
use App\Filament\Resources\BuscaAsoResource\RelationManagers;
use Filament\Pages\Page;
use App\Models\Agendamento;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Exports\AgendamentosExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\SelectColumn;

class BuscaAsoResource extends Resource
{
    protected static ?string $model = Agendamento::class;

    protected static ?string $navigationIcon = 'heroicon-m-magnifying-glass';

    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = 'Busca';
    public static function getNavigationLabel(): string
    {
        return 'Busca de ASO';
    }
    public static function getModelLabel(): string
    {
        return 'Busca de ASO';
    }
    
    public static function getPluralModelLabel(): string
    {
        return 'Busca de ASOs';
    }
    public static function canView(?Model $record = null): bool
    {
        return auth()->user()->can('access_aso_search');
    }
    public static function table(Table $table): Table
    {
        return $table
        ->query(Agendamento::query()) // Mantém a query base
        ->columns([
            Tables\Columns\TextColumn::make('empresa.nome'),
            Tables\Columns\TextColumn::make('unidade.nome'),
            Tables\Columns\TextColumn::make('nome_funcionario')
            ->label('Colaborador'),
            Tables\Columns\TextColumn::make('tipo_exame')
            ->label('Tipo de Exame'),
            Tables\Columns\TextColumn::make('user.name')
            ->label('Solicitante'),
            Tables\Columns\TextColumn::make('data_exame')->date(),
            Tables\Columns\TextColumn::make('sla')
            ->label('SLA'),
            Tables\Columns\SelectColumn::make('status')
            ->label('Status')
            ->sortable()
            ->options([
                'agendado' => 'Agendado',
                'cancelado' => 'Cancelado',
                'ASO ok' => 'ASO OK',
                'ASO enviado' => 'ASO Enviado',
                'não compareceu' => 'Não Compareceu',
            ])
            ->afterStateUpdated(fn ($record, $state) => $record->update(['status' => $state])),            
            Tables\Columns\BadgeColumn::make('estado_atrasado')
                ->label('Situação')
                ->getStateUsing(function ($record) {
                    $dataExame = Carbon::parse($record->data_exame);
                    $hoje = Carbon::today();
                    $prazo = match ($record->sla) {
                        'clinico' => 1,
                        'clinico_complementar' => 3,
                        'clinico_acidos' => 10,
                        default => 0,
                    };
                    return ($record->status === 'pendente' && $dataExame->diffInDays($hoje) > $prazo) 
                        ? 'Atrasado' 
                        : 'No Prazo';
                })
                ->colors([
                    'danger' => 'Atrasado',
                    'success' => 'No Prazo',
                ]),
        ])
        ->filters([
            Tables\Filters\Filter::make('buscar')
            ->form([
                Forms\Components\TextInput::make('search')
                    ->label('Nome ou CPF')
                    ->placeholder('Digite o nome ou CPF...')
                    ->debounce(500), // Pequeno delay para evitar múltiplas requisições
            ])
            ->query(function (Builder $query, array $data) {
                if (!empty($data['search'])) {
                    $searchTerm = $data['search'];
                    return $query->where(function ($query) use ($searchTerm) {
                        $query->where('nome_funcionario', 'like', "%{$searchTerm}%")
                              ->orWhere('doc_identificacao_cpf', 'like', "%{$searchTerm}%");
                    });
                }
                return $query;
            }),
            // Filtro por Ano do Registro
            Filter::make('ano_registro')
                ->form([
                    Forms\Components\Select::make('ano')
                        ->options(function () {
                            return Agendamento::query()
                                ->selectRaw('YEAR(created_at) as ano')
                                ->distinct()
                                ->pluck('ano', 'ano')
                                ->prepend('Todos', '')
                                ->toArray();
                        })
                        ->label('Ano do Registro'),
                ])
                ->query(function (Builder $query, array $data) {
                    if (!empty($data['ano'])) {
                        $query->whereYear('created_at', $data['ano']);
                    }
                }),
        
            // Filtro por Mês do Registro
            Filter::make('mes_registro')
                ->form([
                    Forms\Components\Select::make('mes')
                        ->options([
                            '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
                            '04' => 'Abril', '05' => 'Maio', '06' => 'Junho',
                            '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro',
                            '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro',
                        ])
                        ->label('Mês do Registro'),
                ])
                ->query(function (Builder $query, array $data) {
                    if (!empty($data['mes'])) {
                        $query->whereMonth('created_at', $data['mes']);
                    }
                }),
        
            // Filtro por Empresa
            SelectFilter::make('empresa_id')
                ->relationship('empresa', 'nome')
                ->label('Empresa'),
        
            // Filtro por Tipo de Exame
            SelectFilter::make('tipo_exame')
                ->options([
                    'admissional' => 'Admissional',
                    'periodico' => 'Periódico',
                    'demissional' => 'Demissional',
                    'retorno_trabalho' => 'Retorno ao Trabalho',
                    'mudanca_funcao' => 'Mudança de Função',
                    'avaliacao_clinica' => 'Avaliação Clínica',
                ])
                ->label('Tipo de Exame'),
        
            // Filtro por Status
            SelectFilter::make('status')
                ->options([
                    'agendado' => 'Agendado',
                    'cancelado' => 'Cancelado',
                    'ASO ok' => 'ASO OK',
                    'ASO enviado' => 'ASO Enviado',
                    'não compareceu' => 'Não Compareceu',
                ])
                ->label('Status'),
        
            // Filtro por Data do Exame
            Filter::make('data_exame')
                ->form([
                    Forms\Components\DatePicker::make('data_exame')
                        ->label('Data do Exame'),
                ])
                ->query(function (Builder $query, array $data) {
                    if (!empty($data['data_exame'])) {
                        $query->whereDate('data_exame', $data['data_exame']);
                    }
                }),
        
            // Filtro por SLA
            SelectFilter::make('sla')
                ->options([
                    'clinico' => 'Exame Clínico (1 dia)',
                    'clinico_complementar' => 'Exame Clínico + Complementar (3 dias)',
                    'clinico_acidos' => 'Exame Clínico + Ácidos (5 a 10 dias)',
                ])
                ->label('SLA'),
                
                ])
                ->headerActions([
                    Tables\Actions\Action::make('export')
                        ->label('Exportar para Excel')
                        ->action(function () {
                            // Gera o nome do arquivo com a data de hoje
                            $fileName = 'agendamentos_' . now()->format('Y-m-d') . '.xlsx';
                            
                            // Exporta os dados para o arquivo Excel
                            return Excel::download(new AgendamentosExport, $fileName);
                        }),
                ]);
}

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBuscaAsos::route('/'),
        ];
    }
    public static function canCreate(): bool
{
    return false; // Define como falso para remover a opção de criação
}
}
