<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Livewire\Client\PanierController;
use App\Livewire\Vendeur\TissuController;
use App\Livewire\Vendeur\OrderController;
use App\Livewire\Vendeur\Dashboard;

// Page d'accueil
Route::view('/', 'welcome');

// Authentification
require __DIR__.'/auth.php';

// Redirection après authentification
Route::get('/dashboard', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'vendeur'
            ? redirect()->route('vendeur.dashboard')
            : redirect()->route('client.dashboard');
    }
    return redirect('/');
})->middleware(['auth', 'verified'])->name('dashboard');

// Routes catalogue
Route::prefix('catalogue')->name('catalogue.')->group(function () {
    Route::get('/', \App\Livewire\Client\CatalogueController::class)->name('index');
    Route::get('/{tissu}', \App\Livewire\Client\ProductController::class)->name('show');
});

// Routes protégées
Route::middleware(['auth', 'verified'])->group(function () {
    // Redirection /client vers le tableau de bord client
    Route::get('/client', function () {
        return redirect()->route('client.dashboard');
    })->name('client');
    
    // Dashboard client
    Route::get('/client/dashboard', \App\Livewire\Client\Dashboard::class)->name('client.dashboard');
    
    // Espace vendeur
    Route::middleware(['role:vendeur'])->prefix('vendeur')->name('vendeur.')->group(function () {
        // Tableau de bord
        Route::get('/dashboard', \App\Livewire\Vendeur\Dashboard::class)->name('dashboard');
        
        // Gestion des tissus
        Route::get('/tissus', \App\Livewire\Vendeur\TissusIndex::class)->name('tissus.index');
        
        // Autres routes vendeur...
    });

    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes panier (Livewire)
    Route::get('/panier', PanierController::class)->name('panier.index');
    
    // Routes API pour les actions du panier (utilisées via Livewire)
    Route::prefix('panier')->name('panier.')->group(function () {
        Route::get('/count', [PanierController::class, 'count'])->name('count');
        Route::post('/ajouter', [PanierController::class, 'ajouter'])->name('ajouter');
        Route::post('/mettre-a-jour/{id}', [PanierController::class, 'mettreAJour'])->name('mettre-a-jour');
        Route::delete('/supprimer/{id}', [PanierController::class, 'supprimer'])->name('supprimer');
        Route::post('/vider', [PanierController::class, 'vider'])->name('vider');
        Route::post('/confirmer', [PanierController::class, 'confirmerCommande'])->name('confirmer');
    });

    // Routes des commandes client
    Route::prefix('commandes')->name('commandes.')->group(function () {
        Route::get('/', \App\Livewire\Client\OrderController::class)->name('index');
        Route::get('/{commande}', [\App\Livewire\Client\OrderController::class, 'show'])->name('show');
    });

    // Routes vendeur
    Route::prefix('vendeur')->name('vendeur.')->middleware('role:vendeur')->group(function () {
        Route::get('/dashboard', Dashboard::class)->name('dashboard');
        
        // Gestion des produits
        Route::get('/tissus', TissuController::class)->name('tissus.index');
        
        // Gestion des commandes
        Route::prefix('commandes')->name('commandes.')->group(function () {
            Route::get('/', OrderController::class)->name('index');
            Route::get('/{commande}', [OrderController::class, 'show'])->name('show');
            Route::post('/{commande}/statut', [OrderController::class, 'updateStatus'])->name('update-status');
        });
    });
});
