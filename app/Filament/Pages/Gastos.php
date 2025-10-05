<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Gasto;
use App\Models\Gasolinera;
use App\Models\Combustible;
use App\Models\GastoMensual;

class Gastos extends Page implements Forms\Contracts\HasForms, Tables\Contracts\HasTable
{
    use Forms\Concerns\InteractsWithForms;
    use Tables\Concerns\InteractsWithTable;
    
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    
    protected static ?string $navigationLabel = 'Gastos';
    
    protected static ?string $title = 'Gestión de Gastos Operativos';
    
    protected static ?string $navigationGroup = 'Finanzas';
    
    protected static ?int $navigationSort = 31;

    protected static string $view = 'filament.pages.gastos';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }
    
    protected static ?string $slug = 'gastos';

    // Propiedades para los gastos del mes
    public $mesSeleccionado = 8; // Agosto por defecto
    public $gastos = [
        'impuestos' => 0,
        'servicios' => 0,
        'planilla' => 0,
        'renta' => 0
    ];
    
    // Lista de gastos adicionales
    public $gastosAdicionales = [];

    public function mount()
    {
        $this->mesSeleccionado = now()->format('n'); // Mes actual
        $this->cargarGastosMes();
    }

    // Cambiar el mes seleccionado
    public function seleccionarMes($mes): void
    {
        $this->mesSeleccionado = $mes;
        $this->cargarGastosMes();
    }

    // Cargar los gastos del mes seleccionado
    public function cargarGastosMes(): void
    {
        if ($this->mesSeleccionado) {
            // Buscar gastos existentes para este mes y año en la tabla gastos_mensuales
            $gastoExistente = GastoMensual::where('anio', now()->year)
                ->where('mes', $this->mesSeleccionado)
                ->first();
            
            if ($gastoExistente) {
                $this->gastos = [
                    'impuestos' => $gastoExistente->impuestos,
                    'servicios' => $gastoExistente->servicios,
                    'planilla' => $gastoExistente->planilla,
                    'renta' => $gastoExistente->renta,
                ];
                
                // Cargar gastos adicionales desde JSON
                $this->gastosAdicionales = $gastoExistente->gastos_adicionales ?? [];
            } else {
                // Si no hay datos en gastos_mensuales, calcular desde la tabla gastos
                $gastosDelMes = Gasto::whereMonth('fecha', $this->mesSeleccionado)
                    ->whereYear('fecha', now()->year)
                    ->get();
                
                if ($gastosDelMes->isNotEmpty()) {
                    // Calcular totales por categoría
                    $totales = $gastosDelMes->groupBy('categoria')->map(function ($gastos) {
                        return $gastos->sum('monto');
                    });
                    
                    $this->gastos = [
                        'impuestos' => $totales->get('administrativo', 0),
                        'servicios' => $totales->get('operativo', 0),
                        'planilla' => $totales->get('operativo', 0) * 0.3, // Aproximación
                        'renta' => $totales->get('mantenimiento', 0) * 0.2, // Aproximación
                    ];
                } else {
                    // Reiniciar valores si no hay datos guardados
                    $this->gastos = [
                        'impuestos' => 0,
                        'servicios' => 0,
                        'planilla' => 0,
                        'renta' => 0
                    ];
                }
                $this->gastosAdicionales = [];
            }
        }
    }
    
    // Obtener el total de gastos del mes desde la tabla gastos
    public function getTotalGastosMes()
    {
        return Gasto::whereMonth('fecha', $this->mesSeleccionado)
            ->whereYear('fecha', now()->year)
            ->sum('monto');
    }
    
    // Obtener gastos por categoría del mes
    public function getGastosPorCategoria()
    {
        return Gasto::whereMonth('fecha', $this->mesSeleccionado)
            ->whereYear('fecha', now()->year)
            ->selectRaw('categoria, SUM(monto) as total')
            ->groupBy('categoria')
            ->pluck('total', 'categoria')
            ->toArray();
    }

    public function guardarGastosMes(): void
    {
        try {
            // Validar que al menos un gasto tenga valor
            $totalGastos = array_sum($this->gastos);
            
            if ($totalGastos == 0) {
                Notification::make()
                    ->title('Sin cambios')
                    ->body('No hay gastos para guardar. Ingresa al menos un monto.')
                    ->warning()
                    ->send();
                return;
            }
            
            // Obtener nombres de los meses
            $meses = [
                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
            ];
            
            // Obtener el año actual
            $anioActual = now()->year;
            
            // Crear o actualizar el registro de gastos mensuales
            $gastoMensual = GastoMensual::updateOrCreate(
                [
                    'anio' => $anioActual,
                    'mes' => $this->mesSeleccionado,
                ],
                [
                    'impuestos' => $this->gastos['impuestos'] ?? 0,
                    'servicios' => $this->gastos['servicios'] ?? 0,
                    'planilla' => $this->gastos['planilla'] ?? 0,
                    'renta' => $this->gastos['renta'] ?? 0,
                    'gastos_adicionales' => $this->gastosAdicionales
                ]
            );
            
            Notification::make()
                ->title('Gastos Guardados')
                ->body("Los gastos de {$meses[$this->mesSeleccionado]} han sido guardados exitosamente. Total: Q" . number_format($gastoMensual->total, 2))
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Ocurrió un error al guardar los gastos: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function añadirGasto()
    {
        // Agregar un nuevo campo de gasto adicional
        $this->gastosAdicionales[] = [
            'id' => uniqid(),
            'descripcion' => '',
            'monto' => 0.00
        ];
    }

    public function eliminarGastoAdicional($gastoId)
    {
        $this->gastosAdicionales = array_filter($this->gastosAdicionales, function($gasto) use ($gastoId) {
            return $gasto['id'] !== $gastoId;
        });
        
        // Reindexar el array
        $this->gastosAdicionales = array_values($this->gastosAdicionales);
    }

    protected function getFormSchema(): array
    {
        return [];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Gasto::query()
                    ->whereMonth('fecha', $this->mesSeleccionado)
                    ->whereYear('fecha', now()->year)
                    ->orderBy('fecha', 'desc')
            )
            ->columns([
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('categoria')
                    ->label('Categoría')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'operativo' => 'primary',
                        'mantenimiento' => 'warning',
                        'administrativo' => 'info',
                        'inventario' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                TextColumn::make('monto')
                    ->label('Monto')
                    ->money('GTQ')
                    ->sortable(),
                TextColumn::make('proveedor')
                    ->label('Proveedor')
                    ->limit(30),
                TextColumn::make('gasolinera.nombre')
                    ->label('Gasolinera')
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categoria')
                    ->label('Categoría')
                    ->options([
                        'operativo' => 'Operativo',
                        'mantenimiento' => 'Mantenimiento',
                        'administrativo' => 'Administrativo',
                        'inventario' => 'Inventario',
                    ]),
                Tables\Filters\SelectFilter::make('gasolinera_id')
                    ->label('Gasolinera')
                    ->relationship('gasolinera', 'nombre'),
            ])
            ->actions([
                EditAction::make()
                    ->form([
                        DatePicker::make('fecha')
                            ->required(),
                        Select::make('categoria')
                            ->required()
                            ->options([
                                'operativo' => 'Operativo',
                                'mantenimiento' => 'Mantenimiento',
                                'administrativo' => 'Administrativo',
                                'inventario' => 'Inventario',
                            ]),
                        TextInput::make('descripcion')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('monto')
                            ->required()
                            ->numeric()
                            ->prefix('Q'),
                        TextInput::make('proveedor')
                            ->maxLength(255),
                        Select::make('gasolinera_id')
                            ->relationship('gasolinera', 'nombre')
                            ->required(),
                    ]),
                DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Nuevo Gasto')
                    ->form([
                        DatePicker::make('fecha')
                            ->required()
                            ->default(now()),
                        Select::make('categoria')
                            ->required()
                            ->options([
                                'operativo' => 'Operativo',
                                'mantenimiento' => 'Mantenimiento',
                                'administrativo' => 'Administrativo',
                                'inventario' => 'Inventario',
                            ]),
                        TextInput::make('descripcion')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('monto')
                            ->required()
                            ->numeric()
                            ->prefix('Q'),
                        TextInput::make('proveedor')
                            ->maxLength(255),
                        Select::make('gasolinera_id')
                            ->relationship('gasolinera', 'nombre')
                            ->required(),
                    ])
                    ->using(function (array $data): Model {
                        return Gasto::create($data);
                    }),
            ])
            ->emptyStateDescription('No hay gastos registrados para este mes.')
            ->emptyStateHeading('Sin gastos')
            ->striped();
    }
}
