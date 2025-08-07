<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gasto extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'categoria',
        'descripcion',
        'monto',
        'proveedor',
        'gasolinera_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
    ];

    public function gasolinera(): BelongsTo
    {
        return $this->belongsTo(Gasolinera::class);
    }
}
