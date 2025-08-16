<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Notre catalogue de tissus</h1>
        <p class="text-gray-600">Découvrez notre sélection de tissus de qualité</p>
    </div>

    <div class="flex flex-col md:flex-row gap-8">
        <!-- Filtres -->
        <div class="w-full md:w-1/4">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold mb-4">Filtres</h2>
                <!-- Recherche -->
                <div class="mb-6">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                    <input type="text" id="search" wire:model.live.debounce.500ms="search"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Rechercher un tissu...">
                </div>
                <!-- Catégories -->
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Catégories</h3>
                    <select wire:model.live="categorie"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $categorie)
                            <option value="{{ $categorie->id }}">{{ $categorie->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Prix -->
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Prix</h3>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="minPrice" class="block text-xs text-gray-500 mb-1">Min (FCFA)</label>
                            <input type="number" id="minPrice" wire:model.live.debounce.500ms="minPrice" min="0"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                placeholder="Min">
                        </div>
                        <div>
                            <label for="maxPrice" class="block text-xs text-gray-500 mb-1">Max (FCFA)</label>
                            <input type="number" id="maxPrice" wire:model.live.debounce.500ms="maxPrice" min="0"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                placeholder="Max">
                        </div>
                    </div>
                </div>
                <button wire:click="resetFilters"
                    class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-200 transition-colors">
                    Réinitialiser les filtres
                </button>
            </div>
        </div>
        
        <!-- Liste des tissus -->
        <div class="w-full md:w-3/4">
            <!-- Tri et nombre de résultats -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                <p class="text-sm text-gray-600 mb-4 sm:mb-0">
                    {{ $tissus->total() }} {{ $tissus->total() > 1 ? 'résultats' : 'résultat' }}
                </p>
                
                <div class="flex items-center">
                    <span class="text-sm text-gray-600 mr-2">Trier par :</span>
                    <select 
                        wire:model.live="sortBy"
                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="prix">Prix croissant</option>
                        <option value="prix,desc">Prix décroissant</option>
                        <option value="nom">Nom (A-Z)</option>
                        <option value="nom,desc">Nom (Z-A)</option>
                        <option value="created_at,desc">Nouveautés</option>
                    </select>
                </div>
            </div>
            
            <!-- Grille de produits -->
            @if($tissus->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($tissus as $tissu)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                            <a href="{{ route('catalogue.show', $tissu->slug ?? $tissu->id) }}" class="block">
                                <div class="h-48 bg-gray-100 overflow-hidden">
                                    @if($tissu->image)
                                        <img 
                                            src="{{ asset('storage/' . $tissu->image) }}" 
                                            alt="{{ $tissu->titre }}"
                                            class="w-full h-full object-cover"
                                            onerror="this.onerror=null; this.src='{{ asset('images/placeholder.svg') }}';">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400">
                                            <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <h3 class="font-medium text-gray-900">{{ $tissu->titre }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ $tissu->categorie->nom ?? 'Non catégorisé' }}</p>
                                    <div class="mt-2 flex justify-between items-center">
                                        <span class="font-bold text-gray-900">{{ \App\Helpers\PriceHelper::convertAndFormat($tissu->prix) }}</span>
                                        <span class="text-sm {{ $tissu->quantite > 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $tissu->quantite > 0 ? 'En stock' : 'Rupture' }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                            <div class="px-4 pb-4">
                                <button 
                                    wire:click="addToCart({{ $tissu->id }})"
                                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors flex items-center justify-center"
                                    {{ $tissu->quantite <= 0 ? 'disabled' : '' }}>
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Ajouter au panier
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-8">
                    {{ $tissus->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">Aucun résultat</h3>
                    <p class="mt-1 text-gray-500">Aucun tissu ne correspond à vos critères de recherche.</p>
                    <div class="mt-6">
                        <button 
                            wire:click="resetFilters"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Réinitialiser les filtres
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
