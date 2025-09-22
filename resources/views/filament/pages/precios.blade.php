<x-filament-panels::page>
    {{-- Sección de Precios con card destacado --}}
    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border-l-4 border-blue-500 shadow-lg mb-6">
        <div class="p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="bg-blue-500 text-white p-3 rounded-full">
                    <span class="text-2xl">💵</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Precios de Compra y Venta</h2>
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        Gestiona los precios de combustibles mensuales y calcula márgenes de ganancia
                    </p>
                </div>
            </div>
            
            {{-- Selector de meses para gestión de precios (colapsible) --}}
            <div class="mb-5">
                {{-- Barra de título colapsible --}}
                <div 
                    x-data="{ mostrarMesesPrecios: false }"
                    class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm"
                >
                    <button
                        @click="mostrarMesesPrecios = !mostrarMesesPrecios"
                        class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 rounded-lg"
                    >
                        <div class="flex items-center space-x-3">
                            <div class="bg-purple-100 dark:bg-purple-900/30 p-2 rounded-full">
                                <span class="text-lg">📅</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Meses</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Selecciona el mes para gestionar precios • Año {{ now()->format('Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-gray-400 dark:text-gray-500 transition-transform duration-200"
                             :class="mostrarMesesPrecios ? 'transform rotate-180' : ''">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </button>

                    {{-- Grid de meses colapsible --}}
                    <div x-show="mostrarMesesPrecios" 
                         x-collapse.duration.300ms
                         class="border-t border-gray-200 dark:border-gray-700">
                        <div class="p-4">
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 mb-5">
                                @php
                                    $meses = [
                                        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                                    ];
                                    $mesActual = now()->format('n');
                                @endphp

                                @foreach($meses as $numeroMes => $nombreMes)
                                    @php
                                        $esPasado = $numeroMes < $mesActual;
                                        $esActual = $numeroMes == $mesActual;
                                        $esFuturo = $numeroMes > $mesActual;
                                    @endphp

                                    <div class="relative">
                                        <button 
                                            type="button"
                                            wire:click="seleccionarMes({{ $numeroMes }})"
                                            @if($esFuturo)
                                                disabled
                                                class="w-full p-4 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-600 cursor-not-allowed"
                                            @elseif($esActual)
                                                class="w-full p-4 rounded-lg border-2 @if($numeroMes == $this->mesSeleccionado) border-blue-500 bg-blue-50 dark:bg-blue-900/20 @else border-blue-300 hover:border-blue-400 bg-white dark:bg-gray-800 @endif text-gray-900 dark:text-white transition-all duration-200 hover:shadow-md"
                                            @else
                                                class="w-full p-4 rounded-lg border-2 @if($numeroMes == $this->mesSeleccionado) border-purple-500 bg-purple-50 dark:bg-purple-900/20 @else border-gray-300 dark:border-gray-600 hover:border-purple-400 bg-white dark:bg-gray-800 @endif text-gray-900 dark:text-white transition-all duration-200 hover:shadow-md"
                                            @endif
                                        >
                                            <div class="text-center">
                                                <div class="font-semibold text-sm mb-1">{{ $nombreMes }}</div>
                                                
                                                @if($esFuturo)
                                                    <div class="text-xs text-gray-400">🔒 Bloqueado</div>
                                                @elseif($esActual)
                                                    <div class="text-xs text-blue-600 dark:text-blue-400">📅 Actual</div>
                                                @else
                                                    <div class="text-xs text-purple-600 dark:text-purple-400">💰 Editable</div>
                                                @endif
                                            </div>
                                        </button>
                                        
                                        {{-- Indicador de selección --}}
                                        @if($numeroMes == $this->mesSeleccionado)
                                            <div class="absolute -top-2 -right-2 bg-primary-500 dark:text-white text-xs px-2 py-1 rounded-full font-semibold">
                                                Activo
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Información del mes seleccionado --}}
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 mb-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white">
                            Editando: {{ $meses[$this->mesSeleccionado] ?? 'Agosto' }} {{ now()->format('Y') }}
                        </h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            @if($this->mesSeleccionado == $mesActual)
                                Mes actual - Puedes establecer precios de compra y venta
                            @elseif($this->mesSeleccionado < $mesActual)
                                Mes pasado - Siempre editable para ajustes y correcciones
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Estado:</div>
                        @if($this->mesSeleccionado == $mesActual)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                📅 En curso
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                                💰 Editable
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Separador con título para precios --}}
            <div class="mb-5">
                <div class="flex items-center mb-5">
                    <div class="flex-grow"></div>
                    <div class="px-6 py-3 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/30 dark:to-purple-900/30 rounded-lg">
                        <h3 class="text-lg font-bold text-blue-700 dark:text-blue-300 flex items-center space-x-2">
                            <span class="text-xl">💸</span>
                            <span>Precios del Mes</span>
                        </h3>
                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                            Compra y Venta • {{ $meses[$this->mesSeleccionado] ?? 'Agosto' }} {{ now()->format('Y') }}
                        </p>
                    </div>
                    <div class="flex-grow"></div>
                </div>
            </div>

            {{-- Grid de precios por combustible en dos columnas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
                {{-- Lado izquierdo: Precios de Compra EDITABLES --}}
                <div class="space-y-4">
                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border-l-4 border-green-500">
                        <h4 class="text-lg font-bold text-green-700 dark:text-green-300 mb-2 flex items-center space-x-2">
                            <span class="text-xl">🛒</span>
                            <span>Precios de Compra</span>
                        </h4>
                        <p class="text-xs text-green-600 dark:text-green-400 mb-4">
                            📝 Campos editables - Define tus precios de compra
                        </p>

                        <div class="space-y-4">
                            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center space-x-3 mb-3">
                                    <span class="text-2xl">🟢</span>
                                    <div>
                                        <h5 class="font-semibold text-gray-900 dark:text-white">Super</h5>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Precio de compra por galón</p>
                                    </div>
                                </div>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    min="0" 
                                    placeholder="0.00"
                                    wire:model="precios.super_compra"
                                    class="w-full text-xl font-bold text-green-600 dark:text-green-400 bg-transparent border-0 border-b-2 border-gray-200 dark:border-gray-600 focus:border-green-500 focus:ring-0 px-0"
                                />
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Q por galón
                                </div>
                            </div>

                            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center space-x-3 mb-3">
                                    <span class="text-2xl">🟡</span>
                                    <div>
                                        <h5 class="font-semibold text-gray-900 dark:text-white">Diesel</h5>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Precio de compra por galón</p>
                                    </div>
                                </div>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    min="0" 
                                    placeholder="0.00"
                                    wire:model="precios.diesel_compra"
                                    class="w-full text-xl font-bold text-yellow-600 dark:text-yellow-400 bg-transparent border-0 border-b-2 border-gray-200 dark:border-gray-600 focus:border-yellow-500 focus:ring-0 px-0"
                                />
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Q por galón
                                </div>
                            </div>

                            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center space-x-3 mb-3">
                                    <span class="text-2xl">🔵</span>
                                    <div>
                                        <h5 class="font-semibold text-gray-900 dark:text-white">Regular</h5>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Precio de compra por galón</p>
                                    </div>
                                </div>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    min="0" 
                                    placeholder="0.00"
                                    wire:model="precios.regular_compra"
                                    class="w-full text-xl font-bold text-blue-600 dark:text-blue-400 bg-transparent border-0 border-b-2 border-gray-200 dark:border-gray-600 focus:border-blue-500 focus:ring-0 px-0"
                                />
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Q por galón
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Lado derecho: Precios de Venta SOLO INFORMACIÓN --}}
                <div class="space-y-4">
                    <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg border-l-4 border-purple-500">
                        <h4 class="text-lg font-bold text-purple-700 dark:text-purple-300 mb-2 flex items-center space-x-2">
                            <span class="text-xl">🏪</span>
                            <span>Precios de Venta</span>
                        </h4>
                        <p class="text-xs text-purple-600 dark:text-purple-400 mb-4">
                            📊 Solo consulta - Promedio de ventas registradas
                        </p>

                        <div class="space-y-4">
                            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center space-x-3 mb-3">
                                    <span class="text-2xl">🟢</span>
                                    <div>
                                        <h5 class="font-semibold text-gray-900 dark:text-white">Super</h5>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Promedio de precio de venta</p>
                                    </div>
                                </div>
                                <div class="w-full text-xl font-bold text-green-600 dark:text-green-400 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                    Q{{ number_format($this->obtenerPromedioVenta('super'), 2) }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Promedio por galón
                                </div>
                            </div>

                            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center space-x-3 mb-3">
                                    <span class="text-2xl">🟡</span>
                                    <div>
                                        <h5 class="font-semibold text-gray-900 dark:text-white">Diesel</h5>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Promedio de precio de venta</p>
                                    </div>
                                </div>
                                <div class="w-full text-xl font-bold text-yellow-600 dark:text-yellow-400 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                    Q{{ number_format($this->obtenerPromedioVenta('diesel'), 2) }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Promedio por galón
                                </div>
                            </div>

                            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center space-x-3 mb-3">
                                    <span class="text-2xl">🔵</span>
                                    <div>
                                        <h5 class="font-semibold text-gray-900 dark:text-white">Regular</h5>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Promedio de precio de venta</p>
                                    </div>
                                </div>
                                <div class="w-full text-xl font-bold text-blue-600 dark:text-blue-400 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                    Q{{ number_format($this->obtenerPromedioVenta('regular'), 2) }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Promedio por galón
                                </div>
                            </div>
                        </div>

                        {{-- Nota informativa --}}
                        <div class="mt-4 p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <div class="flex items-start space-x-2">
                                <span class="text-purple-600 dark:text-purple-400 text-sm">ℹ️</span>
                                <div class="text-xs text-purple-700 dark:text-purple-300">
                                    <strong>Información:</strong> Los precios de venta se calculan automáticamente del promedio de todas las gasolineras registradas.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Botón Guardar solo Precios de Compra --}}
            <div class="text-center">
                <button 
                    type="button"
                    wire:click="guardarPreciosCompraMes"
                    class="bg-gradient-to-r from-green-500 to-blue-600 hover:from-green-600 hover:to-blue-700 dark:text-white px-10 py-4 rounded-xl font-semibold text-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 ease-in-out"
                >
                    <span class="text-2xl mr-3">💾</span>
                    Guardar Precios de Compra
                    <span class="ml-2 text-sm opacity-75">({{ $meses[$this->mesSeleccionado] ?? 'Agosto' }})</span>
                </button>
            </div>
        </div>
    </div>
</x-filament-panels::page>
