<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Marca;
use App\Models\Unidad;
use App\Models\Categoria;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';
    protected $primaryKey = 'id';
    
    // Al poner esto en false, Laravel ya no buscará las columnas created_at y updated_at
    public $timestamps = false; 

    protected $casts = [
        'precio_venta'              => 'float',
        'precio_cliente'            => 'float',
        'precio_venta_secundario'   => 'float',
        'precio_cliente_secundario' => 'float',
        'factor_conversion'         => 'float',
        'costo'                     => 'float',
        'stock'                     => 'integer',
        'id_unidad_secundaria'      => 'integer',
    ];

    /**
     * Retorna el precio según el tipo de cliente / tarifa para la unidad base.
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

    /**
     * Retorna el precio según el tipo de cliente / tarifa para la unidad secundaria.
     * Si no tiene precio_cliente_secundario definido o es 0, retorna precio_venta_secundario (o viceversa).
     */
    public function getPrecioSecundarioPara(?string $tipo = 'Ocasional'): float
    {
        $tipoNorm = strtolower($tipo ?? 'ocasional');
        $esRegular = in_array($tipoNorm, ['regular', 'cliente', 'ferreteria', 'ferretería']);

        $pv = ($this->precio_venta_secundario !== null && $this->precio_venta_secundario > 0) ? floatval($this->precio_venta_secundario) : 0.0;
        $pc = ($this->precio_cliente_secundario !== null && $this->precio_cliente_secundario > 0) ? floatval($this->precio_cliente_secundario) : 0.0;

        if ($esRegular) {
            return ($pc > 0) ? $pc : $pv;
        }

        return ($pv > 0) ? $pv : $pc;
    }

    /**
     * Indica si el producto tiene configurada una segunda unidad de medida.
     */
    public function tieneUnidadSecundaria(): bool
    {
        $tieneUnidad = !empty($this->id_unidad_secundaria);
        $tienePrecio = ($this->precio_venta_secundario !== null && $this->precio_venta_secundario > 0)
                    || ($this->precio_cliente_secundario !== null && $this->precio_cliente_secundario > 0);
        $tieneFactor = ($this->factor_conversion !== null && $this->factor_conversion > 1);

        return $tieneUnidad && ($tienePrecio || $tieneFactor);
    }

    // Puente hacia la tabla marcas
    public function marca()
    {
        return $this->belongsTo(Marca::class, 'id_marca');
    }

    // Puente hacia la tabla unidades (Unidad Principal / Menor)
    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'id_unidad');
    }

    // Puente hacia la tabla unidades (Unidad Secundaria / Mayor / Empaque)
    public function unidadSecundaria()
    {
        return $this->belongsTo(Unidad::class, 'id_unidad_secundaria');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }
}
