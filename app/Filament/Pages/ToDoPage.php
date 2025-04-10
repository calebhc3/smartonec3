<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Afastamento;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacaoShopeeRetorno;

class ToDoPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static string $view = 'filament.pages.to-do-page'; // Certifique-se que o Blade está correto
    protected static ?string $navigationLabel = 'Lembretes de Tarefas';
    protected static ?string $title = 'Lembretes de Tarefas';
    protected static ?string $navigationGroup = 'Afastados';

    public function mount()
    {
        $this->enviarNotificacao();
    }

    protected function getViewData(): array
    {
        $afastamentos = $this->getAfastamentos();
    
        return [
            'todos' => [
                [
                    'label' => 'Afastamentos para notificação',
                    'due' => $afastamentos->count(),
                ]
            ],
        ];
    }
    

    protected function getAfastamentos()
    {
        return Afastamento::whereDate('notificar_shopee_retorno', today())->get();
    }

    protected function enviarNotificacao()
    {
        $quantidade = Afastamento::whereDate('notificar_shopee_retorno', today())->count();

        if ($quantidade > 0) {
            Mail::to('agendamentos@c3saude.com.br')->send(new NotificacaoShopeeRetorno($quantidade));
        }
    }
}
