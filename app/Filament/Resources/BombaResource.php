<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BombaResource\Pages;
use App\Filament\Resources\BombaResource\RelationManagers;
use App\Models\Bomba;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BombaResource extends Resource
{
    protected static ?string $model = Bomba::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Bombas (Lista)';
    
    protected static ?string $pluralModelLabel = 'Bombas';
    
    protected static bool $shouldRegisterNavigation = false; // Ocultar del menú

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('gasolinera_id')
                    ->relationship('gasolinera', 'nombre')
                    ->required()
                    ->label('Gasolinera'),
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255)
                    ->label('Nombre de la Bomba')
                    ->placeholder('Ej: Bomba 1'),
                Forms\Components\Select::make('tipo')
                    ->options([
                        'Super' => 'Super',
                        'Regular' => 'Regular',
                        'Diesel' => 'Diesel',
                        'Otro' => 'Otro'
                    ])
                    ->required()
                    ->label('Tipo de Combustible'),
                Forms\Components\TextInput::make('precio')
                    ->required()
                    ->numeric()
                    ->step(0.01)
                    ->prefix('Q')
                    ->label('Precio en Quetzales'),
                Forms\Components\TextInput::make('galonaje')
                    ->required()
                    ->numeric()
                    ->step(0.01)
                    ->suffix('gal')
                    ->label('Galonaje')
                    ->placeholder('Ej: 5224'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('gasolinera.nombre')
                    ->searchable()
                    ->label('Gasolinera')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->label('Bomba'),
                Tables\Columns\BadgeColumn::make('tipo')
                    ->colors([
                        'success' => 'Super',
                        'primary' => 'Regular',
                        'warning' => 'Diesel',
                        'secondary' => 'Otro'
                    ])
                    ->label('Tipo'),
                Tables\Columns\TextColumn::make('precio')
                    ->money('GTQ')
                    ->sortable()
                    ->label('Precio'),
                Tables\Columns\TextColumn::make('galonaje')
                    ->suffix(' gal')
                    ->sortable()
                    ->label('Galonaje'),
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
            ->defaultGroup('gasolinera.nombre')
            ->defaultSort('gasolinera.nombre')
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
            'index' => Pages\ListBombas::route('/'),
            'create' => Pages\CreateBomba::route('/create'),
            'edit' => Pages\EditBomba::route('/{record}/edit'),
        ];
    }
}
