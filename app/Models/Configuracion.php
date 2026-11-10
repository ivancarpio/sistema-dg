<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuraciones';
    protected $fillable = ['clave', 'valor', 'descripcion'];

    /**
     * Obtiene el valor de una configuración por su clave.
     */
    public static function get(string $clave, $default = null)
    {
        try {
            $registro = static::where('clave', $clave)->first();
            return $registro !== null ? $registro->valor : $default;
        } catch (\Throwable $th) {
            return $default;
        }
    }

    /**
     * Establece o actualiza el valor de una configuración.
     */
    public static function set(string $clave, $valor, ?string $descripcion = null): static
    {
        $registro = static::firstOrNew(['clave' => $clave]);
        $registro->valor = $valor !== null ? (string)$valor : null;
        if ($descripcion !== null) {
            $registro->descripcion = $descripcion;
        }
        $registro->save();

        return $registro;
    }
}
