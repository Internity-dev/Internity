<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->status != 1) {
            $user->tokens()->delete();
            Auth::logout();
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Akun anda dinonaktifkan, silakan hubungi admin',
            ], 401);
        }

        return $next($request);
    }
}
