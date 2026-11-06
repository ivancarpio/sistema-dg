<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CierreMesCheck extends Model
{
    use HasFactory;

    protected $table = 'cierre_mes_checks';

    protected $fillable = [
        'mes',
        'id_producto',
        'verificado',
        'id_usuario',
        'fecha_verificacion',
    ];

    protected $casts = [
        'verificado' => 'boolean',
        'fecha_verificacion' => 'datetime',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
