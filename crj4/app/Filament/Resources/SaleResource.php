<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Filament\Resources\SaleResource\RelationManagers;
use App\Models\Sale;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Sale')
                ->schema([
                        Forms\Components\TextInput::make('user_id')
                            ->label('Id Usuario')
                            ->default(Auth::user()->id)
                            ->required()
                            ->disabled() // <- el usuario lo verá pero no podrá editarlo
                            ->dehydrated() // <- asegura que aún se guarde en la BD
                            ,
                        TextInput::make('user_name')
                            ->label('Usuario')
                            ->required()
                            ->disabled()
                            ->afterStateHydrated(function ($set, $record) {
                                if ($record) {
                                    // cuando editas, tomar el nombre del usuario relacionado
                                    $set('user_name', $record->user?->name);
                                } else {
                                    // cuando creas, tomar el usuario logueado
                                    $set('user_name', Auth::user()->name);
                                }
                            })
                            ,
                        TextInput::make('customer_id')
                            ->label('Cliente')
                            ->required()
                            ->disabled()
                            ,
                        Forms\Components\Select::make('customer_id')
                            ->label('Cliente')
                            ->relationship('customer','name')
                            ->searchable()
                            ->required(),
                        Forms\Components\DateTimePicker::make('sale_date')
                            ->required(),
                        Forms\Components\TextInput::make('total')
                            ->required()
                            ->numeric()
                            ->reactive()
                            ->disabled() // <- el usuario lo verá pero no podrá editarlo
                            ->afterStateHydrated(function ($set, $get) {
                                $details = $get('sales_details') ?? [];
                                $set('total', collect($details)->sum('subtotal'));
                            })
                            ->dehydrated() // <- asegura que aún se guarde en la BD
                            ,
                        Forms\Components\Select::make('payment_method')
                             ->options([
                                'transferencia' => 'Transferencia',
                                'efectivo' => 'Efectivo',
                                'tarjeta' => 'Trajeta',
                            ])
                            ->default('efectivo')
                            ->required(),
                        Forms\Components\Select::make('status_of_sale')
                            ->options([
                                'realizada' => 'Realizada',
                                'pendiente' => 'Pendiente',
                                'cancelada' => 'Cancelada',
                            ])
                            ->default('pendiente')
                            ->required(),
                        Forms\Components\Toggle::make('state')
                            ->required(),
                ])
                ->columns(2),
                Section::make('Detalle')
                 ->schema([
                     Forms\Components\Repeater::make('sales_details')
                     ->relationship() 
                    ->schema([
                         Forms\Components\Select::make('product_id')
                            ->relationship('product','name')
                            ->searchable()
                            ->required()
                            ->reactive()
                           ->afterStateUpdated(function ($state, $set, $get) {
                                if ($state) {
                                    $product = \App\Models\Product::find($state);
                                    if ($product) {
                                        $set('unit_price', $product->sales_price); 
                                        $set('subtotal', $product->sales_price * ($get('amount') ?? 1));

                                        // recalcular total sumando todos los subtotales
                                          $set('../../total', collect($get('../../sales_details'))->sum('subtotal'));
                                    }
                                }
                            }),
                         Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $unitPrice = $get('unit_price') ?? 0;
                                $set('subtotal', $unitPrice * $state);

                                $product = \App\Models\Product::find($get('product_id'));
                                 if ($product && $state > $product->stock) {
                                        \Filament\Notifications\Notification::make()
                                            ->title('Stock insuficiente')
                                            ->body("Solo quedan {$product->stock} unidades de {$product->name}.")
                                            ->danger()
                                            ->send();

                                            
                                    }

                                // actualizar total
                                 $set('../../total', collect($get('../../sales_details'))->sum('subtotal'));
                            }),
                        Forms\Components\TextInput::make('unit_price')
                            ->numeric()
                            ->disabled() // <- el usuario lo verá pero no podrá editarlo
                            ->dehydrated() // <- asegura que aún se guarde en la BD
                            ->label('Precio Unitario'),

                         Forms\Components\TextInput::make('subtotal')
                            ->numeric()
                            ->disabled() // <- el usuario lo verá pero no podrá editarlo
                            ->dehydrated() // <- asegura que aún se guarde en la BD
                            ->label('Subtotal'),
                    ])
                ->columns(2)
                 ])
                 
            ]); 
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method'),
                Tables\Columns\TextColumn::make('status_of_sale')
                    ->colors([
                        'success' => 'realizada',
                        'warning' => 'pendiente',
                        'danger'  => 'cancelada',
                    ]),
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
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }
}
