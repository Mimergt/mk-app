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
use App\Models\PrecioMensual;
use App\Models\Gasolinera;

class Precios extends Page implements Forms\Contracts\HasForms, Tables\Contracts\HasTable
{
    use Forms\Concerns\InteractsWithForms;
    use Tables\Concerns\InteractsWithTable;
    
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    
    protected static ?string $navigationLabel = 'Precios';
    
    protected static ?string $title = 'Precios de Compra y Venta';
    
    protected static ?string $navigationGroup = 'Finanzas';
    
    protected static ?int $navigationSort = 32;

    protected static string $view = 'filament.pages.precios';
    
    protected static ?string $slug = 'precios';

    // Propiedades para los precios del mes
    public $mesSeleccionado = 8; // Agosto por defecto
    public $precios = [
        'super_compra' => 0,
        'diesel_compra' => 0,
        'regular_compra' => 0
    ];

    public function mount()
    {
        $this->mesSeleccionado = now()->format('n'); // Mes actual
        $this->cargarPreciosMes();
    }

    // Cambiar el mes seleccionado
    public function seleccionarMes($mes): void
    {
        $this->mesSeleccionado = $mes;
        $this->cargarPreciosMes();
    }

    // Cargar los precios del mes seleccionado
    public function cargarPreciosMes(): void
    {
        if ($this->mesSeleccionado) {
            // Buscar precios existentes para este mes y año
            $precioExistente = PrecioMensual::where('anio', now()->year)
                ->where('mes', $this->mesSeleccionado)
                ->first();
            
            if ($precioExistente) {
                $this->precios = [
                    'super_compra' => $precioExistente->super_compra,
                    'diesel_compra' => $precioExistente->diesel_compra,
                    'regular_compra' => $precioExistente->regular_compra,
                ];
            } else {
                // Reiniciar valores si no hay datos guardados
                $this->precios = [
                    'super_compra' => 0,
                    'diesel_compra' => 0,
                    'regular_compra' => 0
                ];
            }
        }
    }

    public function guardarPreciosCompraMes(): void
    {
        try {
            // Validar que al menos un precio tenga valor
            $totalPrecios = array_sum($this->precios);
            
            if ($totalPrecios == 0) {
                Notification::make()
                    ->title('Sin cambios')
                    ->body('No hay precios para guardar. Ingresa al menos un precio.')
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
            
            // Crear o actualizar el registro de precios mensuales
            $precioMensual = PrecioMensual::updateOrCreate(
                [
                    'anio' => $anioActual,
                    'mes' => $this->mesSeleccionado,
                ],
                [
                    'super_compra' => $this->precios['super_compra'] ?? 0,
                    'diesel_compra' => $this->precios['diesel_compra'] ?? 0,
                    'regular_compra' => $this->precios['regular_compra'] ?? 0,
                ]
            );
            
            Notification::make()
                ->title('Precios de Compra Guardados')
                ->body("Los precios de compra de {$meses[$this->mesSeleccionado]} han sido guardados exitosamente.")
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Ocurrió un error al guardar los precios: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function obtenerPromedioVenta(string $tipoCombustible): float
    {
        // Obtener todas las gasolineras activas con precios establecidos
        $columnaPrecios = 'precio_' . strtolower($tipoCombustible);
        
        // Buscar gasolineras que tienen precios establecidos (> 0) y fecha de actualización
        $query = Gasolinera::where($columnaPrecios, '>', 0);
        
        // Si queremos filtrar por mes específico, incluimos gasolineras actualizadas en este mes
        if ($this->mesSeleccionado !== now()->format('n')) {
            // Para meses pasados, buscamos gasolineras actualizadas en ese mes específico
            $query->whereYear('fecha_actualizacion_precios', now()->year)
                  ->whereMonth('fecha_actualizacion_precios', $this->mesSeleccionado);
        }
        
        // Obtener el promedio de precios
        $promedio = $query->avg($columnaPrecios);
        
        // Si no hay datos para el mes específico, obtener el promedio general de gasolineras activas
        if (!$promedio && $this->mesSeleccionado !== now()->format('n')) {
            $promedio = Gasolinera::where($columnaPrecios, '>', 0)->avg($columnaPrecios);
        }
        
        return round($promedio ?? 0.00, 2);
    }

    protected function getFormSchema(): array
    {
        return [];
    }

    public function table(Table $table): Table
    {
        return $table->query(
            PrecioMensual::query()
        );
    }
}
