<x-filament-panels::page>
    <div class="space-y-6">
        @if(!$selectedGasolinera)
            {{-- Vista de selección de gasolineras --}}
            <div class="rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 bg-white dark:bg-gray-800">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Seleccionar Gasolinera</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @if(count($gasolineras) > 0)
                        @foreach($gasolineras as $gasolinera)
                            <div wire:click="selectGasolinera({{ $gasolinera['id'] }})" 
                                 class="bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 border-2 border-blue-200 dark:border-blue-600 hover:border-blue-400 dark:hover:border-blue-500 rounded-lg p-4 cursor-pointer transition-all duration-200">
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-4 0H7m-2 0h2m0 0V9a2 2 0 012-2h6a2 2 0 012 2v12M9 7h6m-6 4h6m-6 4h6m-6 4h6"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">{{ $gasolinera['nombre'] }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $gasolinera['ubicacion'] ?? 'Sin ubicación' }}</p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-span-full text-center py-8">
                            <div class="text-gray-500 dark:text-gray-400">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-4 0H7m-2 0h2m0 0V9a2 2 0 012-2h6a2 2 0 012 2v12M9 7h6m-6 4h6m-6 4h6m-6 4h6"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No hay gasolineras</h3>
                                <p class="text-gray-600 dark:text-gray-400">Primero crea gasolineras desde el menú "Gasolineras"</p>
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
                        <div class="mt-3 space-y-1">
                            <p class="text-sm text-gray-500 dark:text-gray-400 bg-blue-50 dark:bg-blue-900/20 px-3 py-1 rounded-lg inline-block">
                                ✏️ Edita los galonajes y estado de cada bomba
                            </p>
                            <div class="flex flex-wrap gap-2 text-xs">
                                <span class="bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300 px-2 py-1 rounded">Super: Q{{ $gasolineraActual->precio_super ?? '0.00' }}</span>
                                <span class="bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300 px-2 py-1 rounded">Regular: Q{{ $gasolineraActual->precio_regular ?? '0.00' }}</span>
                                <span class="bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300 px-2 py-1 rounded">Diesel: Q{{ $gasolineraActual->precio_diesel ?? '0.00' }}</span>
                                <span class="bg-purple-100 dark:bg-purple-900/20 text-purple-800 dark:text-purple-300 px-2 py-1 rounded">CC: Q{{ $gasolineraActual->precio_cc ?? '0.00' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button wire:click="agregarNuevaBomba" 
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-bold transition-all duration-200 inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Agregar Bomba
                        </button>
                        <button wire:click="volver" 
                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-bold transition-all duration-200 inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Volver
                        </button>
                    </div>
                </div>
            </div>

            {{-- Bombas existentes --}}
            @if(count($bombas) > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($bombas as $bomba)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            {{-- Header de la bomba --}}
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-xl font-bold">{{ $bomba['nombre'] }}</h3>
                                        <div class="flex items-center mt-1">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                                {{ $bomba['estado'] === 'activa' ? 'bg-green-500 text-white' : 
                                                   ($bomba['estado'] === 'inactiva' ? 'bg-red-500 text-white' : 'bg-yellow-500 text-black') }}">
                                                {{ ucfirst($bomba['estado']) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button wire:click="guardarBomba({{ $bomba['id'] }})" 
                                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-lg text-sm font-bold transition-all duration-200 inline-flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                            </svg>
                                            Guardar
                                        </button>
                                        <button wire:click="eliminarBomba({{ $bomba['id'] }})" 
                                                onclick="return confirm('¿Estás seguro de eliminar {{ $bomba['nombre'] }}?')"
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-sm font-bold transition-all duration-200 inline-flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H9a1 1 0 00-1 1v1M4 7h16"></path>
                                            </svg>
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Contenido de la bomba --}}
                            <div class="p-4">
                                {{-- Estado de la bomba --}}
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Estado de la Bomba:</label>
                                    <select wire:model="bombaData.{{ $bomba['id'] }}.estado" 
                                            class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="activa">Activa</option>
                                        <option value="inactiva">Inactiva</option>
                                        <option value="mantenimiento">En Mantenimiento</option>
                                    </select>
                                </div>

                                {{-- Combustibles --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($bomba['combustibles'] as $tipoCombustible => $combustible)
                                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3
                                            {{ $tipoCombustible === 'super' ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-600' : 
                                               ($tipoCombustible === 'regular' ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-600' : 
                                               ($tipoCombustible === 'diesel' ? 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-600' : 'bg-purple-50 dark:bg-purple-900/20 border-purple-200 dark:border-purple-600')) }}">
                                            
                                            <div class="mb-2">
                                                <h4 class="font-bold text-sm text-gray-900 dark:text-white flex items-center">
                                                    <span class="w-3 h-3 rounded-full mr-2 
                                                        {{ $tipoCombustible === 'super' ? 'bg-green-500' : 
                                                           ($tipoCombustible === 'regular' ? 'bg-blue-500' : 
                                                           ($tipoCombustible === 'diesel' ? 'bg-yellow-500' : 'bg-purple-500')) }}">
                                                    </span>
                                                    {{ $combustible['tipo'] }}
                                                </h4>
                                                <p class="text-xs text-gray-600 dark:text-gray-400">Precio: Q{{ number_format($combustible['precio'], 2) }}</p>
                                            </div>
                                            
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                    Galonaje:
                                                </label>
                                                <input type="number" 
                                                       step="0.01"
                                                       wire:model="bombaData.{{ $bomba['id'] }}.galonaje_{{ $tipoCombustible }}"
                                                       class="w-full px-2 py-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                                       placeholder="0.00">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Historial reciente --}}
                                @if(count($bomba['historial']) > 0)
                                    <div class="mt-4 border-t border-gray-200 dark:border-gray-600 pt-4">
                                        <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Últimas Actualizaciones:</h5>
                                        <div class="space-y-1 max-h-24 overflow-y-auto">
                                            @foreach(array_slice($bomba['historial'], 0, 3) as $historial)
                                                <div class="text-xs text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 p-2 rounded">
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $historial['campo_modificado'])) }}:</strong>
                                                    {{ $historial['valor_anterior'] }} → {{ $historial['valor_nuevo'] }}
                                                    <span class="float-right">{{ \Carbon\Carbon::parse($historial['created_at'])->diffForHumans() }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
                    <div class="text-gray-500 dark:text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No hay bombas configuradas</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Esta gasolinera no tiene bombas. Se crearán automáticamente 4 bombas al cargar.</p>
                        <button wire:click="inicializarBombasDefecto" 
                                class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-bold transition-all duration-200 inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Crear Bombas Iniciales
                        </button>
                    </div>
                </div>
            @endif
        @endif
    </div>

    {{-- Scripts para notificaciones --}}
    <script>
        document.addEventListener('livewire:init', function () {
            Livewire.on('bomba-updated', (message) => {
                // Mostrar notificación de éxito
                console.log('✅', message);
                // Aquí puedes agregar una librería de notificaciones si lo deseas
            });
            
            Livewire.on('bomba-error', (message) => {
                // Mostrar notificación de error
                console.error('❌', message);
                alert('Error: ' + message);
            });
        });
    </script>
</x-filament-panels::page>
