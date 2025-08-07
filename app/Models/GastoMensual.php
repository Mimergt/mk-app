<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GastoMensual extends Model
{
    protected $table = 'gastos_mensuales';
    
    protected $fillable = [
        'anio',
        'mes',
        'impuestos',
        'servicios',
        'planilla',
        'renta',
        'gastos_adicionales'
    ];

    protected $casts = [
        'impuestos' => 'decimal:2',
        'servicios' => 'decimal:2',
        'planilla' => 'decimal:2',
        'renta' => 'decimal:2',
        'gastos_adicionales' => 'array'
    ];
    
    public function getTotalAttribute(): float
    {
        $totalFijo = $this->impuestos + $this->servicios + $this->planilla + $this->renta;
        $totalAdicional = 0;
        
        if ($this->gastos_adicionales) {
            foreach ($this->gastos_adicionales as $gasto) {
                $totalAdicional += (float) ($gasto['monto'] ?? 0);
            }
        }
        
        return $totalFijo + $totalAdicional;
    }
}
