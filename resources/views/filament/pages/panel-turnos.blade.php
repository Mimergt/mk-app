<div class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 text-white">
    <!-- Header -->
    <div class="bg-white/10 backdrop-blur-sm border-b border-white/20 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-4 0H7m-2 0h2m0 0V9a2 2 0 012-2h6a2 2 0 012 2v12M9 7h6m-6 4h6m-6 4h6m-6 4h6"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold">{{ $gasolineraUsuario ? $gasolineraUsuario->nombre : 'Gasolinera' }}</h1>
                    <p class="text-lg text-white/80">{{ $gasolineraUsuario ? $gasolineraUsuario->ubicacion : '' }}</p>
                    <p class="text-sm text-white/60">Operador: {{ auth()->user()->name }}</p>
                </div>
            </div>
            
            <div class="text-right">
                <div class="text-6xl font-mono font-bold">{{ now()->format('H:i') }}</div>
                <div class="text-xl">{{ now()->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Panel Principal -->
    <div class="p-8">
        @if(!$turnoActual)
            <!-- Sin turno activo -->
            <div class="max-w-4xl mx-auto text-center">
                <div class="bg-white/10 backdrop-blur-sm rounded-3xl p-12 border border-white/20">
                    <div class="w-32 h-32 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-8">
                        <svg class="w-16 h-16 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-4xl font-bold mb-4">Turno Cerrado</h2>
                    <p class="text-xl text-white/80 mb-8">Presiona el botón para iniciar tu turno de trabajo</p>
                    
                    <button wire:click="abrirTurno" 
                            class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-12 py-6 rounded-2xl text-2xl font-bold transition-all duration-200 transform hover:scale-105 shadow-2xl">
                        🚀 Abrir Turno
                    </button>
                </div>
            </div>
        @else
            <!-- Turno activo -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Estado del turno -->
                <div class="bg-green-500/20 backdrop-blur-sm rounded-2xl p-6 border border-green-400/30">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-green-400">TURNO ABIERTO</h3>
                        <p class="text-green-300">Iniciado: {{ $turnoActual->hora_inicio->format('H:i') }}</p>
                    </div>
                </div>

                <!-- Contador de tiempo -->
                <div class="bg-blue-500/20 backdrop-blur-sm rounded-2xl p-6 border border-blue-400/30">
                    <div class="text-center">
                        <h3 class="text-lg font-semibold text-blue-300 mb-2">Tiempo Transcurrido</h3>
                        <div class="text-4xl font-mono font-bold text-blue-400" wire:poll.1s="calcularTiempoTranscurrido">
                            {{ $tiempoTranscurrido }}
                        </div>
                        <p class="text-blue-300 text-sm">HH:MM:SS</p>
                    </div>
                </div>

                <!-- Botón cerrar turno -->
                <div class="bg-red-500/20 backdrop-blur-sm rounded-2xl p-6 border border-red-400/30">
                    <div class="text-center">
                        <h3 class="text-lg font-semibold text-red-300 mb-4">Finalizar Turno</h3>
                        <button wire:click="cerrarTurno" 
                                onclick="return confirm('¿Cerrar el turno actual?')"
                                class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-6 py-3 rounded-xl font-bold transition-all duration-200 transform hover:scale-105">
                            🔒 Cerrar Turno
                        </button>
                    </div>
                </div>
            </div>

            <!-- Grid de Bombas -->
            @if($bombas && count($bombas) > 0)
                <div class="mb-8">
                    <div class="flex justify-center mb-6">
                        <button wire:click="guardarLecturasEnVivo" 
                                class="bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white px-8 py-4 rounded-xl text-lg font-bold transition-all duration-200 transform hover:scale-105 shadow-xl">
                            💾 Guardar Lecturas
                        </button>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    @foreach($bombas as $nombreBomba => $bombasPorTipo)
                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20">
                            <h3 class="text-3xl font-bold mb-6 text-center flex items-center justify-center">
                                <div class="w-6 h-6 bg-green-500 rounded-full mr-3"></div>
                                {{ $nombreBomba }}
                            </h3>
                            
                            <div class="space-y-6">
                                @foreach(['Super', 'Regular', 'Diesel'] as $tipo)
                                    @php
                                        $bomba = null;
                                        foreach($bombasPorTipo as $b) {
                                            if($b['tipo'] === $tipo) {
                                                $bomba = $b;
                                                break;
                                            }
                                        }
                                    @endphp
                                    
                                    @if($bomba)
                                        <div class="bg-white/5 rounded-xl p-6 border border-white/10">
                                            <div class="flex items-center justify-between mb-4">
                                                <h4 class="text-xl font-bold flex items-center">
                                                    <span class="w-4 h-4 rounded-full mr-3 
                                                        {{ $tipo === 'Super' ? 'bg-green-500' : 
                                                           ($tipo === 'Regular' ? 'bg-blue-500' : 'bg-yellow-500') }}">
                                                    </span>
                                                    {{ $tipo }}
                                                </h4>
                                                <span class="text-2xl font-bold">Q{{ number_format($bomba['precio'], 2) }}</span>
                                            </div>
                                            
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-semibold text-white/80 mb-2">
                                                        Lectura Anterior:
                                                    </label>
                                                    <div class="bg-gray-700/50 rounded-lg p-3 text-center">
                                                        <span class="text-lg font-mono">{{ number_format($bomba['galonaje'], 2) }}</span>
                                                        <span class="text-sm text-white/60 ml-1">gal</span>
                                                    </div>
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-sm font-semibold text-white/80 mb-2">
                                                        Nueva Lectura:
                                                    </label>
                                                    <input type="number" 
                                                           step="0.01"
                                                           wire:model="lecturas.{{ $bomba['id'] }}"
                                                           class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-lg text-center"
                                                           placeholder="0.00">
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Sección de Totales de Ventas -->
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20 mb-8">
                    <h3 class="text-3xl font-bold mb-6 text-center flex items-center justify-center">
                        <div class="w-6 h-6 bg-green-500 rounded-full mr-3"></div>
                        💰 Totales de Ventas
                    </h3>

                    <form action="{{ route('turnos.guardar-ventas') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-semibold text-white/80 mb-2">Crédito (Q):</label>
                                <input type="number"
                                       step="0.01"
                                       name="venta_credito"
                                       value="{{ $datosVentas['credito'] }}"
                                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-lg text-center"
                                       placeholder="0.00">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-white/80 mb-2">Tarjetas (Q):</label>
                                <input type="number"
                                       step="0.01"
                                       name="venta_tarjetas"
                                       value="{{ $datosVentas['tarjetas'] }}"
                                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-lg text-center"
                                       placeholder="0.00">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-white/80 mb-2">Efectivo (Q):</label>
                                <input type="number"
                                       step="0.01"
                                       name="venta_efectivo"
                                       value="{{ $datosVentas['efectivo'] }}"
                                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-lg text-center"
                                       placeholder="0.00">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-white/80 mb-2">Descuentos (Q):</label>
                                <input type="number"
                                       step="0.01"
                                       name="venta_descuentos"
                                       value="{{ $datosVentas['descuentos'] }}"
                                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-lg text-center"
                                       placeholder="0.00">
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit"
                                    class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-8 py-4 rounded-xl text-lg font-bold transition-all duration-200 transform hover:scale-105 shadow-xl">
                                💾 Guardar Totales de Ventas
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Sección de Nivel de Tanques -->
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20 mb-8">
                    <h3 class="text-3xl font-bold mb-6 text-center flex items-center justify-center">
                        <div class="w-6 h-6 bg-blue-500 rounded-full mr-3"></div>
                        ⛽ Nivel de Tanques
                    </h3>

                    <form action="{{ route('turnos.guardar-tanques') }}" method="POST">
                        @csrf

                        <!-- Sección Pulgadas -->
                        <div class="mb-8">
                            <h4 class="text-xl font-bold mb-4 text-blue-300">📏 En Pulgadas</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-white/80 mb-2">Super (pulg):</label>
                                    <input type="number"
                                           step="0.01"
                                           name="tanque_super_pulgadas"
                                           value="{{ $datosTanques['pulgadas']['super'] }}"
                                           class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 font-mono text-lg text-center"
                                           placeholder="0.00">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-white/80 mb-2">Regular (pulg):</label>
                                    <input type="number"
                                           step="0.01"
                                           name="tanque_regular_pulgadas"
                                           value="{{ $datosTanques['pulgadas']['regular'] }}"
                                           class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-lg text-center"
                                           placeholder="0.00">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-white/80 mb-2">Diesel (pulg):</label>
                                    <input type="number"
                                           step="0.01"
                                           name="tanque_diesel_pulgadas"
                                           value="{{ $datosTanques['pulgadas']['diesel'] }}"
                                           class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 font-mono text-lg text-center"
                                           placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        <!-- Sección Galones -->
                        <div class="mb-8">
                            <h4 class="text-xl font-bold mb-4 text-green-300">🪣 En Galones</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-white/80 mb-2">Super (gal):</label>
                                    <input type="number"
                                           step="0.01"
                                           name="tanque_super_galones"
                                           value="{{ $datosTanques['galones']['super'] }}"
                                           class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 font-mono text-lg text-center"
                                           placeholder="0.00">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-white/80 mb-2">Regular (gal):</label>
                                    <input type="number"
                                           step="0.01"
                                           name="tanque_regular_galones"
                                           value="{{ $datosTanques['galones']['regular'] }}"
                                           class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-lg text-center"
                                           placeholder="0.00">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-white/80 mb-2">Diesel (gal):</label>
                                    <input type="number"
                                           step="0.01"
                                           name="tanque_diesel_galones"
                                           value="{{ $datosTanques['galones']['diesel'] }}"
                                           class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 font-mono text-lg text-center"
                                           placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit"
                                    class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-8 py-4 rounded-xl text-lg font-bold transition-all duration-200 transform hover:scale-105 shadow-xl">
                                💾 Guardar Nivel de Tanques
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        @endif
    </div>

    <!-- Notificaciones -->
    @if (session()->has('error'))
        <div class="fixed bottom-4 right-4 bg-red-500 text-white p-4 rounded-lg shadow-lg z-50 max-w-md">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white p-4 rounded-lg shadow-lg z-50 max-w-md">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif
</div>
