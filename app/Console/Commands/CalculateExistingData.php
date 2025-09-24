<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Turno;
use App\Models\TurnoBombaDatos;

class CalculateExistingData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:calculate-existing-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and populate existing turno and bomba data with new calculated fields';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting calculation of existing data...');

        $this->info('Calculating ventas_netas_turno for existing turnos...');
        $turnos = Turno::all();
        $bar = $this->output->createProgressBar($turnos->count());

        foreach ($turnos as $turno) {
            $venta_credito = $turno->venta_credito ?? 0;
            $venta_tarjetas = $turno->venta_tarjetas ?? 0;
            $venta_efectivo = $turno->venta_efectivo ?? 0;
            $venta_descuentos = $turno->venta_descuentos ?? 0;

            $turno->ventas_netas_turno = $venta_credito + $venta_tarjetas + $venta_efectivo - $venta_descuentos;
            $turno->saveQuietly();
            $bar->advance();
        }
        $bar->finish();

        $this->info("\nCalculating galones vendidos and ventas for turno_bomba_datos...");
        $bombaDatos = TurnoBombaDatos::orderBy('bomba_id')->orderBy('fecha_turno')->get();
        $bar = $this->output->createProgressBar($bombaDatos->count());

        foreach ($bombaDatos as $registro) {
            $previousReading = TurnoBombaDatos::where('bomba_id', $registro->bomba_id)
                ->where('fecha_turno', '<', $registro->fecha_turno)
                ->orderBy('fecha_turno', 'desc')
                ->first();

            if ($previousReading) {
                $registro->galones_vendidos_super = max(0, $registro->galonaje_super - $previousReading->galonaje_super);
                $registro->galones_vendidos_regular = max(0, $registro->galonaje_regular - $previousReading->galonaje_regular);
                $registro->galones_vendidos_diesel = max(0, $registro->galonaje_diesel - $previousReading->galonaje_diesel);
            } else {
                $registro->galones_vendidos_super = $registro->galonaje_super;
                $registro->galones_vendidos_regular = $registro->galonaje_regular;
                $registro->galones_vendidos_diesel = $registro->galonaje_diesel;
            }

            if ($registro->turno) {
                $turno = $registro->turno;
                $registro->ventas_galones_super = $registro->galones_vendidos_super * ($turno->precio_super ?? 0);
                $registro->ventas_galones_regular = $registro->galones_vendidos_regular * ($turno->precio_regular ?? 0);
                $registro->ventas_galones_diesel = $registro->galones_vendidos_diesel * ($turno->precio_diesel ?? 0);
            }

            $registro->saveQuietly();
            $bar->advance();
        }
        $bar->finish();

        $this->info("\nCalculation completed successfully!");
    }
}
