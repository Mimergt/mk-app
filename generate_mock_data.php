<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Cargar configuración de Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🚀 Generando datos ficticios para los últimos 3 meses...\n\n";

// Configuración inicial
$fechaInicio = Carbon::create(2025, 5, 1); // Mayo 2025
$fechaFin = Carbon::create(2025, 7, 31);   // Julio 2025
$fechaActual = $fechaInicio->copy();

// Obtener datos base
$gasolineras = DB::table('gasolineras')->get();
$usuarios = DB::table('users')->whereNotNull('gasolinera_id')->get();
$bombas = DB::table('bombas')->get();

echo "📊 Datos base encontrados:\n";
echo "- Gasolineras: " . $gasolineras->count() . "\n";
echo "- Operadores: " . $usuarios->count() . "\n";
echo "- Bombas: " . $bombas->count() . "\n\n";

// Precios base actuales
$preciosBase = [
    1 => ['super' => 24.50, 'regular' => 23.80, 'diesel' => 22.90, 'cc' => 0.00, 'cc_activo' => false],
    2 => ['super' => 24.45, 'regular' => 23.75, 'diesel' => 22.85, 'cc' => 0.00, 'cc_activo' => true],
    3 => ['super' => 30.00, 'regular' => 28.00, 'diesel' => 25.00, 'cc' => 30.00, 'cc_activo' => true]
];

echo "💰 Actualizando precios cada 12 días...\n";

// 1. ACTUALIZAR PRECIOS CADA 12 DÍAS
$diasCambioPrecio = 0;
$fechaCambioPrecio = $fechaInicio->copy();

while ($fechaCambioPrecio->lte($fechaFin)) {
    foreach ($gasolineras as $gasolinera) {
        if ($diasCambioPrecio > 0) { // No cambiar en la primera iteración
            $variacion = (rand(-200, 200) / 100); // Variación de -2.00 a +2.00 Q
            
            $preciosBase[$gasolinera->id]['super'] += $variacion;
            $preciosBase[$gasolinera->id]['regular'] += $variacion;
            $preciosBase[$gasolinera->id]['diesel'] += $variacion;
            
            if ($preciosBase[$gasolinera->id]['cc_activo']) {
                $preciosBase[$gasolinera->id]['cc'] += $variacion;
            }
            
            // Asegurar que no sean negativos
            $preciosBase[$gasolinera->id]['super'] = max(20.00, $preciosBase[$gasolinera->id]['super']);
            $preciosBase[$gasolinera->id]['regular'] = max(19.00, $preciosBase[$gasolinera->id]['regular']);
            $preciosBase[$gasolinera->id]['diesel'] = max(18.00, $preciosBase[$gasolinera->id]['diesel']);
            
            if ($preciosBase[$gasolinera->id]['cc_activo']) {
                $preciosBase[$gasolinera->id]['cc'] = max(25.00, $preciosBase[$gasolinera->id]['cc']);
            }
        }
        
        // Actualizar precios en la base de datos
        DB::table('gasolineras')
            ->where('id', $gasolinera->id)
            ->update([
                'precio_super' => round($preciosBase[$gasolinera->id]['super'], 2),
                'precio_regular' => round($preciosBase[$gasolinera->id]['regular'], 2),
                'precio_diesel' => round($preciosBase[$gasolinera->id]['diesel'], 2),
                'precio_cc' => round($preciosBase[$gasolinera->id]['cc'], 2),
                'fecha_actualizacion_precios' => $fechaCambioPrecio,
                'updated_at' => now()
            ]);
        
        // Registrar cambio de precios en tabla de precios mensuales si existe
        try {
            DB::table('precios_mensuales')->insert([
                'gasolinera_id' => $gasolinera->id,
                'mes' => $fechaCambioPrecio->month,
                'anio' => $fechaCambioPrecio->year,
                'precio_super' => round($preciosBase[$gasolinera->id]['super'], 2),
                'precio_regular' => round($preciosBase[$gasolinera->id]['regular'], 2),
                'precio_diesel' => round($preciosBase[$gasolinera->id]['diesel'], 2),
                'precio_cc' => round($preciosBase[$gasolinera->id]['cc'], 2),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (Exception $e) {
            // La tabla puede no existir o ya tener el registro
        }
    }
    
    echo "📅 Precios actualizados para: " . $fechaCambioPrecio->format('Y-m-d') . "\n";
    $fechaCambioPrecio->addDays(12);
    $diasCambioPrecio++;
}

echo "\n⏰ Generando turnos (24 horas c/u)...\n";

// 2. GENERAR TURNOS DE 24 HORAS
$fechaActual = $fechaInicio->copy();
$turnoId = 1;

while ($fechaActual->lte($fechaFin)) {
    foreach ($usuarios as $usuario) {
        // Generar turno de 24 horas
        $horaInicio = Carbon::createFromFormat('Y-m-d H:i:s', $fechaActual->format('Y-m-d') . ' 06:00:00');
        $horaFin = $horaInicio->copy()->addHours(24);
        
        $dineroApertura = rand(500, 2000) + (rand(0, 99) / 100); // Entre Q500.00 y Q2000.99
        $ventasEstimadas = rand(3000, 8000) + (rand(0, 99) / 100); // Ventas del día
        $dineroCierre = $dineroApertura + $ventasEstimadas;
        
        DB::table('turnos')->insert([
            'gasolinera_id' => $usuario->gasolinera_id,
            'user_id' => $usuario->id,
            'fecha' => $fechaActual->format('Y-m-d'),
            'hora_inicio' => '06:00:00',
            'hora_fin' => '06:00:00', // Siguiente día
            'dinero_apertura' => $dineroApertura,
            'dinero_cierre' => $dineroCierre,
            'estado' => 'cerrado',
            'created_at' => $horaInicio,
            'updated_at' => $horaFin
        ]);
        
        if ($turnoId % 10 == 0) {
            echo "✅ Generados " . $turnoId . " turnos...\n";
        }
        $turnoId++;
    }
    $fechaActual->addDay();
}

echo "\n⛽ Generando movimientos de galonaje en bombas...\n";

// 3. GENERAR MOVIMIENTOS DE GALONAJE
$fechaActual = $fechaInicio->copy();
$movimientoId = 1;

while ($fechaActual->lte($fechaFin)) {
    foreach ($bombas as $bomba) {
        // Obtener gasolinera de la bomba
        $gasolinera = $gasolineras->where('id', $bomba->gasolinera_id)->first();
        
        // Generar entre 3-8 movimientos por día por bomba
        $numMovimientos = rand(3, 8);
        
        for ($i = 0; $i < $numMovimientos; $i++) {
            $tipoMovimiento = ['super', 'regular', 'diesel', 'cc'][rand(0, 3)];
            
            // Si es CC y la gasolinera no tiene CC activo, cambiar a super
            if ($tipoMovimiento === 'cc' && !$gasolinera->cc_activo) {
                $tipoMovimiento = 'super';
            }
            
            $campoModificado = 'galonaje_' . $tipoMovimiento;
            $valorAnterior = DB::table('bombas')->where('id', $bomba->id)->value($campoModificado);
            
            // Generar venta entre 5 y 50 galones
            $galonesVendidos = rand(5, 50) + (rand(0, 99) / 100);
            $valorNuevo = $valorAnterior + $galonesVendidos;
            
            // Actualizar la bomba
            DB::table('bombas')
                ->where('id', $bomba->id)
                ->update([
                    $campoModificado => $valorNuevo,
                    'updated_at' => now()
                ]);
            
            // Registrar en historial
            DB::table('historial_bombas')->insert([
                'bomba_id' => $bomba->id,
                'user_id' => $usuarios->where('gasolinera_id', $bomba->gasolinera_id)->first()->id,
                'campo_modificado' => $campoModificado,
                'valor_anterior' => $valorAnterior,
                'valor_nuevo' => $valorNuevo,
                'observaciones' => 'Venta de ' . number_format($galonesVendidos, 2) . ' galones de ' . strtoupper($tipoMovimiento),
                'created_at' => $fechaActual->copy()->addHours(rand(6, 20))->addMinutes(rand(0, 59)),
                'updated_at' => now()
            ]);
        }
        
        if ($movimientoId % 50 == 0) {
            echo "🔄 Generados " . $movimientoId . " movimientos...\n";
        }
        $movimientoId++;
    }
    $fechaActual->addDay();
}

echo "\n💸 Generando gastos mensuales...\n";

// 4. GENERAR GASTOS
$fechaActual = $fechaInicio->copy();
$gastoId = 1;

$categoriasGastos = ['operativo', 'mantenimiento', 'administrativo', 'inventario'];
$tiposGastos = [
    'operativo' => [
        'Salarios empleados' => [2000, 5000],
        'Servicios públicos (luz, agua)' => [800, 1500],
        'Combustible para generador' => [300, 800],
        'Materiales de limpieza' => [150, 400],
        'Uniformes empleados' => [200, 600]
    ],
    'mantenimiento' => [
        'Reparación de bombas' => [500, 2000],
        'Mantenimiento de tanques' => [800, 2500],
        'Reparación de equipos' => [300, 1200],
        'Pintura y señalización' => [400, 1000],
        'Calibración de bombas' => [200, 600]
    ],
    'administrativo' => [
        'Papelería y suministros' => [100, 300],
        'Servicios contables' => [500, 1200],
        'Licencias y permisos' => [800, 2000],
        'Seguros' => [1000, 3000],
        'Servicios bancarios' => [50, 200]
    ],
    'inventario' => [
        'Compra de combustible Super' => [15000, 40000],
        'Compra de combustible Regular' => [12000, 35000],
        'Compra de combustible Diesel' => [10000, 30000],
        'Compra de combustible CC' => [8000, 25000],
        'Aditivos y químicos' => [300, 800]
    ]
];

$proveedores = [
    'Puma Energy Guatemala',
    'Shell Guatemala',
    'Texaco Guatemala',
    'Terpel Guatemala',
    'Servicios Técnicos SA',
    'Mantenimiento Industrial GT',
    'Suministros Administrativos',
    'Contadores Asociados',
    'Seguros Universales',
    'Banco Industrial'
];

// Generar gastos por mes
for ($mes = 5; $mes <= 7; $mes++) {
    foreach ($gasolineras as $gasolinera) {
        // Generar entre 15-25 gastos por mes por gasolinera
        $numGastos = rand(15, 25);
        
        for ($i = 0; $i < $numGastos; $i++) {
            $categoria = $categoriasGastos[array_rand($categoriasGastos)];
            $descripcionOpciones = $tiposGastos[$categoria];
            $descripcion = array_rand($descripcionOpciones);
            $rangoMonto = $descripcionOpciones[$descripcion];
            $monto = rand($rangoMonto[0] * 100, $rangoMonto[1] * 100) / 100;
            
            // Si es combustible CC y la gasolinera no tiene CC, cambiar a super
            if (strpos($descripcion, 'CC') !== false && !$gasolinera->cc_activo) {
                $descripcion = 'Compra de combustible Super';
                $monto = rand(15000 * 100, 40000 * 100) / 100;
            }
            
            $fechaGasto = Carbon::create(2025, $mes, rand(1, 28));
            
            DB::table('gastos')->insert([
                'fecha' => $fechaGasto->format('Y-m-d'),
                'categoria' => $categoria,
                'descripcion' => $descripcion,
                'monto' => $monto,
                'proveedor' => $proveedores[array_rand($proveedores)],
                'gasolinera_id' => $gasolinera->id,
                'created_at' => $fechaGasto,
                'updated_at' => now()
            ]);
            
            $gastoId++;
        }
        
        // Registrar totales mensuales en gastos_mensuales si la tabla existe
        try {
            $totalMes = DB::table('gastos')
                ->where('gasolinera_id', $gasolinera->id)
                ->whereMonth('fecha', $mes)
                ->whereYear('fecha', 2025)
                ->sum('monto');
            
            DB::table('gastos_mensuales')->updateOrInsert(
                [
                    'gasolinera_id' => $gasolinera->id,
                    'mes' => $mes,
                    'anio' => 2025
                ],
                [
                    'total_gastos' => $totalMes,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        } catch (Exception $e) {
            // La tabla puede no existir
        }
        
        echo "💰 Gastos generados para " . $gasolinera->nombre . " - Mes " . $mes . "\n";
    }
}

echo "\n📊 Resumen de datos generados:\n";
echo "═══════════════════════════════════════════════\n";

// Mostrar estadísticas finales
$totalTurnos = DB::table('turnos')->count();
$totalMovimientos = DB::table('historial_bombas')->count();
$totalGastos = DB::table('gastos')->count();
$totalVentasGalones = DB::table('historial_bombas')->sum('valor_nuevo') - DB::table('historial_bombas')->sum('valor_anterior');

echo "🔢 Turnos generados: " . number_format($totalTurnos) . "\n";
echo "⛽ Movimientos de bombas: " . number_format($totalMovimientos) . "\n";
echo "💸 Gastos registrados: " . number_format($totalGastos) . "\n";
echo "🚗 Total galones vendidos: " . number_format($totalVentasGalones, 2) . "\n";

// Mostrar totales por gasolinera
echo "\n📈 Resumen por gasolinera:\n";
foreach ($gasolineras as $gasolinera) {
    $turnosGasolinera = DB::table('turnos')->where('gasolinera_id', $gasolinera->id)->count();
    $gastosGasolinera = DB::table('gastos')->where('gasolinera_id', $gasolinera->id)->sum('monto');
    $bombasGasolinera = $bombas->where('gasolinera_id', $gasolinera->id);
    $galonesTotal = 0;
    
    foreach ($bombasGasolinera as $bomba) {
        $galonesTotal += $bomba->galonaje_super + $bomba->galonaje_regular + $bomba->galonaje_diesel + $bomba->galonaje_cc;
    }
    
    echo "  🏪 " . $gasolinera->nombre . ":\n";
    echo "     - Turnos: " . $turnosGasolinera . "\n";
    echo "     - Gastos: Q" . number_format($gastosGasolinera, 2) . "\n";
    echo "     - Galones acumulados: " . number_format($galonesTotal, 2) . "\n";
}

echo "\n✅ ¡Generación de datos ficticios completada exitosamente!\n";
echo "📅 Período: Mayo - Julio 2025\n";
echo "🎯 Los datos incluyen variaciones realistas de precios cada 12 días\n";
echo "⏰ Turnos de 24 horas por operador\n";
echo "💰 Gastos distribuidos por categorías\n";
echo "⛽ Movimientos de galonaje registrados\n\n";

echo "🔄 Para ver el estado actualizado, revisa:\n";
echo "   - Panel de administración: /admin\n";
echo "   - Tabla de turnos\n";
echo "   - Historial de bombas\n";
echo "   - Registro de gastos\n\n";

?>
