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
            // Buscar gastos existentes para este mes y año
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
                // Reiniciar valores si no hay datos guardados
                $this->gastos = [
                    'impuestos' => 0,
                    'servicios' => 0,
                    'planilla' => 0,
                    'renta' => 0
                ];
                $this->gastosAdicionales = [];
            }
        }
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
        return $table->query(
            GastoMensual::query()
        );
    }
}
