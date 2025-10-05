<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Livewire\WithFileUploads;

class Configuraciones extends Page
{
    use WithFileUploads;
    
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    
    protected static ?string $navigationLabel = 'Configuraciones';
    
    protected static ?string $title = 'Configuraciones del Sistema';
    
    protected static ?string $navigationGroup = 'Configuraciones';
    
    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.pages.configuraciones';

    public $archivoBackup;

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function generarBackup()
    {
        try {
            $filename = 'backup_gasolinera_' . date('Y-m-d_H-i-s') . '.sql';
            
            // Obtener configuración de base de datos
            $host = config('database.connections.mysql.host');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            
            // Crear directorio si no existe
            $backupDir = storage_path('app/backups');
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
                chown($backupDir, 'www-data');
                chgrp($backupDir, 'www-data');
            }
            
            $backupPath = $backupDir . '/' . $filename;
            
            // Comando mysqldump mejorado
            $command = sprintf(
                'mysqldump -h%s -u%s --password=%s --single-transaction --routines --triggers %s',
                escapeshellarg($host),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database)
            );
            
            // Ejecutar backup y capturar salida directamente
            $output = shell_exec($command . ' 2>&1');
            
            if ($output && strlen($output) > 100) {
                // Escribir el contenido al archivo
                file_put_contents($backupPath, $output);
                
                // Verificar que se escribió correctamente
                if (file_exists($backupPath) && filesize($backupPath) > 100) {
                    return response()->download($backupPath)
                        ->deleteFileAfterSend(true);
                } else {
                    throw new \Exception('No se pudo escribir el archivo de backup');
                }
            } else {
                throw new \Exception('El comando mysqldump no produjo salida válida: ' . ($output ?: 'Sin output'));
            }
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al generar backup')
                ->body('Error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function restaurarBackup()
    {
        try {
            // Buscar archivos de backup
            $backupDir = storage_path('app/backups');
            $backupFiles = [];
            
            if (file_exists($backupDir)) {
                $files = glob($backupDir . '/backup_gasolinera_*.sql');
                foreach ($files as $file) {
                    if (is_file($file) && filesize($file) > 100) {
                        $backupFiles[] = [
                            'name' => basename($file),
                            'path' => $file,
                            'date' => date('Y-m-d H:i:s', filemtime($file)),
                            'size' => number_format(filesize($file) / 1024, 2) . ' KB'
                        ];
                    }
                }
                
                // Ordenar por fecha más reciente
                usort($backupFiles, function($a, $b) {
                    return filemtime($b['path']) - filemtime($a['path']);
                });
            }

            if (empty($backupFiles)) {
                Notification::make()
                    ->title('Sin backups')
                    ->body('No hay archivos de backup disponibles para restaurar')
                    ->warning()
                    ->send();
                return;
            }

            // Usar el backup más reciente
            $latestBackup = $backupFiles[0];
            
            // Obtener configuración de base de datos
            $host = config('database.connections.mysql.host');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            
            // Comando para restaurar - usando mysql client
            $command = sprintf(
                'mysql -h%s -u%s --password=%s %s',
                escapeshellarg($host),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database)
            );
            
            // Ejecutar restauración usando pipe
            $fullCommand = sprintf('%s < %s 2>&1', $command, escapeshellarg($latestBackup['path']));
            $output = shell_exec($fullCommand);
            
            // Verificar si hubo errores críticos
            if ($output && (strpos(strtolower($output), 'error') !== false || strpos(strtolower($output), 'failed') !== false)) {
                throw new \Exception('Error en restauración: ' . $output);
            }
            
            Notification::make()
                ->title('✅ Restauración exitosa')
                ->body('Base de datos restaurada desde: ' . $latestBackup['name'] . ' (' . $latestBackup['date'] . ')')
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('❌ Error en restauración')
                ->body('Error al restaurar backup: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function restaurarBackupManual()
    {
        try {
            // Validar que se haya subido un archivo
            if (!$this->archivoBackup) {
                Notification::make()
                    ->title('Archivo requerido')
                    ->body('Debes seleccionar un archivo de backup para restaurar')
                    ->warning()
                    ->send();
                return;
            }

            // Validar extensión del archivo
            $extension = pathinfo($this->archivoBackup->getClientOriginalName(), PATHINFO_EXTENSION);
            if (strtolower($extension) !== 'sql') {
                Notification::make()
                    ->title('Archivo inválido')
                    ->body('Solo se permiten archivos .sql')
                    ->danger()
                    ->send();
                return;
            }

            // Crear directorio si no existe
            $backupDir = storage_path('app/backups');
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            // Guardar archivo temporalmente
            $filename = 'manual_backup_' . date('Y-m-d_H-i-s') . '.sql';
            $backupPath = $backupDir . '/' . $filename;
            
            // Mover archivo subido
            $this->archivoBackup->storeAs('backups', $filename);
            
            // Verificar que el archivo se guardó correctamente
            if (!file_exists($backupPath) || filesize($backupPath) < 100) {
                throw new \Exception('El archivo subido no es válido o está vacío');
            }

            // Obtener configuración de base de datos
            $host = config('database.connections.mysql.host');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            
            // Comando para restaurar
            $command = sprintf(
                'mysql -h%s -u%s --password=%s %s',
                escapeshellarg($host),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database)
            );
            
            // Ejecutar restauración
            $fullCommand = sprintf('%s < %s 2>&1', $command, escapeshellarg($backupPath));
            $output = shell_exec($fullCommand);
            
            // Verificar si hubo errores críticos
            if ($output && (strpos(strtolower($output), 'error') !== false || strpos(strtolower($output), 'failed') !== false)) {
                throw new \Exception('Error en restauración: ' . $output);
            }
            
            // Limpiar archivo temporal después de usar
            if (file_exists($backupPath)) {
                unlink($backupPath);
            }
            
            // Reset del campo de archivo
            $originalName = $this->archivoBackup->getClientOriginalName();
            $this->archivoBackup = null;
            
            Notification::make()
                ->title('✅ Restauración manual exitosa')
                ->body('Base de datos restaurada desde el archivo subido: ' . $originalName)
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('❌ Error en restauración manual')
                ->body('Error al restaurar backup: ' . $e->getMessage())
                ->danger()
                ->send();
            
            // Limpiar archivo en caso de error
            $this->archivoBackup = null;
        }
    }
    
    public function limpiezaSegura()
    {
        try {
            DB::transaction(function () {
                // Eliminar datos pero conservar estructura
                DB::table('historial_bombas')->delete();
                DB::table('turnos')->delete();
                
                // Resetear galonajes de bombas a 0
                DB::table('bombas')->update([
                    'galonaje' => 0.00,
                    'updated_at' => now()
                ]);
                
                // Mantener usuarios y gasolineras, pero resetear datos de operación
                DB::table('users')->whereIn('tipo_usuario', ['operador'])->update([
                    'remember_token' => null,
                    'updated_at' => now()
                ]);
            });
            
            Notification::make()
                ->title('Limpieza completada')
                ->body('Todos los datos operacionales han sido limpiados. Gasolineras, bombas y usuarios se mantienen.')
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error en limpieza')
                ->body('Error al realizar limpieza: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
