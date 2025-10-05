<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Panel de Turnos - {{ auth()->user()->gasolinera->nombre }}</title>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#1e3a8a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Panel MK">
    <meta name="msapplication-TileColor" content="#1e3a8a">
    <meta name="msapplication-config" content="/browserconfig.xml">
    
    <!-- Fullscreen Meta Tags -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="msapplication-navbutton-color" content="#1e3a8a">
    <meta name="msapplication-starturl" content="/gas/panel">
    <meta name="format-detection" content="telephone=no">
    
    <!-- Chrome Fullscreen -->
    <meta name="theme-color" content="#1e3a8a" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#1e3a8a" media="(prefers-color-scheme: dark)">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    
    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/icon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/icons/icon-16x16.png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        
        /* Fullscreen PWA styles */
        html, body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100vh;
            overflow-x: hidden;
            /* Evitar bounce en iOS */
            overscroll-behavior: none;
            /* Fullscreen específico para PWA */
            -webkit-user-select: none;
            user-select: none;
        }
        
        /* Eliminar zoom en inputs en mobile */
        input, textarea, select {
            font-size: 16px !important;
            -webkit-user-select: text;
            user-select: text;
        }
        
        /* Evitar scroll horizontal */
        * {
            box-sizing: border-box;
        }
        
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
        
        /* PWA Fullscreen optimization */
        @media (display-mode: fullscreen) {
            body {
                padding-top: 0 !important;
            }
        }
        
        @media (display-mode: standalone) {
            body {
                padding-top: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-gray-900 text-white h-screen w-full overflow-x-hidden"
      x-data="panelTurnos()" x-init="init()">
    
    <!-- Notificaciones -->
    @if(session('success'))
        <div class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg"
             x-data
             x-init="
                setTimeout(() => {
                    const message = '{{ session('success') }}';
                    const bombaMatch = message.match(/✅\s*(.+?):/);
                    if (bombaMatch) {
                        $dispatch('show-success', { bomba: bombaMatch[1].trim() });
                    }
                }, 100);
             ">
            ✅ {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
            ❌ {{ session('error') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
            ❌ {{ $errors->first() }}
        </div>
    @endif

    <!-- Overlay de Carga Global -->
    <div x-data="{
        show: false,
        bombaName: '',
        isSuccess: false,
        showOverlay(bomba) {
            this.bombaName = bomba;
            this.isSuccess = false;
            this.show = true;
        },
        showSuccess(bomba) {
            this.bombaName = bomba;
            this.isSuccess = true;
            this.show = true;
            setTimeout(() => {
                this.show = false;
            }, 2000);
        },
        hide() {
            this.show = false;
        }
    }"
    @show-loading.window="showOverlay($event.detail.bomba)"
    @show-success.window="showSuccess($event.detail.bomba)">

        <!-- Overlay de fondo -->
        <div x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[100] flex items-center justify-center"
             style="display: none;">

            <!-- Mensaje de Carga -->
            <div x-show="!isSuccess" class="bg-white rounded-2xl p-8 shadow-2xl max-w-md mx-4 text-center">
                <div class="mb-6">
                    <div class="animate-spin rounded-full h-16 w-16 border-4 border-blue-200 border-t-blue-600 mx-auto"></div>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Guardando...</h3>
                <p class="text-gray-600 text-lg">
                    Estamos guardando la <span class="font-bold text-blue-600" x-text="bombaName"></span>
                </p>
                <p class="text-gray-500 text-sm mt-2">Por favor espere...</p>
            </div>

            <!-- Mensaje de Éxito -->
            <div x-show="isSuccess" class="bg-white rounded-2xl p-8 shadow-2xl max-w-md mx-4 text-center">
                <div class="mb-6">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">¡Guardado Exitoso!</h3>
                <p class="text-gray-600 text-lg">
                    <span class="font-bold text-green-600" x-text="bombaName"></span> guardada correctamente
                </p>
            </div>
        </div>
    </div>

    <!-- Header -->
    <div class="bg-white/10 backdrop-blur-sm border-b border-white/20 p-2">
        <div class="flex items-center justify-between">
            <!-- Izquierda: Logo, info, salir, fecha/hora -->
            <div class="flex items-center space-x-3">
                <!-- Logo y Info -->
                <div class="flex items-center space-x-2">
                    <div>
                        <div class="flex items-center space-x-2">
                            <img src="{{ asset('images/MONTEKARLO_logo.png') }}" alt="Logo" class="w-20 rounded-full">
                            <h1 class="text-lg font-bold">{{ auth()->user()->gasolinera->nombre }}</h1>
                        </div>
                        <!-- <p class="text-xs text-white/80">Usuaio: {{ auth()->user()->name }}</p> -->
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
                            @if($tiempoTranscurrido)
                                {{ $tiempoTranscurrido }}
                            @endif
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
                    <!-- Botón Abrir Turno -->
                    <form method="POST" action="{{ route('turnos.abrir-turno') }}" class="inline">
                        @csrf
                        <button type="submit" 
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-bold transition-all duration-200 transform hover:scale-105 shadow-lg">
                            🔓 Abrir Turno
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    
    
    <!-- Main Content -->
    <div class="p-3">
        @if($turnoActual)
            <!-- Estado: Turno Activo -->
            <div class="space-y-3">
                <!-- Grid de Combustibles -->
                @if($bombas && count($bombas) > 0)
                    <div class="space-y-3">
                        @foreach(['Super', 'Regular', 'Diesel'] as $tipoCombustible)
                            @php
                                $colorFondo = $tipoCombustible === 'Super' ? 'from-red-600/30 to-red-500/20' : ($tipoCombustible === 'Regular' ? 'from-orange-600/30 to-orange-500/20' : 'from-blue-600/30 to-blue-500/20');
                                $colorBorde = $tipoCombustible === 'Super' ? 'border-red-400/50' : ($tipoCombustible === 'Regular' ? 'border-orange-400/50' : 'border-blue-400/50');
                                $emoji = $tipoCombustible === 'Super' ? '🔴' : ($tipoCombustible === 'Regular' ? '🟡' : '🔵');
                            @endphp
                            <div x-data="{ combustibleExpanded: false }"
                                 data-combustible="{{ $tipoCombustible }}"
                                 class="bg-gradient-to-br {{ $colorFondo }} backdrop-blur-sm rounded-xl border-2 {{ $colorBorde }} shadow-lg overflow-hidden p-2 w-full">
                                <!-- Header del combustible (siempre visible) -->
                                <div class="p-3 cursor-pointer hover:bg-white/5 transition-all duration-200"
                                     @click="combustibleExpanded = !combustibleExpanded">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-bold flex items-center flex-1 mr-2">
                                            <span class="mr-3 text-2xl">{{ $emoji }}</span>
                                            <span class="truncate">{{ $tipoCombustible }}</span>
                                        </h3>
                                        <!-- Indicador de expansión -->
                                        <div class="flex items-center space-x-2 flex-shrink-0">
                                            <svg class="w-5 h-5 transition-transform duration-300"
                                                 :class="combustibleExpanded ? 'rotate-180' : 'rotate-0'"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contenido colapsable -->
                                <div x-show="combustibleExpanded"
                                     x-cloak
                                     class="border-t border-white/20"
                                     style="display: none;">
                                    <div class="p-3 space-y-3">
                                        @foreach($bombas as $nombreBomba => $bombaData)
                                            @if(isset($bombaData['combustibles'][$tipoCombustible]))
                                                @php
                                                    $combustible = $bombaData['combustibles'][$tipoCombustible];
                                                    $gasolinera = auth()->user()->gasolinera;
                                                @endphp
                                                <form action="{{ route('turnos.bomba.guardar-grupo', $nombreBomba) }}" method="POST" enctype="multipart/form-data"
                                                      class="bg-black/30 rounded-lg p-3 border border-white/20"
                                                      @submit="$dispatch('show-loading', { bomba: '{{ $nombreBomba }} - {{ $tipoCombustible }}' })">
                                                    @csrf
                                                    <input type="hidden" name="tipo_combustible" value="{{ strtolower($tipoCombustible) }}">
                                                    <input type="hidden" name="bomba_id" value="{{ $bombaData['id'] }}">

                                                    <div class="flex items-center justify-between mb-3">
                                                        <h4 class="text-sm font-bold flex items-center">
                                                            <div class="w-3 h-3 {{ $bombaData['estado'] === 'activa' ? 'bg-green-500' : 'bg-red-500' }} rounded-full mr-2 {{ $bombaData['estado'] === 'activa' ? 'pulse-dot' : '' }}"></div>
                                                            {{ $nombreBomba }}
                                                        </h4>
                                                        <span class="text-xs font-bold bg-white/10 px-2 py-1 rounded-full">Q{{ number_format($combustible['precio'], 2) }}</span>
                                                    </div>

                                                    <!-- Lecturas -->
                                                    <div class="mb-2">
                                                        <!-- Lectura Anterior -->
                                                        <div class="mb-2">
                                                            <label class="block text-xs font-medium text-white/70 mb-1">Lectura Anterior:</label>
                                                            <div class="bg-gray-700/50 rounded-lg p-2 text-center">
                                                                <span class="text-xs font-mono font-bold">{{ number_format($combustible['lectura_actual'], 3) }}</span>
                                                                <span class="text-xs text-white/60 ml-1">gal</span>
                                                            </div>
                                                            <div class="text-xs text-white/50 mt-1 text-center">{{ $combustible['fecha_lectura'] }}</div>
                                                        </div>

                                                        <!-- Nueva Lectura -->
                                                        <div>
                                                            <label class="block text-xs font-bold text-orange-300 mb-1">📝 Nueva Lectura:</label>
                                                            <input type="number" step="0.001" inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,3})?"
                                                                   name="lectura_{{ $bombaData['id'] }}_{{ strtolower($tipoCombustible) }}"
                                                                   min="{{ $combustible['lectura_actual'] + 0.001 }}"
                                                                   x-data="{ valor: '' }"
                                                                   @input="$dispatch('lectura-actualizada-{{ $bombaData['id'] }}-{{ strtolower($tipoCombustible) }}', { valor: $event.target.value })"
                                                                   class="w-full px-3 py-2 bg-white border-2 border-orange-400/70 text-black placeholder-gray-400 focus:ring-orange-400/50 focus:border-orange-300 hover:bg-orange-50 rounded-lg font-bold focus:outline-none focus:ring-2 font-mono text-sm text-center transition-all duration-200"
                                                                   placeholder="{{ number_format($combustible['lectura_actual'] + 1, 3) }}">
                                                        </div>
                                                    </div>

                                                    <!-- Fila 2: Galones y Ventas -->
                                                    <div class="grid grid-cols-2 gap-2 mb-3">
                                                        <!-- Galones Consumidos -->
                                                        <div x-data="{
                                                            lecturaAnterior: {{ $combustible['lectura_actual'] }},
                                                            lecturaNueva: 0,
                                                            galones: 0
                                                        }"
                                                        @lectura-actualizada-{{ $bombaData['id'] }}-{{ strtolower($tipoCombustible) }}.window="lecturaNueva = parseFloat($event.detail.valor) || 0; galones = lecturaNueva - lecturaAnterior">
                                                            <label class="block text-xs font-medium text-green-300 mb-1">💧 Galones Consumidos:</label>
                                                            <div class="bg-green-600/20 border-2 border-green-400/50 rounded-lg p-2 text-center">
                                                                <span class="text-xs font-mono font-bold text-green-200" x-text="galones > 0 ? galones.toFixed(3) : '0.000'"></span>
                                                                <span class="text-xs text-green-300 ml-1">gal</span>
                                                            </div>
                                                        </div>

                                                        <!-- Venta Total -->
                                                        <div x-data="{
                                                            precio: {{ $combustible['precio'] }},
                                                            lecturaAnterior: {{ $combustible['lectura_actual'] }},
                                                            lecturaNueva: 0,
                                                            venta: 0
                                                        }"
                                                        @lectura-actualizada-{{ $bombaData['id'] }}-{{ strtolower($tipoCombustible) }}.window="lecturaNueva = parseFloat($event.detail.valor) || 0; venta = (lecturaNueva - lecturaAnterior) * precio">
                                                            <label class="block text-xs font-medium text-yellow-300 mb-1">💰 Venta:</label>
                                                            <div class="bg-yellow-600/20 border-2 border-yellow-400/50 rounded-lg p-2 text-center">
                                                                <span class="text-xs font-mono font-bold text-yellow-200">Q<span x-text="venta > 0 ? venta.toFixed(2) : '0.00'"></span></span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Campo de Fotografía -->
                                                    <div class="bg-yellow-500/10 border-2 border-yellow-400/50 rounded-xl p-3 mb-3"
                                                         data-foto-url="{{ $bombaData['fotografia_url'] ?? '' }}"
                                                         data-tiene-foto="{{ $bombaData['tiene_fotografia'] ? '1' : '0' }}"
                                                         x-data="fotografiaComponent()"
                                                         x-init="initFotografia($el.dataset.fotoUrl, $el.dataset.tieneFoto === '1')">

                                                        <h5 class="text-xs font-bold text-yellow-300 mb-2 flex items-center">
                                                            📸 Fotografía de la Bomba
                                                        </h5>

                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                            <!-- Columna izquierda: Campo de subida -->
                                                            <div class="space-y-2">
                                                                <input type="file" name="fotografia_bomba" accept="image/*" capture="environment"
                                                                       @change="handleFileChange($event)"
                                                                       class="w-full px-2 py-2 bg-yellow-400/20 border-2 border-yellow-400/70 text-yellow-100 rounded-lg text-xs font-bold focus:outline-none focus:ring-2 focus:ring-yellow-400/50 focus:border-yellow-300 hover:bg-yellow-400/30 transition-all duration-200"
                                                                       :required="!showPreview"
                                                                       :disabled="processing"
                                                                       id="file-input-{{ $nombreBomba }}-{{ strtolower($tipoCombustible) }}">

                                                                <div x-show="processing" class="text-center py-1">
                                                                    <div class="text-yellow-300 text-xs">🔄 Comprimiendo...</div>
                                                                </div>

                                                                <p class="text-xs text-yellow-200/70 text-center">
                                                                    📱 Toma una foto clara de la bomba
                                                                </p>

                                                                <div class="flex space-x-2" x-show="showPreview">
                                                                    <button type="button" @click="resetPhoto()" x-show="originalUrl !== ''"
                                                                            class="text-xs bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 px-2 py-1 rounded-lg transition-all duration-200">
                                                                        🔄 Restaurar
                                                                    </button>
                                                                    <button type="button" @click="removePhoto()"
                                                                            class="text-xs bg-red-500/20 hover:bg-red-500/30 text-red-300 px-2 py-1 rounded-lg transition-all duration-200">
                                                                        🗑️ Quitar
                                                                    </button>
                                                                </div>
                                                            </div>

                                                            <!-- Columna derecha: Miniatura -->
                                                            <div class="space-y-2" x-show="showPreview">
                                                                <div class="text-xs font-bold text-yellow-300 text-center">Foto cargada:</div>
                                                                <div class="relative bg-black/20 rounded-lg overflow-hidden border-2 border-yellow-400/30 cursor-pointer hover:border-yellow-300 transition-all duration-200"
                                                                     @click="openModal()">
                                                                    <img :src="previewUrl" alt="Miniatura" class="w-full h-16 object-cover">
                                                                    <div class="absolute inset-0 bg-black/0 hover:bg-black/10 transition-all duration-200 flex items-center justify-center">
                                                                        <div class="bg-black/60 text-white px-2 py-1 rounded text-xs opacity-0 hover:opacity-100 transition-opacity duration-200">
                                                                            👁️ Ver
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Modal para imagen completa -->
                                                        <div x-show="modalOpen"
                                                             x-transition:enter="transition ease-out duration-300"
                                                             x-transition:enter-start="opacity-0"
                                                             x-transition:enter-end="opacity-100"
                                                             x-transition:leave="transition ease-in duration-200"
                                                             x-transition:leave-start="opacity-100"
                                                             x-transition:leave-end="opacity-0"
                                                             @click.self="closeModal()"
                                                             @keydown.escape.window="closeModal()"
                                                             class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm">

                                                            <div class="h-full w-full overflow-y-auto flex items-start justify-center p-4">
                                                                <div class="w-full max-w-4xl my-8">
                                                                    <div class="bg-white rounded-lg shadow-2xl overflow-hidden">
                                                                        <div class="flex items-center justify-between p-4 bg-gray-100 border-b sticky top-0">
                                                                            <h3 class="text-lg font-semibold text-gray-800">
                                                                                📸 {{ $nombreBomba }} - {{ $tipoCombustible }}
                                                                            </h3>
                                                                            <button type="button" @click="closeModal()"
                                                                                    class="text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-full p-3 transition-all duration-200 text-xl font-bold">
                                                                                ✕
                                                                            </button>
                                                                        </div>

                                                                        <div class="p-4 bg-gray-50">
                                                                            <img :src="previewUrl" :alt="'Fotografía {{ $nombreBomba }}'"
                                                                                 class="w-full h-auto object-contain rounded-lg shadow-lg">
                                                                        </div>

                                                                        <div class="flex items-center justify-center p-4 bg-gray-100 border-t sticky bottom-0">
                                                                            <button type="button" @click="closeModal()"
                                                                                    class="bg-blue-500 hover:bg-blue-600 text-white px-8 py-3 rounded-lg text-lg font-bold transition-all duration-200">
                                                                                Cerrar
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Botón Guardar -->
                                                    <div class="text-center">
                                                        <button type="submit"
                                                                :disabled="processing"
                                                                class="bg-green-500 hover:bg-green-600 disabled:bg-gray-400 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg text-sm font-bold transition-all duration-200 transform hover:scale-105 w-full shadow-lg">
                                                            💾 Guardar {{ $nombreBomba }} - {{ $tipoCombustible }}
                                                            <span x-show="processing" class="ml-2">🔄</span>
                                                        </button>
                                                    </div>
                                                </form>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- TAREA 2: Bloque de CC -->
                    <div x-data="{ ccExpanded: false }"
                         data-seccion="cc"
                         class="bg-gradient-to-br from-purple-600/30 to-purple-500/20 backdrop-blur-sm rounded-xl border-2 border-purple-400/50 shadow-lg overflow-hidden p-2 w-full">
                        <!-- Header de CC -->
                        <div class="p-3 cursor-pointer hover:bg-white/5 transition-all duration-200"
                             @click="ccExpanded = !ccExpanded">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-bold flex items-center flex-1 mr-2">
                                    <span class="mr-3 text-2xl">🟣</span>
                                    <span class="truncate">CC - Conteo de Combustible</span>
                                </h3>
                                <div class="flex items-center space-x-2 flex-shrink-0">
                                    <svg class="w-5 h-5 transition-transform duration-300"
                                         :class="ccExpanded ? 'rotate-180' : 'rotate-0'"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Contenido colapsable de CC -->
                        <div x-show="ccExpanded"
                             x-cloak
                             class="border-t border-white/20"
                             style="display: none;">
                            <div class="p-3 space-y-3">
                                @foreach($bombas as $nombreBomba => $bombaData)
                                    @if(isset($bombaData['combustibles']['CC']))
                                        @php $combustibleCC = $bombaData['combustibles']['CC']; @endphp
                                        <form action="{{ route('turnos.bomba.guardar-grupo', $nombreBomba) }}" method="POST" enctype="multipart/form-data"
                                              class="bg-black/30 rounded-lg p-3 border border-purple-400/30"
                                              @submit="$dispatch('show-loading', { bomba: '{{ $nombreBomba }} - CC' })">
                                            @csrf
                                            <input type="hidden" name="tipo_combustible" value="cc">
                                            <input type="hidden" name="bomba_id" value="{{ $bombaData['id'] }}">

                                            <div class="flex items-center justify-between mb-3">
                                                <h4 class="text-sm font-bold flex items-center text-purple-200">
                                                    <div class="w-3 h-3 {{ $bombaData['estado'] === 'activa' ? 'bg-green-500' : 'bg-red-500' }} rounded-full mr-2 {{ $bombaData['estado'] === 'activa' ? 'pulse-dot' : '' }}"></div>
                                                    {{ $nombreBomba }} - CC
                                                </h4>
                                                <span class="text-xs font-bold bg-purple-500/20 px-2 py-1 rounded-full text-purple-200">📊 Solo Lectura</span>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-3">
                                                <!-- Lectura Anterior CC -->
                                                <div>
                                                    <label class="block text-xs font-medium text-purple-200 mb-1">Lectura Anterior CC:</label>
                                                    <div class="bg-purple-900/30 rounded-lg p-2 text-center border border-purple-400/30">
                                                        <span class="text-xs font-mono font-bold text-purple-100">{{ number_format($combustibleCC['lectura_actual'], 3) }}</span>
                                                        <span class="text-xs text-purple-300 ml-1">unidades</span>
                                                    </div>
                                                    <div class="text-xs text-purple-200/70 mt-1 text-center">{{ $combustibleCC['fecha_lectura'] }}</div>
                                                </div>

                                                <!-- Nueva Lectura CC -->
                                                <div>
                                                    <label class="block text-xs font-bold text-purple-200 mb-1">📊 Nueva Lectura CC:</label>
                                                    <input type="number" step="0.001" inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,3})?"
                                                           name="lectura_{{ $bombaData['id'] }}_cc"
                                                           min="{{ $combustibleCC['lectura_actual'] + 0.001 }}"
                                                           class="w-full px-3 py-2 bg-gradient-to-r from-purple-500/30 to-purple-400/20 border-2 border-purple-300/80 text-purple-100 placeholder-purple-200/80 focus:ring-purple-300/60 focus:border-purple-200 hover:bg-purple-400/40 rounded-lg font-bold focus:outline-none focus:ring-2 font-mono text-sm text-center transition-all duration-200"
                                                           placeholder="{{ number_format($combustibleCC['lectura_actual'] + 1, 3) }}">
                                                </div>
                                            </div>

                                            <!-- Campo de Fotografía -->
                                            <div class="bg-yellow-500/10 border-2 border-yellow-400/50 rounded-xl p-3 mb-3"
                                                 data-foto-url="{{ $bombaData['fotografia_url'] ?? '' }}"
                                                 data-tiene-foto="{{ $bombaData['tiene_fotografia'] ? '1' : '0' }}"
                                                 x-data="fotografiaComponent()"
                                                 x-init="initFotografia($el.dataset.fotoUrl, $el.dataset.tieneFoto === '1')">

                                                <h5 class="text-xs font-bold text-yellow-300 mb-2 flex items-center">
                                                    📸 Fotografía de la Bomba
                                                </h5>

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                    <div class="space-y-2">
                                                        <input type="file" name="fotografia_bomba" accept="image/*" capture="environment"
                                                               @change="handleFileChange($event)"
                                                               class="w-full px-2 py-2 bg-yellow-400/20 border-2 border-yellow-400/70 text-yellow-100 rounded-lg text-xs font-bold focus:outline-none focus:ring-2 focus:ring-yellow-400/50 focus:border-yellow-300 hover:bg-yellow-400/30 transition-all duration-200"
                                                               :required="!showPreview"
                                                               :disabled="processing"
                                                               id="file-input-{{ $nombreBomba }}-cc">

                                                        <div x-show="processing" class="text-center py-1">
                                                            <div class="text-yellow-300 text-xs">🔄 Comprimiendo...</div>
                                                        </div>

                                                        <p class="text-xs text-yellow-200/70 text-center">
                                                            📱 Toma una foto clara de la bomba
                                                        </p>

                                                        <div class="flex space-x-2" x-show="showPreview">
                                                            <button type="button" @click="resetPhoto()" x-show="originalUrl !== ''"
                                                                    class="text-xs bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 px-2 py-1 rounded-lg transition-all duration-200">
                                                                🔄 Restaurar
                                                            </button>
                                                            <button type="button" @click="removePhoto()"
                                                                    class="text-xs bg-red-500/20 hover:bg-red-500/30 text-red-300 px-2 py-1 rounded-lg transition-all duration-200">
                                                                🗑️ Quitar
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div class="space-y-2" x-show="showPreview">
                                                        <div class="text-xs font-bold text-yellow-300 text-center">Foto cargada:</div>
                                                        <div class="relative bg-black/20 rounded-lg overflow-hidden border-2 border-yellow-400/30 cursor-pointer hover:border-yellow-300 transition-all duration-200"
                                                             @click="openModal()">
                                                            <img :src="previewUrl" alt="Miniatura" class="w-full h-16 object-cover">
                                                            <div class="absolute inset-0 bg-black/0 hover:bg-black/10 transition-all duration-200 flex items-center justify-center">
                                                                <div class="bg-black/60 text-white px-2 py-1 rounded text-xs opacity-0 hover:opacity-100 transition-opacity duration-200">
                                                                    👁️ Ver
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Modal para CC -->
                                                <div x-show="modalOpen"
                                                     x-transition:enter="transition ease-out duration-300"
                                                     x-transition:enter-start="opacity-0"
                                                     x-transition:enter-end="opacity-100"
                                                     x-transition:leave="transition ease-in duration-200"
                                                     x-transition:leave-start="opacity-100"
                                                     x-transition:leave-end="opacity-0"
                                                     @click.self="closeModal()"
                                                     @keydown.escape.window="closeModal()"
                                                     class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm">

                                                    <div class="h-full w-full overflow-y-auto flex items-start justify-center p-4">
                                                        <div class="w-full max-w-4xl my-8">
                                                            <div class="bg-white rounded-lg shadow-2xl overflow-hidden">
                                                                <div class="flex items-center justify-between p-4 bg-gray-100 border-b sticky top-0">
                                                                    <h3 class="text-lg font-semibold text-gray-800">
                                                                        📸 {{ $nombreBomba }} - CC
                                                                    </h3>
                                                                    <button type="button" @click="closeModal()"
                                                                            class="text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-full p-3 transition-all duration-200 text-xl font-bold">
                                                                        ✕
                                                                    </button>
                                                                </div>

                                                                <div class="p-4 bg-gray-50">
                                                                    <img :src="previewUrl" :alt="'Fotografía {{ $nombreBomba }}'"
                                                                         class="w-full h-auto object-contain rounded-lg shadow-lg">
                                                                </div>

                                                                <div class="flex items-center justify-center p-4 bg-gray-100 border-t sticky bottom-0">
                                                                    <button type="button" @click="closeModal()"
                                                                            class="bg-blue-500 hover:bg-blue-600 text-white px-8 py-3 rounded-lg text-lg font-bold transition-all duration-200">
                                                                        Cerrar
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Botón Guardar CC -->
                                            <div class="text-center">
                                                <button type="submit"
                                                        :disabled="processing"
                                                        class="bg-purple-500 hover:bg-purple-600 disabled:bg-gray-400 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg text-sm font-bold transition-all duration-200 transform hover:scale-105 w-full shadow-lg">
                                                    💾 Guardar {{ $nombreBomba }} - CC
                                                    <span x-show="processing" class="ml-2">🔄</span>
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                    <!-- TAREA 3: Bloque de Resumen con Totales -->
                    @if($bombas && count($bombas) > 0)
                        <div class="bg-gradient-to-br from-indigo-600/30 to-indigo-500/20 backdrop-blur-sm rounded-2xl p-6 border-2 border-indigo-400/50 shadow-2xl mb-4"
                             x-data="resumenTotales({{ json_encode($bombas) }})"
                             x-init="init()">
                            <h3 class="text-2xl font-bold mb-6 text-center flex items-center justify-center">
                                <span class="mr-3 text-3xl">📊</span>
                                Resumen de Totales
                            </h3>

                            <!-- Fila 1: Totales por Combustible -->
                            <div class="grid grid-cols-4 gap-2 mb-4">
                                <!-- Total Galones Super -->
                                <div class="bg-red-500/20 border-2 border-red-400/50 rounded-xl p-3">
                                    <div class="text-center">
                                        <h4 class="text-xs font-bold text-red-200 mb-1">
                                            <span class="block text-lg">🔴</span>
                                            Súper
                                        </h4>
                                        <div class="text-xs text-red-200/70 mb-1">Gal:</div>
                                        <div class="text-sm font-bold text-red-100 font-mono" x-text="totalGalonesSuper.toFixed(3)">0.000</div>
                                        <div class="text-xs text-red-200/70 mt-1">Venta:</div>
                                        <div class="text-sm font-bold text-red-100 font-mono">Q<span x-text="totalVentaSuper.toFixed(2)">0.00</span></div>
                                    </div>
                                </div>

                                <!-- Total Galones Regular -->
                                <div class="bg-orange-500/20 border-2 border-orange-400/50 rounded-xl p-3">
                                    <div class="text-center">
                                        <h4 class="text-xs font-bold text-orange-200 mb-1">
                                            <span class="block text-lg">🟡</span>
                                            Regular
                                        </h4>
                                        <div class="text-xs text-orange-200/70 mb-1">Gal:</div>
                                        <div class="text-sm font-bold text-orange-100 font-mono" x-text="totalGalonesRegular.toFixed(3)">0.000</div>
                                        <div class="text-xs text-orange-200/70 mt-1">Venta:</div>
                                        <div class="text-sm font-bold text-orange-100 font-mono">Q<span x-text="totalVentaRegular.toFixed(2)">0.00</span></div>
                                    </div>
                                </div>

                                <!-- Total Galones Diesel -->
                                <div class="bg-blue-500/20 border-2 border-blue-400/50 rounded-xl p-3">
                                    <div class="text-center">
                                        <h4 class="text-xs font-bold text-blue-200 mb-1">
                                            <span class="block text-lg">🔵</span>
                                            Diésel
                                        </h4>
                                        <div class="text-xs text-blue-200/70 mb-1">Gal:</div>
                                        <div class="text-sm font-bold text-blue-100 font-mono" x-text="totalGalonesDiesel.toFixed(3)">0.000</div>
                                        <div class="text-xs text-blue-200/70 mt-1">Venta:</div>
                                        <div class="text-sm font-bold text-blue-100 font-mono">Q<span x-text="totalVentaDiesel.toFixed(2)">0.00</span></div>
                                    </div>
                                </div>

                                <!-- Total CC -->
                                <div class="bg-purple-500/20 border-2 border-purple-400/50 rounded-xl p-3">
                                    <div class="text-center">
                                        <h4 class="text-xs font-bold text-purple-200 mb-1">
                                            <span class="block text-lg">🟣</span>
                                            CC Total
                                        </h4>
                                        <div class="text-xs text-purple-200/70 mb-1">Unid:</div>
                                        <div class="text-sm font-bold text-purple-100 font-mono" x-text="totalCC.toFixed(3)">0.000</div>
                                        <div class="text-xs text-purple-200/70 mt-1">-</div>
                                        <div class="text-sm font-bold text-purple-100/50">-</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Fila 2: Total Galones y Total Ventas -->
                            <div class="grid grid-cols-2 gap-2 mb-4">
                                <!-- Total Galones -->
                                <div class="bg-cyan-600/20 border-2 border-cyan-400/50 rounded-xl p-4">
                                    <h5 class="text-sm font-bold text-cyan-200 text-center mb-2">💧 Total Galones</h5>
                                    <div class="text-2xl font-extrabold text-cyan-100 text-center font-mono" x-text="totalGalones.toFixed(3)">0.000</div>
                                    <div class="text-xs text-cyan-200/70 text-center mt-1">Todos los combustibles</div>
                                </div>

                                <!-- Total Ventas -->
                                <div class="bg-green-600/20 border-2 border-green-400/50 rounded-xl p-4">
                                    <h5 class="text-sm font-bold text-green-200 text-center mb-2">💰 Total Ventas</h5>
                                    <div class="text-2xl font-extrabold text-green-100 text-center font-mono">Q<span x-text="totalVentas.toFixed(2)">0.00</span></div>
                                    <div class="text-xs text-green-200/70 text-center mt-1">Galones × Precio</div>
                                </div>
                            </div>

                            <!-- Fila 3: Diferencia -->
                            <div class="grid grid-cols-1 gap-2">
                                <!-- Diferencia -->
                                <div class="bg-yellow-600/20 border-2 border-yellow-400/50 rounded-xl p-4">
                                    <h5 class="text-sm font-bold text-yellow-200 text-center mb-2">📈 Diferencia</h5>
                                    <div class="text-3xl font-extrabold text-center font-mono"
                                         :class="diferencia >= 0 ? 'text-green-200' : 'text-red-300'">
                                        Q<span x-text="Math.abs(diferencia).toFixed(2)">0.00</span>
                                    </div>
                                    <div class="text-xs text-center mt-1"
                                         :class="diferencia >= 0 ? 'text-green-300' : 'text-red-300'">
                                        <span x-show="diferencia >= 0">✅ Ventas ≥ CC</span>
                                        <span x-show="diferencia < 0">⚠️ Ventas < CC</span>
                                    </div>
                                </div>
                            </div>

                            <p class="text-xs text-white/60 text-center mt-4">
                                ℹ️ Estos valores se calculan automáticamente en base a las lecturas ingresadas en tiempo real
                            </p>
                        </div>
                    @endif

                    <!-- Sección de Dinero Recibido -->
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20 mb-8">
                        <h3 class="text-3xl font-bold mb-6 text-center flex items-center justify-center">
                            <div class="w-6 h-6 bg-green-500 rounded-full mr-3"></div>
                            💰 Dinero Recibido
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

                            <!-- Total calculado en tiempo real -->
                            <div class="bg-green-500/20 border-2 border-green-400/50 rounded-xl p-4 mb-6" x-data="ventasCalculator()" x-init="init()">
                                <h4 class="text-lg font-bold text-green-300 mb-3 text-center flex items-center justify-center">
                                    🧮 Total Calculado de Ventas
                                </h4>
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-green-200 font-mono" x-text="'Q ' + totalVentas.toFixed(2)"></div>
                                    <p class="text-xs text-green-300/70 mt-2">
                                        (Crédito + Tarjetas + Efectivo - Descuentos)
                                    </p>
                                </div>
                            </div>

                            <!-- Total antes del botón -->
                            <div class="bg-gradient-to-r from-green-500/30 to-green-600/30 border-3 border-green-400/70 rounded-2xl p-6 mb-6 shadow-2xl">
                                <div class="text-center">
                                    <h4 class="text-xl font-bold text-green-200 mb-2 flex items-center justify-center">
                                        💰 TOTAL DINERO RECIBIDO
                                    </h4>
                                    <div class="text-4xl font-extrabold text-white font-mono mb-2" x-text="'Q ' + totalVentas.toFixed(2)"></div>
                                    <p class="text-sm text-green-200/80">
                                        Crédito + Tarjetas + Efectivo - Descuentos
                                    </p>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit"
                                        class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-8 py-4 rounded-xl text-lg font-bold transition-all duration-200 transform hover:scale-105 shadow-xl">
                                    💾 Guardar Dinero Recibido
                                </button>
                            </div>
                        </form>
                    </div>
            </div>
        @else
            <!-- Estado: Sin Turno Activo -->
            <div class="text-center py-8">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20 max-w-md mx-auto">
                    <div class="w-16 h-16 bg-yellow-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">No hay turno activo</h3>
                    <p class="text-white/70 text-sm mb-4">
                        Debes abrir un turno para comenzar a registrar las lecturas de las bombas.
                    </p>
                    <form method="POST" action="{{ route('turnos.abrir-turno') }}">
                        @csrf
                        <button type="submit" 
                                class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-xl text-sm font-bold transition-all duration-200 transform hover:scale-105 shadow-lg">
                            🔓 Abrir Turno
                        </button>
                    </form>
                </div>
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
  day: '2-digit',
  month: '2-digit',
  year: 'numeric'
});
                }
            }
        }

        function ventasCalculator() {
            return {
                totalVentas: 0,

                init() {
                    this.calcularTotal();
                    // Escuchar cambios en los inputs de ventas
                    const inputs = ['venta_credito', 'venta_tarjetas', 'venta_efectivo', 'venta_descuentos'];
                    inputs.forEach(inputName => {
                        const input = document.querySelector(`input[name="${inputName}"]`);
                        if (input) {
                            input.addEventListener('input', () => this.calcularTotal());
                        }
                    });
                },

                calcularTotal() {
                    const credito = parseFloat(document.querySelector('input[name="venta_credito"]')?.value) || 0;
                    const tarjetas = parseFloat(document.querySelector('input[name="venta_tarjetas"]')?.value) || 0;
                    const efectivo = parseFloat(document.querySelector('input[name="venta_efectivo"]')?.value) || 0;
                    const descuentos = parseFloat(document.querySelector('input[name="venta_descuentos"]')?.value) || 0;

                    this.totalVentas = credito + tarjetas + efectivo - descuentos;
                }
            }
        }

        function resumenTotales(bombas) {
            return {
                bombas: bombas,
                totalGalonesSuper: 0,
                totalGalonesRegular: 0,
                totalGalonesDiesel: 0,
                totalCC: 0,
                totalVentaSuper: 0,
                totalVentaRegular: 0,
                totalVentaDiesel: 0,
                totalGalones: 0,
                totalVentas: 0,
                diferencia: 0,

                init() {
                    console.log('resumenTotales init con bombas:', this.bombas);
                    this.calcularTotales();

                    // Escuchar eventos de actualización de lecturas
                    window.addEventListener('lectura-actualizada', () => {
                        console.log('Evento lectura-actualizada recibido');
                        this.calcularTotales();
                    });

                    // Polling para actualizar cada segundo (backup por si no se disparan eventos)
                    setInterval(() => {
                        this.calcularTotales();
                    }, 1000);
                },

                calcularTotales() {
                    this.totalGalonesSuper = 0;
                    this.totalGalonesRegular = 0;
                    this.totalGalonesDiesel = 0;
                    this.totalCC = 0;
                    this.totalVentaSuper = 0;
                    this.totalVentaRegular = 0;
                    this.totalVentaDiesel = 0;

                    // Iterar sobre todas las bombas
                    Object.keys(this.bombas).forEach(nombreBomba => {
                        const bomba = this.bombas[nombreBomba];

                        // Super
                        if (bomba.combustibles && bomba.combustibles.Super) {
                            const inputSuper = document.querySelector(`input[name="lectura_${bomba.id}_super"]`);
                            if (inputSuper && inputSuper.value) {
                                const lecturaAnterior = parseFloat(bomba.combustibles.Super.lectura_actual) || 0;
                                const lecturaNueva = parseFloat(inputSuper.value) || 0;
                                const galones = lecturaNueva > lecturaAnterior ? lecturaNueva - lecturaAnterior : 0;
                                const precio = parseFloat(bomba.combustibles.Super.precio) || 0;

                                this.totalGalonesSuper += galones;
                                this.totalVentaSuper += galones * precio;
                            }
                        }

                        // Regular
                        if (bomba.combustibles && bomba.combustibles.Regular) {
                            const inputRegular = document.querySelector(`input[name="lectura_${bomba.id}_regular"]`);
                            if (inputRegular && inputRegular.value) {
                                const lecturaAnterior = parseFloat(bomba.combustibles.Regular.lectura_actual) || 0;
                                const lecturaNueva = parseFloat(inputRegular.value) || 0;
                                const galones = lecturaNueva > lecturaAnterior ? lecturaNueva - lecturaAnterior : 0;
                                const precio = parseFloat(bomba.combustibles.Regular.precio) || 0;

                                this.totalGalonesRegular += galones;
                                this.totalVentaRegular += galones * precio;
                            }
                        }

                        // Diesel
                        if (bomba.combustibles && bomba.combustibles.Diesel) {
                            const inputDiesel = document.querySelector(`input[name="lectura_${bomba.id}_diesel"]`);
                            if (inputDiesel && inputDiesel.value) {
                                const lecturaAnterior = parseFloat(bomba.combustibles.Diesel.lectura_actual) || 0;
                                const lecturaNueva = parseFloat(inputDiesel.value) || 0;
                                const galones = lecturaNueva > lecturaAnterior ? lecturaNueva - lecturaAnterior : 0;
                                const precio = parseFloat(bomba.combustibles.Diesel.precio) || 0;

                                this.totalGalonesDiesel += galones;
                                this.totalVentaDiesel += galones * precio;
                            }
                        }

                        // CC
                        if (bomba.combustibles && bomba.combustibles.CC) {
                            const inputCC = document.querySelector(`input[name="lectura_${bomba.id}_cc"]`);
                            if (inputCC && inputCC.value) {
                                const lecturaAnterior = parseFloat(bomba.combustibles.CC.lectura_actual) || 0;
                                const lecturaNueva = parseFloat(inputCC.value) || 0;
                                const unidades = lecturaNueva > lecturaAnterior ? lecturaNueva - lecturaAnterior : 0;

                                this.totalCC += unidades;
                            }
                        }
                    });

                    // Calcular totales generales
                    this.totalGalones = this.totalGalonesSuper + this.totalGalonesRegular + this.totalGalonesDiesel;
                    this.totalVentas = this.totalVentaSuper + this.totalVentaRegular + this.totalVentaDiesel;

                    // Calcular diferencia (Ventas - CC)
                    // Nota: Asumimos que CC representa el total de ventas en efectivo/conteo
                    this.diferencia = this.totalVentas - this.totalCC;
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
                processing: false,
                
                initFotografia(fotoUrl, tieneFoto) {
                    console.log('initFotografia called:', { fotoUrl, tieneFoto });
                    this.previewUrl = fotoUrl || '';
                    this.originalUrl = fotoUrl || '';
                    this.showPreview = tieneFoto;
                    console.log('After init:', { previewUrl: this.previewUrl, originalUrl: this.originalUrl, showPreview: this.showPreview });
                },
                
                handleFileChange(event) {
                    const file = event.target.files[0];
                    console.log('=== FILE CHANGE DEBUG ===');
                    console.log('Event:', event);
                    console.log('Target:', event.target);
                    console.log('Files length:', event.target.files.length);
                    console.log('File:', file);
                    
                    if (file) {
                        console.log('Archivo original:');
                        console.log('- Nombre:', file.name);
                        console.log('- Tamaño:', file.size, 'bytes (', Math.round(file.size / 1024 / 1024 * 100) / 100, 'MB)');
                        console.log('- Tipo:', file.type);
                        console.log('- Última modificación:', file.lastModified);
                        
                        // Validaciones básicas en frontend
                        if (file.size === 0) {
                            alert('El archivo está vacío. Por favor seleccione una fotografía válida.');
                            event.target.value = '';
                            return;
                        }
                        
                        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                        if (!allowedTypes.includes(file.type)) {
                            alert('Tipo de archivo no válido. Use: JPG, PNG, GIF o WebP');
                            event.target.value = '';
                            return;
                        }
                        
                        // Comprimir la imagen antes de procesar
                        this.processing = true;
                        this.compressImage(file, (compressedFile) => {
                            console.log('Archivo comprimido:');
                            console.log('- Tamaño:', compressedFile.size, 'bytes (', Math.round(compressedFile.size / 1024 / 1024 * 100) / 100, 'MB)');
                            console.log('- Reducción:', Math.round((1 - compressedFile.size / file.size) * 100), '%');
                            
                            // Actualizar el input con el archivo comprimido
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(compressedFile);
                            event.target.files = dataTransfer.files;
                            
                            // Crear preview
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.previewUrl = e.target.result;
                                this.showPreview = true;
                                this.processing = false;
                                console.log('Preview creado exitosamente');
                            };
                            reader.onerror = (e) => {
                                console.error('Error en FileReader:', e);
                                this.processing = false;
                            };
                            reader.readAsDataURL(compressedFile);
                        });
                    } else {
                        console.log('No se seleccionó archivo');
                    }
                },
                
                resetPhoto() {
                    this.previewUrl = this.originalUrl;
                    this.showPreview = this.originalUrl !== '';
                    // Buscar el input dentro del contexto actual
                    const input = this.$el.querySelector('input[name="fotografia_bomba"]');
                    if (input) {
                        input.value = '';
                        console.log('Reset photo');
                    }
                },
                
                removePhoto() {
                    this.previewUrl = '';
                    this.showPreview = false;
                    this.modalOpen = false;
                    // Buscar el input dentro del contexto actual
                    const input = this.$el.querySelector('input[name="fotografia_bomba"]');
                    if (input) {
                        input.value = '';
                        console.log('Removed photo');
                    }
                },
                
                openModal() {
                    console.log('openModal called, previewUrl:', this.previewUrl);
                    this.modalOpen = true;
                },
                
                closeModal() {
                    console.log('closeModal called');
                    this.modalOpen = false;
                },
                
                compressImage(file, callback) {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    const img = new Image();
                    
                    img.onload = () => {
                        // Calcular nuevas dimensiones manteniendo la proporción
                        const maxWidth = 1280;
                        const maxHeight = 1280;
                        let { width, height } = img;
                        
                        console.log('Dimensiones originales:', width, 'x', height);
                        
                        // Solo redimensionar si la imagen es más grande que el máximo
                        if (width > maxWidth || height > maxHeight) {
                            // Calcular la escala para mantener la proporción
                            if (width > height) {
                                if (width > maxWidth) {
                                    height = height * (maxWidth / width);
                                    width = maxWidth;
                                }
                            } else {
                                if (height > maxHeight) {
                                    width = width * (maxHeight / height);
                                    height = maxHeight;
                                }
                            }
                        }
                        
                        console.log('Nuevas dimensiones:', Math.round(width), 'x', Math.round(height));
                        
                        // Configurar canvas con dimensiones enteras
                        canvas.width = Math.round(width);
                        canvas.height = Math.round(height);
                        
                        // Dibujar imagen redimensionada con alta calidad para preservar texto
                        ctx.imageSmoothingEnabled = true;
                        ctx.imageSmoothingQuality = 'high';
                        ctx.drawImage(img, 0, 0, Math.round(width), Math.round(height));
                        
                        // Convertir a Blob con compresión optimizada para texto
                        canvas.toBlob((blob) => {
                            // Crear nuevo archivo con el blob comprimido
                            const compressedFile = new File([blob], file.name.replace(/\.[^/.]+$/, '.jpg'), {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            });
                            
                            callback(compressedFile);
                        }, 'image/jpeg', 0.90); // 90% de calidad para preservar texto legible
                    };
                    
                    img.onerror = () => {
                        console.error('Error al cargar la imagen para compresión');
                        this.processing = false;
                        alert('Error al procesar la imagen. Intente con otra fotografía.');
                    };
                    
                    // Cargar la imagen
                    img.src = URL.createObjectURL(file);
                }
            }
        }
    </script>

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('SW registered: ', registration);
                        
                        // Verificar actualizaciones
                        registration.addEventListener('updatefound', () => {
                            const newWorker = registration.installing;
                            newWorker.addEventListener('statechange', () => {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    // Nueva versión disponible
                                    if (confirm('Nueva versión disponible. ¿Recargar para actualizar?')) {
                                        newWorker.postMessage({ type: 'SKIP_WAITING' });
                                        window.location.reload();
                                    }
                                }
                            });
                        });
                    })
                    .catch(error => {
                        console.log('SW registration failed: ', error);
                    });
            });
        }
        
        // Forzar fullscreen en PWA
        function enableFullscreen() {
            // Detectar si está en modo PWA
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches || 
                                window.navigator.standalone || 
                                document.referrer.includes('android-app://');
            
            if (isStandalone) {
                // Ocultar elementos de navegación del navegador si los hay
                document.body.style.overflow = 'hidden';
                document.documentElement.style.overflow = 'hidden';
                
                // Intentar fullscreen API si está disponible
                if (document.documentElement.requestFullscreen) {
                    document.addEventListener('click', function() {
                        if (!document.fullscreenElement) {
                            document.documentElement.requestFullscreen().catch(err => {
                                console.log('Fullscreen no disponible:', err);
                            });
                        }
                    }, { once: true });
                }
            }
        }
        
        // Ejecutar cuando la página cargue
        document.addEventListener('DOMContentLoaded', enableFullscreen);
        window.addEventListener('load', enableFullscreen);
    </script>
</body>
</html>
