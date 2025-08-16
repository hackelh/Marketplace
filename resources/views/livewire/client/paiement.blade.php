<div class="container mx-auto px-4 py-6">
    <a href="{{ route('catalogue.index') }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 mb-6">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Retour au catalogue
    </a>

    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Paiement sécurisé</h1>
            
            <!-- Détails de la commande -->
            <div class="mb-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Récapitulatif de la commande</h2>
                <div class="bg-gray-50 p-4 rounded-lg">
                    @foreach($panier as $item)
                        <div class="flex justify-between py-2">
                            <span class="text-gray-700">{{ $item['titre'] }} x{{ $item['quantite'] }}</span>
                            <span class="font-medium">{{ \App\Helpers\PriceHelper::convertAndFormat($item['prix'] * $item['quantite']) }}</span>
                        </div>
                    @endforeach
                    <div class="border-t border-gray-200 mt-3 pt-3">
                        <div class="flex justify-between font-medium text-gray-900">
                            <span>Total</span>
                            <span class="font-bold">{{ \App\Helpers\PriceHelper::convertAndFormat($total) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Formulaire de paiement -->
            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-4">Informations de paiement</h2>
                <form wire:submit.prevent="validerPaiement" class="space-y-6">
                    <!-- Section Adresse de livraison -->
                    <div class="bg-gray-50 p-6 rounded-lg mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Adresse de livraison</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nom -->
                            <div class="col-span-1">
                                <label for="nom" class="block text-sm font-medium text-gray-700">Nom *</label>
                                <input type="text" id="nom" wire:model="adresseLivraison.nom" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @error('adresseLivraison.nom') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            
                            <!-- Prénom -->
                            <div class="col-span-1">
                                <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom *</label>
                                <input type="text" id="prenom" wire:model="adresseLivraison.prenom" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @error('adresseLivraison.prenom') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            
                            <!-- Email -->
                            <div class="col-span-1">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                                <input type="email" id="email" wire:model="adresseLivraison.email" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @error('adresseLivraison.email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            
                            <!-- Téléphone -->
                            <div class="col-span-1">
                                <label for="telephone" class="block text-sm font-medium text-gray-700">Téléphone *</label>
                                <input type="tel" id="telephone" wire:model="adresseLivraison.telephone" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @error('adresseLivraison.telephone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            
                            <!-- Adresse -->
                            <div class="col-span-2">
                                <label for="adresse" class="block text-sm font-medium text-gray-700">Adresse *</label>
                                <input type="text" id="adresse" wire:model="adresseLivraison.adresse" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @error('adresseLivraison.adresse') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            
                            <!-- Code postal -->
                            <div class="col-span-1">
                                <label for="code_postal" class="block text-sm font-medium text-gray-700">Code postal *</label>
                                <input type="text" id="code_postal" wire:model="adresseLivraison.code_postal" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @error('adresseLivraison.code_postal') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            
                            <!-- Ville -->
                            <div class="col-span-1">
                                <label for="ville" class="block text-sm font-medium text-gray-700">Ville *</label>
                                <input type="text" id="ville" wire:model="adresseLivraison.ville" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @error('adresseLivraison.ville') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            
                            <!-- Pays -->
                            <div class="col-span-2">
                                <label for="pays" class="block text-sm font-medium text-gray-700">Pays *</label>
                                <select id="pays" wire:model="adresseLivraison.pays" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="France">France</option>
                                    <option value="Belgique">Belgique</option>
                                    <option value="Suisse">Suisse</option>
                                    <option value="Canada">Canada</option>
                                    <option value="Côte d'Ivoire">Côte d'Ivoire</option>
                                    <option value="Sénégal">Sénégal</option>
                                    <option value="Autre">Autre</option>
                                </select>
                                @error('adresseLivraison.pays') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section Paiement à la livraison -->
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Moyen de paiement</h3>
                        
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        Le paiement s'effectuera à la livraison. Notre livreur acceptera uniquement les espèces.
                                        <input type="hidden" name="modePaiement" wire:model="modePaiement" value="espece">
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        @error('modePaiement') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    
                    <!-- Bouton de soumission -->
                    <div class="pt-6">
                        <button type="submit" class="w-full bg-indigo-600 text-white py-3 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 font-medium">
                            Confirmer la commande
                        </button>
                        
                        <p class="mt-3 text-center text-sm text-gray-500">
                            En passant commande, vous acceptez nos conditions générales de vente.
                        </p>
                    </div>
                </form>
                
                <div class="mt-6 text-center">
                    <a href="{{ route('panier.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        ← Retour au panier
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:load', function() {
        // Aucun script de paiement nécessaire pour le paiement à la livraison
    });
</script>
@endpush
