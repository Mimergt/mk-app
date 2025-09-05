<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Filament\Resources\GasolineraResource;
use Filament\Pages\Dashboard;

class RedirectFromDashboard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->route()->getName() === Dashboard::getRouteName()) {
            return redirect(GasolineraResource::getUrl('index'));
        }

        return $next($request);
    }
}
