<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\Jobs\ExportCsv;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Spatie\Permission\Models\Role;

class UserResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $label = 'Usuarios';

    public static function form(Form $form): Form
    {
        return $form
            
           ->schema([
                Section::make("Informacion Del Usuario")
                 ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                    ->unique(ignoreRecord: true)
                        ,
                    Forms\Components\Select::make('roles')
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->options(function () {
                            $query = Role::query();
                            if (!Auth::user()?->hasRole('super_admin')) {
                                $query->where('name', '!=', 'super_admin');
                            }

                            return $query->pluck('name', 'id');
                        })
                        ->default(function () {
                            return Role::where('name', 'vendedor')->pluck('id')->toArray();
                        }),
                    Forms\Components\DateTimePicker::make('email_verified_at'),
                    Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255)
                    ->hidden(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord)
                    ,
                    Forms\Components\FileUpload::make('image_url')
                    ->imageEditor()
                    ->disk('public')
                     ->previewable()
                    ,
                        
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at','desc')
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Avatar')
                    ->circular()
                    ->searchable()
                    ->url(fn ($record) => asset('storage/' . $record->image_url))          
                    ->getStateUsing(fn ($record) =>
        $record->image_url
            ? asset('storage/' . $record->image_url)
            : 'https://ui-avatars.com/api/?name=' . urlencode($record->name)
    )
                ->disabledClick()
    ,
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ,
                 Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'gerente' => 'gray',
                        'vendedor' => 'warning',
                        'super_admin' => 'success',
                        'proveedor' => 'danger',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Actions\Action::make('toggle_verificacion')
                ->label(fn (User $record) => $record->email_verified_at ? 'Desverificar' : 'Verificar')
                ->icon(fn (User $record) => $record->email_verified_at ? 'heroicon-o-x-circle' : 'heroicon-o-check-badge')
                ->button()
                ->visible(fn () => auth()->user()->can('verificar_correo_user') || auth()->user()->can('desverificar_correo_user'))
                ->action(function (User $user) {
                    if ($user->email_verified_at) {
                        // Si ya estaba verificado, desverificar
                        $user->email_verified_at = null;
                    } else {
                        // Si no estaba verificado, verificar
                        $user->email_verified_at = now();
                    }
                    $user->save();
                })
                ,
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),                            
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

     public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'verificar_correo',
            'desverificar_correo',
        ];
    }

}
