<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Valida si el usuario autenticado tiene el permiso requerido para acceder a la ruta.
     * Los administradores tienen acceso irrestricto a todos los módulos.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permiso): Response
    {
        $user = $request->user();

        // Si no está autenticado, redirigir al login
        if (!$user) {
            return redirect()->route('login')->with('warning', 'Debes iniciar sesión para acceder al sistema.');
        }

        // Si es administrador, tiene acceso irrestricto
        if ($user->rol === 'admin') {
            return $next($request);
        }

        // Si es usuario con permisos asignados, validar el permiso específico
        if (!$user->tienePermiso($permiso)) {
            if ($request->expectsJson() || $request->isXmlHttpRequest()) {
                return response()->json([
                    'success' => false,
                    'error'   => 'Acceso denegado: No tienes autorización para realizar esta acción.',
                    'permiso' => $permiso,
                ], 403);
            }

            return redirect('/inicio')->with('error', 'Acceso denegado: Tu perfil no tiene permisos asignados para ingresar al módulo de ' . ucfirst($permiso) . '.');
        }

        return $next($request);
    }
}
