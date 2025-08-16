<div class="container mx-auto px-4 py-8">
    <!-- Fil d'Ariane -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('catalogue.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <svg class="w-3 h-3 mr-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M19.707 9.293l-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L4 10.414V18a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                    </svg>
                    Accueil
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <a href="{{ route('catalogue.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Catalogue</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $tissu->nom }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Détails du produit -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="md:flex">
            <!-- Galerie d'images -->
            <div class="md:w-1/2 p-4">
                <!-- Image principale -->
                <div class="mb-4 bg-gray-100 rounded-lg overflow-hidden" style="height: 400px;">
                    @if($tissu->images->isNotEmpty())
                        <img 
                            src="{{ $tissu->images[$selectedImageIndex]->getUrl() }}" 
                            alt="{{ $tissu->nom }}" 
                            class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400">
                            <svg class="h-24 w-24" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                
                <!-- Vignettes -->
                @if($tissu->images->count() > 1)
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($tissu->images as $index => $image)
                            <button 
                                @click="selectImage({{ $index }})" 
                                class="h-20 border-2 rounded overflow-hidden {{ $selectedImageIndex === $index ? 'border-blue-500' : 'border-transparent' }}">
                                <img 
                                    src="{{ $image->getUrl('thumb') }}" 
                                    alt="{{ $tissu->nom }} - Vue {{ $index + 1 }}" 
                                    class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
            
            <!-- Informations du produit -->
            <div class="md:w-1/2 p-6">
                <!-- En-tête avec titre et catégorie -->
                <div class="mb-4">
                    <div class="flex justify-between items-start">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $tissu->nom }}</h1>
                        <button 
                            type="button" 
                            wire:click="toggleFavori"
                            class="ml-4 p-2 rounded-full {{ $estFavori ? 'text-red-500 hover:text-red-600' : 'text-gray-400 hover:text-gray-500' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            aria-label="{{ $estFavori ? 'Retirer des favoris' : 'Ajouter aux favoris' }}"
                        >
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="{{ $estFavori ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $tissu->quantite > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $tissu->quantite > 0 ? 'En stock' : 'Rupture' }}
                        </span>
                    </div>
                    <div class="text-indigo-600 text-sm mt-1">
                        {{ $tissu->categorie->nom ?? 'Non catégorisé' }}
                    </div>
                </div>
                
                <!-- Prix -->
                <div class="mt-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-3xl font-bold text-gray-900">{{ number_format($tissu->prix, 2, ',', ' ') }} €</span>
                        <span class="ml-2 text-sm text-gray-500">/ {{ $tissu->unite_mesure ?? 'unité' }}</span>
                    </div>
                    
                    <div class="md:hidden">
                        <button 
                            type="button" 
                            wire:click="toggleFavori"
                            class="p-2 rounded-full {{ $estFavori ? 'text-red-500 hover:text-red-600' : 'text-gray-400 hover:text-gray-500' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            aria-label="{{ $estFavori ? 'Retirer des favoris' : 'Ajouter aux favoris' }}"
                        >
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="{{ $estFavori ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Description -->
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-900">Description</h3>
                    <div class="mt-2 text-gray-600 prose max-w-none">
                        {!! nl2br(e($tissu->description)) !!}
                    </div>
                </div>
                
                <!-- Caractéristiques -->
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-900">Caractéristiques</h3>
                    <dl class="mt-2 grid grid-cols-2 gap-2 text-sm text-gray-600">
                        @if($tissu->matiere)
                            <div class="flex">
                                <dt class="font-medium text-gray-500 w-24">Matière</dt>
                                <dd>{{ $tissu->matiere }}</dd>
                            </div>
                        @endif
                        @if($tissu->largeur)
                            <div class="flex">
                                <dt class="font-medium text-gray-500 w-24">Largeur</dt>
                                <dd>{{ $tissu->largeur }} cm</dd>
                            </div>
                        @endif
                        @if($tissu->poids)
                            <div class="flex">
                                <dt class="font-medium text-gray-500 w-24">Poids</dt>
                                <dd>{{ $tissu->poids }} g/m²</dd>
                            </div>
                        @endif
                        @if($tissu->origine)
                            <div class="flex">
                                <dt class="font-medium text-gray-500 w-24">Origine</dt>
                                <dd>{{ $tissu->origine }}</dd>
                            </div>
                        @endif
                        @if($tissu->entretien)
                            <div class="col-span-2">
                                <dt class="font-medium text-gray-500">Entretien</dt>
                                <dd>{{ $tissu->entretien }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
                
                <!-- Formulaire d'ajout au panier -->
                <div class="mt-8">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="flex items-center border border-gray-300 rounded-md">
                            <button 
                                wire:click="decrementerQuantite"
                                class="px-3 py-2 text-gray-600 hover:bg-gray-100 rounded-l-md"
                                {{ $quantite <= 1 ? 'disabled' : '' }}>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                            <input 
                                type="number" 
                                min="1" 
                                max="{{ $tissu->quantite }}"
                                wire:model.live="quantite"
                                class="w-16 text-center border-0 focus:ring-0">
                            <button 
                                wire:click="incrementerQuantite"
                                class="px-3 py-2 text-gray-600 hover:bg-gray-100 rounded-r-md"
                                {{ $quantite >= $tissu->quantite ? 'disabled' : '' }}>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $tissu->quantite }} disponible(s)
                        </div>
                    </div>
                    
                    <button 
                        wire:click="ajouterAuPanier"
                        class="w-full bg-blue-600 text-white py-3 px-6 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex items-center justify-center space-x-2"
                        {{ $tissu->quantite <= 0 ? 'disabled' : '' }}>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Ajouter au panier</span>
                    </button>
                    
                    <div class="mt-4 flex items-center justify-center space-x-4">
                        <button class="text-gray-600 hover:text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <span>Ajouter aux favoris</span>
                        </button>
                        <button class="text-gray-600 hover:text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                            </svg>
                            <span>Partager</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Produits similaires -->
    @if($tissusSimilaires->isNotEmpty())
        <div class="mb-12">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Vous aimerez aussi</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($tissusSimilaires as $tissuSimilaire)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <a href="{{ route('catalogue.show', $tissuSimilaire->slug) }}" class="block">
                            <div class="h-48 bg-gray-100 overflow-hidden">
                                @if($tissuSimilaire->images->isNotEmpty())
                                    <img 
                                        src="{{ $tissuSimilaire->images->first()->getUrl() }}" 
                                        alt="{{ $tissuSimilaire->nom }}"
                                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400">
                                        <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="font-medium text-gray-900">{{ $tissuSimilaire->nom }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ $tissuSimilaire->categorie->nom ?? 'Non catégorisé' }}</p>
                                <div class="mt-2 flex justify-between items-center">
                                    <span class="font-bold text-gray-900">{{ number_format($tissuSimilaire->prix, 2, ',', ' ') }} €</span>
                                    <span class="text-xs {{ $tissuSimilaire->quantite > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $tissuSimilaire->quantite > 0 ? 'En stock' : 'Rupture' }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Script pour la galerie d'images
    document.addEventListener('livewire:initialized', () => {
        // Navigation au clavier pour la galerie d'images
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                // Flèche gauche - image précédente
                const prevIndex = Math.max(0, @this.selectedImageIndex - 1);
                if (prevIndex !== @this.selectedImageIndex) {
                    @this.selectImage(prevIndex);
                }
            } else if (e.key === 'ArrowRight') {
                // Flèche droite - image suivante
                const nextIndex = Math.min({{ $tissu->images->count() - 1 }}, @this.selectedImageIndex + 1);
                if (nextIndex !== @this.selectedImageIndex) {
                    @this.selectImage(nextIndex);
                }
            }
        });
        
        // Gestion du zoom sur l'image principale (au survol)
        const mainImage = document.querySelector('.main-image-container');
        if (mainImage) {
            mainImage.addEventListener('mousemove', function(e) {
                const { left, top, width, height } = this.getBoundingClientRect();
                const x = (e.clientX - left) / width * 100;
                const y = (e.clientY - top) / height * 100;
                this.style.backgroundPosition = `${x}% ${y}%`;
            });
            
            mainImage.addEventListener('mouseleave', function() {
                this.style.backgroundPosition = 'center';
            });
        }
    });
</script>
@endpush
