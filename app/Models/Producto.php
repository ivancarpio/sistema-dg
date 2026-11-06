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