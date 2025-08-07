<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Combustible extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'precio',
        'precio_compra',
        'precio_venta',
        'activo',
        'gasolinera_id',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function gasolinera(): BelongsTo
    {
        return $this->belongsTo(Gasolinera::class);
    }

    public function bombas(): HasMany
    {
        return $this->hasMany(Bomba::class);
    }
}