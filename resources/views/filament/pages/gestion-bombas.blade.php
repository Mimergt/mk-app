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
                            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-4 0H7m-2 0h2m0 0V9a2 2 0 012-2h6a2 2 0 012 2v12M9 7h6m-6 4h6m-6 4h6m-6 4h6"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No hay gasolineras disponibles</h3>
                            <p class="text-gray-600 dark:text-gray-400">Crea una gasolinera desde el panel de administración.</p>
                        </div>
                    @endif
                </div>
            </div>
        @else
            {{-- Vista de gestión de bombas --}}
            <div class="rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                {{-- Header con información de la gasolinera --}}
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 rounded-t-xl">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold">{{ $gasolineraActual->nombre }}</h1>
                            <p class="text-blue-100 mt-1">{{ $gasolineraActual->ubicacion ?? 'Sin ubicación especificada' }}</p>
                        </div>
                        <button wire:click="$set('selectedGasolinera', null)" 
                                class="bg-blue-500 hover:bg-blue-400 dark:text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver
                        </button>
                    </div>
                </div>

                
            </div>

            {{-- Gestión de precios --}}
            <div class="rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
                <div class="flex justify-between items-center pb-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Gestión de Precios</h2>
                    <x-filament::button wire:click="actualizarPrecios" icon="heroicon-o-arrow-path" color="primary" class="font-medium">
                        Actualizar Precios
                    </x-filament::button>
                </div>

                @if($mensajeExito)
                    <div x-data="{ show: true }" 
                         x-show="show" 
                         x-init="setTimeout(() => show = false, 5000)"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="mb-4 p-4 bg-green-100 dark:bg-green-900/20 border border-green-300 dark:border-green-600 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h4 class="text-green-800 dark:text-green-300 font-semibold">¡Precios actualizados exitosamente!</h4>
                                <p class="text-green-700 dark:text-green-400 text-sm">
                                    Los nuevos precios se aplicaron a todas las bombas de esta gasolinera.
                                    <span class="font-medium">Actualizado: {{ now()->format('d/m/Y H:i') }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {{-- Super --}}
                    <div class="bg-green-50 dark:bg-green-900/20 border-2 border-green-200 dark:border-green-600 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <span class="w-4 h-4 bg-green-500 rounded-full mr-2"></span>
                            <h3 class="font-bold text-green-800 dark:text-green-300">Super</h3>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Precio por galón (Q):
                            </label>
                            <input type="number" 
                                   step="0.01"
                                   wire:model="preciosData.precio_super"
                                   class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-green-300 dark:border-green-600 rounded-md text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                   placeholder="0.00">
                        </div>
                    </div>

                    {{-- Regular --}}
                    <div class="bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-200 dark:border-blue-600 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <span class="w-4 h-4 bg-blue-500 rounded-full mr-2"></span>
                            <h3 class="font-bold text-blue-800 dark:text-blue-300">Regular</h3>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Precio por galón (Q):
                            </label>
                            <input type="number" 
                                   step="0.01"
                                   wire:model="preciosData.precio_regular"
                                   class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-blue-300 dark:border-blue-600 rounded-md text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="0.00">
                        </div>
                    </div>

                    {{-- Diesel --}}
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border-2 border-yellow-200 dark:border-yellow-600 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <span class="w-4 h-4 bg-yellow-500 rounded-full mr-2"></span>
                            <h3 class="font-bold text-yellow-800 dark:text-yellow-300">Diesel</h3>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Precio por galón (Q):
                            </label>
                            <input type="number" 
                                   step="0.01"
                                   wire:model="preciosData.precio_diesel"
                                   class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-yellow-300 dark:border-yellow-600 rounded-md text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                   placeholder="0.00">
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-600 rounded-lg">
                    <p class="text-sm text-blue-800 dark:text-blue-300 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <strong>Información:</strong> Los precios se aplicarán a todas las bombas de esta gasolinera. Asegúrate de guardar los cambios antes de continuar.
                    </p>
                </div>
            </div>

            {{-- Bombas existentes --}}
            @if(count($bombas) > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($bombas as $bomba)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            {{-- Header de la bomba --}}
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 dark:text-white p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-lg font-bold">{{ $bomba['numero'] }}</span>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold">{{ $bomba['nombre'] }}</h3>
                                            <p class="text-blue-100 text-sm">
                                                Estado: 
                                                @if($bomba['estado'] === 'activa')
                                                    <span class="bg-green-500 px-2 py-1 rounded-full text-xs font-bold">Activa</span>
                                                @elseif($bomba['estado'] === 'inactiva')
                                                    <span class="bg-red-500 px-2 py-1 rounded-full text-xs font-bold">Inactiva</span>
                                                @else
                                                    <span class="bg-yellow-500 px-2 py-1 rounded-full text-xs font-bold">Mantenimiento</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button wire:click="eliminarBomba({{ $bomba['id'] }})" 
                                                onclick="return confirm('¿Estás seguro de eliminar {{ $bomba['nombre'] }}?')"
                                                class="bg-red-500 hover:bg-red-600 dark:text-white px-3 py-1 rounded-lg text-sm font-bold transition-all duration-200 inline-flex items-center">
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
                                                    @if($tipoCombustible !== 'cc')
                                                        <p class="text-xs text-gray-600 dark:text-gray-400">Precio: Q{{ number_format($combustible['precio'], 2) }}</p>
                                                    @else
                                                        <p class="text-xs text-purple-600 dark:text-purple-400">Solo lectura</p>
                                                    @endif
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                        @if($tipoCombustible !== 'cc')
                                                            Galonaje:
                                                        @else
                                                            Lectura:
                                                        @endif
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
                                <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Actividad Reciente:</h5>
                                    <div class="space-y-1">
                                        <p class="text-xs text-gray-600 dark:text-gray-400">• Última actualización: {{ now()->subMinutes(rand(5, 60))->format('H:i') }}</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">• Ventas hoy: Q{{ number_format(rand(500, 2000), 2) }}</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">• Transacciones: {{ rand(15, 50) }}</p>
                                    </div>
                                </div>
                                
                                {{-- Botón de guardar individual por bomba --}}
                                <div class="mt-4 text-center">
                                    <button wire:click="guardarBomba({{ $bomba['id'] }})" 
                                            class="bg-green-600 hover:bg-green-700 dark:text-white px-6 py-2 rounded-lg font-bold transition-all duration-200 inline-flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Guardar {{ $bomba['nombre'] }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                {{-- Botón para añadir nueva bomba --}}
                <div class="mt-6 text-center">
                    <button wire:click="agregarNuevaBomba" 
                            class="bg-blue-600 hover:bg-blue-700 dark:text-white px-8 py-3 rounded-lg font-bold text-lg transition-all duration-200 inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Añadir Nueva Bomba
                    </button>
                </div>
            @else
                {{-- No hay bombas --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No hay bombas registradas</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Esta gasolinera no tiene bombas configuradas.</p>
                    <button wire:click="crearBombasIniciales" 
                            class="bg-blue-600 hover:bg-blue-700 dark:text-white px-6 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Crear 4 Bombas Iniciales
                    </button>
                </div>
            @endif
        @endif
    </div>
</x-filament-panels::page>
