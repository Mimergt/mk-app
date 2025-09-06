<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Simular autenticación
$user = \App\Models\User::find(2);
auth()->login($user);

echo "=== DEBUG PANEL FOTOGRAFÍAS ===\n";
echo "Usuario autenticado: " . auth()->user()->id . "\n";
echo "Gasolinera: " . auth()->user()->gasolinera_id . "\n";

// Reproducir la lógica del método panel()
$turnoActual = \App\Models\Turno::where('user_id', auth()->id())
                               ->where('gasolinera_id', auth()->user()->gasolinera_id)
                               ->where('estado', 'abierto')
                               ->latest()
                               ->first();

echo "Turno activo: " . ($turnoActual ? $turnoActual->id : 'NULL') . "\n";

$fotografiasPorBomba = [];

if ($turnoActual) {
    echo "Buscando fotografías para turno: " . $turnoActual->id . "\n";
    
    $fotografiasExistentes = \App\Models\TurnoBombaDatos::where('turno_id', $turnoActual->id)
                                                       ->whereNotNull('fotografia')
                                                       ->get()
                                                       ->keyBy('bomba_id');
    
    echo "Fotografías encontradas: " . $fotografiasExistentes->count() . "\n";
    
    foreach ($fotografiasExistentes as $bombaId => $datos) {
        $fotografiasPorBomba[$bombaId] = $datos->fotografia_url;
        echo "  Bomba $bombaId: " . $datos->fotografia_url . "\n";
    }
}

// Cargar bombas
$bombas = [];
if (auth()->user()->gasolinera_id) {
    $bombasQuery = \App\Models\Bomba::where('gasolinera_id', auth()->user()->gasolinera_id)
                                   ->orderBy('nombre')
                                   ->get();
    
    echo "\nBombas encontradas: " . $bombasQuery->count() . "\n";
    
    foreach ($bombasQuery as $bomba) {
        $gasolinera = $bomba->gasolinera;
        $fotografiaUrl = $fotografiasPorBomba[$bomba->id] ?? null;
        
        echo "Procesando bomba {$bomba->nombre} (ID: {$bomba->id})\n";
        echo "  Fotografía URL asignada: " . ($fotografiaUrl ?? 'NULL') . "\n";
        
        $bombas[$bomba->nombre] = [
            'id' => $bomba->id,
            'estado' => $bomba->estado,
            'fotografia_url' => $fotografiaUrl,
            // ... resto de datos
        ];
    }
}

echo "\n=== RESUMEN ===\n";
echo "Array \$fotografiasPorBomba:\n";
var_export($fotografiasPorBomba);
echo "\n\nDatos fotografia_url en bombas:\n";
foreach ($bombas as $nombre => $data) {
    echo "$nombre: " . ($data['fotografia_url'] ?? 'NULL') . "\n";
}
