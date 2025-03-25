<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AfastadoResource\Pages;
use App\Models\Afastado;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;

class AfastadoResource extends Resource
{
    protected static ?string $model = Afastado::class;

    protected static ?string $navigationIcon = 'heroicon-c-user-minus';

    protected static ?string $navigationGroup = 'Afastamentos';

    protected static ?int $navigationSort = 4;

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
                Tables\Columns\TextColumn::make('cargo'),
                Tables\Columns\BooleanColumn::make('afastada_atividades'),
            ])
            ->filters([])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAfastados::route('/'),
            'create' => Pages\CreateAfastado::route('/create'),
            'edit' => Pages\EditAfastado::route('/{record}/edit'),
        ];
    }
}
