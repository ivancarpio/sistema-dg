<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArqueoCaja extends Model
{
    use HasFactory;

    protected $table = 'arqueos_caja';

    protected $fillable = [
        'id_usuario',
        'fecha_apertura',
        'fecha_cierre',
        'monto_inicial',
        'total_efectivo_sistema',
        'total_qr_sistema',
        'total_transferencia_sistema',
        'total_egresos',
        'monto_fisico_declarado',
        'diferencia',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre'   => 'datetime',
        'monto_inicial'  => 'decimal:2',
        'total_efectivo_sistema' => 'decimal:2',
        'total_qr_sistema' => 'decimal:2',
        'total_transferencia_sistema' => 'decimal:2',
        'total_egresos'  => 'decimal:2',
        'monto_fisico_declarado' => 'decimal:2',
        'diferencia'     => 'decimal:2',
    ];

    /**
     * Cajero que abrió/gestionó la sesión.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    /**
     * Pagos y cobros reales recaudados durante este arqueo.
     */
    public function pagos()
    {
        return $this->hasMany(PagoVenta::class, 'arqueo_caja_id');
    }

    /**
     * Movimientos de caja (gastos, retiros, ingresos extras, devoluciones).
     */
    public function movimientos()
    {
        return $this->hasMany(MovimientoCaja::class, 'arqueo_caja_id');
    }

    /**
     * Solo los egresos (gastos operativos y devoluciones de ventas).
     */
    public function egresos()
    {
        return $this->hasMany(MovimientoCaja::class, 'arqueo_caja_id')
            ->whereIn('tipo', ['egreso_gasto', 'egreso_devolucion']);
    }

    /**
     * Efectivo físico esperado en gaveta (Monto Inicial + Efectivo cobrado - Egresos en efectivo).
     */
    public function getEfectivoEsperadoAttribute(): float
    {
        $egresosEfectivo = (float) $this->movimientos()
            ->whereIn('tipo', ['egreso_gasto', 'egreso_devolucion'])
            ->where('metodo_pago', 'Efectivo')
            ->sum('monto');

        $ingresosExtraEfectivo = (float) $this->movimientos()
            ->where('tipo', 'ingreso_extra')
            ->where('metodo_pago', 'Efectivo')
            ->sum('monto');

        return round(floatval($this->monto_inicial) + floatval($this->total_efectivo_sistema) + $ingresosExtraEfectivo - $egresosEfectivo, 2);
    }

    /**
     * Total general recaudado (Efectivo + QR + Transferencias).
     */
    public function getTotalRecaudadoAttribute(): float
    {
        return round(floatval($this->total_efectivo_sistema) + floatval($this->total_qr_sistema) + floatval($this->total_transferencia_sistema), 2);
    }

    /**
     * Retorna la sesión de caja actualmente abierta (si existe).
     */
    public static function sesionActiva()
    {
        return static::where('estado', 'abierto')->latest('fecha_apertura')->first();
    }
}
