<?php

namespace App\Filament\Resources;

use App\Filament\Exports\ProductExporter;
use App\Filament\Exports\ProductsExporter;
use App\Filament\Imports\ProductsImportImporter;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Imports\Jobs\ImportCsv;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction as TablesExportBulkAction;

class ProductResource extends Resource
{
    
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->default(fn () => 'PROD-' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT))
                    ->required()
                    ->unique()
                    ->disabled() // <- el usuario lo verá pero no podrá editarlo
                    ->dehydrated() // <- asegura que aún se guarde en la BD
                    ->rules([
                        Rule::unique('products', 'code')->ignore(fn ($record) => $record?->id),
                    ])
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name',fn($query)=>$query->where('state',true))
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\TextInput::make('description')
                            ->required(),
                        Forms\Components\Toggle::make('state')
                            ->required(),
                    ])
                    ->createOptionAction(function (Action $action) {
                        return $action->visible(fn () => auth()->user()?->can('create_category'));
                    })
                    ->required(),
                Forms\Components\Select::make('supplier_id')
                    ->relationship('supplier', 'name',fn($query)=>$query->where('state',true))
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('address')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\Toggle::make('state')
                            ->required(),
                    ])
                    ->createOptionAction(function (Action $action) {
                        return $action->visible(fn () => auth()->user()?->can('create_supplier'));
                    })
                    ->default(null),
                Forms\Components\TextInput::make('sales_price')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->type('text')
                    ->default(0.00),
                Forms\Components\TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->prefixIcon('heroicon-o-clipboard-document-list')
                    ->disabled()
                    ->dehydrated()
                    ->default(0),
                Forms\Components\Toggle::make('state')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at','desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sales_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('state')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),                
            ])
            ->headerActions([
               
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    TablesExportBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }


}
