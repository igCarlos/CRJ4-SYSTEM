<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use Filament\Widgets\Widget;

class UsuarioLogueado extends Widget
{
    protected static string $view = 'filament.resources.dashboard-resource.widgets.usuario-logueado';
    protected int| string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && $user->can('widget_UsuarioLogueado');
       
    }

    public function getViewData(): array
    {
        return[
            'rol'=> auth()->user()->getRoleNames()->first(),
            'user'=> auth()->user()->name
        ];
    }
}
