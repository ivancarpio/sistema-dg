<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'rol', 'ci', 'celular', 'fecha_nacimiento', 'fecha_ingreso', 'permisos'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'fecha_nacimiento'  => 'date',
            'fecha_ingreso'     => 'date',
            'permisos'          => 'array',
        ];
    }

    /**
     * Verifica si el usuario cuenta con un permiso específico en el sistema.
     * Los administradores tienen acceso irrestricto.
     */
    public function tienePermiso(string $permiso): bool
    {
        if ($this->rol === 'admin') {
            return true;
        }

        $permisos = $this->permisos;
        if (is_string($permisos)) {
            $permisos = json_decode($permisos, true) ?? [];
        }

        if (!is_array($permisos)) {
            return false;
        }

        return in_array($permiso, $permisos, true);
    }

    /**
     * Lista de módulos y permisos configurables en el sistema.
     */
    public static function catalogoPermisos(): array
    {
        return [
            'pos' => [
                'nombre' => 'Punto de Venta (POS)',
                'descripcion' => 'Permite registrar ventas de mostrador y cobros.',
                'icono' => 'bi-cart-check',
                'color' => 'primary',
            ],
            'productos' => [
                'nombre' => 'Catálogo de Productos',
                'descripcion' => 'Crear, editar productos, precios y stocks.',
                'icono' => 'bi-box-seam',
                'color' => 'info',
            ],
            'ventas' => [
                'nombre' => 'Historial de Ventas',
                'descripcion' => 'Consultar y reimprimir comprobantes de venta.',
                'icono' => 'bi-receipt',
                'color' => 'success',
            ],
            'anular_ventas' => [
                'nombre' => 'Anulación de Ventas',
                'descripcion' => 'Permiso especial para anular ventas y devolver stock.',
                'icono' => 'bi-slash-circle',
                'color' => 'danger',
            ],
            'arqueos' => [
                'nombre' => 'Arqueo de Caja',
                'descripcion' => 'Apertura, verificación de billetes y cierre de caja.',
                'icono' => 'bi-calculator',
                'color' => 'warning',
            ],
            'reportes' => [
                'nombre' => 'Reportes y Cierre de Mes',
                'descripcion' => 'Reportes de ventas por período y cierre físico de mes.',
                'icono' => 'bi-graph-up',
                'color' => 'primary',
            ],
            'inventario' => [
                'nombre' => 'Control de Inventario (Kardex)',
                'descripcion' => 'Bitácora de movimientos y ajustes manuales.',
                'icono' => 'bi-diagram-3',
                'color' => 'secondary',
            ],
            'clientes' => [
                'nombre' => 'Clientes Regulares',
                'descripcion' => 'Gestión y directorio de clientes frecuentes y crédito.',
                'icono' => 'bi-person-badge',
                'color' => 'info',
            ],
            'usuarios' => [
                'nombre' => 'Gestión de Usuarios',
                'descripcion' => 'Crear personal, asignar roles y configurar permisos.',
                'icono' => 'bi-people',
                'color' => 'dark',
            ],
            'configuracion' => [
                'nombre' => 'Configuración del Sistema',
                'descripcion' => 'Ajustes de marcas, unidades, categorías y QR.',
                'icono' => 'bi-gear',
                'color' => 'dark',
            ],
        ];
    }
}
