<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\GerenciamentoUsuariosWidget;
use App\Filament\Widgets\UltimasAtividadesWidget;
use App\Filament\Widgets\ExclusoesRegistrosWidget;

class PainelProdutividade extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $title = 'Painel de Produtividade';

    protected static ?string $navigationLabel = 'Painel de Produtividade';

    protected static ?string $slug = 'painel-prod';

    protected static ?int $navigationSort = 1; // Define a posição no menu

    protected static ?string $navigationGroup = 'Gestão'; // Agrupa os itens
    // ❗ ALTERADO DE 'protected' PARA 'public'
    public function getWidgets(): array
    {
        return [
            UltimasAtividadesWidget::class,
            ExclusoesRegistrosWidget::class,
        ];
    }

    public function getColumns(): int
    {
        return 2; // Define que os widgets ocupam até 2 colunas no grid
    }
}
