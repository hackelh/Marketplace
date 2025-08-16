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
        return view('livewire.pages.admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/admin/utilisateurs', function () {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Accès non autorisé');
        }
        return view('livewire.pages.admin.utilisateurs');
    })->name('admin.utilisateurs');

    Route::get('/admin/categories', function () {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Accès non autorisé');
        }
        return view('livewire.pages.admin.categories');
    })->name('admin.categories');

    Route::get('/admin/tissus', function () {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Accès non autorisé');
        }
        return view('livewire.pages.admin.tissus');
    })->name('admin.tissus');

    Route::get('/admin/commandes', function () {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Accès non autorisé');
        }
        return view('livewire.pages.admin.commandes');
    })->name('admin.commandes');

    Route::get('/admin/statistiques', function () {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Accès non autorisé');
        }
        return view('livewire.pages.admin.statistiques');
    })->name('admin.statistiques');
    
    Route::get('/vendeur/dashboard', function () {
        // Vérifier que l'utilisateur est bien vendeur
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        return view('livewire.pages.vendeur.dashboard');
    })->name('vendeur.dashboard');
    
    
    Route::get('/vendeur/categories', function () {
        // Vérifier que l'utilisateur est bien vendeur
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        return view('livewire.pages.vendeur.gestion-categories');
    })->name('vendeur.categories');
    
    Route::get('/vendeur/ajouter-tissu', function () {
        // Vérifier que l'utilisateur est bien vendeur
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        return view('livewire.pages.vendeur.ajouter-tissu');
    })->name('vendeur.ajouter-tissu');
    
    Route::post('/vendeur/ajouter-tissu', function () {
        // Vérifier que l'utilisateur est bien vendeur
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        
        // Validation des données
        $validated = request()->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'couleur' => 'required|string|max:255',
            'categorie_id' => 'required|exists:categories,id',
            'origine' => 'nullable|string|max:255',
            'composition' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'disponible' => 'boolean',
        ]);
        
        // Traitement de l'image si fournie
        if (request()->hasFile('image')) {
            $imagePath = request()->file('image')->store('tissus', 'public');
            $validated['image'] = $imagePath;
        }
        
        // Ajout de l'utilisateur connecté
        $validated['user_id'] = auth()->id();
        $validated['disponible'] = request()->has('disponible');
        
        // Création du tissu
        \App\Models\Tissu::create($validated);
        
        return redirect()->route('vendeur.gestion-tissus')->with('success', 'Tissu ajouté avec succès !');
    })->name('vendeur.ajouter-tissu.store');
    
    Route::get('/vendeur/pays', function () {
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        return view('livewire.pages.vendeur.pays');
    })->name('vendeur.pays');

    Route::get('/vendeur/couleurs', function () {
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        return view('livewire.pages.vendeur.couleurs');
    })->name('vendeur.couleurs');
    
    Route::get('/vendeur/ajouter-categorie', function () {
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        return view('livewire.pages.vendeur.ajouter-categorie');
    })->name('vendeur.ajouter-categorie');
    
    Route::post('/vendeur/ajouter-categorie', function () {
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        
        $validated = request()->validate([
            'nom' => 'required|string|max:255|unique:categories,nom',
            'description' => 'nullable|string',
            'couleur_hex' => 'nullable|string|max:7',
        ]);
        
        \App\Models\Categorie::create($validated);
        
        return redirect()->route('vendeur.categories')->with('success', 'Catégorie ajoutée avec succès !');
    })->name('vendeur.ajouter-categorie.store');
    
    Route::get('/vendeur/gestion-tissus', function () {
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        return view('livewire.pages.vendeur.gestion-tissus');
    })->name('vendeur.gestion-tissus');
    
    Route::get('/vendeur/statistiques', function () {
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        return view('livewire.pages.vendeur.statistiques');
    })->name('vendeur.statistiques');
    
    // Routes pour les actions sur les tissus
    Route::get('/vendeur/tissu/{id}', function ($id) {
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        
        $tissu = \App\Models\Tissu::with('categorie')->where('user_id', auth()->id())->findOrFail($id);
        return view('livewire.pages.vendeur.tissu-show', compact('tissu'));
    })->name('vendeur.tissu.show');
    
    Route::get('/vendeur/tissu/{id}/edit', function ($id) {
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        
        $tissu = \App\Models\Tissu::where('user_id', auth()->id())->findOrFail($id);
        $categories = \App\Models\Categorie::all();
        return view('livewire.pages.vendeur.tissu-edit', compact('tissu', 'categories'));
    })->name('vendeur.tissu.edit');
    
    Route::post('/vendeur/tissu/{id}/update', function ($id) {
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        
        $tissu = \App\Models\Tissu::where('user_id', auth()->id())->findOrFail($id);
        
        $validated = request()->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'couleur' => 'required|string|max:255',
            'categorie_id' => 'required|exists:categories,id',
            'origine' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'disponible' => 'boolean',
        ]);
        
        if (request()->hasFile('image')) {
            $imagePath = request()->file('image')->store('tissus', 'public');
            $validated['image'] = $imagePath;
        }
        
        $validated['disponible'] = request()->has('disponible');
        
        $tissu->update($validated);
        
        return redirect()->route('vendeur.gestion-tissus')->with('success', 'Tissu modifié avec succès !');
    })->name('vendeur.tissu.update');
    
    Route::delete('/vendeur/tissu/{id}', function ($id) {
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        
        $tissu = \App\Models\Tissu::where('user_id', auth()->id())->findOrFail($id);
        $tissu->delete();
        
        return redirect()->route('vendeur.gestion-tissus')->with('success', 'Tissu supprimé avec succès !');
    })->name('vendeur.tissu.delete');
    
    Route::post('/vendeur/tissu/{id}/add-metres', function ($id) {
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        
        $validated = request()->validate([
            'metre_ajout' => 'required|integer|min:1',
        ]);
        
        $tissu = \App\Models\Tissu::where('user_id', auth()->id())->findOrFail($id);
        $tissu->increment('stock', $validated['metre_ajout']);
        
        return redirect()->route('vendeur.gestion-tissus')->with('success', $validated['metre_ajout'] . ' mètres ajoutés au stock !');
    })->name('vendeur.tissu.add-metres');
    
    Route::get('/vendeur/commandes/en-cours', function () {
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        return view('livewire.pages.vendeur.commandes-en-cours');
    })->name('vendeur.commandes.en-cours');

    Route::get('/vendeur/commandes/valide', function () {
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        return view('livewire.pages.vendeur.commandes-valide');
    })->name('vendeur.commandes.valide');
    
    Route::get('/tailleur/dashboard', function () {
        // Vérifier que l'utilisateur est bien tailleur
        if (!auth()->user()->isTailleur()) {
            abort(403, 'Accès non autorisé');
        }
        return view('livewire.pages.tailleur.dashboard');
    })->name('tailleur.dashboard');
    
    Route::get('/client/dashboard', function () {
        // Vérifier que l'utilisateur est bien client
        if (!auth()->user()->isClient()) {
            abort(403, 'Accès non autorisé');
        }
        return view('livewire.pages.client.dashboard');
    })->name('client.dashboard');

    Route::get('/client/catalogue', function () {
        if (!auth()->user()->isClient()) {
            abort(403, 'Accès non autorisé');
        }
        return view('livewire.pages.client.catalogue');
    })->name('client.catalogue');
});
