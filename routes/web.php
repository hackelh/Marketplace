<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Route principale qui redirige selon le rôle
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        // Redirection selon le rôle de l'utilisateur
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'vendeur':
                return redirect()->route('vendeur.dashboard');
            case 'tailleur':
                return redirect()->route('tailleur.dashboard');
            case 'client':
            default:
                return redirect()->route('client.dashboard');
        }
    })->name('dashboard');
    
    // Routes spécifiques pour chaque rôle
    Route::get('/admin/dashboard', function () {
        // Vérifier que l'utilisateur est bien admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Accès non autorisé');
        }
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    Route::get('/vendeur/dashboard', function () {
        // Vérifier que l'utilisateur est bien vendeur
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        return view('vendeur.dashboard');
    })->name('vendeur.dashboard');
    
    Route::get('/vendeur/dashboard/tissus', function () {
        // Vérifier que l'utilisateur est bien vendeur
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        return view('vendeur.tissus');
    })->name('vendeur.tissus');
    
    Route::get('/tailleur/dashboard', function () {
        // Vérifier que l'utilisateur est bien tailleur
        if (!auth()->user()->isTailleur()) {
            abort(403, 'Accès non autorisé');
        }
        return view('tailleur.dashboard');
    })->name('tailleur.dashboard');
    
    Route::get('/client/dashboard', function () {
        // Vérifier que l'utilisateur est bien client
        if (!auth()->user()->isClient()) {
            abort(403, 'Accès non autorisé');
        }
        return view('client.dashboard');
    })->name('client.dashboard');
});
