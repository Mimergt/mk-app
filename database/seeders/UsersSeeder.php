<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Gasolinera;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $operadorRole = Role::firstOrCreate(['name' => 'operador']);

        // Crear permisos
        $permissions = [
            'gestionar usuarios',
            'gestionar gasolineras', 
            'gestionar bombas',
            'ver panel turnos',
            'abrir cerrar turnos',
            'actualizar lecturas'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Asignar permisos a roles
        $adminRole->givePermissionTo($permissions);
        $operadorRole->givePermissionTo(['ver panel turnos', 'abrir cerrar turnos', 'actualizar lecturas']);

        // Crear gasolineras de prueba
        $gasolinera1 = Gasolinera::firstOrCreate([
            'nombre' => 'Gasolinera Central',
            'ubicacion' => 'Zona 1, Ciudad de Guatemala'
        ]);

        $gasolinera2 = Gasolinera::firstOrCreate([
            'nombre' => 'Gasolinera Norte',
            'ubicacion' => 'Zona 17, Ciudad de Guatemala'
        ]);

        // Crear usuario administrador
        $admin = User::firstOrCreate([
            'email' => 'admin@gasolinera.com'
        ], [
            'name' => 'Administrador Principal',
            'password' => Hash::make('password'),
            'tipo_usuario' => 'admin'
        ]);
        $admin->assignRole($adminRole);

        // Crear operadores
        $operador1 = User::firstOrCreate([
            'email' => 'operador1@gasolinera.com'
        ], [
            'name' => 'Juan Pérez',
            'password' => Hash::make('password'),
            'tipo_usuario' => 'operador',
            'gasolinera_id' => $gasolinera1->id
        ]);
        $operador1->assignRole($operadorRole);

        $operador2 = User::firstOrCreate([
            'email' => 'operador2@gasolinera.com'
        ], [
            'name' => 'María García',
            'password' => Hash::make('password'),
            'tipo_usuario' => 'operador',
            'gasolinera_id' => $gasolinera2->id
        ]);
        $operador2->assignRole($operadorRole);

        echo "✅ Usuarios creados:\n";
        echo "👤 Admin: admin@gasolinera.com / password\n";
        echo "👤 Operador 1: operador1@gasolinera.com / password (Gasolinera Central)\n";
        echo "👤 Operador 2: operador2@gasolinera.com / password (Gasolinera Norte)\n";
    }
}
