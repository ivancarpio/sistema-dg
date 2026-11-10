<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    use HasFactory;

    protected $table = 'unidades';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['nombre'];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_unidad');
    }
}
