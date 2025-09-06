<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TurnoBombaDatos extends Model
{
    protected $table = 'turno_bomba_datos';
    
    protected $fillable = [
        'bomba_id',
        'turno_id',
        'user_id',
        'galonaje_super',
        'galonaje_regular',
        'galonaje_diesel',
        'lectura_cc',
        'fotografia',
        'observaciones',
        'fecha_turno'
    ];

    protected $casts = [
        'galonaje_super' => 'decimal:3',
        'galonaje_regular' => 'decimal:3',
        'galonaje_diesel' => 'decimal:3',
        'lectura_cc' => 'decimal:3',
        'fecha_turno' => 'datetime',
    ];

    public function bomba()
    {
        return $this->belongsTo(Bomba::class);
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFotografiaUrlAttribute()
    {
        if ($this->fotografia) {
            return asset('storage/' . $this->fotografia);
        }
        return null;
    }
}
