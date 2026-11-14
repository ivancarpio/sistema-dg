<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Marca;
use App\Models\Unidad;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';
    protected $primaryKey = 'id';
    
    // Al poner esto en false, Laravel ya no buscará las columnas created_at y updated_at
    public $timestamps = false; 

    protected $casts = [
        'precio_venta'   => 'float',
        'precio_cliente' => 'float',
        'costo'          => 'float',
        'stock'          => 'integer',
    ];

    /**
     * Retorna el precio según el tipo de cliente / tarifa.
     * Si no tiene precio_cliente definido o es 0, retorna precio_venta.
     */
    public function getPrecioPara(?string $tipo = 'Ocasional'): float
    {
        if ($tipo && in_array(strtolower($tipo), ['regular', 'cliente', 'ferreteria', 'ferretería'])) {
            return ($this->precio_cliente !== null && $this->precio_cliente > 0)
                ? floatval($this->precio_cliente)
                : floatval($this->precio_venta);
        }
        return floatval($this->precio_venta);
    }

    // Puente hacia la tabla marcas
    public function marca()
    {
        return $this->belongsTo(Marca::class, 'id_marca');
    }

    // Puente hacia la tabla unidades
    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'id_unidad');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }
}