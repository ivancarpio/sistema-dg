<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckInactivity
{
    /**
     * Tiempo límite de inactividad en segundos (15 minutos = 900 segundos).
     */
    protected int $timeout = 900;

    /**
     * Valida el tiempo de inactividad de la sesión del usuario.
     * Si supera el límite permitido, cierra la sesión de forma segura.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $lastActivity = session('last_activity_time');

            if ($lastActivity && (time() - $lastActivity) > $this->timeout) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson() || $request->isXmlHttpRequest()) {
                    return response()->json([
                        'expired' => true,
                        'message' => 'Sesión expirada por inactividad.',
                    ], 401);
                }

                return redirect()->route('login')->with('warning', 'Tu sesión se cerró automáticamente por inactividad (15 minutos sin uso). Por tu seguridad, vuelve a ingresar.');
            }

            // Actualizar la marca de tiempo de la última actividad
            session(['last_activity_time' => time()]);
        }

        return $next($request);
    }
}
