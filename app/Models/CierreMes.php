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
        'id_usuario',
        'fecha_cierre',
        'notas',
        'snapshot_items',
        'estado',
    ];

    protected $casts = [
        'fecha_cierre'   => 'datetime',
        'snapshot_items' => 'array',
        'total_productos'=> 'integer',
        'total_ventas'   => 'integer',
        'total_stock'    => 'integer',
        'verificados_count' => 'integer',
        'porcentaje'     => 'integer',
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
