<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'telefono',
        'ci_nit',
        'direccion',
        'email',
        'notas',
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_cliente');
    }

    public function getTotalCompradoAttribute(): float
    {
        return (float) $this->ventas()
            ->where('estado', '!=', 'Anulada')
            ->sum('total');
    }

    public function getSaldoPendienteTotalAttribute(): float
    {
        return (float) $this->ventas()
            ->where('estado', '!=', 'Anulada')
            ->where('saldo_pendiente', '>', 0)
            ->sum('saldo_pendiente');
    }

    public function pagos()
    {
        return $this->hasMany(PagoVenta::class, 'id_cliente');
    }

    public function getCantidadComprasAttribute(): int
    {
        return $this->ventas()
            ->where('estado', '!=', 'Anulada')
            ->count();
    }
}
