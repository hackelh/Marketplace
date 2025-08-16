<div>
    <!-- En-tête -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Mes favoris</h1>
                <div class="mt-4 md:mt-0">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        {{ $tissus->total() }} {{ Str::plural('article', $tissus->total()) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Filtres et recherche -->
        <div class="mb-6 bg-white p-4 rounded-lg shadow">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Champ de recherche -->
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input 
                            wire:model.live.debounce.300ms="search"
                            type="text" 
                            placeholder="Rechercher dans mes favoris..."
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        >
                    </div>
                </div>
                
                <!-- Bouton de filtres -->
                <div class="relative" x-data="{ open: false }">
                    <button 
                        @click="open = !open"
                        type="button"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 7.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                        </svg>
                        Filtres
                    </button>
                    
                    <!-- Panneau des filtres -->
                    <div 
                        x-show="open" 
                        @click.away="open = false"
                        class="origin-top-right absolute right-0 mt-2 w-72 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        style="display: none;"
                    >
                        <div class="p-4 space-y-4">
                            <!-- Catégorie -->
                            <div>
                                <label for="categorie" class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                                <select 
                                    id="categorie"
                                    wire:model.live="filters.categorie"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                >
                                    <option value="">Toutes les catégories</option>
                                    @foreach($categories as $categorie)
                                        <option value="{{ $categorie->id }}">{{ $categorie->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Couleur -->
                            <div>
                                <label for="couleur" class="block text-sm font-medium text-gray-700 mb-1">Couleur</label>
                                <select 
                                    id="couleur"
                                    wire:model.live="filters.couleur"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                >
                                    <option value="">Toutes les couleurs</option>
                                    @foreach($couleurs as $couleur)
                                        <option value="{{ $couleur }}" style="color: {{ $couleur }}">
                                            {{ ucfirst($couleur) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Prix -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="prix_min" class="block text-sm font-medium text-gray-700 mb-1">Prix min (€)</label>
                                    <input 
                                        type="number" 
                                        id="prix_min" 
                                        wire:model.live.debounce.500ms="filters.prix_min"
                                        min="0" 
                                        step="0.01"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                        placeholder="Min"
                                    >
                                </div>
                                <div>
                                    <label for="prix_max" class="block text-sm font-medium text-gray-700 mb-1">Prix max (€)</label>
                                    <input 
                                        type="number" 
                                        id="prix_max" 
                                        wire:model.live.debounce.500ms="filters.prix_max"
                                        min="0" 
                                        step="0.01"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                        placeholder="Max"
                                    >
                                </div>
                            </div>
                            
                            <!-- Boutons d'action -->
                            <div class="flex justify-between pt-2">
                                <button 
                                    type="button" 
                                    wire:click="resetFilters"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                >
                                    Réinitialiser
                                </button>
                                <button 
                                    type="button" 
                                    @click="open = false"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                >
                                    Appliquer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Trier par -->
                <div class="flex-shrink-0">
                    <select 
                        wire:model.live="sortField"
                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                    >
                        <option value="created_at">Date d'ajout (plus récent)</option>
                        <option value="prix">Prix croissant</option>
                        <option value="prix_desc">Prix décroissant</option>
                        <option value="nom">Nom (A-Z)</option>
                    </select>
                </div>
            </div>
            
            <!-- Filtres actifs -->
            @if($filters['categorie'] || $filters['couleur'] || $filters['prix_min'] || $filters['prix_max'])
                <div class="mt-4 flex flex-wrap gap-2">
                    @if($filters['categorie'])
                        @php $categorie = \App\Models\Categorie::find($filters['categorie']); @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Catégorie: {{ $categorie->nom ?? 'Inconnue' }}
                            <button type="button" wire:click="$set('filters.categorie', null)" class="ml-1.5 inline-flex items-center justify-center h-4 w-4 rounded-full bg-blue-200 text-blue-600 hover:bg-blue-300">
                                <span class="sr-only">Supprimer le filtre</span>
                                <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                                    <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7" />
                                </svg>
                            </button>
                        </span>
                    @endif
                    
                    @if($filters['couleur'])
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Couleur: {{ ucfirst($filters['couleur']) }}
                            <button type="button" wire:click="$set('filters.couleur', null)" class="ml-1.5 inline-flex items-center justify-center h-4 w-4 rounded-full bg-blue-200 text-blue-600 hover:bg-blue-300">
                                <span class="sr-only">Supprimer le filtre</span>
                                <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                                    <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7" />
                                </svg>
                            </button>
                        </span>
                    @endif
                    
                    @if($filters['prix_min'])
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            À partir de {{ number_format($filters['prix_min'], 2, ',', ' ') }} €
                            <button type="button" wire:click="$set('filters.prix_min', null)" class="ml-1.5 inline-flex items-center justify-center h-4 w-4 rounded-full bg-blue-200 text-blue-600 hover:bg-blue-300">
                                <span class="sr-only">Supprimer le filtre</span>
                                <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                                    <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7" />
                                </svg>
                            </button>
                        </span>
                    @endif
                    
                    @if($filters['prix_max'])
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Jusqu'à {{ number_format($filters['prix_max'], 2, ',', ' ') }} €
                            <button type="button" wire:click="$set('filters.prix_max', null)" class="ml-1.5 inline-flex items-center justify-center h-4 w-4 rounded-full bg-blue-200 text-blue-600 hover:bg-blue-300">
                                <span class="sr-only">Supprimer le filtre</span>
                                <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                                    <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7" />
                                </svg>
                            </button>
                        </span>
                    @endif
                    
                    <button 
                        type="button" 
                        wire:click="resetFilters"
                        class="text-xs text-blue-600 hover:text-blue-800 font-medium"
                    >
                        Tout effacer
                    </button>
                </div>
            @endif
        </div>

        <!-- Liste des favoris -->
        @if($tissus->count() > 0)
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul role="list" class="divide-y divide-gray-200">
                    @foreach($tissus as $tissu)
                        <li>
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-20 w-20 rounded-md overflow-hidden border border-gray-200">
                                            @if($tissu->images->isNotEmpty())
                                                <img 
                                                    src="{{ $tissu->images->first()->getUrl('thumb') }}" 
                                                    alt="{{ $tissu->nom }}" 
                                                    class="h-full w-full object-cover"
                                                >
                                            @else
                                                <div class="h-full w-full bg-gray-100 flex items-center justify-center text-gray-400">
                                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4
                                        ">
                                            <h3 class="text-lg font-medium text-gray-900">
                                                <a href="{{ route('catalogue.show', $tissu->slug) }}" class="hover:text-blue-600">
                                                    {{ $tissu->nom }}
                                                </a>
                                            </h3>
                                            <p class="text-sm text-gray-500">
                                                {{ $tissu->categorie->nom ?? 'Non catégorisé' }}
                                                @if($tissu->couleur)
                                                    • <span class="capitalize">{{ $tissu->couleur }}</span>
                                                @endif
                                            </p>
                                            <p class="mt-1 text-sm font-medium text-gray-900">
                                                {{ number_format($tissu->prix, 2, ',', ' ') }} €
                                                <span class="text-gray-500 text-xs">/ {{ $tissu->unite_mesure ?? 'unité' }}</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span class="text-sm text-gray-500">
                                            Ajouté le {{ $tissu->pivot->created_at->format('d/m/Y') }}
                                        </span>
                                        <div class="flex space-x-2">
                                            <a 
                                                href="{{ route('catalogue.show', $tissu->slug) }}" 
                                                class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                            >
                                                Voir
                                            </a>
                                            <button 
                                                type="button" 
                                                wire:click="supprimerFavori({{ $tissu->id }})"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                            >
                                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                                Retirer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            
            <!-- Pagination -->
            <div class="mt-4">
                {{ $tissus->links() }}
            </div>
        @else
            <!-- État vide -->
            <div class="text-center bg-white py-16 px-4 rounded-lg shadow">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">Aucun favori pour le moment</h3>
                <p class="mt-1 text-sm text-gray-500">Commencez par ajouter des tissus à vos favoris pour les retrouver facilement.</p>
                <div class="mt-6">
                    <a 
                        href="{{ route('catalogue.index') }}" 
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Parcourir le catalogue
                    </a>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Script pour gérer les interactions -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Gestion des notifications
            Livewire.on('notify', (event) => {
                // Implémentez ici votre système de notification
                // Par exemple, avec Toastr, SweetAlert2, ou une solution personnalisée
                console.log(event);
                alert(event.message);
            });
        });
    </script>
</div>
