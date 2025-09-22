<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Turno;
use App\Models\TurnoBombaDatos;
use Carbon\Carbon;

class GenerateTurnos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:turnos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generar turnos de los últimos 3 meses para todas las gasolineras';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Datos base extraídos del turno 305
        $gasolineras = [
            4 => ['user_id' => 10, 'bombas' => [13, 14, 15, 16]],
            5 => ['user_id' => 11, 'bombas' => [17, 18, 19, 20]],
            6 => ['user_id' => 12, 'bombas' => [21, 22, 23, 24]],
            7 => ['user_id' => 13, 'bombas' => [25, 26, 27, 28]]
        ];

        // Valores base del turno 305 para variaciones
        $ventasBase = [
            'venta_credito' => 3030.00,
            'venta_tarjetas' => 225.00,
            'venta_efectivo' => 12776.00,
            'venta_descuentos' => 0.00
        ];


        // Lecturas base de bombas del turno 305
        $bombasBase = [
            13 => ['super' => 2829.222, 'regular' => 1684.089, 'diesel' => 2301.334, 'cc' => 214433.200],
            14 => ['super' => 3139.106, 'regular' => 1950.377, 'diesel' => 2577.453, 'cc' => 241198.500],
            15 => ['super' => 36228.468, 'regular' => 29100.576, 'diesel' => 43018.536, 'cc' => 3598101.400],
            16 => ['super' => 0.000, 'regular' => 0.000, 'diesel' => 0.000, 'cc' => 49267.084]
        ];

        $fotografias = [
            'turnos/bombas/turno_305_bomba_13_1757817061.jpg',
            'turnos/bombas/turno_305_bomba_14_1757817138.jpg',
            'turnos/bombas/turno_305_bomba_15_1757817216.jpg',
            'turnos/bombas/turno_305_bomba_16_1757817381.jpg'
        ];

        // Generar fechas para los últimos 3 meses
        $fechaInicio = Carbon::now()->subMonths(3);
        $fechaFin = Carbon::now()->subDay(); // Hasta ayer para no incluir hoy
        $fechas = [];

        $fecha = $fechaInicio->copy();
        while ($fecha->lte($fechaFin)) {
            $fechas[] = $fecha->copy();
            $fecha->addDay();
        }

        $this->info("Generando " . count($fechas) . " días de turnos para 4 gasolineras...");

        foreach ($gasolineras as $gasolineraId => $config) {
            $this->info("Procesando gasolinera $gasolineraId...");

            // Valores iniciales de bombas para esta gasolinera
            $bombasActuales = [];
            foreach ($config['bombas'] as $index => $bombaId) {
                $bombasActuales[$bombaId] = [
                    'super' => $bombasBase[13]['super'] + ($index * 100) + ($gasolineraId * 500),
                    'regular' => $bombasBase[13]['regular'] + ($index * 80) + ($gasolineraId * 400),
                    'diesel' => $bombasBase[13]['diesel'] + ($index * 120) + ($gasolineraId * 600),
                    'cc' => $bombasBase[13]['cc'] + ($index * 1000) + ($gasolineraId * 5000)
                ];
            }

            foreach ($fechas as $fecha) {
                // Crear el turno
                $horaInicio = $fecha->copy()->setTime(8, rand(0, 59));
                $horaFin = $horaInicio->copy()->addHours(rand(8, 12))->addMinutes(rand(0, 59));

                // Variaciones en ventas (±30%)
                $variacion = 1 + (rand(-30, 30) / 100);

                $turno = Turno::create([
                    'gasolinera_id' => $gasolineraId,
                    'user_id' => $config['user_id'],
                    'fecha' => $fecha->toDateString(),
                    'hora_inicio' => $horaInicio,
                    'hora_fin' => $horaFin,
                    'dinero_apertura' => 0.00,
                    'dinero_cierre' => 0.00,
                    'estado' => 'cerrado',
                    'venta_credito' => round($ventasBase['venta_credito'] * $variacion, 2),
                    'venta_tarjetas' => round($ventasBase['venta_tarjetas'] * $variacion, 2),
                    'venta_efectivo' => round($ventasBase['venta_efectivo'] * $variacion, 2),
                    'venta_descuentos' => round($ventasBase['venta_descuentos'] * $variacion, 2),
                    'created_at' => $horaInicio,
                    'updated_at' => $horaFin
                ]);

                // Crear lecturas de bombas
                foreach ($config['bombas'] as $index => $bombaId) {
                    // Incremento diario realista (10-50 galones por día por bomba)
                    $incrementoSuper = rand(10, 50) + (rand(0, 100) / 100);
                    $incrementoRegular = rand(8, 40) + (rand(0, 100) / 100);
                    $incrementoDiesel = rand(12, 60) + (rand(0, 100) / 100);
                    $incrementoCC = rand(50, 200) + (rand(0, 100) / 100);

                    $bombasActuales[$bombaId]['super'] += $incrementoSuper;
                    $bombasActuales[$bombaId]['regular'] += $incrementoRegular;
                    $bombasActuales[$bombaId]['diesel'] += $incrementoDiesel;
                    $bombasActuales[$bombaId]['cc'] += $incrementoCC;

                    // Para bomba 4 (inactiva en combustibles)
                    if ($index == 3) {
                        $bombasActuales[$bombaId]['super'] = 0;
                        $bombasActuales[$bombaId]['regular'] = 0;
                        $bombasActuales[$bombaId]['diesel'] = 0;
                    }

                    TurnoBombaDatos::create([
                        'bomba_id' => $bombaId,
                        'turno_id' => $turno->id,
                        'user_id' => $config['user_id'],
                        'galonaje_super' => round($bombasActuales[$bombaId]['super'], 3),
                        'galonaje_regular' => round($bombasActuales[$bombaId]['regular'], 3),
                        'galonaje_diesel' => round($bombasActuales[$bombaId]['diesel'], 3),
                        'lectura_cc' => round($bombasActuales[$bombaId]['cc'], 3),
                        'fotografia' => $fotografias[$index],
                        'observaciones' => "Datos generados automáticamente para turno {$turno->id} - Bomba " . ($index + 1),
                        'fecha_turno' => $fecha,
                        'created_at' => $horaInicio,
                        'updated_at' => $horaFin
                    ]);
                }

                if ($fecha->day % 10 == 0) {
                    $this->line("  Procesado hasta: " . $fecha->format('d/m/Y'));
                }
            }

            $this->info("  Completado gasolinera $gasolineraId: " . count($fechas) . " turnos creados");
        }

        $this->info("¡Generación completada!");
        $this->info("Total: " . (count($fechas) * 4) . " turnos creados");
        $this->info("Total lecturas de bombas: " . (count($fechas) * 4 * 4) . " registros creados");

        return 0;
    }
}
