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

class AgendamentoResource extends Resource
{
    protected static ?string $model = Agendamento::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Operação';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('access_scheduling');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('empresa_id')
                    ->relationship('empresa', 'nome')
                    ->required()
                    ->label('Empresa'),
                Forms\Components\Select::make('unidade_id')
                    ->relationship('unidade', 'nome')
                    ->required()
                    ->label('Unidade'),
                Forms\Components\Select::make('estado_atendimento')
                    ->options(self::getEstadosBrasileiros())
                    ->required()
                    ->label('Estado do Atendimento'),
                Forms\Components\TextInput::make('cidade_atendimento')
                    ->required()
                    ->label('Cidade do Atendimento'),
                Forms\Components\DatePicker::make('data_exame')
                    ->required()
                    ->label('Data do Exame'),
                Forms\Components\TimePicker::make('horario_exame')
                    ->required()
                    ->label('Horário do Exame'),
                Forms\Components\TextInput::make('clinica_agendada')
                    ->required()
                    ->label('Clínica Agendada'),
                Forms\Components\TextInput::make('nome_funcionario')
                    ->required()
                    ->label('Nome do Funcionário'),
                Forms\Components\TextInput::make('contato_whatsapp')
                    ->required()
                    ->label('Contato WhatsApp')
                    ->mask('(99) 99999-9999'),
                Forms\Components\TextInput::make('doc_identificacao_rg')
                    ->required()
                    ->label('RG')
                    ->mask('99.999.999-99'),
                Forms\Components\TextInput::make('doc_identificacao_cpf')
                    ->required()
                    ->label('CPF')
                    ->mask('999.999.999-99'),
                Forms\Components\DatePicker::make('data_nascimento')
                    ->required()
                    ->label('Data de Nascimento')
                    ->displayFormat('d/m/Y')
                    ->maxDate(now()),
                Forms\Components\DatePicker::make('data_admissao')
                    ->required()
                    ->label('Data de Admissão')
                    ->displayFormat('d/m/Y'),
                Forms\Components\TextInput::make('funcao')
                    ->required()
                    ->label('Função'),
                Forms\Components\TextInput::make('setor')
                    ->required()
                    ->label('Setor'),
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
                            ->afterStateUpdated(fn ($set) => $set('senha_confirmacao', null)) // Limpa a senha ao mudar a data
                            ->rules([
                                function ($get) {
                                    return function (string $attribute, $value, \Closure $fail) use ($get) {
                                        $dataSolicitacao = Carbon::parse($value);
                                        $ontem = Carbon::yesterday();
                    
                                        if ($dataSolicitacao->lessThan($ontem)) {
                                            // Se a data for antes de ontem, verifica a senha
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
                            ->hint('Se a data for anterior a ontem, entre em contato com um gestor para autorização.') // Dica explicativa                            ->hidden(fn ($get) => Carbon::parse($get('data_solicitacao'))->greaterThanOrEqualTo(Carbon::yesterday())) // Oculta se a data for hoje ou no futuro
                            ->required(fn ($get) => Carbon::parse($get('data_solicitacao'))->lessThan(Carbon::yesterday())) // Exige senha só se a data for antes de ontem
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
                    ->label('Nome do Solicitante'),
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
                    ->hidden(fn ($get) => $get('origem_agendamento') !== 'whatsapp'),
                Forms\Components\TextInput::make('email_solicitante')
                    ->label('E-mail do Solicitante')
                    ->required(fn ($get) => $get('origem_agendamento') === 'email')
                    ->hidden(fn ($get) => $get('origem_agendamento') !== 'email')
                    ->rule('regex:/^([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,},\s*)*([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$/')
                    ->helperText('Separe múltiplos e-mails com vírgula.'),
                Forms\Components\DateTimePicker::make('data_devolutiva')
                    ->required()
                    ->label('Data e Hora da Devolutiva')
                    ->maxDate(now()),
                Forms\Components\Select::make('comparecimento')
                    ->label('Comparecimento')
                    ->options([
                        'nao_informado' => 'Não Informado',
                        'compareceu' => 'Compareceu',
                        'nao_compareceu' => 'Não Compareceu',
                    ])
                    ->default('nao_informado'),
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('empresa.nome'),
                Tables\Columns\TextColumn::make('nome_funcionario'),
                Tables\Columns\TextColumn::make('data_exame')->date('d/m/Y'),
                Tables\Columns\TextColumn::make('sla'),
                Tables\Columns\TextColumn::make('tipo_exame'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => self::getStatusExame()[$state] ?? 'Desconhecido')
                    ->colors([
                        'warning' => 'agendado',
                        'danger' => 'cancelado',
                        'success' => 'ASO ok',
                        'info' => 'ASO enviado',
                        'gray' => 'não compareceu',
                    ])
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('estado_atrasado')
                    ->label('Situação')
                    ->getStateUsing(fn ($record) => self::getSituacaoAtrasado($record))
                    ->colors([
                        'danger' => 'Atrasado',
                        'success' => 'No Prazo',
                    ]),
            ])
            ->filters([
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
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(), // Ação de deletar em massa
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('importar')
                    ->label('Importar Agendamentos')
                    ->form([
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
            
                            // Importa o arquivo Excel
                            Excel::import(new AgendamentosImport, $filePath);
            
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
                    ->label('Exportar para Excel')
                    ->icon('heroicon-m-inbox')
                    ->action(fn () => Excel::download(new AgendamentosExport, 'agendamentos_' . now()->format('Y-m-d') . '.xlsx')),
            ]);
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
            'ASO ok' => 'ASO OK',
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
        $prazo = match ($record->sla) {
            'clinico' => 1,
            'clinico_complementar' => 3,
            'clinico_acidos' => 10,
            default => 0,
        };
        return ($record->status === 'pendente' && $dataExame->diffInDays($hoje) > $prazo) 
            ? 'Atrasado' 
            : 'No Prazo';
    }
}