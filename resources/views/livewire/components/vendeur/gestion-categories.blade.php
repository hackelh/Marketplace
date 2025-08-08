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
                    <a href="{{ route('vendeur.ajouter-categorie', false) ?? '#' }}" 
                       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Ajouter une Catégorie
                    </a>
                </div>

                <!-- Statistiques -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-blue-600">Total Catégories</p>
                                <p class="text-2xl font-semibold text-blue-900">{{ \App\Models\Categorie::count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-green-600">Catégories Utilisées</p>
                                <p class="text-2xl font-semibold text-green-900">{{ \App\Models\Categorie::whereHas('tissus')->count() }}</p>
                            </div>
                        </div>
                    </div>
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
                                        <button onclick="modifierCategorie('{{ $categorie->id }}')"
                                                class="text-indigo-600 hover:text-indigo-900 p-1 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button onclick="supprimerCategorie('{{ $categorie->id }}')"
                                                class="text-red-600 hover:text-red-900 p-1 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
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
    </div>
</div>

<script>
function resetFilters() {
    document.getElementById('recherche').value = '';
    document.getElementById('tri-select').value = 'recent';
}

function modifierCategorie(id) {
    // Ici vous pouvez ajouter la logique pour modifier une catégorie
    console.log('Modifier catégorie:', id);
}

function supprimerCategorie(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')) {
        // Ici vous pouvez ajouter une requête AJAX pour supprimer la catégorie
        console.log('Suppression catégorie:', id);
    }
}
</script>
