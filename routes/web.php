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

    // Routes Admin placeholders (à remplacer par vos pages réelles)
    Route::get('/admin/utilisateurs', function () {
        abort_unless(auth()->user()->isAdmin(), 403);
        return view('livewire.pages.admin.utilisateurs');
    })->name('admin.utilisateurs');

    Route::get('/admin/categories', function () {
        abort_unless(auth()->user()->isAdmin(), 403);
        return view('livewire.pages.admin.categories')
            ->with('title', 'Catégories');
    })->name('admin.categories');

    // Route désactivée: Tissus (Admin ne voit pas les tissus)
    // Route::get('/admin/tissus', function () {
    //     abort_unless(auth()->user()->isAdmin(), 403);
    //     return view('livewire.pages.admin.tissus')
    //         ->with('title', 'Tissus');
    // })->name('admin.tissus');

    // Route désactivée: Commandes (Admin ne voit pas les commandes)
    // Route::get('/admin/commandes', function () {
    //     abort_unless(auth()->user()->isAdmin(), 403);
    //     return view('livewire.pages.admin.commandes')
    //         ->with('title', 'Commandes');
    // })->name('admin.commandes');

    Route::get('/admin/statistiques', function () {
        abort_unless(auth()->user()->isAdmin(), 403);
        return view('livewire.pages.admin.statistiques')
            ->with('title', 'Statistiques');
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

        // Générer un nom par défaut (ex: Tissu-20250818-155200) et une description vide si absente
        $validated['nom'] = 'Tissu-' . now()->format('Ymd-His');
        $validated['description'] = $validated['description'] ?? '';
        
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

    Route::post('/vendeur/pays', function () {
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        $validated = request()->validate([
            'nom' => 'required|string|max:255|unique:pays,nom',
        ]);
        \App\Models\Pays::create($validated);
        return redirect()->route('vendeur.pays')->with('success', 'Pays ajouté avec succès !');
    })->name('vendeur.pays.store');

    Route::put('/vendeur/pays/{id}', function ($id) {
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        $pays = \App\Models\Pays::findOrFail($id);
        $validated = request()->validate([
            'nom' => 'required|string|max:255|unique:pays,nom,' . $pays->id,
        ]);
        $pays->update($validated);
        return redirect()->route('vendeur.pays')->with('success', 'Pays mis à jour avec succès !');
    })->name('vendeur.pays.update');

    Route::delete('/vendeur/pays/{id}', function ($id) {
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        $pays = \App\Models\Pays::findOrFail($id);
        $utilise = \App\Models\Tissu::where('origine', $pays->nom)->exists();
        if ($utilise) {
            return redirect()->route('vendeur.pays')->with('error', 'Impossible de supprimer: des tissus utilisent ce pays comme origine.');
        }
        $pays->delete();
        return redirect()->route('vendeur.pays')->with('success', 'Pays supprimé avec succès !');
    })->name('vendeur.pays.delete');

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
    
    // Mise à jour d'une catégorie (CRUD depuis la page gestion-categories)
    Route::put('/vendeur/categorie/{id}', function ($id) {
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        
        $categorie = \App\Models\Categorie::findOrFail($id);
        
        $validated = request()->validate([
            'nom' => 'required|string|max:255|unique:categories,nom,' . $categorie->id,
            'description' => 'nullable|string',
            'couleur_hex' => 'nullable|string|max:7',
        ]);
        
        $categorie->update($validated);
        
        return redirect()->route('vendeur.categories')->with('success', 'Catégorie mise à jour avec succès !');
    })->name('vendeur.categorie.update');

    // Suppression d'une catégorie (empêche la suppression si liée à des tissus)
    Route::delete('/vendeur/categorie/{id}', function ($id) {
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        
        $categorie = \App\Models\Categorie::withCount('tissus')->findOrFail($id);
        if ($categorie->tissus_count > 0) {
            return redirect()->route('vendeur.categories')->with('error', 'Impossible de supprimer: la catégorie est associée à des tissus.');
        }
        
        $categorie->delete();
        
        return redirect()->route('vendeur.categories')->with('success', 'Catégorie supprimée avec succès !');
    })->name('vendeur.categorie.delete');
    
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
    
    Route::put('/vendeur/tissu/{id}/update', function ($id) {
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
        $tissu->ajouterStock($validated['metre_ajout'], 'Ajout manuel', null, 'Ajout via interface vendeur');
        
        return redirect()->route('vendeur.gestion-tissus')->with('success', $validated['metre_ajout'] . ' mètres ajoutés au stock !');
    })->name('vendeur.tissu.add-metres');

    Route::get('/vendeur/tissu/{id}/historique', function ($id) {
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        
        return view('livewire.pages.vendeur.historique-tissu', ['tissuId' => $id]);
    })->name('vendeur.historique-tissu');
    
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
    
    // Détail d'une commande (vendeur)
    Route::get('/vendeur/commande/{id}', function ($id) {
        if (!auth()->user()->isVendeur()) {
            abort(403, 'Accès non autorisé');
        }
        
        $commande = \App\Models\Commande::with(['client', 'details.tissu'])
            ->where('vendeur_id', auth()->id())
            ->findOrFail($id);
        return view('livewire.pages.vendeur.commande-show', compact('commande'));
    })->name('vendeur.commande.show');

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
});
