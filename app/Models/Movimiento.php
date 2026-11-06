<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    use HasFactory;

    protected $table = 'movimientos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    // NUEVA RELACIÓN: Un movimiento pertenece a un Usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}