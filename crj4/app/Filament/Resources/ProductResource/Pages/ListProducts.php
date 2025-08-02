<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Imports\ProductsImportImporter;
use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
        Actions\CreateAction::make(),
        ImportAction::make()
            ->importer(ProductsImportImporter::class)
            ->visible(fn () => auth()->user()->can('import_product')),
            ];
    }

}
