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
                    
                Forms\Components\Section::make('Configuración de Combustibles')
                    ->description('Configuración de tipos de combustible disponibles')
                    ->schema([
                        Forms\Components\Toggle::make('cc_activo')
                            ->label('Combustible CC Activo')
                            ->helperText('Activar o desactivar el manejo de combustible CC en esta gasolinera')
                            ->default(true)
                            ->inline(false),
                            
                        Forms\Components\TextInput::make('precio_cc')
                            ->numeric()
                            ->step(0.01)
                            ->label('Precio CC (Q)')
                            ->helperText('Precio por galón del combustible CC')
                            ->visible(fn (Forms\Get $get): bool => $get('cc_activo'))
                            ->placeholder('0.00'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Precios de Combustibles')
                    ->description('Configuración de precios por galón')
                    ->schema([
                        Forms\Components\TextInput::make('precio_super')
                            ->numeric()
                            ->step(0.01)
                            ->label('Precio Super (Q)')
                            ->placeholder('0.00'),
                            
                        Forms\Components\TextInput::make('precio_regular')
                            ->numeric()
                            ->step(0.01)
                            ->label('Precio Regular (Q)')
                            ->placeholder('0.00'),
                            
                        Forms\Components\TextInput::make('precio_diesel')
                            ->numeric()
                            ->step(0.01)
                            ->label('Precio Diesel (Q)')
                            ->placeholder('0.00'),
                    ])
                    ->columns(3),
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
                Tables\Columns\IconColumn::make('cc_activo')
                    ->boolean()
                    ->label('CC Activo')
                    ->tooltip('Combustible CC disponible'),
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
                Tables\Columns\TextColumn::make('precio_cc')
                    ->money('GTQ')
                    ->label('Precio CC')
                    ->toggleable()
                    ->visible(fn ($record): bool => $record->cc_activo ?? false),
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
                Tables\Filters\TernaryFilter::make('cc_activo')
                    ->label('CC Activo')
                    ->placeholder('Todos')
                    ->trueLabel('Con CC')
                    ->falseLabel('Sin CC'),
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
