<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GasolineraResource\Pages;
use App\Filament\Resources\GasolineraResource\RelationManagers;
use App\Models\Gasolinera;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GasolineraResource extends Resource
{
    protected static ?string $model = Gasolinera::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'Gasolineras';
    
    protected static ?string $pluralModelLabel = 'Gasolineras';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255)
                    ->label('Nombre'),
                Forms\Components\TextInput::make('ubicacion')
                    ->maxLength(255)
                    ->label('Ubicación'),
                    
                Forms\Components\Section::make('Precios de Combustibles')
                    ->description('Configuración de precios por galón (CC no maneja precio, solo lectura)')
                    ->schema([
                        Forms\Components\TextInput::make('precio_super')
                            ->numeric()
                            ->step(0.01)
                            ->label('Precio Super (Q)')
                            ->default(0.00)
                            ->placeholder('0.00'),
                            
                        Forms\Components\TextInput::make('precio_regular')
                            ->numeric()
                            ->step(0.01)
                            ->label('Precio Regular (Q)')
                            ->default(0.00)
                            ->placeholder('0.00'),
                            
                        Forms\Components\TextInput::make('precio_diesel')
                            ->numeric()
                            ->step(0.01)
                            ->label('Precio Diesel (Q)')
                            ->default(0.00)
                            ->placeholder('0.00'),
                    ])
                    ->columns(3),
                    
                Forms\Components\Section::make('Información CC')
                    ->description('Centro de Cargas (CC) disponible en todas las bombas - Solo lectura, sin precio')
                    ->schema([
                        Forms\Components\Placeholder::make('cc_info')
                            ->label('')
                            ->content('ℹ️ CC está habilitado automáticamente en todas las bombas de esta gasolinera como una lectura adicional sin precio.')
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->label('Nombre'),
                Tables\Columns\TextColumn::make('ubicacion')
                    ->searchable()
                    ->label('Ubicación'),
                Tables\Columns\TextColumn::make('precio_super')
                    ->money('GTQ')
                    ->label('Precio Super')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('precio_regular')
                    ->money('GTQ')
                    ->label('Precio Regular')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('precio_diesel')
                    ->money('GTQ')
                    ->label('Precio Diesel')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('cc_disponible')
                    ->label('CC Disponible')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->tooltip('Centro de Cargas disponible en todas las bombas')
                    ->state(fn ($record): bool => true), // Siempre true ahora
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Creado'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Actualizado'),
            ])
            ->filters([
                // Filtros pueden agregarse aquí en el futuro
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
            'index' => Pages\ListGasolineras::route('/'),
            'create' => Pages\CreateGasolinera::route('/create'),
            'edit' => Pages\EditGasolinera::route('/{record}/edit'),
        ];
    }
}
