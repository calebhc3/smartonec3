<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AfastamentoResource\Pages;
use App\Models\Afastamento;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Actions\Action;
use App\Exports\AfastamentoExport;
use App\Imports\AfastamentoImport;
use Filament\Actions\Notification;
use Filament\Tables\Columns\TextInputColumn;

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
                        Forms\Components\TextInput::make('cpf')->required()->unique(),
                        Forms\Components\DatePicker::make('data_admissao'),
                        Forms\Components\TextInput::make('cargo'),
                        Forms\Components\TextInput::make('setor'),
                        Forms\Components\Select::make('genero')->options([
                            'Masculino' => 'Masculino',
                            'Feminino' => 'Feminino',
                            'Outro' => 'Outro',
                        ]),
                        Forms\Components\DatePicker::make('data_psc'),
                        Forms\Components\DatePicker::make('data_notificacao'),
                        Forms\Components\TextInput::make('andamento_processo_shopee'),
                        Forms\Components\TextInput::make('codigo'),
                        Forms\Components\DatePicker::make('data_nascimento'),
                        Forms\Components\TextInput::make('idade')->numeric(),
                    ])
                    ->collapsible(),

                // Seção 2: Controle Interno C3 Saúde
                Forms\Components\Section::make('Controle Interno C3 Saúde')
                    ->schema([
                        Forms\Components\DatePicker::make('data_carta_dut_enviada_assinatura')->nullable(),
                        Forms\Components\DatePicker::make('data_carta_dut_recebida_assinada')->nullable(),
                        Forms\Components\DatePicker::make('data_carta_dut_enviada_colaborador')->nullable(),
                        Forms\Components\DatePicker::make('data_ultimo_dia_trabalhado')->nullable(),
                        Forms\Components\Toggle::make('condicao_abertura_cat'),
                        Forms\Components\TextInput::make('cid'),
                        Forms\Components\TextInput::make('patologia'),
                        Forms\Components\Textarea::make('descricao_patologia'),
                        Forms\Components\TextInput::make('especie_beneficio_inss'),
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
                        Forms\Components\TextInput::make('tipo_pericia'),
                        Forms\Components\Toggle::make('pericia_realizada'),
                        Forms\Components\TextInput::make('numero_beneficio'),
                        Forms\Components\TextInput::make('status_pericia'),
                        Forms\Components\Textarea::make('motivo'),
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
                        Forms\Components\DatePicker::make('afastamento_inicial'),
                        Forms\Components\DatePicker::make('data_recebimento_aso'),
                        Forms\Components\DatePicker::make('data_envio_aso_shopee'),
                    ])
                    ->collapsible(),

                // Seção 5: Informação para Folha de Pagamento
                Forms\Components\Section::make('Informação para Folha de Pagamento')
                    ->schema([
                        Forms\Components\TextInput::make('status_atual'),
                        Forms\Components\DatePicker::make('data_retorno_atividades'),
                        Forms\Components\TextInput::make('periodo_restricao'),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('cpf')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('data_carta_dut_enviada_assinatura')->label('Envio da carta de DUT para assinatura'),
                // Exibe o comentário com opção de editar
                Tables\Columns\TextColumn::make('comentario')
                    ->label('Comentário')
                    ->searchable(),
                Tables\Columns\TextInputColumn::make('comentario')
                    ->label('Editar Comentário'),
            ])
            ->filters([])
            ->actions([Tables\Actions\EditAction::make()])
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAfastamentos::route('/'),
            'create' => Pages\CreateAfastamento::route('/create'),
            'edit' => Pages\EditAfastamento::route('/{record}/edit'),
        ];
    }
}
