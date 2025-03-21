<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ResumoOperacionalPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar'; // Ícone do menu
    protected static ?string $navigationLabel = 'Resumo Operacional'; // Rótulo do menu
    protected static ?string $title = 'Resumo Operacional'; // Título da página
    protected static ?string $slug = 'resumo-operacional'; // Slug da URL
    protected static ?int $navigationSort = 1; // Define a posição no menu
    protected static ?string $navigationGroup = 'Operação'; // Agrupa os itens

    protected static string $view = 'filament.pages.resumo-operacional-page'; // View da página
    // Adicione métodos ou lógica específica da página, se necessário
}