<x-filament-panels::page>
    {{-- Sección de Gastos con card destacado --}}
    <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl border-l-4 border-green-500 shadow-lg mb-6">
        <div class="p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="bg-green-500 dark:text-white p-3 rounded-full">
                    <span class="text-2xl">💰</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Gestión de Gastos</h2>
                    <p class="text-sm text-green-700 dark:text-green-300">
                        Registra y administra todos los gastos operativos de la gasolinera
                    </p>
                </div>
            </div>
            
            {{-- Selector de meses para gestión de gastos (colapsible) --}}
            <div class="mb-5">
                {{-- Barra de título colapsible --}}
                <div 
                    x-data="{ mostrarMeses: false }"
                    class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm"
                >
                    <button
                        @click="mostrarMeses = !mostrarMeses"
                        class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 rounded-lg"
                    >
                        <div class="flex items-center space-x-3">
                            <div class="bg-blue-100 dark:bg-blue-900/30 p-2 rounded-full">
                                <span class="text-lg">📅</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Meses</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Selecciona el mes para gestionar gastos • Año {{ now()->format('Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-gray-400 dark:text-gray-500 transition-transform duration-200"
                             :class="mostrarMeses ? 'transform rotate-180' : ''">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </button>

                    {{-- Grid de meses colapsible --}}
                    <div x-show="mostrarMeses" 
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
                                        $estaCompleto = false; // Esto vendría de la base de datos
                                        $ultimaActualizacion = null; // Esto vendría de la base de datos
                                    @endphp

                                    <div class="relative">
                                        <button 
                                            type="button"
                                            wire:click="seleccionarMes({{ $numeroMes }})"
                                            @if($esFuturo)
                                                disabled
                                                class="w-full p-4 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-600 cursor-not-allowed"
                                            @elseif($esActual)
                                                class="w-full p-4 rounded-lg border-2 @if($numeroMes == $this->mesSeleccionado) border-green-500 bg-green-50 dark:bg-green-900/20 @else border-green-300 hover:border-green-400 bg-white dark:bg-gray-800 @endif text-gray-900 dark:text-white transition-all duration-200 hover:shadow-md"
                                            @else
                                                class="w-full p-4 rounded-lg border-2 @if($numeroMes == $this->mesSeleccionado) border-blue-500 bg-blue-50 dark:bg-blue-900/20 @else border-gray-300 dark:border-gray-600 hover:border-blue-400 bg-white dark:bg-gray-800 @endif text-gray-900 dark:text-white transition-all duration-200 hover:shadow-md"
                                            @endif
                                        >
                                            <div class="text-center">
                                                <div class="font-semibold text-sm mb-1">{{ $nombreMes }}</div>
                                                
                                                @if($esFuturo)
                                                    <div class="text-xs text-gray-400">🔒 Bloqueado</div>
                                                @elseif($esActual)
                                                    <div class="text-xs text-green-600 dark:text-green-400">📅 Actual</div>
                                                    @if($ultimaActualizacion)
                                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        </div>
                                                    @else
                                                        <div class="text-xs text-orange-500 dark:text-orange-400 mt-1">
                                                        </div>
                                                    @endif
                                                @else
                                                    @if($estaCompleto)
                                                        <div class="text-xs text-blue-600 dark:text-blue-400">✅ Completo</div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        </div>
                                                        <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                                                            🖊️ Editable
                                                        </div>
                                                    @else
                                                        <div class="text-xs text-yellow-600 dark:text-yellow-400">⚠️ Pendiente</div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                            No registrado
                                                        </div>
                                                        <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                                                            🖊️ Editable
                                                        </div>
                                                    @endif
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
                                Mes actual - Puedes agregar y modificar gastos
                            @elseif($this->mesSeleccionado < $mesActual)
                                Mes pasado - Siempre editable para ajustes y correcciones
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Estado:</div>
                        @if($this->mesSeleccionado == $mesActual)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                📅 En curso
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                @if($estaCompleto) 
                                    ✅ Completo (Editable)
                                @else
                                    ⚠️ Pendiente (Editable)
                                @endif
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Separador con título para gastos --}}
            <div class="mb-5">
                <div class="flex items-center mb-5">
                    <div class="flex-grow "></div>
                    <div class="px-6 py-3 bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900/30 dark:to-blue-900/30 rounded-lg">
                        <h3 class="text-lg font-bold text-indigo-700 dark:text-indigo-300 flex items-center space-x-2">
                            <span class="text-xl">💳</span>
                            <span>Registro de Gastos</span>
                        </h3>
                        <p class="text-xs text-indigo-600 dark:text-indigo-400 mt-1">
                            Categorías principales • {{ $meses[$this->mesSeleccionado] ?? 'Agosto' }} {{ now()->format('Y') }}
                        </p>
                    </div>
                    <div class="flex-grow "></div>
                </div>
            </div>

            {{-- Gastos fijos principales del mes seleccionado --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
                <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="bg-red-100 dark:bg-red-900/30 p-2 rounded-full">
                            <span class="text-xl">📋</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Impuestos</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">SAT, IVA, ISR</p>
                        </div>
                    </div>
                    <div class="mb-2">
                        <input 
                            type="number" 
                            step="0.01" 
                            min="0" 
                            placeholder="0.00"
                            wire:model="gastos.impuestos"
                            class="w-full text-2xl font-bold text-red-600 dark:text-red-400 bg-transparent border-0 border-b-2 border-gray-200 dark:border-gray-600 focus:border-red-500 focus:ring-0 px-0"
                        />
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <span>{{ $meses[$this->mesSeleccionado] ?? 'Agosto' }}</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="bg-blue-100 dark:bg-blue-900/30 p-2 rounded-full">
                            <span class="text-xl">⚡</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Servicios</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Agua, luz, teléfono</p>
                        </div>
                    </div>
                    <div class="mb-2">
                        <input 
                            type="number" 
                            step="0.01" 
                            min="0" 
                            placeholder="0.00"
                            wire:model="gastos.servicios"
                            class="w-full text-2xl font-bold text-blue-600 dark:text-blue-400 bg-transparent border-0 border-b-2 border-gray-200 dark:border-gray-600 focus:border-blue-500 focus:ring-0 px-0"
                        />
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <span>{{ $meses[$this->mesSeleccionado] ?? 'Agosto' }}</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="bg-green-100 dark:bg-green-900/30 p-2 rounded-full">
                            <span class="text-xl">👥</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Planilla</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Sueldos, prestaciones</p>
                        </div>
                    </div>
                    <div class="mb-2">
                        <input 
                            type="number" 
                            step="0.01" 
                            min="0" 
                            placeholder="0.00"
                            wire:model="gastos.planilla"
                            class="w-full text-2xl font-bold text-green-600 dark:text-green-400 bg-transparent border-0 border-b-2 border-gray-200 dark:border-gray-600 focus:border-green-500 focus:ring-0 px-0"
                        />
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <span>{{ $meses[$this->mesSeleccionado] ?? 'Agosto' }}</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="bg-purple-100 dark:bg-purple-900/30 p-2 rounded-full">
                            <span class="text-xl">🏢</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Renta</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Alquiler del local</p>
                        </div>
                    </div>
                    <div class="mb-2">
                        <input 
                            type="number" 
                            step="0.01" 
                            min="0" 
                            placeholder="0.00"
                            wire:model="gastos.renta"
                            class="w-full text-2xl font-bold text-purple-600 dark:text-purple-400 bg-transparent border-0 border-b-2 border-gray-200 dark:border-gray-600 focus:border-purple-500 focus:ring-0 px-0"
                        />
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <span>{{ $meses[$this->mesSeleccionado] ?? 'Agosto' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Sección de gastos adicionales --}}
    @if(count($gastosAdicionales) > 0)
    <div class="bg-gradient-to-br from-orange-50 to-yellow-50 dark:from-orange-900/20 dark:to-yellow-900/20 rounded-xl border-l-4 border-orange-500 shadow-lg mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="bg-orange-500 dark:text-white p-3 rounded-full">
                        <span class="text-2xl">📝</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Gastos Adicionales</h3>
                        <p class="text-sm text-orange-700 dark:text-orange-300">
                            {{ count($gastosAdicionales) }} gasto(s) adicional(es) para {{ $meses[$this->mesSeleccionado] ?? 'Agosto' }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="space-y-4">
                @foreach($gastosAdicionales as $index => $gasto)
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="flex items-center space-x-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Descripción del gasto
                            </label>
                            <input 
                                type="text" 
                                placeholder="Ej: Mantenimiento de equipos, Publicidad, etc."
                                wire:model="gastosAdicionales.{{ $index }}.descripcion"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-orange-500 focus:ring-orange-500"
                            />
                        </div>
                        <div class="w-40">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Monto (Q)
                            </label>
                            <input 
                                type="number" 
                                step="0.01" 
                                min="0" 
                                placeholder="0.00"
                                wire:model="gastosAdicionales.{{ $index }}.monto"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-orange-500 focus:ring-orange-500 text-right font-bold"
                            />
                        </div>
                        <div class="flex-shrink-0">
                            <button 
                                type="button"
                                wire:click="eliminarGastoAdicional('{{ $gasto['id'] }}')"
                                class="bg-red-500 hover:bg-red-600 dark:text-white p-2 rounded-lg transition-colors duration-200"
                                title="Eliminar gasto"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    
    {{-- Botones de acción --}}
    <div class="text-center mb-5 space-y-3">
        {{-- Botón Guardar Gastos del Mes --}}
        <div>
            <button 
                type="button"
                wire:click="guardarGastosMes"
                class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 dark:text-white px-10 py-4 rounded-xl font-semibold text-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 ease-in-out"
            >
                <span class="text-2xl mr-3">💾</span>
                Guardar Gastos del Mes
                <span class="ml-2 text-sm opacity-75">({{ $meses[$this->mesSeleccionado] ?? 'Agosto' }})</span>
            </button>
        </div>
        
        {{-- Botón Añadir Gasto --}}
        <div>
            <button 
                type="button"
                wire:click="añadirGasto"
                class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 dark:text-white px-8 py-3 rounded-xl font-semibold text-base shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 ease-in-out"
            >
                <span class="text-xl mr-3">➕</span>
                Añadir Gasto Adicional
            </button>
        </div>
    </div>

  
    {{-- Resumen de gastos del mes seleccionado --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Resumen de {{ $meses[$this->mesSeleccionado] ?? 'Agosto' }} {{ now()->format('Y') }}
                </h3>
                <div class="text-right">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Última actualización</div>
                    <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        @if($this->mesSeleccionado == $mesActual)
                            {{ now()->format('d/m/Y H:i') }}
                        @else
                            Sin actualizar
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 dark:bg-gray-700/50 rounded-lg">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Gastos Fijos</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        Q{{ number_format(($gastos['impuestos'] ?? 0) + ($gastos['servicios'] ?? 0) + ($gastos['planilla'] ?? 0) + ($gastos['renta'] ?? 0), 2) }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Impuestos + Servicios + Planilla + Renta</div>
                </div>
                <div class="text-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                    <div class="text-sm text-orange-700 dark:text-orange-300 mb-1">Gastos Variables</div>
                    <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                        @php
                            $totalAdicionales = 0;
                            foreach ($gastosAdicionales as $gasto) {
                                $totalAdicionales += (float) ($gasto['monto'] ?? 0);
                            }
                        @endphp
                        Q{{ number_format($totalAdicionales, 2) }}
                    </div>
                    <div class="text-xs text-orange-600 dark:text-orange-400 mt-1">{{ count($gastosAdicionales) }} gastos adicionales</div>
                </div>
                <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border-l-4 border-red-500">
                    <div class="text-sm text-red-700 dark:text-red-300 mb-1">Total del Mes</div>
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                        Q{{ number_format(($gastos['impuestos'] ?? 0) + ($gastos['servicios'] ?? 0) + ($gastos['planilla'] ?? 0) + ($gastos['renta'] ?? 0) + $totalAdicionales, 2) }}
                    </div>
                    <div class="text-xs text-red-600 dark:text-red-400 mt-1">
                        @if($this->mesSeleccionado == $mesActual)
                            En curso
                        @elseif($this->mesSeleccionado < $mesActual)
                            Pendiente de completar
                        @endif
                    </div>
                </div>
            </div>

            {{-- Acciones del mes --}}
            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        @if($this->mesSeleccionado == $mesActual)
                            📅 Este es el mes actual, puedes agregar gastos en cualquier momento
                        @elseif($this->mesSeleccionado < $mesActual)
                            📅 Mes pasado - Siempre disponible para editar y hacer correcciones
                        @endif
                    </div>
                    <div class="flex space-x-2">
                        {{-- Botón "Marcar como Completo" eliminado por requerimiento --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
