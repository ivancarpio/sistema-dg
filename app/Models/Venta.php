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

    public function usuarioAnulacion()
    {
        return $this->belongsTo(User::class, 'id_usuario_anulacion');
    }

    public function pagos()
    {
        return $this->hasMany(PagoVenta::class, 'id_venta');
    }
}
