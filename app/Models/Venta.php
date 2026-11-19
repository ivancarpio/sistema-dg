<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'id_venta');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function clienteModel()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function clienteRelacionado()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function usuarioAnulacion()
    {
        return $this->belongsTo(User::class, 'id_usuario_anulacion');
    }

    public function pagos()
    {
        return $this->hasMany(PagoVenta::class, 'id_venta');
    }

    /**
     * Venta original que fue anulada para dar origen a esta venta rehecha.
     */
    public function ventaOrigen()
    {
        return $this->belongsTo(Venta::class, 'venta_origen_id');
    }

    /**
     * Nueva venta que reemplazó a esta venta si fue anulada y rehecha.
     */
    public function ventaReemplazo()
    {
        return $this->hasOne(Venta::class, 'venta_origen_id');
    }

    /**
     * Movimientos de caja vinculados (ej. egreso por devolución si el monto bajó).
     */
    public function movimientosCaja()
    {
        return $this->hasMany(MovimientoCaja::class, 'id_venta');
    }

    /**
     * Retorna el método real con el que se cobró el dinero.
     * Si la venta nació como "Por Pagar" pero ya se registraron abonos/pagos,
     * devuelve el método de los abonos (ej: 'QR', 'Efectivo' o 'Mixto').
     */
    public function getMetodoCobroRealAttribute(): string
    {
        if ($this->metodo_pago !== 'Por Pagar') {
            return $this->metodo_pago ?: 'Efectivo';
        }

        // Revisar pagos registrados
        $pagos = $this->relationLoaded('pagos') ? $this->pagos : $this->pagos()->get();
        if ($pagos->isNotEmpty()) {
            $metodos = $pagos->pluck('metodo_pago')->filter()->unique();
            if ($metodos->count() === 1) {
                return $metodos->first();
            } elseif ($metodos->count() > 1) {
                return 'Mixto (' . $metodos->implode('/') . ')';
            }
        }

        // Si tuvo adelanto registrado
        if (!empty($this->metodo_adelanto) && floatval($this->monto_pagado ?? 0) > 0) {
            return $this->metodo_adelanto;
        }

        return 'Por Pagar';
    }

    /**
     * Determina si la venta ya no tiene deuda pendiente.
     */
    public function getEstaAlDiaAttribute(): bool
    {
        return floatval($this->saldo_pendiente ?? 0) <= 0.00;
    }
}
