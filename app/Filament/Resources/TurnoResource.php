<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TurnoResource\Pages;
use App\Filament\Resources\TurnoResource\RelationManagers;
use App\Models\Turno;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TurnoResource extends Resource
{
    protected static ?string $model = Turno::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\Select::make('gasolinera_id')
                            ->relationship('gasolinera', 'nombre')
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable(),
                        Forms\Components\DatePicker::make('fecha')
                            ->required(),
                        Forms\Components\TimePicker::make('hora_inicio')
                            ->seconds(false),
                        Forms\Components\TimePicker::make('hora_fin')
                            ->seconds(false),
                        Forms\Components\Select::make('estado')
                            ->options([
                                'abierto' => 'Abierto',
                                'cerrado' => 'Cerrado',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Dinero')
                    ->schema([
                        Forms\Components\TextInput::make('dinero_apertura')
                            ->label('Dinero Apertura')
                            ->numeric()
                            ->prefix('Q'),
                        Forms\Components\TextInput::make('dinero_cierre')
                            ->label('Dinero Cierre')
                            ->numeric()
                            ->prefix('Q'),
                    ])->columns(2),

                Forms\Components\Section::make('Totales de Ventas')
                    ->schema([
                        Forms\Components\TextInput::make('venta_credito')
                            ->label('Crédito')
                            ->numeric()
                            ->prefix('Q'),
                        Forms\Components\TextInput::make('venta_tarjetas')
                            ->label('Tarjetas')
                            ->numeric()
                            ->prefix('Q'),
                        Forms\Components\TextInput::make('venta_efectivo')
                            ->label('Efectivo')
                            ->numeric()
                            ->prefix('Q'),
                        Forms\Components\TextInput::make('venta_descuentos')
                            ->label('Descuentos')
                            ->numeric()
                            ->prefix('Q'),
                    ])->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('gasolinera.nombre')
                    ->label('Gasolinera')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Operador')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('hora_inicio')
                    ->time('H:i')
                    ->label('Inicio'),
                Tables\Columns\TextColumn::make('hora_fin')
                    ->time('H:i')
                    ->label('Fin'),
                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cerrado' => 'success',
                        'abierto' => 'warning',
                        default => 'gray',
                    }),

                // Totales de Ventas
                Tables\Columns\TextColumn::make('total_ventas')
                    ->label('Total Ventas')
                    ->getStateUsing(fn ($record) =>
                        'Q' . number_format(
                            ($record->venta_credito ?? 0) +
                            ($record->venta_tarjetas ?? 0) +
                            ($record->venta_efectivo ?? 0) -
                            ($record->venta_descuentos ?? 0), 2
                        )
                    )
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('venta_credito')
                    ->label('Crédito')
                    ->money('GTQ')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('venta_tarjetas')
                    ->label('Tarjetas')
                    ->money('GTQ')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('venta_efectivo')
                    ->label('Efectivo')
                    ->money('GTQ')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('venta_descuentos')
                    ->label('Descuentos')
                    ->money('GTQ')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),


                Tables\Columns\TextColumn::make('dinero_apertura')
                    ->label('Dinero Apertura')
                    ->money('GTQ')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('dinero_cierre')
                    ->label('Dinero Cierre')
                    ->money('GTQ')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gasolinera_id')
                    ->relationship('gasolinera', 'nombre')
                    ->label('Gasolinera'),
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Operador'),
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'abierto' => 'Abierto',
                        'cerrado' => 'Cerrado',
                    ]),
                Tables\Filters\Filter::make('fecha')
                    ->form([
                        Forms\Components\DatePicker::make('fecha_desde'),
                        Forms\Components\DatePicker::make('fecha_hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['fecha_desde'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha', '>=', $date),
                            )
                            ->when(
                                $data['fecha_hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver'),
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TurnoBombaDatosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTurnos::route('/'),
            'create' => Pages\CreateTurno::route('/create'),
            'view' => Pages\ViewTurno::route('/{record}'),
            'edit' => Pages\EditTurno::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'supervisor']);
    }
}
