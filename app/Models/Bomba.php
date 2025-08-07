<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bomba extends Model
{
    protected $fillable = [
        'gasolinera_id',
        'nombre',
        'galonaje_super',
        'galonaje_regular',
        'galonaje_diesel',
        'galonaje_cc',
        'estado'
    ];

    protected $casts = [
        'galonaje_super' => 'decimal:2',
        'galonaje_regular' => 'decimal:2',
        'galonaje_diesel' => 'decimal:2',
        'galonaje_cc' => 'decimal:2',
    ];

    public function gasolinera()
    {
        return $this->belongsTo(Gasolinera::class);
    }

    public function historial()
    {
        return $this->hasMany(HistorialBomba::class);
    }

    public function ultimaActualizacion()
    {
        return $this->historial()->latest()->first();
    }

    // Métodos helper para obtener galonajes
    public function getGalonajeSuper()
    {
        return $this->galonaje_super;
    }
    
    public function getGalonajeRegular()
    {
        return $this->galonaje_regular;
    }
    
    public function getGalonajeDiesel()
    {
        return $this->galonaje_diesel;
    }
    
    public function getGalonajeCC()
    {
        return $this->galonaje_cc;
    }

    // Método para obtener galonaje por tipo de combustible
    public function getGalonajeCombustible($tipo)
    {
        return match($tipo) {
            'super' => $this->galonaje_super,
            'regular' => $this->galonaje_regular,
            'diesel' => $this->galonaje_diesel,
            'cc' => $this->galonaje_cc,
            default => 0.00
        };
    }

    // Método para añadir galonaje
    public function addGalonaje($tipo, $cantidad)
    {
        $campo = 'galonaje_' . $tipo;
        if (in_array($campo, $this->fillable)) {
            $this->$campo += $cantidad;
            $this->save();
        }
    }

    // Verificar si la bomba está activa
    public function isActiva()
    {
        return $this->estado === 'activa';
    }
}