<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BombasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gasolineras = \App\Models\Gasolinera::all();

        foreach ($gasolineras as $gasolinera) {
            // Limpiar bombas existentes de esta gasolinera
            \App\Models\Bomba::where('gasolinera_id', $gasolinera->id)->delete();
            
            // Crear exactamente 4 bombas para cada gasolinera
            for ($i = 1; $i <= 4; $i++) {
                \App\Models\Bomba::create([
                    'nombre' => "Bomba {$i}",
                    'gasolinera_id' => $gasolinera->id,
                    'galonaje_super' => rand(1000, 9999) + (rand(0, 99) / 100),
                    'galonaje_regular' => rand(1000, 9999) + (rand(0, 99) / 100),
                    'galonaje_diesel' => rand(1000, 9999) + (rand(0, 99) / 100),
                    'galonaje_cc' => rand(500, 5000) + (rand(0, 99) / 100),
                    'estado' => 'activa'
                ]);
            }
        }
        
        echo "✅ Bombas creadas para " . $gasolineras->count() . " gasolineras\n";
    }
}
