<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarGasolineraAsignada
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Si es admin, permitir acceso
        if ($user && $user->tipo_usuario === 'admin') {
            return $next($request);
        }
        
        // Si es operador, verificar que tenga gasolinera asignada
        if ($user && $user->tipo_usuario === 'operador' && !$user->gasolinera_id) {
            abort(403, 'No tienes una gasolinera asignada. Contacta al administrador.');
        }
        
        return $next($request);
    }
}
