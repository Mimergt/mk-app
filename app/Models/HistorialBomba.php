<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialBomba extends Model
{
    protected $table = 'historial_bombas';
    
    protected $fillable = [
        'bomba_id',
        'user_id',
        'campo_modificado',
        'valor_anterior',
        'valor_nuevo',
        'observaciones'
    ];

    protected $casts = [
        'valor_anterior' => 'decimal:2',
        'valor_nuevo' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function bomba()
    {
        return $this->belongsTo(Bomba::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
