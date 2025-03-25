<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Afastamento;

class ToDoPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static string $view = 'filament.pages.to-do-page'; // Certifique-se que está apontando para o Blade correto
    protected static ?string $navigationLabel = 'Lembretes de Tarefas';
    protected static ?string $title = 'Lembretes de Tarefas';
    protected static ?string $navigationGroup = 'Afastados';

    // Método que passa os dados para a view
    protected function getViewData(): array
    {
        return [
            'todos' => $this->getTodos(), // Garante que $todos esteja disponível no Blade
        ];
    }

    // Método que busca os dados
    protected function getTodos(): array
    {
        return [

        ];
    }
}