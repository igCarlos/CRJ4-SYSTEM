<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Filament\Resources\SaleResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSale extends CreateRecord
{
    protected static string $resource = SaleResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // asegurarnos que sales_details exista
        $details = $data['sales_details'] ?? [];

        // calcular total sumando subtotales
        $data['total'] = collect($details)->sum('subtotal');

        return $data;
    }

    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Venta creada')
            ->body('La venta se creó correctamente.')
            ->success()
            ->send();
    }


}
