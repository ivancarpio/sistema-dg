<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    use HasFactory;

    protected $table = 'movimientos_caja';

    protected $fillable = [
        'arqueo_caja_id',
        'id_usuario',
        'id_venta',
        'tipo',
        'metodo_pago',
        'monto',
        'motivo',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'monto' => 'decimal:2',
    ];

    public function arqueo()
    {
        return $this->belongsTo(ArqueoCaja::class, 'arqueo_caja_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta');
    }
}
