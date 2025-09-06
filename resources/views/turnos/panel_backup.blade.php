<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Turnos - {{ auth()->user()->gasolinera->nombre }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .pulse-dot { animation: pulse-dot 2s infinite; }
        
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type="number"] {
            -moz-appearance: textfield;
        }
        
        /* Optimización para tablets */
        @media (min-width: 768px) and (max-width: 1024px) {
            body {
                font-size: 14px;
            }
        }
        
        /* Alpine.js cloak */
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 text-white min-h-screen overflow-x-hidden" 
      x-data="panelTurnos()" x-init="init()">
    
    <!-- Notificaciones -->
    @if (session()->has('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-white p-4 rounded-lg shadow-lg z-50 max-w-md">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white p-4 rounded-lg shadow-lg z-50 max-w-md">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif
    
    <!-- Header -->
    <div class="bg-white/10 backdrop-blur-sm border-b border-white/20 p-2">
        <div class="flex items-center justify-between">
            <!-- Izquierda: Logo, info, salir, fecha/hora -->
            <div class="flex items-center space-x-3">
                <!-- Logo y Info -->
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19.44 6.5L15.5 2.56a1 1 0 00-.71-.29H8a2 2 0 00-2 2v16a2 2 0 002 2h8a2 2 0 002-2V7.21a1 1 0 00-.29-.71zM16 18H8v-2h8v2zm0-4H8v-2h8v2zm-3-6V3l5 5h-5z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold">{{ auth()->user()->gasolinera->nombre }}</h1>
                        <p class="text-xs text-white/80">{{ auth()->user()->name }}</p>
                    </div>
                </div>

                <!-- Botón Salir -->
                <form method="POST" action="{{ route('turnos.logout') }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="bg-red-500/20 hover:bg-red-500/30 text-red-300 px-3 py-1 rounded-lg text-xs transition-all duration-200 flex items-center space-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Salir</span>
                    </button>
                </form>
                
                <!-- Fecha y Hora -->
                <div class="text-right">
                    <div class="text-lg font-mono font-bold" x-text="tiempoActual"></div>
                    <div class="text-xs text-white/80" x-text="fechaActual"></div>
                </div>
            </div>
            
            <!-- Derecha: Estado del Turno y Botón Cerrar -->
            <div class="flex items-center space-x-2">
                @if($turnoActual)
                    <!-- Turno Abierto - Solo indicador visual -->
                    <div class="flex items-center space-x-2">
                        <div class="pulse-dot w-2 h-2 bg-green-500 rounded-full"></div>
                        <div class="text-center">
                            <div class="text-xs font-medium text-green-400">TURNO ABIERTO</div>
                                {{ $tiempoTranscurrido }}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botón Cerrar Turno -->
                    <form method="POST" action="{{ route('turnos.cerrar-turno') }}" class="inline">
                        @csrf
                        <button type="submit" 
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-bold transition-all duration-200 transform hover:scale-105 shadow-lg"
                                onclick="return confirm('¿Estás seguro de que deseas cerrar el turno?')">
                            🔒 Cerrar
                        </button>
                    </form>
                @else
                    <!-- Sin Turno - Solo indicador visual -->
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-red-500 rounded-full opacity-70"></div>
                        <div class="text-center">
                            <div class="text-xs font-medium text-red-400">CERRADO</div>
                            <div class="text-xs text-white/60">Sin actividad</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="p-3">
        @if(!$turnoActual)
            <!-- Estado: Sin Turno Activo -->
            <div class="flex items-center justify-center min-h-[40vh]">
                <div class="bg-white/10 backdrop-blur-sm rounded-3xl p-8 text-center border border-white/20 max-w-md">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold mb-3 text-red-400">Turno Cerrado</h2>
                    <p class="text-white/80 mb-4">Presiona el botón para iniciar tu turno de trabajo</p>
                    
                    <form action="{{ route('turnos.abrir-turno') }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-xl text-sm font-bold transition-all duration-200 transform hover:scale-105 shadow-xl">
                            🚀 Abrir Turno
                        </button>
                    </form>
                </div>
            </div>
        @else
            <!-- Estado: Turno Activo -->
            <div class="space-y-3">
                <!-- Efectivo en Turno -->
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3 border border-white/20">
                    <div class="flex items-center mb-2">
                        <h3 class="text-sm font-bold flex items-center flex-1">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                            Efectivo en Turno
                        </h3>
                        @if($ultimaActualizacionEfectivo)
                            <span class="text-xs text-white/60">
                                {{ $ultimaActualizacionEfectivo }}
                            </span>
                        @endif
                    </div>
                    
                    <form method="POST" action="{{ route('turnos.guardar-efectivo') }}" class="flex items-end space-x-2">
                        @csrf
                        <div class="flex-1">
                            <input type="number" 
                                   step="0.001"
                                   inputmode="decimal"
                                   pattern="[0-9]+(\.[0-9]{1,3})?"
                                   name="efectivo"
                                   value="{{ $efectivo }}"
                                   class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 font-mono text-sm"
                                   placeholder="0.00"
                                   required />
                        </div>
                        <button type="submit"
                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded-lg text-xs font-bold transition-all duration-200">
                            💾 Guardar
                        </button>
                    </form>
                </div>

                <!-- Grid de Bombas -->
                @if($bombas && count($bombas) > 0)
                    <div class="space-y-3">
                        @foreach($bombas as $nombreBomba => $bombaData)
                            <div x-data="{ bombaExpanded: false }" 
                                 data-bomba="{{ $nombreBomba }}"
                                 class="bg-white/10 backdrop-blur-sm rounded-xl border border-white/20 shadow-lg overflow-hidden p-2 w-full"
                                 :class="bombaExpanded ? '' : ''">
                                <!-- Header de la bomba (siempre visible) -->
                                <div class="p-3 cursor-pointer hover:bg-white/5 transition-all duration-200" 
                                     @click="bombaExpanded = !bombaExpanded; console.log('{{ $nombreBomba }}:', bombaExpanded)">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-bold flex items-center flex-1 mr-2">
                                            <div class="w-4 h-4 bg-green-500 rounded-full mr-2 pulse-dot flex-shrink-0"></div>
                                            <span class="truncate">{{ $nombreBomba }}</span>
                                            <span class="ml-2 text-xs px-2 py-1 rounded-full flex-shrink-0 
                                                {{ $bombaData['estado'] === 'activa' ? 'bg-green-500/20 text-green-300' : 
                                                   ($bombaData['estado'] === 'inactiva' ? 'bg-red-500/20 text-red-300' : 'bg-yellow-500/20 text-yellow-300') }}">
                                                {{ ucfirst($bombaData['estado']) }}
                                            </span>
                                        </h3>
                                        <!-- Indicador de expansión -->
                                        <div class="flex items-center space-x-2 flex-shrink-0">
                                            <svg class="w-4 h-4 transition-transform duration-300" 
                                                 :class="bombaExpanded ? 'rotate-180' : 'rotate-0'"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Contenido colapsable -->
                                <div x-show="bombaExpanded" 
                                     x-cloak
                                     class="border-t border-white/20 bg-white/5"
                                     style="display: none;">
                                    <form action="{{ route('turnos.bomba.guardar-grupo', $nombreBomba) }}" method="POST" enctype="multipart/form-data" class="p-3">
                                        @csrf
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 mb-4">
                                            @foreach(['Super', 'Regular', 'Diesel', 'CC'] as $tipo)
                                                @if(isset($bombaData['combustibles'][$tipo]))
                                                    @php $combustible = $bombaData['combustibles'][$tipo]; @endphp
                                                    <div class="@if($tipo === 'CC') bg-gradient-to-br from-purple-600/20 to-purple-400/10 rounded-xl p-3 border-2 border-purple-400/60 shadow-lg ring-2 ring-purple-300/30 @else bg-white/10 rounded-xl p-3 border border-white/10 shadow-sm @endif">
                                                        <div class="flex items-center justify-between mb-2">
                                                            <h4 class="text-sm font-bold flex items-center @if($tipo === 'CC') text-purple-200 bg-purple-500/20 px-2 py-1 rounded-lg @endif">
                                                                <span class="mr-2 text-base @if($tipo === 'CC') text-lg @endif">
                                                                    @if($tipo === 'Super') 🟢
                                                                    @elseif($tipo === 'CC') 🟣
                                                                    @elseif($tipo === 'Regular') 🔵  
                                                                    @elseif($tipo === 'Diesel') 🟡
                                                                    @else 🟣
                                                                    @endif
                                                                </span>
                                                                @if($tipo === 'CC') 
                                                                    <span class="font-extrabold text-purple-100">{{ $tipo }} - CONTEO</span>
                                                                @else
                                                                    {{ $tipo }}
                                                                @endif
                                                            </h4>
                                                            @if($tipo === 'CC') 
                                                                <span class="text-xs font-bold bg-gradient-to-r from-purple-500/30 to-purple-400/20 text-purple-200 px-3 py-1 rounded-full border border-purple-400/50 shadow-sm">📊 SOLO LECTURA</span> 
                                                            @else 
                                                                <span class="text-xs font-bold bg-white/10 px-2 py-1 rounded-full">Q{{ number_format($combustible['precio'], 2) }}</span> 
                                                            @endif
                                                        </div>
                                                        
                                                        <div class="space-y-2">
                                                            <div>
                                                                <label class="block text-xs font-medium text-white/70 mb-1">
                                                                    Actual:
                                                                </label>
                                                                <div class="bg-gray-700/50 rounded-lg p-2 text-center">
                                                                    <span class="text-xs font-mono font-bold">{{ number_format($combustible['galonaje'], 3) }}</span>
                                                                    @if($tipo === 'CC') 
                                                                        <span class="text-xs text-purple-300 ml-1 font-bold">unidades 📊</span> 
                                                                    @else 
                                                                        <span class="text-xs text-white/60 ml-1">gal</span> 
                                                                    @endif
                                                                </div>
                                                                <div class="text-xs text-white/50 mt-1 text-center">
                                                                    {{ $bombaData['updated_at'] ?? 'No disponible' }}
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="@if($tipo === 'CC') bg-gradient-to-br from-purple-600/20 to-purple-500/10 border-3 border-purple-400/70 shadow-lg ring-1 ring-purple-300/40 @else bg-red-500/10 border-2 border-orange-400/50 @endif rounded-lg p-3">
                                                                <label class="block text-sm font-bold @if($tipo === 'CC') text-purple-200 @else text-orange-300 @endif mb-2 text-center">
                                                                    @if($tipo === 'CC') 
                                                                        📊 Nuevo valor lectura - {{ $tipo }} (CONTEO) 
                                                                    @else 
                                                                        📝 Nuevo valor galones - {{ $tipo }} 
                                                                    @endif
                                                                </label>
                                                                <input type="number" 
                                                                       step="0.001"
                                                                       inputmode="decimal"
                                                                       pattern="[0-9]+(\.[0-9]{1,3})?"
                                                                       name="lectura_{{ $bombaData['id'] }}_{{ strtolower($tipo) }}"
                                                                       min="{{ $combustible['galonaje'] + 0.001 }}"
                                                                       class="w-full px-3 py-3 @if($tipo === 'CC') bg-gradient-to-r from-purple-500/30 to-purple-400/20 border-3 border-purple-300/80 text-purple-100 placeholder-purple-200/80 focus:ring-purple-300/60 focus:border-purple-200 hover:bg-purple-400/40 shadow-lg font-extrabold @else bg-orange-400/20 border-2 border-orange-400/70 text-white placeholder-orange-200/70 focus:ring-orange-400/50 focus:border-orange-300 hover:bg-orange-400/30 @endif rounded-lg font-bold focus:outline-none focus:ring-4 font-mono text-sm text-center transition-all duration-200"
                                                                       placeholder="{{ number_format($combustible['galonaje'] + 1, 3) }}" />
                                                                <input type="hidden" name="bomba_id" value="{{ $bombaData['id'] }}">
                                                                <input type="hidden" name="tipo_combustible_{{ strtolower($tipo) }}" value="{{ strtolower($tipo) }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                        
                                        <!-- Campo de Fotografía -->
                                        <div class="mt-4 bg-yellow-500/10 border-2 border-yellow-400/50 rounded-xl p-4" 
                                             data-foto-url="{{ $bombaData['fotografia_url'] ?? '' }}"
                                             data-tiene-foto="{{ !empty($bombaData['fotografia_url']) ? '1' : '0' }}"
                                             x-data="fotografiaComponent()"
                                             x-init="initFotografia($el.dataset.fotoUrl, $el.dataset.tieneFoto === '1')">
                                            
                                            <h4 class="text-sm font-bold text-yellow-300 mb-3 flex items-center">
                                                📸 Fotografía de la Bomba (Requerida)
                                            </h4>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <!-- Columna izquierda: Campo de subida -->
                                                <div class="space-y-3">
                                                    <input type="file" 
                                                           name="fotografia_bomba"
                                                           accept="image/*"
                                                           capture="environment"
                                                           @change="handleFileChange($event)"
                                                           class="w-full px-3 py-3 bg-yellow-400/20 border-2 border-yellow-400/70 text-yellow-100 rounded-lg font-bold focus:outline-none focus:ring-4 focus:ring-yellow-400/50 focus:border-yellow-300 hover:bg-yellow-400/30 transition-all duration-200"
                                                           :required="!showPreview" />
                                                    <p class="text-xs text-yellow-200/70 text-center">
                                                        📱 Toma una foto clara de la bomba antes de guardar los valores
                                                    </p>
                                                    
                                                    <!-- Botones de control -->
                                                    <div class="flex space-x-2" x-show="showPreview">
                                                        <button type="button" 
                                                                @click="resetPhoto()"
                                                                x-show="originalUrl !== ''"
                                                                class="text-xs bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 px-3 py-1 rounded-lg transition-all duration-200">
                                                            🔄 Restaurar original
                                                        </button>
                                                        <button type="button" 
                                                                @click="removePhoto()"
                                                                class="text-xs bg-red-500/20 hover:bg-red-500/30 text-red-300 px-3 py-1 rounded-lg transition-all duration-200">
                                                            🗑️ Quitar foto
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                <!-- Columna derecha: Miniatura clickeable -->
                                                <div class="space-y-2" x-show="showPreview">
                                                    <div class="text-xs font-bold text-yellow-300 text-center">Foto cargada:</div>
                                                    <div class="relative bg-black/20 rounded-lg overflow-hidden border-2 border-yellow-400/30 cursor-pointer hover:border-yellow-300 transition-all duration-200"
                                                         @click="openModal()">
                                                        <img :src="previewUrl" 
                                                             alt="Miniatura de fotografía"
                                                             class="w-full h-20 object-cover">
                                                        <div class="absolute inset-0 bg-black/0 hover:bg-black/10 transition-all duration-200 flex items-center justify-center">
                                                            <div class="bg-black/60 text-white px-2 py-1 rounded text-xs opacity-0 hover:opacity-100 transition-opacity duration-200">
                                                                👁️ Ver imagen completa
                                                            </div>
                                                        </div>
                                                        <div class="absolute top-1 right-1">
                                                            <button type="button" 
                                                                    @click.stop="removePhoto()"
                                                                    class="bg-red-500/80 hover:bg-red-600 text-white w-5 h-5 rounded-full text-xs flex items-center justify-center">
                                                                ✕
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="text-xs text-center">
                                                        <span x-show="originalUrl !== '' && previewUrl !== originalUrl" class="text-yellow-200/70">
                                                            📸 Nueva foto seleccionada
                                                        </span>
                                                        <span x-show="originalUrl !== '' && previewUrl === originalUrl" class="text-green-300/70">
                                                            ✅ Foto guardada anteriormente
                                                        </span>
                                                        <div class="text-yellow-200/50 mt-1">
                                                            �️ Haz click para ver en grande
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        
                                        <!-- Botón Guardar al final -->
                                        <div class="mt-4 text-center">
                                            <button type="submit"
                                                    class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-xl text-sm font-bold transition-all duration-200 transform hover:scale-105 w-full shadow-lg">
                                                💾 Guardar {{ $nombreBomba }}
                                            </button>
                                        </div>
                                    </form>
                                    
                                    <!-- Modal para imagen completa - FUERA DEL FORMULARIO -->
                                    <div x-show="modalOpen" 
                                         x-transition:enter="transition ease-out duration-300" 
                                         x-transition:enter-start="opacity-0" 
                                         x-transition:enter-end="opacity-100" 
                                         x-transition:leave="transition ease-in duration-200" 
                                         x-transition:leave-start="opacity-100" 
                                         x-transition:leave-end="opacity-0"
                                         @click.self="closeModal()"
                                         @keydown.escape.window="closeModal()"
                                         class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm"
                                         style="display: none;">
                                        <div class="relative max-w-4xl max-h-full p-4">
                                            <div class="relative bg-white rounded-lg shadow-2xl overflow-hidden">
                                                <!-- Header del modal -->
                                                <div class="flex items-center justify-between p-4 bg-gray-100 border-b">
                                                    <h3 class="text-lg font-semibold text-gray-800">
                                                        📸 Fotografía de {{ $nombreBomba }}
                                                    </h3>
                                                    <button type="button" 
                                                            @click="closeModal()"
                                                            class="text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-full p-2 transition-all duration-200">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                                
                                                <!-- Imagen completa -->
                                                <div class="p-4 bg-gray-50">
                                                    <img :src="previewUrl" 
                                                         alt="Fotografía completa de {{ $nombreBomba }}"
                                                         class="w-full max-h-[70vh] object-contain rounded-lg shadow-lg">
                                                </div>
                                                
                                                <!-- Footer simple -->
                                                <div class="flex items-center justify-center p-4 bg-gray-100 border-t">
                                                    <button type="button" 
                                                            @click="closeModal()"
                                                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg text-sm transition-all duration-200">
                                                        Cerrar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>

    <script>
        function panelTurnos() {
            return {
                tiempoActual: '',
                fechaActual: '',
                
                init() {
                    this.actualizarTiempo();
                    setInterval(() => {
                        this.actualizarTiempo();
                    }, 1000);
                },
                
                actualizarTiempo() {
                    const ahora = new Date();
                    
                    this.tiempoActual = ahora.toLocaleTimeString('es-GT', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: false
                    });
                    
                    this.fechaActual = ahora.toLocaleDateString('es-GT', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                }
            }
        }
        
        // Auto-hide notifications after 5 seconds
        setTimeout(() => {
            const notifications = document.querySelectorAll('.fixed.top-4.right-4');
            notifications.forEach(notification => {
                notification.style.transition = 'opacity 0.5s ease-out';
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 500);
            });
        }, 5000);
        
        // Función para componente de fotografía
        function fotografiaComponent() {
            return {
                previewUrl: '',
                originalUrl: '',
                showPreview: false,
                modalOpen: false,
                
                initFotografia(fotoUrl, tieneFoto) {
                    console.log('initFotografia called:', { fotoUrl, tieneFoto });
                    this.previewUrl = fotoUrl || '';
                    this.originalUrl = fotoUrl || '';
                    this.showPreview = tieneFoto;
                    console.log('After init:', { previewUrl: this.previewUrl, originalUrl: this.originalUrl, showPreview: this.showPreview });
                },
                
                handleFileChange(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.previewUrl = e.target.result;
                            this.showPreview = true;
                        };
                        reader.readAsDataURL(file);
                    }
                },
                
                resetPhoto() {
                    this.previewUrl = this.originalUrl;
                    this.showPreview = this.originalUrl !== '';
                    document.querySelector('input[name="fotografia_bomba"]').value = '';
                },
                
                removePhoto() {
                    this.previewUrl = '';
                    this.showPreview = false;
                    this.modalOpen = false;
                    document.querySelector('input[name="fotografia_bomba"]').value = '';
                },
                
                openModal() {
                    this.modalOpen = true;
                    // Prevenir scroll del body cuando el modal está abierto
                    document.body.style.overflow = 'hidden';
                },
                
                closeModal() {
                    this.modalOpen = false;
                    // Restaurar scroll del body
                    document.body.style.overflow = '';
                }
            }
        }
    </script>
</body>
</html>
