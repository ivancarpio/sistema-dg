<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CajaChicaMovimiento extends Model
{
    use HasFactory;

    protected $table = 'caja_chica_movimientos';

    protected $fillable = [
        'fecha',
        'tipo', // 'apertura_o_reposicion', 'gasto', 'reversion_gasto', 'ajuste_ingreso', 'ajuste_egreso'
        'monto',
        'saldo_resultante',
        'motivo',
        'gasto_id',
        'turno_caja_id',
        'movimiento_caja_id',
        'comprobante',
        'user_id',
    ];

    protected $casts = [
        'fecha'            => 'datetime',
        'monto'            => 'decimal:2',
        'saldo_resultante' => 'decimal:2',
    ];

    public function gasto()
    {
        return $this->belongsTo(Gasto::class, 'gasto_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function turnoCaja()
    {
        return $this->belongsTo(ArqueoCaja::class, 'turno_caja_id');
    }

    public function movimientoCaja()
    {
        return $this->belongsTo(MovimientoCaja::class, 'movimiento_caja_id');
    }

    /**
     * Retorna el saldo actual de Caja Chica.
     */
    public static function saldoActual(): float
    {
        $ultimo = static::latest('id')->first();
        return $ultimo ? (float) $ultimo->saldo_resultante : 0.00;
    }
}
