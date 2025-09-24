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
        'venta_descuentos',
        'precio_super',
        'precio_regular',
        'precio_diesel',
        'ventas_netas_turno'
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
        'venta_descuentos' => 'decimal:2',
        'precio_super' => 'decimal:4',
        'precio_regular' => 'decimal:4',
        'precio_diesel' => 'decimal:4',
        'ventas_netas_turno' => 'decimal:2'
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

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->calculateVentasNetasTurno();
        });
    }

    private function calculateVentasNetasTurno()
    {
        $venta_credito = $this->venta_credito ?? 0;
        $venta_tarjetas = $this->venta_tarjetas ?? 0;
        $venta_efectivo = $this->venta_efectivo ?? 0;
        $venta_descuentos = $this->venta_descuentos ?? 0;

        $this->ventas_netas_turno = $venta_credito + $venta_tarjetas + $venta_efectivo - $venta_descuentos;
    }
}
