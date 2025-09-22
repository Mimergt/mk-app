<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Turnos - Ingreso</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        
        /* Estilos personalizados para tablet */
        .btn-keyboard {
            @apply bg-blue-500 hover:bg-blue-600 active:bg-blue-700 text-white font-bold text-2xl rounded-xl shadow-lg transition-all duration-100 transform active:scale-95;
            min-height: 4rem;
            min-width: 4rem;
        }
        
        .btn-keyboard-special {
            @apply bg-gray-500 hover:bg-gray-600 active:bg-gray-700;
        }
        
        .gasolinera-card {
            @apply bg-gray-800 hover:bg-gray-700 border-2 border-gray-600 hover:border-blue-400 rounded-xl p-4 cursor-pointer transition-all duration-200 transform hover:scale-102 shadow-md hover:shadow-lg text-white;
        }
        
        .gasolinera-card.selected {
            @apply border-blue-500 bg-gray-700;
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 min-h-screen">
    
    <div x-data="{
        paso: 1,
        gasolineraSeleccionada: null,
        operadores: [],
        usuarioSeleccionado: null,
        password: '',
        cargandoOperadores: false,
        
        async seleccionarGasolinera(gasolinera) {
            this.gasolineraSeleccionada = gasolinera;
            this.cargandoOperadores = true;
            
            try {
                const response = await fetch('{{ route('turnos.operadores') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        gasolinera_id: gasolinera.id
                    })
                });
                
                this.operadores = await response.json();
                this.paso = 2;
            } catch (error) {
                console.error('Error:', error);
                alert('Error al cargar operadores');
            } finally {
                this.cargandoOperadores = false;
            }
        },
        
        seleccionarOperador(operador) {
            this.usuarioSeleccionado = operador;
            this.paso = 3;
        },
        
        agregarDigito(digito) {
            this.password += digito;
        },
        
        limpiarPassword() {
            this.password = this.password.slice(0, -1);
        },
        
        limpiarTodo() {
            this.password = '';
        },
        
        volver() {
            if (this.paso === 3) {
                this.paso = 2;
                this.password = '';
            } else if (this.paso === 2) {
                this.paso = 1;
                this.gasolineraSeleccionada = null;
                this.operadores = [];
                this.usuarioSeleccionado = null;
            }
        }
    }" class="min-h-screen flex items-center justify-center p-4">
        
        <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-4xl">
            <!-- Header -->
            <div class="text-center mb-6">
                <div class="flex items-center justify-center space-x-4 mb-4">
                    
                    <h1 class="text-2xl font-bold text-gray-800">Gasolineras - Monte Karlo</h1>
                </div>
                
                <!-- Indicador de pasos -->
                <div class="flex justify-center space-x-4 mb-4">
                    <div class="flex items-center space-x-2" :class="paso >= 1 ? 'text-blue-600' : 'text-gray-400'">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-sm" 
                             :class="paso >= 1 ? 'bg-blue-500 text-white' : 'bg-gray-300'">1</div>
                        <span class="text-sm font-medium">Gasolinera</span>
                    </div>
                    <div class="w-6 border-t-2 mt-3" :class="paso >= 2 ? 'border-blue-500' : 'border-gray-300'"></div>
                    <div class="flex items-center space-x-2" :class="paso >= 2 ? 'text-blue-600' : 'text-gray-400'">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-sm" 
                             :class="paso >= 2 ? 'bg-blue-500 text-white' : 'bg-gray-300'">2</div>
                        <span class="text-sm font-medium">Operador</span>
                    </div>
                    <div class="w-6 border-t-2 mt-3" :class="paso >= 3 ? 'border-blue-500' : 'border-gray-300'"></div>
                    <div class="flex items-center space-x-2" :class="paso >= 3 ? 'text-blue-600' : 'text-gray-400'">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-sm" 
                             :class="paso >= 3 ? 'bg-blue-500 text-white' : 'bg-gray-300'">3</div>
                        <span class="text-sm font-medium">Contraseña</span>
                    </div>
                </div>
            </div>

            <!-- Errores -->
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                    @foreach($errors->all() as $error)
                        <p class="text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Paso 1: Seleccionar Gasolinera -->
            <div x-show="paso === 1" class="fade-in">
                <h2 class="text-xl font-bold text-gray-800 mb-4 text-center">Selecciona tu Gasolinera</h2>
                
                <div class="flex justify-center">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 max-w-2xl w-full">
                        @foreach($gasolineras as $gasolinera)
                            <div class="gasolinera-card px-5 gasolinera-card px-5 bg-black border-2 border-black-200 hover:border-blue-400 rounded-lg p-4 cursor-pointer transition-all duration-200" @click="seleccionarGasolinera({{ $gasolinera }})">
                                <div class="text-center">
                                    
                                    <h3 class="text-lg font-bold text-white mb-1">{{ $gasolinera->nombre }}</h3>
                                    <p class="text-sm text-gray-300">{{ $gasolinera->ubicacion }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Paso 2: Seleccionar Operador -->
            <div x-show="paso === 2" class="fade-in">
                <div class="flex items-center justify-between mb-4">
                    <button @click="volver()" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg text-sm">
                        ← Volver
                    </button>
                    <h2 class="text-xl font-bold text-gray-800">
                        Operadores - <span x-text="gasolineraSeleccionada?.nombre" class="text-blue-600"></span>
                    </h2>
                    <div></div>
                </div>
                
                <div x-show="cargandoOperadores" class="text-center py-6">
                    <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                    <p class="mt-2 text-gray-600 text-sm">Cargando operadores...</p>
                </div>
                
                <div x-show="!cargandoOperadores" class="flex justify-center">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 max-w-2xl w-full">
                        <template x-for="operador in operadores" :key="operador.id">
                            <div class="gasolinera-card bg-black border-2 border-black-200 hover:border-blue-400 rounded-lg p-4 cursor-pointer transition-all duration-200" @click="seleccionarOperador(operador)">
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-white" x-text="operador.name"></h3>
                                    <p class="text-sm text-gray-600">Operador</p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Paso 3: Contraseña -->
            <div x-show="paso === 3" class="fade-in">
                <div class="flex items-center justify-between mb-4">
                    <button @click="volver()" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg text-sm">
                        ← Volver
                    </button>
                    <h2 class="text-xl font-bold text-gray-800">
                        Contraseña para <span x-text="usuarioSeleccionado?.name" class="text-blue-600"></span>
                    </h2>
                    <div></div>
                </div>

                <form method="POST" action="{{ route('turnos.procesar') }}" class="max-w-sm mx-auto">
                    @csrf
                    <input type="hidden" name="usuario_id" :value="usuarioSeleccionado?.id">
                    
                    <!-- Campo de Contraseña -->
                    <div class="mb-4">
                        <label class="block text-base font-semibold text-gray-700 mb-2">Contraseña:</label>
                        <input type="password" name="password" x-model="password" required
                               class="w-full p-3 text-xl font-mono border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center"
                               placeholder="••••••••" readonly>
                    </div>

                    <!-- Teclado Numérico -->
                    <div class="bg-gray-50 rounded-lg p-3 mb-4">
                        <div class="grid grid-cols-3 gap-2">
                            <!-- Números 1-9 -->
                            <template x-for="i in [1,2,3,4,5,6,7,8,9]" :key="i">
                                <button type="button" @click="agregarDigito(i.toString())" 
                                        class="bg-blue-500 hover:bg-blue-600 active:bg-blue-700 text-white font-bold text-lg rounded-lg shadow-md transition-all duration-100 transform active:scale-95 h-12 w-full" x-text="i"></button>
                            </template>
                            
                            <!-- Fila inferior -->
                            <button type="button" @click="limpiarPassword()" 
                                    class="bg-gray-500 hover:bg-gray-600 active:bg-gray-700 text-white font-bold text-lg rounded-lg shadow-md transition-all duration-100 transform active:scale-95 h-12 w-full">⌫</button>
                            <button type="button" @click="agregarDigito('0')" 
                                    class="bg-blue-500 hover:bg-blue-600 active:bg-blue-700 text-white font-bold text-lg rounded-lg shadow-md transition-all duration-100 transform active:scale-95 h-12 w-full">0</button>
                            <button type="button" @click="limpiarTodo()" 
                                    class="bg-gray-500 hover:bg-gray-600 active:bg-gray-700 text-white font-bold text-lg rounded-lg shadow-md transition-all duration-100 transform active:scale-95 h-12 w-full">C</button>
                        </div>
                    </div>

                    <!-- Botón de Conectar -->
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-3 px-6 rounded-lg text-lg transition-all duration-200 transform hover:scale-105 shadow-lg">
                        🚀 Conectar
                    </button>
                </form>
            </div>

         
        </div>
    </div>
</body>
</html>
