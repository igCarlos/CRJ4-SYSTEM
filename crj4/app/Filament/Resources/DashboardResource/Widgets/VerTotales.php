<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VerTotales extends BaseWidget
{
    
    protected function getStats(): array
    {
        return [
             Stat::make('Total Categorias', Category::count())
            ->icon('heroicon-O-tag'),
            Stat::make('Total Productos', Product::count())
            ->icon('heroicon-o-rectangle-stack'),
            Stat::make('Total Proveedores', Supplier::count())
            ->icon('heroicon-o-clipboard-document-list'),
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && $user->can('widget_VerTotales');
       
    }
}
