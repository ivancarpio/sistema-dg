<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    use HasFactory;

    protected $table = 'gastos';

    protected $fillable = [
        'fecha',
        'categoria_gasto_id',
        'descripcion',
        'monto',
        'origen_pago', // 'gaveta', 'caja_chica', 'banco_qr', 'gerencia'
        'turno_caja_id',
        'movimiento_caja_id',
        'nro_comprobante',
        'observacion',
        'user_id',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'monto' => 'decimal:2',
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaGasto::class, 'categoria_gasto_id');
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

    public function movimientoCajaChica()
    {
        return $this->hasOne(CajaChicaMovimiento::class, 'gasto_id');
    }
}
