<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;

    // Le decimos a Laravel el nombre exacto de la tabla
    protected $table = 'marcas';
    protected $primaryKey = 'id';
    public $timestamps = false;
}