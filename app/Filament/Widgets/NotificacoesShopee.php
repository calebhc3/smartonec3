<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use App\Models\Afastamento;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacaoShopeeRetorno;

class NotificacoesShopee extends BaseWidget
{
    protected static ?string $heading = 'Notificações de Afastamentos';
    protected static bool $isLazy = false; // Carregar automaticamente
    protected int|string|array $columnSpan = 'full'; // Ocupar toda a largura

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(Afastamento::whereDate('notificar_shopee_retorno', today()))
            ->columns([
                TextColumn::make('funcionario.nome')
                    ->label('Funcionário')
                    ->searchable(),

                TextColumn::make('data_afastamento')
                    ->label('Data do Afastamento')
                    ->date(),

                TextColumn::make('notificar_shopee_retorno')
                    ->label('Notificação Shopee')
                    ->date(),
            ])
            ->filters([])
            ->defaultSort('notificar_shopee_retorno', 'desc');
    }

    public static function enviarNotificacao()
    {
        $quantidade = Afastamento::whereDate('notificar_shopee_retorno', today())->count();

        if ($quantidade > 0) {
            Mail::to('agendamentos@c3saude.com.br')->send(new NotificacaoShopeeRetorno($quantidade));
        }
    }
}
