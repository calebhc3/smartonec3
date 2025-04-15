<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AfastamentoResource\Pages;
use App\Models\Afastamento;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Tables\Actions\Action;
use App\Exports\AfastamentoExport;
use App\Imports\AfastamentoImport;
use Filament\Actions\Notification;
use Filament\Tables\Columns\TextInputColumn;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use carbon\Carbon;

class AfastamentoResource extends Resource
{
    protected static ?string $model = Afastamento::class;

    protected static ?string $navigationIcon = 'heroicon-c-user-minus';

    protected static ?string $navigationGroup = 'Afastados';

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'afastamentos'; // Slug da URL

    // Filament Update
        public function updateComment($recordId, $comment)
        {
            $record = Afastamento::find($recordId);
            if ($record) {
                $record->comentario = $comment;
                $record->save();
            }
        }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                // Seção 1: Dados Iniciais
                Forms\Components\Section::make('Dados Iniciais')
                    ->schema([
                        Forms\Components\TextInput::make('nome')->required(),
                        Forms\Components\TextInput::make('cpf')->required(),
                        DatePicker::make('data_nascimento')
                        ->label('Data de Nascimento')
                        ->reactive()
                        ->afterStateUpdated(function (Carbon|string|null $state, Set $set) {
                            if (!$state) {
                                $set('idade', null);
                                return;
                            }
                    
                            $dataNascimento = Carbon::parse($state);
                            $idade = $dataNascimento->age;
                    
                            $set('idade', $idade);
                        }),
                    
                    TextInput::make('idade')
                        ->label('Idade')
                        ->numeric()
                        ->disabled(), // impede edição manual, já que é calculado automaticamente
                        Forms\Components\TextInput::make('cnpj_unidade')
                        ->label('CNPJ da Unidade')
                        ->required()
                        ->mask('99.999.999/9999-99')
                        ->live(debounce: 500)
                        ->afterStateUpdated(function ($state, callable $set) {
                            if (strlen(preg_replace('/\D/', '', $state)) === 14) {
                                $set('nome_unidade', self::buscarNomeUnidade($state));
                            }
                        }),
    
                        Forms\Components\TextInput::make('nome_unidade')
                        ->label('Nome da Unidade')
                        ->readonly(),    
                        Forms\Components\DatePicker::make('data_admissao'),
                        Forms\Components\TextInput::make('cargo'),
                        Forms\Components\TextInput::make('setor'),
                        Forms\Components\Select::make('genero')->options([
                            'Masculino' => 'Masculino',
                            'Feminino' => 'Feminino',
                            'Outro' => 'Outro',
                        ]),
                        Forms\Components\DatePicker::make('data_psc'),
                        Forms\Components\DatePicker::make('data_notificacao')
                        ->label('Data de Notificação do Início do Afastamento')
                        ->default(now())
                        ->reactive(),
                        Forms\Components\TextInput::make('codigo'),
                    ])
                    ->collapsible(),

                // Seção 2: Controle Interno C3 Saúde
                Forms\Components\Section::make('Controle Interno C3 Saúde')
                    ->schema([
                        Forms\Components\DatePicker::make('data_carta_dut_enviada_assinatura')->nullable()->label('Data do Envio da Carta DUT para Assinatura'),
                        Forms\Components\DatePicker::make('data_carta_dut_recebida_assinada')->nullable()->label('Data do Recebimento da Carta DUT'),
                        Forms\Components\DatePicker::make('data_carta_dut_enviada_colaborador')->nullable()->label('Data do Envio da Carta DUT para Colaborador'),
                        Forms\Components\DatePicker::make('data_ultimo_dia_trabalhado')->nullable()->label('Data do Último Dia Trabalhado'),
                        Forms\Components\Toggle::make('condicao_abertura_cat')->label('Condição de Abertura CAT'),
                        Forms\Components\TextInput::make('cid')->label('CID'),
                        Forms\Components\TextInput::make('patologia'),
                        Forms\Components\Select::make('especie_beneficio_inss')
                        ->label('Espécie do Benefício INSS')
                        ->options([
                            'b31_auxilio_doenca_previdenciario' => 'B31 - Auxílio Doença Previdenciário',
                            'b91_auxilio_doenca_acidentario' => 'B91 - Auxílio Doença Acidentário',
                            'b32_aposentadoria_por_invalidez' => 'B32 - Aposentadoria por Invalidez',
                        ])
                        ->searchable()
                        ->preload()
                        ->required(),                    
                        Forms\Components\Toggle::make('afastada_atividades'),
                        Forms\Components\Toggle::make('afastados_inss'),
                        Forms\Components\Toggle::make('limbo_previdenciario'),
                    ])
                    ->collapsible(),

                // Seção 3: Dados Iniciais da Perícia
                Forms\Components\Section::make('Dados Iniciais da Perícia')
                    ->schema([
                        Forms\Components\Toggle::make('alta_antecipada'),
                        Forms\Components\DatePicker::make('entrada_pericia'),
                        Forms\Components\DatePicker::make('data_pericia'),
                        Forms\Components\Select::make('tipo_pericia')
                        ->label('Tipo de Perícia')
                        ->options([
                            'presencial' => 'Presencial',
                            'documental' => 'Documental',
                            'a_iniciar' => 'A Iniciar',
                            'nao_realizada' => 'Não Realizada',
                        ])
                        ->required()
                        ->searchable(),
                        Forms\Components\Toggle::make('pericia_realizada'),
                        Forms\Components\TextInput::make('numero_beneficio'),
                        Forms\Components\Select::make('status_pericia')
                        ->label('Status da Perícia')
                        ->options([
                            'deferido' => 'Deferido',
                            'indeferido' => 'Indeferido',
                            'em_analise' => 'Em Análise',
                            'pericia_cancelada' => 'Perícia Cancelada',
                            'em_agendamento' => 'Em Agendamento',
                        ])
                        ->required()
                        ->searchable(),
                        Forms\Components\Select::make('motivo')
                        ->options( [
                            'constatao_de_incapacidade_laborativa' => 'Constatação de Incapacidade Laborativa',
                            'nao_constatacao_da_Incapacidade_laborativa' => 'Não Constatação da Incapacidade Laborativa',
                        ]),
                        Forms\Components\Toggle::make('nexo_tecnico'),
                        Forms\Components\Toggle::make('contestacao'),
                    ])
                    ->collapsible(),

                // Seção 4: Notificação Shopee Retorno Colaborador
                Forms\Components\Section::make('Notificação Shopee Retorno Colaborador')
                    ->schema([
                        Forms\Components\DatePicker::make('termino_previsto_beneficio'),
                        Forms\Components\DatePicker::make('notificar_shopee_retorno'),
                        Forms\Components\DatePicker::make('data_prevista_exame_retorno'),
                        Forms\Components\TextInput::make('clinica'),
                        Forms\Components\Select::make('afastamento_inicial')
                        ->options([
                            'Afastado' => 'Afastado',
                            'Falta_histórico' => 'Falta Histórico',
                        ]),
                        Forms\Components\DatePicker::make('data_recebimento_aso'),
                        Forms\Components\DatePicker::make('data_envio_aso_shopee'),
                    ])
                    ->collapsible(),

                // Seção 5: Informação para Folha de Pagamento
                Forms\Components\Section::make('Informação para Folha de Pagamento')
                    ->schema([
                        Forms\Components\Select::make('status_atual')
                        ->label('Status Atual')
                        ->options([
                            'recorrente' => 'Recorrente',
                            'afastado' => 'Afastado',
                            'liberado_ao_retorno' => 'Liberado ao Retorno',
                            'desligado' => 'Desligado',
                            'liberado_com_termo' => 'Liberado com Termo',
                            'liberado_com_restricao' => 'Liberado com Restrição',
                            'licenca_maternidade' => 'Licença Maternidade',
                            'pericia_cancelada' => 'Perícia Cancelada',
                            'rescisao_indireta' => 'Rescisão Indireta',
                            'falecimento' => 'Falecimento',
                        ])
                        ->required()
                        ->searchable(),
                        Forms\Components\DatePicker::make('data_retorno_atividades'),
                        Forms\Components\TextInput::make('periodo_restricao'),
                        Forms\Components\TextInput::make('comentario')
                            ->label('Comentário')
                            ->reactive()
                            ->afterStateUpdated(function (string $state, Set $set) {
                                // Atualiza o comentário no banco de dados
                                $set('comentario', $state);
                            }),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('cpf')->sortable()->searchable()->label('CPF'),
                Tables\Columns\BadgeColumn::make('status_pericia')
                ->label('Status da Perícia')
                ->formatStateUsing(function (string $state): string {
                    return match ($state) {
                        'deferido' => 'Deferido',
                        'indeferido' => 'Indeferido',
                        'em_analise' => 'Em Análise',
                        'pericia_cancelada' => 'Perícia Cancelada',
                        'em_agendamento' => 'Em Agendamento',
                        default => ucfirst(str_replace('_', ' ', $state)),
                    };
                })
                ->colors([
                    'success' => ['deferido'],
                    'danger' => ['indeferido', 'pericia_cancelada'],
                    'warning' => ['em_analise', 'em_agendamento'],
                ]),
                Tables\Columns\BadgeColumn::make('status_atual')
                ->label('Status Atual')
                ->formatStateUsing(function (string $state): string {
                    return match ($state) {
                        'recorrente' => 'Recorrente',
                        'afastado' => 'Afastado',
                        'liberado_ao_retorno' => 'Liberado ao Retorno',
                        'desligado' => 'Desligado',
                        'liberado_com_termo' => 'Liberado com Termo',
                        'liberado_com_restricao' => 'Liberado com Restrição',
                        'licenca_maternidade' => 'Licença Maternidade',
                        'pericia_cancelada' => 'Perícia Cancelada',
                        'rescisao_indireta' => 'Rescisão Indireta',
                        'falecimento' => 'Falecimento',
                        default => ucfirst(str_replace('_', ' ', $state)),
                    };
                })
                ->colors([
                    'primary' => [
                        'recorrente',
                        'liberado_ao_retorno',
                        'liberado_com_termo',
                        'liberado_com_restricao',
                    ],
                    'warning' => [
                        'afastado',
                        'licenca_maternidade',
                    ],
                    'danger' => [
                        'desligado',
                        'rescisao_indireta',
                        'falecimento',
                    ],
                    'gray' => [
                        'pericia_cancelada',
                    ],
                ]),
            ])
            ->filters([])
            ->actions([
                Action::make('prorrogar')
                    ->label('Prorrogar')
                    ->icon('heroicon-o-plus-circle')
                    ->modalHeading('Prorrogar Afastamento')
                    ->modalSubmitActionLabel('Confirmar')
                    ->form([
                        Forms\Components\DatePicker::make('nova_data')
                            ->label('Nova data de término do benefício')
                            ->required(),
                    ])
                    ->action(function (array $data, \App\Models\Afastamento $record): void {
                        // Atualiza o registro atual como prorrogado
                        $record->update([
                            'is_prorrogado' => true,
                        ]);
            
                        // Clona o afastamento com a nova data
                        $novoAfastamento = $record->replicate([
                            'termino_previsto_beneficio' => $data['nova_data'],
                        ]);
                        $novoAfastamento->termino_previsto_beneficio = $data['nova_data'];
                        $novoAfastamento->is_prorrogado = false;
                        $novoAfastamento->save();
                    })
                    ->visible(fn (\App\Models\Afastamento $record) => !$record->is_prorrogado),
            ])
            ->headerActions([
                Tables\Actions\Action::make('importar')
                ->label('Importar Afastamentos')
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
            
                        // Importa o arquivo Excel e passa o ID da empresa
                        Excel::import(new AfastamentoImport ($data['empresa_id']), $filePath);
            
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
                    ->action(function ($livewire) {
                        // Obtém os filtros aplicados na tabela
                        $filters = $livewire->tableFilters;
                
                        // Exporta os dados filtrados
                        return Excel::download(new AgendamentosExport($filters), 'agendamentos_' . now()->format('Y-m-d') . '.xlsx');
                    }),
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25) // Define 25 registros por página como padrão
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAfastamentos::route('/'),
            'create' => Pages\CreateAfastamento::route('/create'),
            'edit' => Pages\EditAfastamento::route('/{record}/edit'),
        ];
    }
}
