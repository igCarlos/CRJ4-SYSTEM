<?php

namespace App\Filament\Exports;

use App\Models\Product;
use App\Models\Products;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductsExporter extends Exporter
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            
            ExportColumn::make('name'),
            ExportColumn::make('code'),
            ExportColumn::make('description'),
            ExportColumn::make('category_id'),
            ExportColumn::make('supplier_id'),
            ExportColumn::make('sales_price'),
            ExportColumn::make('stock'),    
            ExportColumn::make('state'),    
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Productos exportados correctamente...' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' Fallo la exportacion.';
        }

        return $body;
    }
}
