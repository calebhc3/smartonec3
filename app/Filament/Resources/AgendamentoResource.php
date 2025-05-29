<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgendamentoResource\Pages;
use App\Models\Agendamento;
use App\Exports\AgendamentosExport;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Radio;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Form; // Certifique-se de que esta importação está presente
use Filament\Tables\Table; // Certifique-se de que esta importação está presente
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use App\Imports\AgendamentosImport;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;  // Correct import
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Http;

class AgendamentoResource extends Resource
{
    protected static ?string $model = Agendamento::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Operação';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('empresa_id')
                    ->relationship('empresa', 'nome')
                    ->required()
                    ->label('Empresa'),

                    TextInput::make('cnpj_unidade')
                    ->label('CNPJ da Unidade')
                    ->required()
                    ->mask('99.999.999/9999-99')
                    ->live(debounce: 500)
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (strlen(preg_replace('/\D/', '', $state)) === 14) {
                            $set('nome_unidade', self::buscarNomeUnidade($state));
                        }
                    }),

                TextInput::make('nome_unidade')
                    ->label('Nome da Unidade')
                    ->readonly(),

                Forms\Components\Select::make('estado_atendimento')
                    ->options(self::getEstadosBrasileiros())
                    ->required()
                    ->label('Estado do Atendimento'),
    
                Forms\Components\TextInput::make('cidade_atendimento')
                    ->required()
                    ->label('Cidade do Atendimento')
                    ->maxLength(100),
    
                Forms\Components\DatePicker::make('data_exame')
                    ->required()
                    ->label('Data do Exame')
                    ->minDate(now()->startOfDay()) // Permite datas a partir de hoje (incluindo hoje)
                    ->displayFormat('d/m/Y'),
    
                Forms\Components\TimePicker::make('horario_exame')
                    ->label('Horário do Exame')
                    ->seconds(false),
    
                Forms\Components\TextInput::make('clinica_agendada')
                    ->required()
                    ->label('Clínica Agendada')
                    ->maxLength(150),
    
                Forms\Components\TextInput::make('nome_funcionario')
                    ->required()
                    ->label('Nome do Funcionário')
                    ->maxLength(150),
    
                Forms\Components\TextInput::make('contato_whatsapp')
                    ->label('Contato WhatsApp')
                    ->mask('(99) 99999-9999')
                    ->rule('regex:/^\(\d{2}\) \d{5}-\d{4}$/'),
    
                Forms\Components\TextInput::make('doc_identificacao_rg')
                    ->label('RG')
                    ->mask('99.999.999-9')
                    ->rule('regex:/^\d{2}\.\d{3}\.\d{3}-\d$/'),
    
                Forms\Components\TextInput::make('doc_identificacao_cpf')
                    ->label('CPF')
                    ->mask('999.999.999-99')
                    ->required(),
    
                Forms\Components\DatePicker::make('data_nascimento')
                    ->required()
                    ->label('Data de Nascimento')
                    ->displayFormat('d/m/Y')
                    ->maxDate(now()->subYears(18)), // Apenas maiores de idade
    
                Forms\Components\DatePicker::make('data_admissao')
                    ->label('Data de Admissão')
                    ->displayFormat('d/m/Y')
                    ->beforeOrEqual('today'),
    
                Forms\Components\TextInput::make('funcao')
                    ->required()
                    ->label('Função')
                    ->maxLength(100),
    
                Forms\Components\TextInput::make('setor')
                    ->required()
                    ->label('Setor')
                    ->maxLength(100),
    
                Forms\Components\Select::make('tipo_exame')
                    ->options(self::getTiposExame())
                    ->required()
                    ->label('Tipo de Exame'),
    
                Forms\Components\Select::make('status')
                    ->options(self::getStatusExame())
                    ->default('agendado')
                    ->required()
                    ->label('Status do Exame'),
    
                Forms\Components\Select::make('sla')
                    ->options(self::getSlaOptions())
                    ->required()
                    ->label('SLA'),
    
                Group::make([
                    DateTimePicker::make('data_solicitacao')
                        ->required()
                        ->label('Data e Hora da Solicitação')
                        ->default(now())
                        ->reactive()
                        ->seconds(false)
                        ->afterStateUpdated(fn ($set) => $set('senha_confirmacao', null))
                        ->rules([
                            function ($get) {
                                return function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $dataSolicitacao = Carbon::parse($value);
                                    $ontem = Carbon::yesterday();
                    
                                    if ($dataSolicitacao->lessThan($ontem)) {
                                        if ($get('senha_confirmacao') !== 'senha_segura') {
                                            $fail('A data de solicitação está no passado. Insira uma senha válida.');
                                        }
                                    }
                                };
                            },
                        ]),
    
                    TextInput::make('senha_confirmacao')
                        ->label('Senha de Confirmação')
                        ->password()
                        ->reactive()
                        ->hint('Se a data for anterior a ontem, entre em contato com um gestor para autorização.') 
                        ->hidden(fn ($get) => Carbon::parse($get('data_solicitacao'))->greaterThanOrEqualTo(Carbon::yesterday()))
                        ->required(fn ($get) => Carbon::parse($get('data_solicitacao'))->lessThan(Carbon::yesterday()))
                        ->rules([
                            function ($get) {
                                return function (string $attribute, $value, \Closure $fail) {
                                    if ($value !== 'senha_segura') {
                                        $fail('Senha de confirmação incorreta.');
                                    }
                                };
                            },
                        ]),
                ]),   
    
                Forms\Components\TextInput::make('nome_solicitante')
                    ->required()
                    ->label('Nome do Solicitante')
                    ->maxLength(150),
    
                Radio::make('origem_agendamento')
                    ->options([
                        'whatsapp' => 'WhatsApp',
                        'email' => 'E-mail',
                    ])
                    ->default('email')
                    ->reactive()
                    ->label('Origem do Agendamento'),
    
                Forms\Components\TextInput::make('contato_whatsapp')
                    ->label('Contato WhatsApp')
                    ->mask('(99) 99999-9999')
                    ->required(fn ($get) => $get('origem_agendamento') === 'whatsapp')
                    ->hidden(fn ($get) => $get('origem_agendamento') !== 'whatsapp')
                    ->rule('regex:/^\(\d{2}\) \d{5}-\d{4}$/'),
    
                Forms\Components\TextInput::make('email_solicitante')
                    ->label('E-mail do Solicitante')
                    ->required(fn ($get) => $get('origem_agendamento') === 'email')
                    ->hidden(fn ($get) => $get('origem_agendamento') !== 'email')
                    ->rule('regex:/^([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,},\s*)*([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$/')
                    ->helperText('Separe múltiplos e-mails com vírgula.'),
    
                Forms\Components\DateTimePicker::make('data_devolutiva')
                    ->required()
                    ->label('Data e Hora da Devolutiva')
                    ->seconds(false)
                    ->maxDate(now()),

                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
        ->query(Agendamento::query()->with('empresa')) // Carrega o relacionamento empresa antecipadamente
        ->columns([
            Tables\Columns\TextColumn::make('empresa.nome')->label('Empresa'),
            Tables\Columns\TextColumn::make('nome_funcionario')->label('Funcionário'),
            Tables\Columns\TextColumn::make('data_exame')->date('d/m/Y')->label('Data do Exame'),
            Tables\Columns\TextColumn::make('sla')->label('SLA'),
            Tables\Columns\TextColumn::make('tipo_exame')->label('Tipo de Exame'),
            Tables\Columns\BadgeColumn::make('status')
                ->label('Status')
                ->formatStateUsing(fn ($state) => self::getStatusExame()[$state] ?? 'Desconhecido')
                ->colors([
                    'warning' => 'agendado',
                    'danger' => 'cancelado',
                    'success' => 'ASO pendente',
                    'info' => 'ASO enviado',
                    'gray' => 'não compareceu',
                ])
                ->sortable(),
            // Coluna Badge para Não Compareceu (com contagem)
            Tables\Columns\BadgeColumn::make('nao_compareceu_count')
            ->label('Faltas')
            ->colors([
                'success' => fn ($state) => $state === 1,
                'warning' => fn ($state) => $state === 2,
                'danger' => fn ($state) => $state === 3,
            ])
            ->formatStateUsing(fn ($state) => $state > 0 ? "$state º falta" : ''),
            Tables\Columns\BadgeColumn::make('estado_atrasado')
                ->label('Situação')
                ->getStateUsing(fn ($record) => $record ? self::getSituacaoAtrasado($record) : 'Desconhecido')
                ->colors([
                    'danger' => 'Atrasado',
                    'success' => 'No Prazo',
                ]),
            Tables\Columns\TextColumn::make('created_at')->dateTime('d/m/Y')->label('Data de Solicitação'),
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
                Filter::make('data_registro')
                    ->form([Forms\Components\DatePicker::make('data_registro')->label('Data de Registro')])
                    ->query(fn (Builder $query, array $data) => $query->when(!empty($data['data_registro']), function ($query) use ($data) {
                        $query->whereDate('created_at', $data['data_registro']);
                    })),
                Filter::make('ano_registro')
                    ->form([Forms\Components\Select::make('ano')->options(self::getAnosRegistro())])
                    ->query(fn (Builder $query, array $data) => self::applyAnoRegistroFilter($query, $data)),
                Filter::make('mes_registro')
                    ->form([Forms\Components\Select::make('mes')->options(self::getMesesRegistro())])
                    ->query(fn (Builder $query, array $data) => self::applyMesRegistroFilter($query, $data)),
                SelectFilter::make('empresa_id')->relationship('empresa', 'nome')->label('Empresa'),
                SelectFilter::make('tipo_exame')->options(self::getTiposExame())->label('Tipo de Exame'),
                SelectFilter::make('status')->options(self::getStatusExame())->label('Status'),
                Filter::make('data_exame')
                    ->form([Forms\Components\DatePicker::make('data_exame')])
                    ->query(fn (Builder $query, array $data) => self::applyDataExameFilter($query, $data)),
                SelectFilter::make('sla')->options(self::getSlaOptions())->label('SLA'),
                SelectFilter::make('nao_compareceu_count')
                ->label('Filtrar por Faltas')
                ->options([
                    1 => '1º Falta',
                    2 => '2º Falta',
                    3 => '3º Falta',
                ])
                ->query(function (Builder $query, array $data) {
                    if (!empty($data['value'])) {
                        $query->where('nao_compareceu_count', $data['value']);
                    }
                }),
                    // Novo filtro por Situação (Atrasado / No Prazo)
                    SelectFilter::make('situacao')
                    ->label('Situação')
                    ->options([
                        'Atrasado' => 'Atrasado',
                        'No Prazo' => 'No Prazo',
                    ])
                    ->modifyQueryUsing(function (Builder $query, array $data) {
                        // Verifica se o valor do filtro foi passado corretamente
                        if (!empty($data['value'])) {
                            $situacao = $data['value'];
                
                            // Carrega todos os registros da query
                            $registros = $query->get();
                
                            // Filtra os registros com base na lógica de getSituacaoAtrasado
                            $registrosFiltrados = $registros->filter(function ($record) use ($situacao) {
                                return self::getSituacaoAtrasado($record) === $situacao;
                            });
                
                            // Obtém os IDs dos registros filtrados
                            $idsFiltrados = $registrosFiltrados->pluck('id');
                
                            // Aplica o filtro na query original usando os IDs
                            $query->whereIn('id', $idsFiltrados);
                        }
                
                        return $query;
                    })           
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(), // Ação de deletar em massa
            ])
            ->actions([
                Action::make('incrementFaltas')
                ->label('Incrementar Faltas')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->action(function ($record) {
                    // Verifica se o número de faltas não atingiu o limite de 3
                    if ($record->nao_compareceu_count < 3) {
                        // Incrementa o contador de "não compareceu"
                        $newCount = $record->nao_compareceu_count + 1;
            
                        // Atualiza o valor no banco de dados
                        $record->update(['nao_compareceu_count' => $newCount]);
                    }
                })
                ->visible(fn ($record) => $record && $record->status === 'não compareceu' && $record->nao_compareceu_count < 3),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('importar')
                ->label('Importar')
                ->icon('heroicon-o-arrow-down-tray')
                ->form([
                    Forms\Components\Select::make('empresa_id')
                        ->label('Selecione a Empresa')
                        ->options(\App\Models\Empresa::pluck('nome', 'id')) // Lista todas as empresas cadastradas
                        ->required()
                        ->searchable(), // Permite busca dentro do select
                    Forms\Components\FileUpload::make('arquivo')
                        ->label('Arquivo Excel')
                        ->disk('public') // Salva em storage/app/public
                        ->directory('uploads') // Salva os arquivos dentro de storage/app/public/uploads
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel'
                        ]) // Apenas arquivos Excel
                        ->required(),
                ])
                ->action(function ($data) {
                    try {
                        // Obtém o caminho correto do arquivo sem incluir "public/"
                        $filePath = storage_path('app/public/' . $data['arquivo']);
            
                        // Verifica se o arquivo realmente existe
                        if (!file_exists($filePath)) {
                            throw new \Exception("Arquivo não encontrado: {$filePath}");
                        }
            
                        // Obtém a empresa selecionada
                        $empresa = \App\Models\Empresa::find($data['empresa_id']);
                        if (!$empresa) {
                            throw new \Exception("Empresa selecionada não encontrada.");
                        }
            
                        // Importa o arquivo Excel e passa o ID da empresa
                        Excel::import(new AgendamentosImport($data['empresa_id']), $filePath);
            
                        // Notificação de sucesso
                        Notification::make()
                            ->title('Importação concluída')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Log::error('Erro na importação: ' . $e->getMessage());
            
                        Notification::make()
                            ->title('Erro na importação')
                            ->body('Erro: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),               
                    Tables\Actions\Action::make('export')
                    ->label('Exportar')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->action(function ($livewire) {
                        // Obtém os filtros aplicados na tabela
                        $filters = $livewire->tableFilters;
                
                        // Exporta os dados filtrados
                        return Excel::download(new AgendamentosExport($filters), 'agendamentos_' . now()->format('Y-m-d') . '.xlsx');
                    }),
                    Tables\Actions\Action::make('baixar_template')
                    ->label('Baixar Template')
                    ->icon('heroicon-o-cloud-arrow-down')
                    ->action(function () {
                        $filename = 'template_agendamentos.xlsx';
                
                        // Cria o arquivo em tempo real e faz o download
                        return Excel::download(new \App\Exports\AgendamentosTemplateExport, $filename);
                    }),
                
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25); // Define 25 registros por página como padrão
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgendamentos::route('/'),
            'create' => Pages\CreateAgendamento::route('/create'),
            'edit' => Pages\EditAgendamento::route('/{record}/edit'),
        ];
    }

    private static function getEstadosBrasileiros(): array
    {
        return [
            'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá', 'AM' => 'Amazonas',
            'BA' => 'Bahia', 'CE' => 'Ceará', 'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo',
            'GO' => 'Goiás', 'MA' => 'Maranhão', 'MT' => 'Mato Grosso', 'MS' => 'Mato Grosso do Sul',
            'MG' => 'Minas Gerais', 'PA' => 'Pará', 'PB' => 'Paraíba', 'PR' => 'Paraná',
            'PE' => 'Pernambuco', 'PI' => 'Piauí', 'RJ' => 'Rio de Janeiro', 'RN' => 'Rio Grande do Norte',
            'RS' => 'Rio Grande do Sul', 'RO' => 'Rondônia', 'RR' => 'Roraima', 'SC' => 'Santa Catarina',
            'SP' => 'São Paulo', 'SE' => 'Sergipe', 'TO' => 'Tocantins',
        ];
    }

    private static function getTiposExame(): array
    {
        return [
            'admissional' => 'Admissional',
            'periodico' => 'Periódico',
            'demissional' => 'Demissional',
            'retorno_trabalho' => 'Retorno ao Trabalho',
            'mudanca_funcao' => 'Mudança de Função',
            'avaliacao_clinica' => 'Avaliação Clínica',
        ];
    }

    private static function getStatusExame(): array
    {
        return [
            'agendado' => 'Agendado',
            'cancelado' => 'Cancelado',
            'ASO pendente' => 'ASO Pendente',
            'ASO enviado' => 'ASO Enviado',
            'não compareceu' => 'Não Compareceu',
        ];
    }

    private static function getSlaOptions(): array
    {
        return [
            'clinico' => 'Exame Clínico (1 dia)',
            'clinico_complementar' => 'Exame Clínico + Complementar (3 dias)',
            'clinico_acidos' => 'Exame Clínico + Ácidos (5 a 10 dias)',
        ];
    }

    private static function getAnosRegistro(): array
    {
        return Agendamento::query()
            ->selectRaw('YEAR(created_at) as ano')
            ->distinct()
            ->pluck('ano', 'ano')
            ->prepend('Todos', '')
            ->toArray();
    }

    private static function getMesesRegistro(): array
    {
        return [
            '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
            '04' => 'Abril', '05' => 'Maio', '06' => 'Junho',
            '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro',
            '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro',
        ];
    }

    private static function applyAnoRegistroFilter(Builder $query, array $data): void
    {
        if (!empty($data['ano'])) {
            $query->whereYear('created_at', $data['ano']);
        }
    }

    private static function applyMesRegistroFilter(Builder $query, array $data): void
    {
        if (!empty($data['mes'])) {
            $query->whereMonth('created_at', $data['mes']);
        }
    }

    private static function applyDataExameFilter(Builder $query, array $data): void
    {
        if (!empty($data['data_exame'])) {
            $query->whereDate('data_exame', $data['data_exame']);
        }
    }

    private static function getSituacaoAtrasado($record): string
    {
        $dataExame = Carbon::parse($record->data_exame);
        $hoje = Carbon::today();
    
        // Definição do prazo conforme o SLA
        $prazo = match ($record->sla) {
            'clinico' => 1,
            'clinico_complementar' => 3,
            'clinico_acidos' => 10,
            default => 0,
        };
    
        // Verifica se o exame está atrasado
        if (in_array($record->status, ['agendado', 'não compareceu']) && $dataExame->isBefore($hoje)) {
            return 'Atrasado';
        }
    
        return 'No Prazo';
    }
    public static function buscarNomeUnidade(string $cnpj): ?string
    {
        $token = '0e9a9921cdcaf47915ada588639404097eef2785052c31f9b3f1f5456ffec09f'; // Substitua pelo seu token da ReceitaWS

        $response = Http::withoutVerifying()->get("https://www.receitaws.com.br/v1/cnpj/" . preg_replace('/\D/', '', $cnpj), [
            'token' => $token,
        ]);

        if ($response->successful()) {
            return $response->json()['nome'] ?? 'Não encontrado';
        }

        return 'Erro ao buscar CNPJ';
    }
}