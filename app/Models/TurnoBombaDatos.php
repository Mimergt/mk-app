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
        'resultado_cc',
        'fotografia',
        'observaciones',
        'fecha_turno',
        'galones_vendidos_super',
        'galones_vendidos_regular',
        'galones_vendidos_diesel',
        'ventas_galones_super',
        'ventas_galones_regular',
        'ventas_galones_diesel'
    ];

    protected $casts = [
        'galonaje_super' => 'decimal:3',
        'galonaje_regular' => 'decimal:3',
        'galonaje_diesel' => 'decimal:3',
        'lectura_cc' => 'decimal:3',
        'resultado_cc' => 'decimal:3',
        'fecha_turno' => 'datetime',
        'galones_vendidos_super' => 'decimal:3',
        'galones_vendidos_regular' => 'decimal:3',
        'galones_vendidos_diesel' => 'decimal:3',
        'ventas_galones_super' => 'decimal:2',
        'ventas_galones_regular' => 'decimal:2',
        'ventas_galones_diesel' => 'decimal:2',
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

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->calculateGalonesVendidos();
            $model->calculateResultadoCc();
            $model->calculateVentasGalones();
        });
    }

    private function calculateGalonesVendidos()
    {
        $previousReading = static::where('bomba_id', $this->bomba_id)
            ->where('fecha_turno', '<', $this->fecha_turno)
            ->orderBy('fecha_turno', 'desc')
            ->first();

        if ($previousReading) {
            $this->galones_vendidos_super = $this->galonaje_super - $previousReading->galonaje_super;
            $this->galones_vendidos_regular = $this->galonaje_regular - $previousReading->galonaje_regular;
            $this->galones_vendidos_diesel = $this->galonaje_diesel - $previousReading->galonaje_diesel;
        } else {
            $this->galones_vendidos_super = 0;
            $this->galones_vendidos_regular = 0;
            $this->galones_vendidos_diesel = 0;
        }

        if ($this->galones_vendidos_super < 0) $this->galones_vendidos_super = 0;
        if ($this->galones_vendidos_regular < 0) $this->galones_vendidos_regular = 0;
        if ($this->galones_vendidos_diesel < 0) $this->galones_vendidos_diesel = 0;
    }

    private function calculateResultadoCc()
    {
        $previousReading = static::where('bomba_id', $this->bomba_id)
            ->where('fecha_turno', '<', $this->fecha_turno)
            ->orderBy('fecha_turno', 'desc')
            ->first();

        if ($previousReading) {
            $this->resultado_cc = $this->lectura_cc - $previousReading->lectura_cc;
        } else {
            $this->resultado_cc = 0;
        }

        if ($this->resultado_cc < 0) $this->resultado_cc = 0;
    }

    private function calculateVentasGalones()
    {
        if ($this->turno) {
            $turno = $this->turno;
            $this->ventas_galones_super = $this->galones_vendidos_super * ($turno->precio_super ?? 0);
            $this->ventas_galones_regular = $this->galones_vendidos_regular * ($turno->precio_regular ?? 0);
            $this->ventas_galones_diesel = $this->galones_vendidos_diesel * ($turno->precio_diesel ?? 0);
        } else if ($this->turno_id) {
            $turno = Turno::find($this->turno_id);
            if ($turno) {
                $this->ventas_galones_super = $this->galones_vendidos_super * ($turno->precio_super ?? 0);
                $this->ventas_galones_regular = $this->galones_vendidos_regular * ($turno->precio_regular ?? 0);
                $this->ventas_galones_diesel = $this->galones_vendidos_diesel * ($turno->precio_diesel ?? 0);
            }
        }
    }
}
