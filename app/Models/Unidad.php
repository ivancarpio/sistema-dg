<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    use HasFactory;

    // Le decimos a Laravel el nombre exacto de la tabla en español
    protected $table = 'unidades';
    protected $primaryKey = 'id';
    public $timestamps = false;
}