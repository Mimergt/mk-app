<x-filament-panels::page>
    <div class="space-y-6">
        @if(!$selectedGasolinera)
            {{-- Vista de selección de gasolineras --}}
            <div class="rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Seleccionar Gasolinera</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @if(count($gasolineras) > 0)
                        @foreach($gasolineras as $gasolinera)
                            <div wire:click="selectGasolinera({{ $gasolinera['id'] }})" 
                                 class="bg-blue-50 hover:bg-blue-100 border-2 border-blue-200 hover:border-blue-400 rounded-lg p-4 cursor-pointer transition-all duration-200">
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-4 0H7m-2 0h2m0 0V9a2 2 0 012-2h6a2 2 0 012 2v12M9 7h6m-6 4h6m-6 4h6m-6 4h6"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $gasolinera['nombre'] }}</h3>
                                    <p class="text-sm text-gray-600">{{ $gasolinera['ubicacion'] ?? 'Sin ubicación' }}</p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-span-full text-center py-8">
                            <div class="text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-4 0H7m-2 0h2m0 0V9a2 2 0 012-2h6a2 2 0 012 2v12M9 7h6m-6 4h6m-6 4h6m-6 4h6"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No hay gasolineras</h3>
                                <p class="text-gray-600">Primero crea gasolineras desde el menú "Gasolineras"</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @else
            {{-- Vista de gestión de bombas --}}
            
            {{-- Header con nombre de gasolinera y botón volver --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            🏪 {{ $gasolineraActual ? $gasolineraActual->nombre : 'Gasolinera' }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-1">
                            📍 {{ $gasolineraActual ? $gasolineraActual->ubicacion : '' }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 bg-blue-50 dark:bg-blue-900/20 px-3 py-1 rounded-lg inline-block">
                            ✏️ Edita los precios y galones, luego usa el botón "Guardar" de cada bomba
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <button wire:click="volver" 
                                class="bg-gray-500 hover:bg-gray-600 dark:text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver
                        </button>
                    </div>
                </div>
            </div>

            {{-- Contenido de bombas --}}
            @if($bombas && count($bombas) > 0)
                <div class="space-y-6">
                    @foreach($bombas as $bombaInfo)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                                        <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                                        {{ $bombaInfo['nombre'] }}
                                    </h3>
                                    @php
                                        $ultimaActualizacionPrecio = null;
                                        $ultimaActualizacionGalonaje = null;

                                        // Obtener fechas del historial si existe
                                        if(isset($bombaInfo['historial']) && is_array($bombaInfo['historial'])) {
                                            foreach($bombaInfo['historial'] as $historial) {
                                                if(isset($historial['created_at'])) {
                                                    if(!$ultimaActualizacionGalonaje || $historial['created_at'] > $ultimaActualizacionGalonaje) {
                                                        $ultimaActualizacionGalonaje = $historial['created_at'];
                                                    }
                                                }
                                            }
                                        }

                                        // Usar la fecha de actualización de precios de la gasolinera
                                        if($gasolineraActual && $gasolineraActual->fecha_actualizacion_precios) {
                                            $ultimaActualizacionPrecio = $gasolineraActual->fecha_actualizacion_precios;
                                        }
                                    @endphp
                                    
                                    <div class="mt-1 space-y-1">
                                        @if($ultimaActualizacionPrecio)
                                            <p class="text-xs text-blue-600 dark:text-blue-400 flex items-center">
                                                � Última actualización PRECIOS: {{ \Carbon\Carbon::parse($ultimaActualizacionPrecio)->setTimezone('America/Guatemala')->format('d/m/Y H:i') }}
                                            </p>
                                        @else
                                            <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                                                💰 PRECIOS: Sin actualizaciones
                                            </p>
                                        @endif
                                        
                                        @if($ultimaActualizacionGalonaje)
                                            <p class="text-xs text-green-600 dark:text-green-400 flex items-center">
                                                ⛽ Última actualización GALONAJE: {{ \Carbon\Carbon::parse($ultimaActualizacionGalonaje)->setTimezone('America/Guatemala')->format('d/m/Y H:i') }}
                                            </p>
                                        @else
                                            <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                                                ⛽ GALONAJE: Sin actualizaciones
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <button wire:click="guardarBomba({{ $bombaInfo['id'] }})"
                                        class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 ite px-6 py-3 rounded-lg font-bold transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl inline-flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                    </svg>
                                    💾 Guardar {{ $bombaInfo['nombre'] }}
                                </button>
                            </div>
                            
                            @php
                                $combustiblesDisponibles = ['super', 'regular', 'diesel'];
                                // Agregar CC si está activo en la gasolinera
                                if($gasolineraActual && $gasolineraActual->cc_activo && isset($bombaInfo['combustibles']['cc'])) {
                                    $combustiblesDisponibles[] = 'cc';
                                }
                            @endphp

                            <div class="grid grid-cols-1 md:grid-cols-{{ count($combustiblesDisponibles) == 4 ? '4' : '3' }} gap-6">
                                @foreach($combustiblesDisponibles as $tipoCombustible)
                                    @if(isset($bombaInfo['combustibles'][$tipoCombustible]))
                                        @php
                                            $combustible = $bombaInfo['combustibles'][$tipoCombustible];
                                            $tipo = $combustible['tipo']; // Super, Regular, Diesel, CC
                                        @endphp
                                    
                                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4
                                        {{ $tipo === 'Super' ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-600' :
                                           ($tipo === 'Regular' ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-600' :
                                           ($tipo === 'Diesel' ? 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-600' : 'bg-purple-50 dark:bg-purple-900/20 border-purple-200 dark:border-purple-600')) }}">
                                        
                                        <div class="mb-3">
                                            <h4 class="font-bold text-lg text-gray-900 dark:text-white flex items-center">
                                                <span class="w-4 h-4 rounded-full mr-3
                                                    {{ $tipo === 'Super' ? 'bg-green-500' :
                                                       ($tipo === 'Regular' ? 'bg-blue-500' :
                                                       ($tipo === 'Diesel' ? 'bg-yellow-500' : 'bg-purple-500')) }}">
                                                </span>
                                                Combustible {{ $tipo }}
                                            </h4>
                                        </div>
                                        
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                                    💰 Precio (Quetzales):
                                                </label>
                                                <div class="w-full px-3 py-3 bg-gray-100 dark:bg-gray-600 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white font-bold text-lg">
                                                    Q{{ number_format($combustible['precio'], 2) }}
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1">Los precios se modifican desde el botón "Actualizar Precios" arriba</p>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                                    ⛽ Galones disponibles:
                                                </label>
                                                <input type="number"
                                                       step="0.01"
                                                       wire:model="bombaData.{{ $bombaInfo['id'] }}.galonaje_{{ $tipoCombustible }}"
                                                       class="w-full px-3 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-bold text-lg"
                                                       placeholder="0.00"
                                                       value="{{ $combustible['galonaje'] }}">
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                
                {{-- Botón para agregar nueva bomba --}}
                <div class="text-center">
                    <button wire:click="agregarNuevaBomba" 
                            class="bg-green-600 hover:bg-green-700 dark:text-white px-6 py-3 rounded-lg font-medium transition-colors inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Agregar Nueva Bomba (Bomba {{ count($bombas) + 1 }})
                    </button>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">🔧 Cada nueva bomba incluirá Super, Regular y Diesel automáticamente</p>
                </div>
            @else
                {{-- No hay bombas --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">⚠️ No hay bombas configuradas</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">🚀 Se crearán automáticamente <strong>Bomba 1</strong> y <strong>Bomba 2</strong> con todos los tipos de combustible.</p>
                        <button wire:click="inicializarBombasDefecto" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Crear Bombas Iniciales (Bomba 1 y Bomba 2)
                        </button>
                    </div>
                </div>
            @endif
        @endif
    </div>

    {{-- Notificaciones --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('bomba-updated', (message) => {
                alert('✅ ' + message);
            });
            
            Livewire.on('bomba-error', (message) => {
                alert('❌ ' + message);
            });
        });
    </script>
</x-filament-panels::page>
