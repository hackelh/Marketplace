<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserNotBlocked
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ($user->is_blocked ?? false)) {
            // Déconnexion avec le guard de session (web)
            Auth::guard('web')->logout();

            // Optionnel: révoquer les jetons Sanctum si présents (API)
            if (method_exists($user, 'tokens')) {
                $user->tokens()->delete();
            }

            // Invalidation de la session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Afficher page d'erreur dédiée (403)
            return response()->view('errors.blocked', [], 403);
        }

        return $next($request);
    }
}