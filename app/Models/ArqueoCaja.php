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
        'id_usuario_cierre',
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
     * Cajero o administrador que cerró la sesión.
     */
    public function usuarioCierre()
    {
        return $this->belongsTo(User::class, 'id_usuario_cierre');
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
     * Solo los egresos (gastos operativos, retiros y devoluciones de ventas).
     */
    public function egresos()
    {
        return $this->hasMany(MovimientoCaja::class, 'arqueo_caja_id')
            ->whereIn('tipo', ['egreso_gasto', 'egreso_devolucion', 'egreso_retiro']);
    }

    /**
     * Gastos operativos registrados en este turno.
     */
    public function gastos()
    {
        return $this->hasMany(Gasto::class, 'turno_caja_id');
    }

    /**
     * Egresos de efectivo físico que salieron de la gaveta.
     */
    public function getEgresosEfectivoAttribute(): float
    {
        return (float) $this->movimientos()
            ->whereIn('tipo', ['egreso_gasto', 'egreso_devolucion', 'egreso_retiro'])
            ->where('metodo_pago', 'Efectivo')
            ->sum('monto');
    }

    /**
     * Ingresos extra de efectivo físico ingresados a la gaveta.
     */
    public function getIngresosExtraEfectivoAttribute(): float
    {
        return (float) $this->movimientos()
            ->where('tipo', 'ingreso_extra')
            ->where('metodo_pago', 'Efectivo')
            ->sum('monto');
    }

    /**
     * Efectivo físico esperado en gaveta (Monto Inicial + Efectivo cobrado + Ingresos extras - Egresos en efectivo).
     */
    public function getEfectivoEsperadoAttribute(): float
    {
        return round(floatval($this->monto_inicial) + floatval($this->total_efectivo_sistema) + $this->ingresos_extra_efectivo - $this->egresos_efectivo, 2);
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
