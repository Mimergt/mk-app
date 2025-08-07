<div x-data="{ activeTab: 'backups' }">
    <x-filament-panels::page>
        <!-- Tabs Header -->
        <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button @click="activeTab = 'backups'"
                        :class="{'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'backups', 
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'backups'}"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Backups y Respaldos
                    </div>
                </button>
                <button @click="activeTab = 'sistema'"
                        :class="{'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'sistema', 
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'sistema'}"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Información del Sistema
                    </div>
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="space-y-6">
            <!-- Tab: Backups y Respaldos -->
            <div x-show="activeTab === 'backups'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="space-y-6">
                    <!-- Backup de Base de Datos -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="sm:flex sm:items-start sm:justify-between">
                                <div>
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                        Generar Respaldo
                                    </h3>
                                    <div class="mt-2 max-w-xl text-sm text-gray-500 dark:text-gray-400">
                                        <p>Genera y descarga un respaldo completo de toda la base de datos del sistema.</p>
                                    </div>
                                    <div class="mt-3">
                                        <div class="rounded-md bg-blue-50 dark:bg-blue-900/20 p-4">
                                            <div class="flex">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                                        Incluye: Gasolineras, bombas, usuarios, turnos, historial y configuraciones.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-5 sm:mt-0 sm:ml-6 sm:flex-shrink-0 sm:flex sm:items-center">
                                    <button wire:click="generarBackup" 
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Descargar Backup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Restaurar Backup Manual -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="sm:flex sm:items-start sm:justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                        Subir y Restaurar Backup Manual
                                    </h3>
                                    <div class="mt-2 max-w-xl text-sm text-gray-500 dark:text-gray-400">
                                        <p>Sube un archivo .sql de backup que hayas descargado previamente para restaurar.</p>
                                    </div>
                                    <div class="mt-3">
                                        <div class="rounded-md bg-purple-50 dark:bg-purple-900/20 p-4">
                                            <div class="flex">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-5 w-5 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm text-purple-700 dark:text-purple-300">
                                                        <strong>Solo archivos .sql:</strong> Sube backups válidos generados por este sistema o mysqldump.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Seleccionar archivo de backup (.sql)
                                        </label>
                                        <input type="file" 
                                               wire:model="archivoBackup" 
                                               accept=".sql"
                                               class="block w-full text-sm text-gray-500 dark:text-gray-400
                                                      file:mr-4 file:py-2 file:px-4
                                                      file:rounded-full file:border-0
                                                      file:text-sm file:font-semibold
                                                      file:bg-purple-50 file:text-purple-700
                                                      hover:file:bg-purple-100
                                                      dark:file:bg-purple-900/20 dark:file:text-purple-300">
                                        @if($archivoBackup)
                                            <p class="mt-2 text-sm text-green-600 dark:text-green-400">
                                                ✓ Archivo seleccionado: {{ $archivoBackup->getClientOriginalName() }}
                                            </p>
                                        @endif
                                        @error('archivoBackup')
                                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mt-5 sm:mt-0 sm:ml-6 sm:flex-shrink-0 sm:flex sm:items-center">
                                    <button wire:click="restaurarBackupManual" 
                                            @if(!$archivoBackup) disabled @endif
                                            onclick="return confirm('¿Estás COMPLETAMENTE SEGURO de que deseas restaurar desde este archivo? Esto SOBRESCRIBIRÁ todos los datos actuales. Esta acción NO se puede deshacer.')"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white 
                                                   @if($archivoBackup) bg-purple-600 hover:bg-purple-700 focus:ring-purple-500 @else bg-gray-400 cursor-not-allowed @endif
                                                   focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                        </svg>
                                        Subir y Restaurar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Restaurar Backup Automático -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="sm:flex sm:items-start sm:justify-between">
                                <div>
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                        Restaurar desde Backup Automático
                                    </h3>
                                    <div class="mt-2 max-w-xl text-sm text-gray-500 dark:text-gray-400">
                                        <p>Restaura la base de datos desde el backup más reciente disponible en el servidor.</p>
                                    </div>
                                    <div class="mt-3">
                                        <div class="rounded-md bg-green-50 dark:bg-green-900/20 p-4">
                                            <div class="flex">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm text-green-700 dark:text-green-300">
                                                        <strong>⚠️ CUIDADO:</strong> Esto sobrescribirá TODOS los datos actuales con el backup.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-5 sm:mt-0 sm:ml-6 sm:flex-shrink-0 sm:flex sm:items-center">
                                    <button wire:click="restaurarBackup" 
                                            onclick="return confirm('¿Estás COMPLETAMENTE SEGURO de que deseas restaurar desde backup? Esto SOBRESCRIBIRÁ todos los datos actuales. Esta acción NO se puede deshacer.')"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                        </svg>
                                        Restaurar Backup Automático
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Información del Sistema -->
            <div x-show="activeTab === 'sistema'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="space-y-6">
                    <!-- Limpieza Segura -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="sm:flex sm:items-start sm:justify-between">
                                <div>
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                        Limpieza Segura del Sistema
                                    </h3>
                                    <div class="mt-2 max-w-xl text-sm text-gray-500 dark:text-gray-400">
                                        <p>Elimina todos los datos operacionales y resetea el sistema a estado inicial.</p>
                                    </div>
                                    <div class="mt-3">
                                        <div class="rounded-md bg-amber-50 dark:bg-amber-900/20 p-4">
                                            <div class="flex">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm text-amber-700 dark:text-amber-300">
                                                        <strong>Se eliminará:</strong> Turnos, historial de bombas, galonajes (reseteo a 0)<br>
                                                        <strong>Se conserva:</strong> Gasolineras, bombas, usuarios, precios
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-5 sm:mt-0 sm:ml-6 sm:flex-shrink-0 sm:flex sm:items-center">
                                    <button wire:click="limpiezaSegura" 
                                            onclick="return confirm('¿Estás COMPLETAMENTE SEGURO de que deseas limpiar todos los datos operacionales? Esta acción NO se puede deshacer.')"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H9a1 1 0 00-1 1v1M4 7h16"></path>
                                        </svg>
                                        Limpiar Sistema
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información del Sistema -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                                Información del Sistema
                            </h3>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Versión Laravel</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ app()->version() }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">PHP</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ PHP_VERSION }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Base de Datos</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ config('database.default') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Zona Horaria</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ config('app.timezone') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-filament-panels::page>
</div>
