<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoVenta extends Model
{
    use HasFactory;

    protected $table = 'pagos_ventas';

    protected $fillable = [
        'id_venta',
        'arqueo_caja_id',
        'id_cliente',
        'id_usuario',
        'monto',
        'metodo_pago',
        'referencia',
        'observacion',
        'fecha',
    ];

    public function arqueo()
    {
        return $this->belongsTo(ArqueoCaja::class, 'arqueo_caja_id');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
