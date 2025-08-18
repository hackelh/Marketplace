<div class="min-h-screen bg-gray-50">
    {{-- Nothing in the world is as soft and yielding as water. --}}
    <div>
        <!-- En-tête avec statistiques -->
        <div class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Gestion des Catégories</h1>
                        <p class="text-gray-600">Organisez vos tissus par catégories</p>
                    </div>
                    <button type="button" onclick="document.getElementById('form-create').classList.toggle('hidden')"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Nouvelle Catégorie
                    </button>
                </div>

                <!-- Statistiques (style dashboard small-box) -->
                @php
                    $topCat = \App\Models\Categorie::withCount('tissus')->orderByDesc('tissus_count')->first();
                @endphp
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-info">
                            <div class="inner">
                                <h3>{{ \App\Models\Categorie::count() }}</h3>
                                <p>Total Catégories</p>
                            </div>
                            <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 0 1 0 2.828l-7 7a2 2 0 0 1-2.828 0l-7-7A1.994 1.994 0 0 1 3 12V7a4 4 0 0 1 4-4z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-success">
                            <div class="inner">
                                <h3>{{ \App\Models\Categorie::whereHas('tissus')->count() }}</h3>
                                <p>Catégories utilisées</p>
                            </div>
                            <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-primary">
                            <div class="inner">
                                <h3>{{ \App\Models\Tissu::count() }}</h3>
                                <p>Total Tissus</p>
                            </div>
                            <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M3 7h18M3 12h18M3 17h18"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-warning">
                            <div class="inner">
                                <h3>{{ $topCat? $topCat->tissus_count : 0 }}</h3>
                                <p>Top Catégorie {{ $topCat? '— '.$topCat->nom : '' }}</p>
                            </div>
                            <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M8 7V3m8 4V3M5 21h14a2 2 0 0 0 2-2V7H3v12a2 2 0 0 0 2 2zm3-8h8"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Formulaire création catégorie -->
                <div id="form-create" class="mt-6 bg-white border rounded-lg p-6 hidden">
                    <form action="{{ route('vendeur.ajouter-categorie.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nom *</label>
                            <input type="text" name="nom" required class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Ex: Coton" value="{{ old('nom') }}">
                            @error('nom')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Description de la catégorie...">{{ old('description') }}</textarea>
                            @error('description')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="md:col-span-3 flex justify-end gap-2">
                            <button type="button" onclick="document.getElementById('form-create').classList.add('hidden')" class="px-4 py-2 border rounded-md">Annuler</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Créer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Messages flash -->
        @if (session()->has('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Contenu principal -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Filtres -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                        <input type="text" id="recherche" placeholder="Nom ou description..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Trier par</label>
                        <select id="tri-select" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="recent">Plus récentes</option>
                            <option value="nom">Nom (A-Z)</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button onclick="resetFilters()" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Réinitialiser
                        </button>
                    </div>
                </div>
            </div>

            <!-- Liste des catégories -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Liste des Catégories</h3>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse(\App\Models\Categorie::withCount('tissus')->get() as $categorie)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full" style="background-color: {{ $categorie->couleur_hex ?: '#3B82F6' }}"></div>
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-900">{{ $categorie->nom }}</h3>
                                            <p class="text-sm text-gray-500">{{ $categorie->tissus_count ?? 0 }} tissus</p>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button type="button" onclick="toggleEdit('{{ $categorie->id }}')" aria-label="Modifier" title="Modifier" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-indigo-200 text-indigo-600 hover:bg-indigo-50 hover:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form action="{{ route('vendeur.categorie.delete', $categorie->id) }}" method="POST" onsubmit="return confirm('Supprimer cette catégorie ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" aria-label="Supprimer" title="Supprimer" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 focus:outline-none focus:ring-2 focus:ring-red-400/50">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                @if($categorie->description)
                                    <div class="mt-3">
                                        <p class="text-sm text-gray-600">{{ Str::limit($categorie->description, 100) }}</p>
                                    </div>
                                @endif
                                
                                <div class="mt-4 flex items-center justify-between">
                                    <span class="text-xs text-gray-500">
                                        Créée le {{ $categorie->created_at->format('d/m/Y') }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $categorie->tissus_count ?? 0 }} tissus
                                    </span>
                                </div>
+
                                <!-- Formulaire d'édition (masqué par défaut) -->
                                <div id="edit-{{ $categorie->id }}" class="mt-4 hidden border-t pt-4">
                                    <form action="{{ route('vendeur.categorie.update', $categorie->id) }}" method="POST" class="grid grid-cols-1 gap-3">
                                        @csrf
                                        @method('PUT')
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                                                <input type="text" name="nom" value="{{ old('nom', $categorie->nom) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Couleur</label>
                                                <input type="color" name="couleur_hex" value="{{ old('couleur_hex', $categorie->couleur_hex ?? '#3B82F6') }}" class="w-full h-10 border border-gray-300 rounded-md">
                                            </div>
                                            <div class="md:col-span-3">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                                <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md">{{ old('description', $categorie->description) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <button type="button" onclick="toggleEdit('{{ $categorie->id }}')" class="px-3 py-2 border rounded-md">Annuler</button>
                                            <button type="submit" class="px-3 py-2 bg-indigo-600 text-white rounded-md">Enregistrer</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune catégorie</h3>
                                <p class="mt-1 text-sm text-gray-500">Commencez par créer votre première catégorie.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('form-create').classList.toggle('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Créer une nouvelle catégorie</button>
            </div>
        </div>
    </div>
</div>

<script>
function resetFilters() {
    document.getElementById('recherche').value = '';
    document.getElementById('tri-select').value = 'recent';
}

function toggleEdit(id) {
    const el = document.getElementById('edit-' + id);
    if (el) el.classList.toggle('hidden');
}
</script>
