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
        'estado'
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora_inicio' => 'datetime',
        'hora_fin' => 'datetime',
        'dinero_apertura' => 'decimal:2',
        'dinero_cierre' => 'decimal:2'
    ];

    public function gasolinera()
    {
        return $this->belongsTo(Gasolinera::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
