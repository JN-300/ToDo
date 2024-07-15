<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::user() || !Auth::user()->isAdmin()){
            if ($request->wantsJson()) {
                return \response()->json('Forbidden', Response::HTTP_FORBIDDEN);
            }
            return abort(\Illuminate\Http\Response::HTTP_FORBIDDEN);
        }
        return $next($request);
    }
}
