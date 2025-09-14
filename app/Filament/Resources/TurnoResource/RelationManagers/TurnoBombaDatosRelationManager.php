<?php

namespace App\Filament\Resources\TurnoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TurnoBombaDatosRelationManager extends RelationManager
{
    protected static string $relationship = 'bombaDatos';
    protected static ?string $title = 'Lecturas de Bombas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('bomba_id')
                    ->relationship('bomba', 'nombre')
                    ->required(),
                Forms\Components\TextInput::make('galonaje_super')
                    ->label('Super (galones)')
                    ->numeric(),
                Forms\Components\TextInput::make('galonaje_regular')
                    ->label('Regular (galones)')
                    ->numeric(),
                Forms\Components\TextInput::make('galonaje_diesel')
                    ->label('Diesel (galones)')
                    ->numeric(),
                Forms\Components\TextInput::make('lectura_cc')
                    ->label('Lectura CC')
                    ->numeric(),
                Forms\Components\FileUpload::make('fotografia')
                    ->label('Fotografía')
                    ->image()
                    ->directory('turnos/bombas'),
                Forms\Components\Textarea::make('observaciones')
                    ->label('Observaciones')
                    ->maxLength(500),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('bomba.nombre')
            ->columns([
                Tables\Columns\TextColumn::make('bomba.nombre')
                    ->label('Bomba')
                    ->sortable(),
                Tables\Columns\TextColumn::make('galonaje_super')
                    ->label('Super')
                    ->suffix(' gal')
                    ->numeric(2),
                Tables\Columns\TextColumn::make('galonaje_regular')
                    ->label('Regular')
                    ->suffix(' gal')
                    ->numeric(2),
                Tables\Columns\TextColumn::make('galonaje_diesel')
                    ->label('Diesel')
                    ->suffix(' gal')
                    ->numeric(2),
                Tables\Columns\TextColumn::make('lectura_cc')
                    ->label('CC')
                    ->numeric(2),
                Tables\Columns\ImageColumn::make('fotografia')
                    ->label('Foto')
                    ->square(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('bomba_id');
    }
}
