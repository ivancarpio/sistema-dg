<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CierreMes extends Model
{
    use HasFactory;

    protected $table = 'cierres_mes';

    protected $fillable = [
        'mes',
        'total_productos',
        'total_ventas',
        'total_stock',
        'verificados_count',
        'porcentaje',
        'total_ingresos_ventas',
        'total_costo_ventas',
        'total_gastos_operativos',
        'utilidad_neta',
        'id_usuario',
        'fecha_cierre',
        'notas',
        'snapshot_items',
        'estado',
    ];

    protected $casts = [
        'fecha_cierre'            => 'datetime',
        'snapshot_items'          => 'array',
        'total_productos'         => 'integer',
        'total_ventas'            => 'integer',
        'total_stock'             => 'integer',
        'verificados_count'       => 'integer',
        'porcentaje'              => 'integer',
        'total_ingresos_ventas'   => 'decimal:2',
        'total_costo_ventas'      => 'decimal:2',
        'total_gastos_operativos' => 'decimal:2',
        'utilidad_neta'           => 'decimal:2',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function isCerrado(): bool
    {
        return $this->estado === 'Cerrado';
    }
}
