<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Où rediriger les utilisateurs après la connexion.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo(Request $request)
    {
        // Vérifier le rôle de l'utilisateur connecté
        if (Auth::check()) {
            if (Auth::user()->role === 'vendeur') {
                return route('vendeur.dashboard');
            } else {
                // Rediriger vers le catalogue pour les clients
                return route('catalogue.index');
            }
        }
        
        // Par défaut, rediriger vers la page d'accueil
        return '/';
    }
}
