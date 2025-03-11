<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class WelcomePage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home'; // Ícone da sidebar
    protected static ?string $navigationLabel = 'Início'; // Nome da página na sidebar
    protected static ?string $title = 'Bem-vindo ao SmartOne C3'; // Título da página
    protected static ?string $slug = ''; // Slug da URL
    protected static string $view = 'filament.pages.welcome'; // View da página

    public function getUserRole(): string
    {
        return auth()->user()->getRoleNames()->first() ?? 'Sem função';
    }

    // Método customizado para verificar se a logo deve ser exibida
    public function hasLogo(): bool
    {
        return file_exists(public_path('path_to_logo/logo.png'));
    }

}