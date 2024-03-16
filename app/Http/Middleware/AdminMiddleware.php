<?php

namespace App\Http\Middleware;

use Closure;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Verificar si el usuario tiene el rol de administrador
        if ($request->user() && $request->user()->hasRole('admin')) {
            return $next($request);
        }
        
        // Si el usuario no es administrador, redirigirlo o devolver un error
        return response()->json(['error' => 'Acceso no autorizado'], 403);
    }
}
