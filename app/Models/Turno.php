<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    protected $fillable = [
        'gasolinera_id',
        'user_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'dinero_apertura',
        'dinero_cierre',
        'estado',
        'venta_credito',
        'venta_tarjetas',
        'venta_efectivo',
        'venta_descuentos'
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora_inicio' => 'datetime',
        'hora_fin' => 'datetime',
        'dinero_apertura' => 'decimal:2',
        'dinero_cierre' => 'decimal:2',
        'venta_credito' => 'decimal:2',
        'venta_tarjetas' => 'decimal:2',
        'venta_efectivo' => 'decimal:2',
        'venta_descuentos' => 'decimal:2'
    ];

    public function gasolinera()
    {
        return $this->belongsTo(Gasolinera::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function turnoBombaDatos()
    {
        return $this->hasMany(TurnoBombaDatos::class);
    }

    public function bombaDatos()
    {
        return $this->hasMany(TurnoBombaDatos::class);
    }
}
