<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gasolinera extends Model
{
    protected $fillable = [
        'nombre',
        'ubicacion',
        'precio_super',
        'precio_regular', 
        'precio_diesel',
        'fecha_actualizacion_precios'
    ];

    protected $casts = [
        'precio_super' => 'decimal:2',
        'precio_regular' => 'decimal:2',
        'precio_diesel' => 'decimal:2',
        'fecha_actualizacion_precios' => 'datetime',
    ];

    public function bombas()
    {
        return $this->hasMany(Bomba::class);
    }

    public function turnos()
    {
        return $this->hasMany(Turno::class);
    }
    
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Métodos helper para obtener precios
    public function getPrecioSuper()
    {
        return $this->precio_super;
    }
    
    public function getPrecioRegular()
    {
        return $this->precio_regular;
    }
    
    public function getPrecioDiesel()
    {
        return $this->precio_diesel;
    }
    
    public function getPrecioCC()
    {
        return null; // CC ya no maneja precio
    }

    // Método para obtener precio por tipo de combustible
    public function getPrecioCombustible($tipo)
    {
        return match($tipo) {
            'super' => $this->precio_super,
            'regular' => $this->precio_regular,
            'diesel' => $this->precio_diesel,
            'cc' => null, // CC sin precio
            default => 0.00
        };
    }
}