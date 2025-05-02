<?php

namespace App\Filament\Pages;

use App\Models\Afastamento;
use Filament\Pages\Page;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;

class PainelAfastamentos extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $title = 'Painel de Afastamentos';
    protected static ?string $slug = 'painel-afastamentos';
    protected static string $view = 'filament.pages.painel-afastamentos';
    protected static ?string $navigationGroup = 'Afastados';

    protected function getHeaderWidgets(): array
    {
        return [
            AfastamentosHeaderOverview::class,
        ];
    }
    protected function getTableQuery(): Builder
    {
        return Afastamento::query()->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('nome')
                ->label('Colaborador')
                ->searchable(),

            TextColumn::make('data_psc')
                ->label('Data PSC')
                ->date()
                ->sortable(),

            TextColumn::make('data_ultimo_dia_trabalhado')
                ->label('Último dia trabalhado')
                ->date()
                ->sortable(),

            TextColumn::make('entrada_pericia')
                ->label('Entrada na Perícia')
                ->date()
                ->sortable(),

            TextColumn::make('data_pericia')
                ->label('Data da Perícia')
                ->date()
                ->sortable(),

                TextColumn::make('tipo_pericia')
                ->label('Tipo de Perícia')
                ->formatStateUsing(fn ($state) => match ($state) {
                    'presencial' => 'Presencial',
                    'documental' => 'Documental',
                    'a_iniciar' => 'A Iniciar',
                    'nao_realizada' => 'Não Realizada',
                    default => ucfirst($state),
                })
                ->badge()
                ->color(fn ($state) => match ($state) {
                    'presencial' => 'info',
                    'documental' => 'success',
                    'a_iniciar' => 'warning',
                    'nao_realizada' => 'danger',
                    default => 'gray',
                }),

            TextColumn::make('especie_beneficio_inss')
                ->label('Espécie INSS')
                ->formatStateUsing(fn ($state) => match ($state) {
                    'b31_auxilio_doenca_previdenciario' => 'B31 - Auxílio Doença Previdenciário',
                    'b91_auxilio_doenca_acidentario' => 'B91 - Auxílio Doença Acidentário',
                    'b32_aposentadoria_por_invalidez' => 'B32 - Aposentadoria por Invalidez',
                    default => ucfirst($state),
                })
                ->searchable(),

                TextColumn::make('status_pericia')
                ->label('Status da Perícia')
                ->formatStateUsing(fn ($state) => match ($state) {
                    'deferido' => 'Deferido',
                    'indeferido' => 'Indeferido',
                    'em_analise' => 'Em Análise',
                    'pericia_cancelada' => 'Perícia Cancelada',
                    'em_agendamento' => 'Em Agendamento',
                    default => ucfirst($state),
                })
                ->badge()
                ->color(fn ($state) => match ($state) {
                    'deferido' => 'success',
                    'indeferido' => 'danger',
                    'em_analise' => 'warning',
                    'pericia_cancelada' => 'gray',
                    'em_agendamento' => 'info',
                    default => 'gray',
                }),

        TextColumn::make('status_atual')
            ->label('Status Atual')
            ->formatStateUsing(fn ($state) => match ($state) {
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
                default => ucfirst($state),
            })
            ->badge()
            ->color(fn ($state) => match ($state) {
                'afastado' => 'warning',
                'recorrente' => 'info',
                'liberado_ao_retorno' => 'success',
                'liberado_com_termo', 'liberado_com_restricao' => 'success',
                'licenca_maternidade' => 'gray',
                'pericia_cancelada', 'desligado', 'rescisao_indireta', 'falecimento' => 'gray',
                default => 'gray',
            }),

            TextColumn::make('notificar_shopee_retorno')
                ->label('Notificar Shopee?')
                ->formatStateUsing(fn ($state) => $state ? 'Sim' : 'Não'),
        ];
    }
    protected function getTableEmptyStateHeading(): string
    {
        return 'Nenhum afastamento encontrado';
    }
    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Não há afastamentos cadastrados no sistema.';
    }
    protected function getTableActions(): array
    {
        return []; // Sem botões de editar/excluir
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('status')
                ->label('Status')
                ->options([
                    'ativo' => 'Ativo',
                    'encerrado' => 'Encerrado',
                    'prorrogado' => 'Prorrogado',
                ]),
        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return true;
    }
}
