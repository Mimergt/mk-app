<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gasolinera;
use App\Models\Bomba;

class GasolineraSeeder extends Seeder
{
    public function run(): void
    {
        // Crear gasolineras
        $gasolinera1 = Gasolinera::create([
            'nombre' => 'Gasolinera Central',
            'ubicacion' => 'Avenida Central, Guatemala'
        ]);

        $gasolinera2 = Gasolinera::create([
            'nombre' => 'Gasolinera Norte',
            'ubicacion' => 'Zona 17, Ciudad de Guatemala'
        ]);

        // Crear bombas para gasolinera 1 (Gasolinera Central)
        $bombasGas1 = [
            ['nombre' => 'Bomba 1', 'tipo' => 'Super', 'precio' => 25.50, 'galonaje' => 5224.00],
            ['nombre' => 'Bomba 1', 'tipo' => 'Regular', 'precio' => 24.20, 'galonaje' => 4850.00],
            ['nombre' => 'Bomba 1', 'tipo' => 'Diesel', 'precio' => 22.80, 'galonaje' => 3920.00],
            ['nombre' => 'Bomba 2', 'tipo' => 'Super', 'precio' => 25.50, 'galonaje' => 5100.00],
            ['nombre' => 'Bomba 2', 'tipo' => 'Regular', 'precio' => 24.20, 'galonaje' => 4900.00],
            ['nombre' => 'Bomba 2', 'tipo' => 'Diesel', 'precio' => 22.80, 'galonaje' => 4200.00],
        ];

        foreach ($bombasGas1 as $bombaData) {
            Bomba::create([
                'gasolinera_id' => $gasolinera1->id,
                'nombre' => $bombaData['nombre'],
                'tipo' => $bombaData['tipo'],
                'precio' => $bombaData['precio'],
                'galonaje' => $bombaData['galonaje']
            ]);
        }

        // Crear bombas para gasolinera 2 (Gasolinera Norte)
        $bombasGas2 = [
            ['nombre' => 'Bomba 1', 'tipo' => 'Super', 'precio' => 25.75, 'galonaje' => 6000.00],
            ['nombre' => 'Bomba 1', 'tipo' => 'Regular', 'precio' => 24.45, 'galonaje' => 5500.00],
            ['nombre' => 'Bomba 1', 'tipo' => 'Diesel', 'precio' => 23.00, 'galonaje' => 4800.00],
        ];

        foreach ($bombasGas2 as $bombaData) {
            Bomba::create([
                'gasolinera_id' => $gasolinera2->id,
                'nombre' => $bombaData['nombre'],
                'tipo' => $bombaData['tipo'],
                'precio' => $bombaData['precio'],
                'galonaje' => $bombaData['galonaje']
            ]);
        }
    }
}
