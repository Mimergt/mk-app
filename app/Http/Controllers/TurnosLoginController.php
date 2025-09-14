<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\TurnoBombaDatos;

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
        $fotografiasPorBomba = [];
        
        if (auth()->user()->gasolinera_id) {
            $bombasQuery = \App\Models\Bomba::where('gasolinera_id', auth()->user()->gasolinera_id)
                                           ->orderBy('nombre')
                                           ->get();
            
        // Si hay turno activo, cargar fotografías existentes
        if ($turnoActual) {
            $fotografiasExistentes = \App\Models\TurnoBombaDatos::where('turno_id', $turnoActual->id)
                                                               ->whereNotNull('fotografia')
                                                               ->get()
                                                               ->keyBy('bomba_id');
                                                               
            foreach ($fotografiasExistentes as $bombaId => $datos) {
                $fotografiasPorBomba[$bombaId] = $datos->fotografia_url;
            }
        }            foreach ($bombasQuery as $bomba) {
                $gasolinera = $bomba->gasolinera;
                $fotografiaUrl = $fotografiasPorBomba[$bomba->id] ?? null;
                
                $bombas[$bomba->nombre] = [
                    'id' => $bomba->id,
                    'estado' => $bomba->estado,
                    'fotografia_url' => $fotografiaUrl,
                    'tiene_fotografia' => !empty($fotografiaUrl),
                    'combustibles' => [
                        'Super' => [
                            'lectura_actual' => $bomba->galonaje_super,
                            'precio' => $gasolinera->precio_super,
                            'fecha_lectura' => $bomba->updated_at->format('d/m/Y H:i'),
                        ],
                        'Regular' => [
                            'lectura_actual' => $bomba->galonaje_regular,
                            'precio' => $gasolinera->precio_regular,
                            'fecha_lectura' => $bomba->updated_at->format('d/m/Y H:i'),
                        ],
                        'Diesel' => [
                            'lectura_actual' => $bomba->galonaje_diesel,
                            'precio' => $gasolinera->precio_diesel,
                            'fecha_lectura' => $bomba->updated_at->format('d/m/Y H:i'),
                        ]
                    ],
                    'updated_at' => $bomba->updated_at->format('d/m/Y H:i')
                ];
                
                // Agregar CC como lectura sin precio
                $bombas[$bomba->nombre]['combustibles']['CC'] = [
                    'lectura_actual' => $bomba->galonaje_cc,
                    'precio' => null, // CC sin precio
                    'fecha_lectura' => $bomba->updated_at->format('d/m/Y H:i'),
                ];
            }
            
            // Inicializar lecturas vacías
            foreach ($bombas as $nombreBomba => $bombaData) {
                $lecturas[$bombaData['id']] = '';
            }
        }

        // Datos de ventas
        $datosVentas = [
            'credito' => $turnoActual ? ($turnoActual->venta_credito ?? 0) : 0,
            'tarjetas' => $turnoActual ? ($turnoActual->venta_tarjetas ?? 0) : 0,
            'efectivo' => $turnoActual ? ($turnoActual->venta_efectivo ?? 0) : 0,
            'descuentos' => $turnoActual ? ($turnoActual->venta_descuentos ?? 0) : 0,
        ];

        // Datos de nivel de tanques
        $datosTanques = [
            'pulgadas' => [
                'super' => $turnoActual ? ($turnoActual->tanque_super_pulgadas ?? 0) : 0,
                'regular' => $turnoActual ? ($turnoActual->tanque_regular_pulgadas ?? 0) : 0,
                'diesel' => $turnoActual ? ($turnoActual->tanque_diesel_pulgadas ?? 0) : 0,
            ],
            'galones' => [
                'super' => $turnoActual ? ($turnoActual->tanque_super_galones ?? 0) : 0,
                'regular' => $turnoActual ? ($turnoActual->tanque_regular_galones ?? 0) : 0,
                'diesel' => $turnoActual ? ($turnoActual->tanque_diesel_galones ?? 0) : 0,
            ]
        ];

        return view('turnos.panel', compact(
            'gasolineraUsuario',
            'turnoActual',
            'tiempoTranscurrido',
            'bombas',
            'lecturas',
            'fotografiasPorBomba',
            'datosVentas',
            'datosTanques'
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

    public function guardarVentas(Request $request)
    {
        $request->validate([
            'venta_credito' => 'nullable|numeric|min:0',
            'venta_tarjetas' => 'nullable|numeric|min:0',
            'venta_efectivo' => 'nullable|numeric|min:0',
            'venta_descuentos' => 'nullable|numeric|min:0',
        ]);

        try {
            $turnoActual = \App\Models\Turno::where('user_id', auth()->id())
                                           ->where('gasolinera_id', auth()->user()->gasolinera_id)
                                           ->where('estado', 'abierto')
                                           ->first();

            if (!$turnoActual) {
                return back()->with('error', 'No hay un turno activo');
            }

            $turnoActual->update([
                'venta_credito' => $request->venta_credito ?? 0,
                'venta_tarjetas' => $request->venta_tarjetas ?? 0,
                'venta_efectivo' => $request->venta_efectivo ?? 0,
                'venta_descuentos' => $request->venta_descuentos ?? 0,
            ]);

            return back()->with('success', 'Datos de ventas guardados correctamente');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al guardar datos de ventas: ' . $e->getMessage());
        }
    }

    public function guardarTanques(Request $request)
    {
        $request->validate([
            'tanque_super_pulgadas' => 'nullable|numeric|min:0',
            'tanque_regular_pulgadas' => 'nullable|numeric|min:0',
            'tanque_diesel_pulgadas' => 'nullable|numeric|min:0',
            'tanque_super_galones' => 'nullable|numeric|min:0',
            'tanque_regular_galones' => 'nullable|numeric|min:0',
            'tanque_diesel_galones' => 'nullable|numeric|min:0',
        ]);

        try {
            $turnoActual = \App\Models\Turno::where('user_id', auth()->id())
                                           ->where('gasolinera_id', auth()->user()->gasolinera_id)
                                           ->where('estado', 'abierto')
                                           ->first();

            if (!$turnoActual) {
                return back()->with('error', 'No hay un turno activo');
            }

            $turnoActual->update([
                'tanque_super_pulgadas' => $request->tanque_super_pulgadas ?? 0,
                'tanque_regular_pulgadas' => $request->tanque_regular_pulgadas ?? 0,
                'tanque_diesel_pulgadas' => $request->tanque_diesel_pulgadas ?? 0,
                'tanque_super_galones' => $request->tanque_super_galones ?? 0,
                'tanque_regular_galones' => $request->tanque_regular_galones ?? 0,
                'tanque_diesel_galones' => $request->tanque_diesel_galones ?? 0,
            ]);

            return back()->with('success', 'Datos de nivel de tanques guardados correctamente');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al guardar datos de tanques: ' . $e->getMessage());
        }
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
            \Log::info("=== INICIO GUARDADO LECTURAS ===");
            \Log::info("Bomba: " . $nombreBomba);
            \Log::info("Datos recibidos: ", $request->all());
            
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
            
            // Obtener turno activo
            $turnoActual = \App\Models\Turno::where('user_id', auth()->id())
                                           ->where('gasolinera_id', auth()->user()->gasolinera_id)
                                           ->where('estado', 'abierto')
                                           ->first();
            
            if (!$turnoActual) {
                return redirect()->route('turnos.panel')
                    ->with('error', 'No hay un turno activo para guardar los datos');
            }
            
            $bombaId = $bomba->id;
            
            // Validar que se haya subido una fotografía o que ya exista una
            \Log::info("Verificando fotografía...");
            \Log::info("hasFile('fotografia_bomba'): " . ($request->hasFile('fotografia_bomba') ? 'true' : 'false'));
            
            if ($request->hasFile('fotografia_bomba')) {
                $file = $request->file('fotografia_bomba');
                \Log::info("Archivo encontrado:");
                \Log::info("- Nombre original: " . $file->getClientOriginalName());
                \Log::info("- Tamaño: " . $file->getSize() . " bytes");
                \Log::info("- Tipo MIME: " . $file->getMimeType());
                \Log::info("- Es válido: " . ($file->isValid() ? 'true' : 'false'));
                \Log::info("- Error: " . $file->getError());
            }
            
            $tieneArchivo = $request->hasFile('fotografia_bomba') && $request->file('fotografia_bomba')->isValid();
            $fotografiaExistente = null;
            
            if ($turnoActual) {
                $fotografiaExistente = \App\Models\TurnoBombaDatos::where('bomba_id', $bombaId)
                                                                 ->where('turno_id', $turnoActual->id)
                                                                 ->whereNotNull('fotografia')
                                                                 ->first();
            }
            
            \Log::info("Tiene archivo válido: " . ($tieneArchivo ? 'true' : 'false'));
            \Log::info("Fotografía existente: " . ($fotografiaExistente ? 'true' : 'false'));
            
            if (!$tieneArchivo && !$fotografiaExistente) {
                \Log::error("No se encontró archivo de fotografía válido ni existe una fotografía previa");
                return redirect()->route('turnos.panel')
                    ->with('error', "❌ Debe subir una fotografía válida para {$nombreBomba} antes de guardar los valores");
            }
            
            if ($tieneArchivo) {
                \Log::info("Fotografía nueva encontrada: " . $request->file('fotografia_bomba')->getClientOriginalName());
                \Log::info("Tamaño del archivo: " . $request->file('fotografia_bomba')->getSize() . " bytes");
            } else {
                \Log::info("Usando fotografía existente: " . $fotografiaExistente->fotografia);
            }
            
            $datosParaGuardar = [
                'bomba_id' => $bombaId,
                'turno_id' => $turnoActual->id,
                'user_id' => auth()->id(),
                'galonaje_super' => 0,
                'galonaje_regular' => 0,
                'galonaje_diesel' => 0,
                'lectura_cc' => 0,
                'fotografia' => null,
                'fecha_turno' => now()
            ];
            
            // Procesar cada tipo de combustible
            \Log::info("Procesando combustibles para bomba ID: " . $bombaId);
            foreach (['super', 'regular', 'diesel', 'cc'] as $tipo) {
                $lectura = $request->input("lectura_{$bombaId}_{$tipo}");
                $campoGalonaje = $tipo === 'cc' ? 'lectura_cc' : "galonaje_{$tipo}";
                $campoActual = "galonaje_{$tipo}";
                
                \Log::info("Tipo: {$tipo}, Lectura ingresada: {$lectura}, Campo: {$campoGalonaje}");
                
                if ($lectura && is_numeric($lectura)) {
                    $galonajeActual = $bomba->$campoActual;
                    \Log::info("Galonaje actual {$tipo}: {$galonajeActual}");
                    
                    // Validar que la nueva lectura sea mayor que la actual
                    if ($lectura <= $galonajeActual) {
                        $errores[] = ucfirst($tipo) . ": La nueva lectura debe ser mayor que {$galonajeActual}";
                        \Log::warning("Error validación {$tipo}: {$lectura} <= {$galonajeActual}");
                        continue;
                    }
                    
                    // Guardar en los datos del turno
                    $datosParaGuardar[$campoGalonaje] = $lectura;
                    $contador++;
                    \Log::info("Datos válidos para {$tipo}: {$lectura}");
                } else {
                    \Log::info("Lectura no válida para {$tipo}: {$lectura}");
                }
            }
            
            \Log::info("Contador final: " . $contador);
            \Log::info("Errores: ", $errores);
            
            if (!empty($errores)) {
                \Log::error("Errores en validación: ", $errores);
                return redirect()->route('turnos.panel')
                    ->with('error', 'Errores en ' . $nombreBomba . ': ' . implode(', ', $errores));
            }
            
            // Permitir guardar si hay lecturas válidas O si hay una fotografía nueva válida
            if ($contador > 0 || ($request->hasFile('fotografia_bomba') && $request->file('fotografia_bomba')->isValid())) {
                \Log::info("Procediendo a guardar datos y/o fotografía...");
                
                // Manejar la subida de fotografía (solo si hay archivo nuevo y válido)
                $rutaFotografia = null;
                if ($request->hasFile('fotografia_bomba') && $request->file('fotografia_bomba')->isValid()) {
                    $file = $request->file('fotografia_bomba');
                    
                    // Validaciones adicionales del archivo
                    if ($file->getSize() == 0) {
                        \Log::error("El archivo está vacío");
                        return redirect()->route('turnos.panel')
                            ->with('error', "❌ El archivo de fotografía está vacío para {$nombreBomba}");
                    }
                    
                    if ($file->getSize() > 10 * 1024 * 1024) { // 10MB máximo
                        \Log::error("El archivo es demasiado grande: " . $file->getSize() . " bytes");
                        return redirect()->route('turnos.panel')
                            ->with('error', "❌ El archivo de fotografía es demasiado grande para {$nombreBomba} (máximo 10MB)");
                    }
                    
                    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    $extension = strtolower($file->getClientOriginalExtension());
                    if (!in_array($extension, $allowedTypes)) {
                        \Log::error("Tipo de archivo no permitido: " . $extension);
                        return redirect()->route('turnos.panel')
                            ->with('error', "❌ Tipo de archivo no permitido para {$nombreBomba}. Use: " . implode(', ', $allowedTypes));
                    }
                    
                    $filename = 'turno_' . $turnoActual->id . '_bomba_' . $bombaId . '_' . time() . '.' . $extension;
                    \Log::info("Intentando guardar archivo: " . $filename);
                    \Log::info("Archivo válido: " . ($file->isValid() ? 'true' : 'false'));
                    \Log::info("Tamaño archivo: " . $file->getSize() . " bytes");
                    \Log::info("Tipo MIME: " . $file->getMimeType());
                    
                    try {
                        $rutaFotografia = $file->storeAs('turnos/bombas', $filename, 'public');
                        if ($rutaFotografia) {
                            \Log::info("Fotografía guardada exitosamente: " . $rutaFotografia);
                        } else {
                            \Log::error("Error al guardar fotografía: storeAs retornó false");
                            return redirect()->route('turnos.panel')
                                ->with('error', "❌ Error al guardar la fotografía para {$nombreBomba}");
                        }
                    } catch (\Exception $e) {
                        \Log::error("Exception al guardar fotografía: " . $e->getMessage());
                        return redirect()->route('turnos.panel')
                            ->with('error', "❌ Error técnico al guardar fotografía para {$nombreBomba}: " . $e->getMessage());
                    }
                } else if ($fotografiaExistente) {
                    $rutaFotografia = $fotografiaExistente->fotografia;
                    \Log::info("Usando fotografía existente: " . $rutaFotografia);
                }
                
                $datosParaGuardar['fotografia'] = $rutaFotografia;
                $datosParaGuardar['observaciones'] = "Datos guardados para turno {$turnoActual->id} - {$nombreBomba}";
                
                // Debugging: Log los datos antes de guardar
                \Log::info("Datos para guardar: ", $datosParaGuardar);
                
                // Guardar o actualizar datos del turno para esta bomba
                \App\Models\TurnoBombaDatos::updateOrCreate(
                    [
                        'bomba_id' => $bombaId,
                        'turno_id' => $turnoActual->id
                    ],
                    $datosParaGuardar
                );
                
                // También actualizar la tabla principal de bombas para mantener compatibilidad
                $actualizacionesBomba = [];
                foreach (['super', 'regular', 'diesel', 'cc'] as $tipo) {
                    $lectura = $request->input("lectura_{$bombaId}_{$tipo}");
                    if ($lectura && is_numeric($lectura)) {
                        $actualizacionesBomba["galonaje_{$tipo}"] = $lectura;
                    }
                }
                
                if (!empty($actualizacionesBomba)) {
                    $bomba->update($actualizacionesBomba);
                }
                
                return redirect()->route('turnos.panel')
                    ->with('success', "✅ {$nombreBomba}: " . ($contador > 0 ? "Datos y fotografía" : "Fotografía") . " guardados correctamente para el turno actual");
            } else {
                \Log::warning("No se encontraron lecturas válidas ni fotografía nueva. Contador: " . $contador);
                return redirect()->route('turnos.panel')
                    ->with('info', "ℹ️ {$nombreBomba}: No se ingresaron lecturas válidas ni se subió fotografía nueva");
            }
            
        } catch (\Exception $e) {
            return redirect()->route('turnos.panel')
                ->with('error', 'Error al guardar lecturas de ' . $nombreBomba . ': ' . $e->getMessage());
        }
    }
    
}
