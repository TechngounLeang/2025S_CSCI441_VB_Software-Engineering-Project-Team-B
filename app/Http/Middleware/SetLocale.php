<?php
// Written & debugged by: Tech Ngoun Leang & Ratanakvesal Thong
// Tested by: Tech Ngoun Leang
// Middleware for Language

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('locale')) {
            app()->setLocale(session('locale'));
        }
        
        return $next($request);
    }
}