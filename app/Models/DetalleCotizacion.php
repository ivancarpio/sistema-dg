<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCotizacion extends Model
{
    use HasFactory;

    protected $table = 'detalle_cotizaciones';

    protected $fillable = [
        'id_cotizacion',
        'id_producto',
        'codigo_producto',
        'nombre_producto',
        'unidad_medida',
        'es_externo',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    protected $casts = [
        'es_externo' => 'boolean',
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class, 'id_cotizacion');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}
