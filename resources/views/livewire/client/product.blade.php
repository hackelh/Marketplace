<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg p-6">
            <div class="md:flex gap-6">
                <!-- Images -->
                <div class="md:w-1/2">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" 
                             alt="{{ $product->titre }}" 
                             class="w-full h-auto rounded-lg shadow-md mb-4">
                    @else
                        <div class="bg-gray-100 h-64 flex items-center justify-center rounded-lg">
                            <span class="text-gray-400">Aucune image disponible</span>
                        </div>
                    @endif
                </div>

                <!-- Détails -->
                <div class="md:w-1/2">
                    <h1 class="text-3xl font-bold mb-2">{{ $product->titre }}</h1>
                    
                    <div class="flex items-center mb-4">
                        <span class="text-2xl font-semibold text-indigo-600">
                            {{ number_format($product->prix, 2, ',', ' ') }} €
                        </span>
                        @if($product->quantite > 0)
                            <span class="ml-4 px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                En stock ({{ $product->quantite }})
                            </span>
                        @else
                            <span class="ml-4 px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">
                                Rupture de stock
                            </span>
                        @endif
                    </div>

                    @if($product->description)
                        <p class="mb-6 text-gray-700">{{ $product->description }}</p>
                    @endif

                    <!-- Informations complémentaires -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        @if($product->categorie)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Catégorie</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ $product->categorie->nom }}</p>
                            </div>
                        @endif
                        
                        @if($product->matiere)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Matière</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ $product->matiere }}</p>
                            </div>
                        @endif
                        
                        @if($product->couleur)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Couleur</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ $product->couleur }}</p>
                            </div>
                        @endif
                        
                        @if($product->vendeur)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Vendeur</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ $product->vendeur->name }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Bouton Ajouter au panier -->
                    @if($product->quantite > 0)
                        <button wire:click="addToCart({{ $product->id }})"
                                wire:loading.attr="disabled"
                                class="w-full bg-indigo-600 text-white py-3 px-6 rounded-md hover:bg-indigo-700">
                            <span wire:loading.remove wire:target="addToCart({{ $product->id }})">
                                Ajouter au panier
                            </span>
                            <span wire:loading wire:target="addToCart({{ $product->id }})">
                                Ajout en cours...
                            </span>
                        </button>
                    @else
                        <button disabled class="w-full bg-gray-400 text-white py-3 px-6 rounded-md cursor-not-allowed">
                            Produit indisponible
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        // Mise à jour du compteur de panier
        Livewire.on('panier-mis-a-jour', () => {
            const event = new CustomEvent('update-cart-count');
            window.dispatchEvent(event);
        });

        // Gestion des notifications
        Livewire.on('notify', (data) => {
            if (data.type === 'success') {
                toastr.success(data.message);
            } else if (data.type === 'error') {
                toastr.error(data.message);
            }
        });
    });
</script>
@endpush
