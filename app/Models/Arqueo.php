<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arqueo extends Model
{
    use HasFactory;

    protected $table = 'arqueos';

    protected $fillable = [
        'id_usuario',
        'total_efectivo',
        'total_qr',
        'total_general',
        'cantidad_ventas',
        'observaciones',
    ];

    /**
     * El usuario (cajero) que realizó el arqueo.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    /**
     * Las ventas incluidas en este arqueo.
     */
    public function ventas()
    {
        return $this->belongsToMany(Venta::class, 'arqueo_venta', 'arqueo_id', 'venta_id');
    }
}
