<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrecioMensual extends Model
{
    protected $table = 'precios_mensuales';
    
    protected $fillable = [
        'anio',
        'mes',
        'super_compra',
        'diesel_compra',
        'regular_compra',
        'super_venta',
        'diesel_venta',
        'regular_venta'
    ];

    protected $casts = [
        'super_compra' => 'decimal:2',
        'diesel_compra' => 'decimal:2',
        'regular_compra' => 'decimal:2',
        'super_venta' => 'decimal:2',
        'diesel_venta' => 'decimal:2',
        'regular_venta' => 'decimal:2',
    ];
    
    // Calcular margen de ganancia para cada combustible
    public function getSuperMargenAttribute(): float
    {
        if ($this->super_compra > 0) {
            return round((($this->super_venta - $this->super_compra) / $this->super_compra) * 100, 2);
        }
        return 0.00;
    }
    
    public function getDieselMargenAttribute(): float
    {
        if ($this->diesel_compra > 0) {
            return round((($this->diesel_venta - $this->diesel_compra) / $this->diesel_compra) * 100, 2);
        }
        return 0.00;
    }
    
    public function getRegularMargenAttribute(): float
    {
        if ($this->regular_compra > 0) {
            return round((($this->regular_venta - $this->regular_compra) / $this->regular_compra) * 100, 2);
        }
        return 0.00;
    }
}
