<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Gasolinera;
use App\Models\Bomba;
use App\Models\HistorialBomba;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class GestionBombas extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    
    protected static ?string $navigationLabel = 'Bombas';
    
    protected static ?string $title = 'Gestión de Bombas por Gasolinera';

    protected static string $view = 'filament.pages.gestion-bombas';
    
    public $selectedGasolinera = null;
    public $gasolineras = [];
    public $bombas = [];
    public $gasolineraActual = null;
    public $bombaData = []; // Para almacenar datos temporales
    public $preciosData = []; // Para almacenar precios editables
    public $preciosGuardadosExito = false; // Para mostrar mensaje de éxito
    public $totalBombasActivas = 0; // Total de bombas activas
    public $ventasHoy = 0; // Ventas del día actual
    public $mensajeExito = false; // Para mostrar mensajes de éxito

    public function mount()
    {
        $this->gasolineras = Gasolinera::select('id', 'nombre', 'ubicacion')->get()->toArray();
    }

    public function selectGasolinera($gasolineraId)
    {
        $this->selectedGasolinera = $gasolineraId;
        $this->gasolineraActual = Gasolinera::find($gasolineraId);
        $this->loadBombas();
        $this->inicializarBombasDefecto();
        $this->cargarPrecios();
        $this->calcularEstadisticas();
        $this->preciosGuardadosExito = false; // Reset del mensaje de éxito
        $this->mensajeExito = false; // Reset del mensaje de éxito
    }

    public function loadBombas()
    {
        if ($this->selectedGasolinera) {
            $bombas = Bomba::where('gasolinera_id', $this->selectedGasolinera)
                ->with(['historial' => function($query) {
                    $query->orderBy('created_at', 'desc');
                }])
                ->orderBy('nombre')
                ->get();
            
            // Preparar datos para la vista
            $bombasData = [];
            $bombaDataTemp = [];
            
            foreach ($bombas as $bomba) {
                // Obtener precios de la gasolinera
                $gasolinera = $bomba->gasolinera;
                
                // Estructura de datos para cada bomba con los 4 tipos de combustible
                $bombaInfo = [
                    'id' => $bomba->id,
                    'nombre' => $bomba->nombre,
                    'numero' => str_replace('Bomba ', '', $bomba->nombre), // Extraer número del nombre
                    'estado' => $bomba->estado,
                    'gasolinera_id' => $bomba->gasolinera_id,
                    'combustibles' => [
                        'super' => [
                            'tipo' => 'Super',
                            'galonaje' => $bomba->galonaje_super,
                            'precio' => $gasolinera->precio_super,
                        ],
                        'regular' => [
                            'tipo' => 'Regular', 
                            'galonaje' => $bomba->galonaje_regular,
                            'precio' => $gasolinera->precio_regular,
                        ],
                        'diesel' => [
                            'tipo' => 'Diesel',
                            'galonaje' => $bomba->galonaje_diesel,
                            'precio' => $gasolinera->precio_diesel,
                        ],
                        'cc' => [
                            'tipo' => 'CC',
                            'galonaje' => $bomba->galonaje_cc,
                            'precio' => null, // CC sin precio
                        ]
                    ],
                    'historial' => $bomba->historial->toArray()
                ];
                
                $bombasData[] = $bombaInfo;
                
                // Datos temporales para wire:model (galonajes editables)
                $bombaDataTemp[$bomba->id] = [
                    'galonaje_super' => $bomba->galonaje_super,
                    'galonaje_regular' => $bomba->galonaje_regular,
                    'galonaje_diesel' => $bomba->galonaje_diesel,
                    'galonaje_cc' => $bomba->galonaje_cc,
                    'estado' => $bomba->estado
                ];
            }
            
            $this->bombas = $bombasData;
            $this->bombaData = $bombaDataTemp;
        }
    }

    public function inicializarBombasDefecto()
    {
        if (!$this->selectedGasolinera) return;

        // Verificar si ya existen bombas
        $bombasExistentes = Bomba::where('gasolinera_id', $this->selectedGasolinera)->count();
        
        if ($bombasExistentes == 0) {
            // Crear 4 bombas para la gasolinera
            for ($numeroBomba = 1; $numeroBomba <= 4; $numeroBomba++) {
                Bomba::create([
                    'gasolinera_id' => $this->selectedGasolinera,
                    'nombre' => "Bomba {$numeroBomba}",
                    'galonaje_super' => 0.00,
                    'galonaje_regular' => 0.00,
                    'galonaje_diesel' => 0.00,
                    'galonaje_cc' => 0.00,
                    'estado' => 'activa'
                ]);
            }
            
            // Recargar las bombas después de crearlas
            $this->loadBombas();
            $this->dispatch('bomba-updated', 'Bombas iniciales creadas: 4 bombas con todos los combustibles');
        }
    }

    public function eliminarBomba($bombaId)
    {
        try {
            $bomba = Bomba::find($bombaId);
            if ($bomba) {
                $nombreBomba = $bomba->nombre;
                $bomba->delete();
                $this->dispatch('bomba-updated', "Bomba {$nombreBomba} eliminada correctamente");
                $this->loadBombas();
            }
        } catch (\Exception $e) {
            $this->dispatch('bomba-error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    public function crearBomba($nombre)
    {
        try {
            // Verificar que no existan más de 4 bombas
            $bombasExistentes = Bomba::where('gasolinera_id', $this->selectedGasolinera)->count();
            
            if ($bombasExistentes >= 4) {
                $this->dispatch('bomba-error', 'No se pueden crear más de 4 bombas por gasolinera');
                return;
            }
            
            Bomba::create([
                'gasolinera_id' => $this->selectedGasolinera,
                'nombre' => $nombre,
                'galonaje_super' => 0.00,
                'galonaje_regular' => 0.00,
                'galonaje_diesel' => 0.00,
                'galonaje_cc' => 0.00,
                'estado' => 'activa'
            ]);
            
            $this->loadBombas();
            $this->dispatch('bomba-updated', "Bomba {$nombre} creada exitosamente");
            
        } catch(\Exception $e) {
            $this->dispatch('bomba-error', 'Error al crear bomba: ' . $e->getMessage());
        }
    }

    public function guardarBomba($bombaId)
    {
        try {
            $bomba = Bomba::find($bombaId);
            if (!$bomba || !isset($this->bombaData[$bombaId])) {
                throw new \Exception('Bomba no encontrada');
            }
            
            $data = $this->bombaData[$bombaId];
            
            // Guardar valores anteriores para historial
            $valoresAnteriores = [
                'galonaje_super' => $bomba->galonaje_super,
                'galonaje_regular' => $bomba->galonaje_regular,
                'galonaje_diesel' => $bomba->galonaje_diesel,
                'galonaje_cc' => $bomba->galonaje_cc,
                'estado' => $bomba->estado
            ];
            
            // Actualizar la bomba
            $bomba->update([
                'galonaje_super' => $data['galonaje_super'] ?? $bomba->galonaje_super,
                'galonaje_regular' => $data['galonaje_regular'] ?? $bomba->galonaje_regular,
                'galonaje_diesel' => $data['galonaje_diesel'] ?? $bomba->galonaje_diesel,
                'galonaje_cc' => $data['galonaje_cc'] ?? $bomba->galonaje_cc,
                'estado' => $data['estado'] ?? $bomba->estado
            ]);
            
            // Registrar historial para cada campo que cambió
            foreach (['galonaje_super', 'galonaje_regular', 'galonaje_diesel', 'galonaje_cc', 'estado'] as $campo) {
                if ($valoresAnteriores[$campo] != $bomba->$campo) {
                    HistorialBomba::create([
                        'bomba_id' => $bomba->id,
                        'user_id' => auth()->id(),
                        'campo_modificado' => $campo,
                        'valor_anterior' => $valoresAnteriores[$campo],
                        'valor_nuevo' => $bomba->$campo,
                        'observaciones' => ucfirst(str_replace('_', ' ', $campo)) . " actualizado"
                    ]);
                }
            }
            
            // Recargar datos
            $this->loadBombas();
            $this->dispatch('bomba-updated', "✅ {$bomba->nombre} guardada correctamente");
            
        } catch(\Exception $e) {
            $this->dispatch('bomba-error', 'Error al guardar bomba: ' . $e->getMessage());
        }
    }

    public function agregarNuevaBomba()
    {
        try {
            // Verificar límite de 4 bombas
            $bombasExistentes = Bomba::where('gasolinera_id', $this->selectedGasolinera)->count();
            
            if ($bombasExistentes >= 4) {
                $this->dispatch('bomba-error', 'No se pueden crear más de 4 bombas por gasolinera');
                return;
            }
            
            // Determinar el siguiente número de bomba
            $bombas = Bomba::where('gasolinera_id', $this->selectedGasolinera)->get();
            $numeros = [];
            
            foreach($bombas as $bomba) {
                if(preg_match('/Bomba (\d+)/', $bomba->nombre, $matches)) {
                    $numeros[] = (int)$matches[1];
                }
            }
            
            $siguienteNumero = empty($numeros) ? 1 : max($numeros) + 1;
            $nombreBomba = "Bomba {$siguienteNumero}";
            
            // Crear la nueva bomba con todos los tipos de combustible
            Bomba::create([
                'gasolinera_id' => $this->selectedGasolinera,
                'nombre' => $nombreBomba,
                'galonaje_super' => 0.00,
                'galonaje_regular' => 0.00,
                'galonaje_diesel' => 0.00,
                'galonaje_cc' => 0.00,
                'estado' => 'activa'
            ]);
            
            $this->loadBombas();
            $this->dispatch('bomba-updated', "✅ {$nombreBomba} agregada exitosamente con todos los tipos de combustible");
            
        } catch(\Exception $e) {
            $this->dispatch('bomba-error', 'Error al agregar bomba: ' . $e->getMessage());
        }
    }

    public function volver()
    {
        $this->selectedGasolinera = null;
        $this->bombas = [];
        $this->gasolineraActual = null;
        $this->preciosData = [];
        $this->preciosGuardadosExito = false;
    }

    public function cargarPrecios()
    {
        if ($this->gasolineraActual) {
            $this->preciosData = [
                'precio_super' => $this->gasolineraActual->precio_super,
                'precio_regular' => $this->gasolineraActual->precio_regular,
                'precio_diesel' => $this->gasolineraActual->precio_diesel,
            ];
            
            // CC ya no maneja precio - información removida
        }
    }

    public function guardarPrecios()
    {
        try {
            if (!$this->gasolineraActual) {
                throw new \Exception('Gasolinera no encontrada');
            }

            // Validar que los precios sean números válidos
            $precios = [
                'precio_super' => floatval($this->preciosData['precio_super'] ?? 0),
                'precio_regular' => floatval($this->preciosData['precio_regular'] ?? 0),
                'precio_diesel' => floatval($this->preciosData['precio_diesel'] ?? 0),
                'fecha_actualizacion_precios' => now(), // Agregar timestamp actual
            ];

            // Actualizar la gasolinera
            $this->gasolineraActual->update($precios);
            
            // Recargar datos
            $this->gasolineraActual = Gasolinera::find($this->selectedGasolinera);
            $this->loadBombas(); // Recargar bombas para mostrar precios actualizados
            
            // Marcar como exitoso para mostrar mensaje
            $this->preciosGuardadosExito = true;
            $this->mensajeExito = true;
            
            $this->dispatch('bomba-updated', '✅ Precios actualizados correctamente');
            
            // Auto-ocultar el mensaje después de 5 segundos
            $this->dispatch('hide-success-after-delay');
            
        } catch(\Exception $e) {
            $this->preciosGuardadosExito = false;
            $this->dispatch('bomba-error', 'Error al guardar precios: ' . $e->getMessage());
        }
    }
    
    private function calcularEstadisticas()
    {
        if (!$this->selectedGasolinera) {
            return;
        }
        
        // Calcular bombas activas
        $this->totalBombasActivas = Bomba::where('gasolinera_id', $this->selectedGasolinera)
            ->where('estado', 'activa')
            ->count();
            
        // Calcular ventas del día (simulado - aquí podrías implementar lógica real)
        $this->ventasHoy = rand(1500, 8000); // Valor simulado
    }
    
    public function guardarCambios()
    {
        try {
            // Guardar cambios de galonajes de todas las bombas
            foreach ($this->bombaData as $bombaId => $datos) {
                $bomba = Bomba::find($bombaId);
                if ($bomba) {
                    $bomba->update([
                        'galonaje_super' => $datos['galonaje_super'] ?? $bomba->galonaje_super,
                        'galonaje_regular' => $datos['galonaje_regular'] ?? $bomba->galonaje_regular,
                        'galonaje_diesel' => $datos['galonaje_diesel'] ?? $bomba->galonaje_diesel,
                        'galonaje_cc' => $datos['galonaje_cc'] ?? $bomba->galonaje_cc,
                        'estado' => $datos['estado'] ?? $bomba->estado,
                    ]);
                    
                    // Registrar en historial si hay cambios en galonajes
                    if (isset($datos['galonaje_super']) || isset($datos['galonaje_regular']) || 
                        isset($datos['galonaje_diesel']) || isset($datos['galonaje_cc'])) {
                        HistorialBomba::create([
                            'bomba_id' => $bombaId,
                            'galonaje_super' => $datos['galonaje_super'] ?? $bomba->galonaje_super,
                            'galonaje_regular' => $datos['galonaje_regular'] ?? $bomba->galonaje_regular,
                            'galonaje_diesel' => $datos['galonaje_diesel'] ?? $bomba->galonaje_diesel,
                            'galonaje_cc' => $datos['galonaje_cc'] ?? $bomba->galonaje_cc,
                        ]);
                    }
                }
            }
            
            // Recargar datos
            $this->loadBombas();
            $this->calcularEstadisticas();
            
            $this->dispatch('bomba-updated', '✅ Cambios guardados exitosamente');
            
        } catch(\Exception $e) {
            $this->dispatch('bomba-error', 'Error al guardar cambios: ' . $e->getMessage());
        }
    }
    
    public function actualizarPrecios()
    {
        try {
            // Validar que hay datos de precios
            if (!$this->preciosData || empty($this->preciosData)) {
                $this->dispatch('bomba-error', 'No hay datos de precios para actualizar');
                return;
            }

            // Validar que los precios sean números válidos
            $precios = [
                'precio_super' => floatval($this->preciosData['precio_super'] ?? 0),
                'precio_regular' => floatval($this->preciosData['precio_regular'] ?? 0),
                'precio_diesel' => floatval($this->preciosData['precio_diesel'] ?? 0),
                'fecha_actualizacion_precios' => now(),
            ];

            // Actualizar la gasolinera
            $this->gasolineraActual->update($precios);
            
            // Recargar datos
            $this->gasolineraActual = Gasolinera::find($this->selectedGasolinera);
            $this->loadBombas();
            
            // Marcar como exitoso para mostrar mensaje
            $this->mensajeExito = true;
            
            $this->dispatch('bomba-updated', '✅ Precios actualizados correctamente');
            
        } catch(\Exception $e) {
            $this->mensajeExito = false;
            $this->dispatch('bomba-error', 'Error al actualizar precios: ' . $e->getMessage());
        }
    }
    
    public function crearBombasIniciales()
    {
        try {
            if (!$this->selectedGasolinera) {
                $this->dispatch('bomba-error', 'Debe seleccionar una gasolinera primero');
                return;
            }

            // Crear 4 bombas por defecto
            for ($i = 1; $i <= 4; $i++) {
                Bomba::create([
                    'gasolinera_id' => $this->selectedGasolinera,
                    'nombre' => "Bomba {$i}",
                    'galonaje_super' => 0.00,
                    'galonaje_regular' => 0.00,
                    'galonaje_diesel' => 0.00,
                    'galonaje_cc' => 0.00,
                    'estado' => 'activa',
                ]);
            }

            // Recargar datos
            $this->loadBombas();
            $this->calcularEstadisticas();

            $this->dispatch('bomba-updated', '✅ Se crearon 4 bombas iniciales exitosamente');
            
        } catch(\Exception $e) {
            $this->dispatch('bomba-error', 'Error al crear bombas iniciales: ' . $e->getMessage());
        }
    }
}
