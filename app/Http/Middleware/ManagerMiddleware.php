<?php
// Written & debugged by: Tech Ngoun Leang
// Tested & Debugged by: Tech Ngoun Leang
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ManagerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->hasManagerAccess()) {
            return $next($request);
        }

        return redirect('/')->with('error', 'Access denied. Manager permissions required.');
    }
}