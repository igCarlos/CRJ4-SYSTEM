<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DetailResource\Pages;
use App\Models\Detail;
use App\Models\Sale;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Mail\Events\MessageSent;

use function Laravel\Prompts\alert;

class DetailResource extends Resource
{
    protected static ?string $model = Detail::class;
    protected static ?string $label = "Comprobar Factura";
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // 🚫 No necesitamos form porque no se podrá crear/editar
    public static function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('created_at','desc')
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

                // 🔹 Filtro por Categoria
                Tables\Filters\SelectFilter::make('product_id')
                    ->relationship('product', 'name')
                    ->label('Producto'),
            ])
            ->actions([
                ViewAction::make('ver_detalle')
                    ->label('Ver Detalle')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detalle de la Venta')
                    ->modalWidth('lg') // tamaño del modal
                    ->record(fn ($record) => $record->load('sale','sale.customer', 'sale.user', 'sale.sales_details.product'))
                    
            ]) // 🚫 sin acciones (editar/eliminar en filas)
            ->bulkActions([]) // 🚫 sin acciones masivas
           ->headerActions([
             Tables\Actions\Action::make('buscar_factura')
                ->form([
                    Forms\Components\TextInput::make('sale_id')
                        ->label('Buscar ID de Venta')
                        ->numeric(),
                ])
             ->action(function (array $data) {
                    if (!empty($data['sale_id'])) {
                        $sale = Sale::find($data['sale_id']); // Busca por la clave primaria del modelo

                        if ($sale) {
                            Notification::make()
                                ->title('La Factura Que Solicita Es Valida.')
                                ->body("La venta con ID {$data['sale_id']} existe.")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('La Factura Que Solicita No Es Valida')
                                ->body("La venta con ID {$data['sale_id']} no existe.")
                                ->danger()
                                ->send();
                        }
                    }
                }),
            ]);
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
