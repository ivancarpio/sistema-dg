<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuraciones';
    protected $fillable = ['clave', 'valor', 'descripcion'];

    /**
     * Caché estática por petición HTTP para evitar múltiples consultas SQL a configuraciones.
     */
    private static array $cacheMemoria = [];
    private static bool $todosCargados = false;

    /**
     * Obtiene el valor de una configuración por su clave.
     */
    public static function get(string $clave, $default = null)
    {
        if (array_key_exists($clave, static::$cacheMemoria)) {
            return static::$cacheMemoria[$clave] !== null ? static::$cacheMemoria[$clave] : $default;
        }

        try {
            if (!static::$todosCargados) {
                static::$cacheMemoria = static::pluck('valor', 'clave')->toArray();
                static::$todosCargados = true;
                if (array_key_exists($clave, static::$cacheMemoria)) {
                    return static::$cacheMemoria[$clave] !== null ? static::$cacheMemoria[$clave] : $default;
                }
            }

            static::$cacheMemoria[$clave] = null;
            return $default;
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

        static::$cacheMemoria[$clave] = $registro->valor;

        return $registro;
    }

    public static function limpiarCacheMemoria(): void
    {
        static::$cacheMemoria = [];
        static::$todosCargados = false;
    }

    public static function paisOperacion(): string
    {
        return static::get('pais_operacion', 'BO') ?: 'BO';
    }

    public static function monedaSimbolo(): string
    {
        $pais = static::paisOperacion();
        return static::get('moneda_simbolo', $pais === 'PE' ? 'S/' : 'Bs.') ?: ($pais === 'PE' ? 'S/' : 'Bs.');
    }

    public static function monedaPrincipal(): string
    {
        $pais = static::paisOperacion();
        return static::get('moneda_principal', $pais === 'PE' ? 'PEN' : 'BOB') ?: ($pais === 'PE' ? 'PEN' : 'BOB');
    }

    public static function monedaSecundariaSimbolo(): string
    {
        return static::paisOperacion() === 'PE' ? 'Bs.' : 'S/';
    }
}
