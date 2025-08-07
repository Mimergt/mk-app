<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TurnosLoginController extends Controller
{
    public function mostrarLogin()
    {
        // Obtener gasolineras que tienen operadores asignados
        $gasolineras = \App\Models\Gasolinera::whereHas('users', function($query) {
            $query->where('tipo_usuario', 'operador');
        })->get();
                         
        return view('turnos.login', compact('gasolineras'));
    }
    
    public function obtenerOperadores(Request $request)
    {
        $request->validate([
            'gasolinera_id' => 'required|exists:gasolineras,id'
        ]);
        
        $operadores = User::where('tipo_usuario', 'operador')
                         ->where('gasolinera_id', $request->gasolinera_id)
                         ->get();
                         
        return response()->json($operadores);
    }
    
    public function procesarLogin(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|exists:users,id',
            'password' => 'required'
        ]);
        
        $usuario = User::find($request->usuario_id);
        
        if (Auth::attempt(['email' => $usuario->email, 'password' => $request->password])) {
            $request->session()->regenerate();
            return redirect()->route('turnos.panel');
        }
        
        return back()->withErrors([
            'password' => 'Contraseña incorrecta.'
        ])->withInput();
    }
    
    public function panel()
    {
        // Verificar que el usuario tenga gasolinera asignada
        if (!auth()->user()->gasolinera_id) {
            return redirect()->route('turnos.login')->withErrors([
                'error' => 'No tienes una gasolinera asignada.'
            ]);
        }
        
        // Cargar datos necesarios para el panel
        $gasolineraUsuario = auth()->user()->gasolinera;
        
        // Buscar turno activo
        $turnoActual = \App\Models\Turno::where('user_id', auth()->id())
                                       ->where('gasolinera_id', auth()->user()->gasolinera_id)
                                       ->where('estado', 'abierto')
                                       ->latest()
                                       ->first();
        
        // Calcular tiempo transcurrido si hay turno activo
        $tiempoTranscurrido = '';
        if ($turnoActual) {
            $inicio = \Carbon\Carbon::parse($turnoActual->hora_inicio);
            $ahora = \Carbon\Carbon::now();
            $diff = $inicio->diff($ahora);
            
            $tiempoTranscurrido = sprintf(
                '%02d:%02d:%02d',
                $diff->h + ($diff->days * 24),
                $diff->i,
                $diff->s
            );
        }        // Cargar bombas con nueva estructura
        $bombas = [];
        $lecturas = [];
        if (auth()->user()->gasolinera_id) {
            $bombasQuery = \App\Models\Bomba::where('gasolinera_id', auth()->user()->gasolinera_id)
                                           ->orderBy('nombre')
                                           ->get();
            
            foreach ($bombasQuery as $bomba) {
                $gasolinera = $bomba->gasolinera;
                $bombas[$bomba->nombre] = [
                    'id' => $bomba->id,
                    'estado' => $bomba->estado,
                    'combustibles' => [
                        'Super' => [
                            'galonaje' => $bomba->galonaje_super,
                            'precio' => $gasolinera->precio_super,
                        ],
                        'Regular' => [
                            'galonaje' => $bomba->galonaje_regular,
                            'precio' => $gasolinera->precio_regular,
                        ],
                        'Diesel' => [
                            'galonaje' => $bomba->galonaje_diesel,
                            'precio' => $gasolinera->precio_diesel,
                        ]
                    ],
                    'updated_at' => $bomba->updated_at->format('d/m/Y H:i:s')
                ];
                
                // Solo añadir CC si está activo en la gasolinera
                if ($gasolinera->cc_activo) {
                    $bombas[$bomba->nombre]['combustibles']['CC'] = [
                        'galonaje' => $bomba->galonaje_cc,
                        'precio' => $gasolinera->precio_cc,
                    ];
                }
            }
            
            // Inicializar lecturas vacías
            foreach ($bombas as $nombreBomba => $bombaData) {
                $lecturas[$bombaData['id']] = '';
            }
        }
        
        // Cargar efectivo
        $efectivo = 0.00;
        $ultimaActualizacionEfectivo = null;
        if ($turnoActual) {
            $efectivo = $turnoActual->dinero_cierre ?? 0.00;
        }
        
        // Buscar la última actualización de efectivo
        $ultimoTurno = \App\Models\Turno::where('gasolinera_id', auth()->user()->gasolinera_id)
                                       ->whereNotNull('dinero_cierre')
                                       ->latest('updated_at')
                                       ->first();
                                       
        $ultimaActualizacionEfectivo = $ultimoTurno ? 
            $ultimoTurno->updated_at->format('d/m/Y H:i:s') : null;
        
        return view('turnos.panel', compact(
            'gasolineraUsuario',
            'turnoActual', 
            'tiempoTranscurrido',
            'bombas',
            'lecturas',
            'efectivo',
            'ultimaActualizacionEfectivo'
        ));
    }
    
    public function abrirTurno()
    {
        try {
            // Cerrar cualquier turno abierto anterior del usuario
            \App\Models\Turno::where('user_id', auth()->id())
                 ->where('gasolinera_id', auth()->user()->gasolinera_id)
                 ->where('estado', 'abierto')
                 ->update([
                     'estado' => 'cerrado',
                     'hora_fin' => now()
                 ]);
            
            // Crear nuevo turno
            \App\Models\Turno::create([
                'gasolinera_id' => auth()->user()->gasolinera_id,
                'user_id' => auth()->id(),
                'fecha' => now()->toDateString(),
                'hora_inicio' => now(),
                'dinero_apertura' => 0.00,
                'estado' => 'abierto'
            ]);
            
            return redirect()->route('turnos.panel')
                ->with('success', 'Turno abierto correctamente');
                
        } catch (\Exception $e) {
            return redirect()->route('turnos.panel')
                ->with('error', 'Error al abrir turno: ' . $e->getMessage());
        }
    }
    
    public function cerrarTurno()
    {
        try {
            // Buscar turno activo
            $turnoActual = \App\Models\Turno::where('user_id', auth()->id())
                                           ->where('gasolinera_id', auth()->user()->gasolinera_id)
                                           ->where('estado', 'abierto')
                                           ->first();
            
            if (!$turnoActual) {
                return redirect()->route('turnos.panel')
                    ->with('error', 'No hay un turno activo para cerrar');
            }
            
            // Cerrar el turno
            $turnoActual->update([
                'estado' => 'cerrado',
                'hora_fin' => now(),
                'dinero_cierre' => $turnoActual->dinero_cierre ?? 0.00
            ]);
            
            return redirect()->route('turnos.panel')
                ->with('success', 'Turno cerrado correctamente');
                
        } catch (\Exception $e) {
            return redirect()->route('turnos.panel')
                ->with('error', 'Error al cerrar turno: ' . $e->getMessage());
        }
    }
    
    public function logout()
    {
        Auth::logout();
        return redirect()->route('turnos.login');
    }
    
    public function guardarLecturaBomba(\App\Models\Bomba $bomba, Request $request)
    {
        $request->validate([
            'lectura' => 'required|numeric|min:0'
        ]);
        
        $nuevaLectura = $request->input('lectura');
        
        // Validar que la nueva lectura sea mayor que la actual
        if ($nuevaLectura <= $bomba->galonaje) {
            return redirect()->route('turnos.panel')
                ->with('error', "La nueva lectura debe ser mayor que la actual ({$bomba->galonaje})");
        }
        
        // Guardar historial antes de actualizar
        \App\Models\HistorialBomba::create([
            'bomba_id' => $bomba->id,
            'campo_modificado' => 'galonaje',
            'tipo_cambio' => 'galonaje',
            'valor_anterior' => $bomba->galonaje,
            'valor_nuevo' => $nuevaLectura,
            'user_id' => auth()->id(),
            'observaciones' => 'Actualización de lectura desde panel de turno'
        ]);
        
        // Actualizar la bomba
        $bomba->update([
            'galonaje' => $nuevaLectura
        ]);
        
        return redirect()->route('turnos.panel')
            ->with('success', "Lectura de {$bomba->nombre} {$bomba->tipo} actualizada correctamente");
    }
    
    public function guardarLecturasGrupo($nombreBomba, Request $request)
    {
        try {
            $contador = 0;
            $errores = [];
            
            // Obtener la bomba específica por nombre
            $bomba = \App\Models\Bomba::where('gasolinera_id', auth()->user()->gasolinera_id)
                                     ->where('nombre', $nombreBomba)
                                     ->first();
            
            if (!$bomba) {
                return redirect()->route('turnos.panel')
                    ->with('error', "Bomba {$nombreBomba} no encontrada");
            }
            
            $bombaId = $bomba->id;
            $actualizaciones = [];
            $historialEntradas = [];
            
            // Procesar cada tipo de combustible
            foreach (['super', 'regular', 'diesel', 'cc'] as $tipo) {
                $lectura = $request->input("lectura_{$bombaId}_{$tipo}");
                $campoGalonaje = "galonaje_{$tipo}";
                
                if ($lectura && is_numeric($lectura)) {
                    $galonajeActual = $bomba->$campoGalonaje;
                    
                    // Validar que la nueva lectura sea mayor que la actual
                    if ($lectura <= $galonajeActual) {
                        $errores[] = ucfirst($tipo) . ": La nueva lectura debe ser mayor que {$galonajeActual}";
                        continue;
                    }
                    
                    // Preparar actualización
                    $actualizaciones[$campoGalonaje] = $lectura;
                    
                    // Preparar entrada de historial
                    $historialEntradas[] = [
                        'bomba_id' => $bomba->id,
                        'campo_modificado' => $campoGalonaje,
                        'valor_anterior' => $galonajeActual,
                        'valor_nuevo' => $lectura,
                        'user_id' => auth()->id(),
                        'observaciones' => 'Actualización de ' . ucfirst($tipo) . ' desde panel de turno',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    
                    $contador++;
                }
            }
            
            if (!empty($errores)) {
                return redirect()->route('turnos.panel')
                    ->with('error', 'Errores en ' . $nombreBomba . ': ' . implode(', ', $errores));
            }
            
            if ($contador > 0) {
                // Realizar todas las actualizaciones
                $bomba->update($actualizaciones);
                
                // Insertar historial en batch
                \App\Models\HistorialBomba::insert($historialEntradas);
                
                return redirect()->route('turnos.panel')
                    ->with('success', "✅ {$nombreBomba}: {$contador} " . ($contador == 1 ? 'combustible actualizado' : 'combustibles actualizados'));
            } else {
                return redirect()->route('turnos.panel')
                    ->with('info', "No se encontraron lecturas válidas para {$nombreBomba}");
            }
            
        } catch (\Exception $e) {
            return redirect()->route('turnos.panel')
                ->with('error', 'Error al guardar lecturas de ' . $nombreBomba . ': ' . $e->getMessage());
        }
    }
    
    public function guardarEfectivo(Request $request)
    {
        $request->validate([
            'efectivo' => 'required|numeric|min:0'
        ]);
        
        $nuevoEfectivo = $request->input('efectivo');
        
        // Buscar o crear turno actual
        $turnoActual = \App\Models\Turno::where('user_id', auth()->id())
                                       ->where('gasolinera_id', auth()->user()->gasolinera_id)
                                       ->where('estado', 'abierto')
                                       ->first();
        
        if (!$turnoActual) {
            return redirect()->route('turnos.panel')
                ->with('error', 'No hay un turno activo para actualizar el efectivo');
        }
        
        // Actualizar el efectivo
        $turnoActual->update([
            'dinero_cierre' => $nuevoEfectivo
        ]);
        
        return redirect()->route('turnos.panel')
            ->with('success', 'Efectivo actualizado correctamente');
    }
}
