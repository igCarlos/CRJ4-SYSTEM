<?php

namespace App\Filament\Imports;

use App\Models\Product;
use App\Models\ProductsImport;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ProductsImportImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name'),
            ImportColumn::make('code'),
            ImportColumn::make('description'),
            ImportColumn::make('category_id'),
            ImportColumn::make('supplier_id'),
            ImportColumn::make('sales_price'),
            ImportColumn::make('stock'),    
            ImportColumn::make('state'),    
        ];
    }

    public function resolveRecord(): ?Product
    {
         // Evitar duplicados por código
        $existing = Product::where('code', $this->data['code'])->first();

        if ($existing) {
            return null; // ya existe, no lo importe
        }

        return new Product([
            'name'         => $this->data['name'],
            'code'         => $this->data['code'],
            'description'  => $this->data['description'],
            'category_id'  => $this->data['category_id'],
            'supplier_id'  => $this->data['supplier_id'],
            'sales_price'  => $this->data['sales_price'],
            'stock'        => $this->data['stock'],
            'state'        => true,
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Tu importación se completó. Se importaron ' . number_format($import->successful_rows) . ' producto(s).';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' Fallaron ' . number_format($failedRowsCount) . ' fila(s).';
        }

        return $body;
    }
}
