<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $table = 'detalle_ventas';
    protected $primaryKey = 'id';
    public $timestamps = false;

    // Le decimos que este detalle pertenece a un Producto específico
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}