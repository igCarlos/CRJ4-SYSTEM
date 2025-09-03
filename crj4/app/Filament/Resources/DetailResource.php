<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DetailResource\Pages;
use App\Models\Detail;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DetailResource extends Resource
{
    protected static ?string $model = Detail::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // 🚫 No necesitamos form porque no se podrá crear/editar
    public static function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sale.id')
                    ->label('Venta')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale.user.name')
                    ->label('Usuario')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale.payment_method')
                    ->label('Metodo Pago')
                    ->sortable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Producto')
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Cantidad')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Precio Unitario')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale.total')
                    ->label('total')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Registro')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                // 🔹 Filtro por fechas
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Desde'),
                        Forms\Components\DatePicker::make('until')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),

                // 🔹 Filtro por producto
                Tables\Filters\SelectFilter::make('product_id')
                    ->relationship('product', 'name')
                    ->label('Producto'),
            ])
            ->actions([]) // 🚫 sin acciones (editar/eliminar en filas)
            ->bulkActions([]); // 🚫 sin acciones masivas
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDetails::route('/'),
            // 🚫 quitamos create y edit
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}
