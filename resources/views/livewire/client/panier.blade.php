<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-gray-800">Mon Panier</h1>

        @if(session()->has('message'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                <p>{{ session('message') }}</p>
            </div>
        @endif

        @if(session()->has('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        @if($panierVide)
            <div class="text-center p-12 bg-white rounded-lg shadow-sm border">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h2 class="mt-4 text-xl font-medium text-gray-900">Votre panier est vide</h2>
                <p class="mt-2 text-gray-500">Parcourez notre catalogue et trouvez des articles à ajouter à votre panier.</p>
                <div class="mt-6">
                    <a href="{{ route('catalogue.index') }}" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition duration-150 ease-in-out">
                        Voir le catalogue
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Liste des articles -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
                        <div class="p-4 border-b">
                            <h2 class="text-lg font-medium text-gray-900">Vos articles ({{ $panierItems->count() }})</h2>
                        </div>
                        
                        <div class="divide-y divide-gray-200">
                            @forelse($panierItems ?? [] as $item)
                                @php
                                    // Valeurs par défaut pour éviter les erreurs
                                    $item = array_merge([
                                        'lien' => '#',
                                        'image' => asset('images/default-tissu.jpg'),
                                        'nom' => 'Produit inconnu',
                                        'reference' => 'N/A',
                                        'prix' => 0,
                                        'prix_initial' => 0,
                                        'en_promotion' => false,
                                        'quantite' => 1,
                                        'stock_disponible' => 0,
                                        'disponible' => false
                                    ], (array) $item);
                                @endphp
                                
                                <div class="p-4 flex flex-col sm:flex-row items-start sm:items-center gap-4 hover:bg-gray-50 transition-colors">
                                    <a href="{{ $item['lien'] }}" class="flex-shrink-0">
                                        <img src="{{ $item['image'] }}" alt="{{ $item['nom'] }}" class="w-20 h-20 object-cover rounded" onerror="this.onerror=null; this.src='{{ asset('images/default-tissu.jpg') }}';">
                                    </a>
                                    
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ $item['lien'] }}" class="text-base font-medium text-gray-900 hover:text-blue-600 line-clamp-2">
                                            {{ $item['nom'] }}
                                        </a>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Réf: {{ $item['reference'] }}
                                        </p>
                                        @if($item['en_promotion'])
                                            <div class="flex items-center mt-1">
                                                <span class="text-sm font-medium text-red-600">
                                                    {{ number_format($item['prix'], 0, ',', ' ') }} FCFA
                                                </span>
                                                <span class="ml-2 text-sm text-gray-500 line-through">
                                                    {{ number_format($item['prix_initial'], 0, ',', ' ') }} FCFA
                                                </span>
                                            </div>
                                        @else
                                            <p class="text-sm font-medium text-gray-900 mt-1">
                                                {{ number_format($item['prix'], 0, ',', ' ') }} FCFA
                                            </p>
                                        @endif
                                        
                                        @if(!$item['disponible'])
                                            <p class="text-sm text-red-600 mt-1">Stock épuisé</p>
                                        @elseif($item['quantite'] > $item['stock_disponible'])
                                            <p class="text-sm text-yellow-600 mt-1">Stock limité ({{ $item['stock_disponible'] }} disponible(s))</p>
                                        @endif
                                    </div>
                                    
                                    <div class="flex items-center gap-3 mt-2 sm:mt-0">
                                        <div class="flex items-center border rounded-md">
                                            <button 
                                                wire:click="decrementer({{ $item['id'] }})" 
                                                class="px-3 py-1 text-gray-600 hover:bg-gray-100"
                                                {{ $item['quantite'] <= 1 ? 'disabled' : '' }}
                                            >
                                                -
                                            </button>
                                            <input 
                                                type="number" 
                                                min="1" 
                                                max="{{ $item['stock_disponible'] }}"
                                                wire:model.live="quantites.{{ $item['id'] }}"
                                                wire:change="mettreAJour({{ $item['id'] }}, $event.target.value)"
                                                class="w-12 text-center border-x bg-white py-1 text-sm [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                            >
                                            <button 
                                                wire:click="incrementer({{ $item['id'] }})" 
                                                class="px-3 py-1 text-gray-600 hover:bg-gray-100"
                                                {{ $item['quantite'] >= $item['stock_disponible'] ? 'disabled' : '' }}
                                            >
                                                +
                                            </button>
                                        </div>
                                        
                                        <button 
                                            wire:click="supprimer({{ $item['id'] }})" 
                                            class="p-1.5 text-gray-400 hover:text-red-600 transition-colors"
                                            title="Supprimer"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-gray-500">
                                    Votre panier est vide pour le moment.
                                </div>
                            @endforelse
                        </div>
                        
                        <div class="p-4 border-t flex justify-between items-center">
                            <button 
                                wire:click="vider" 
                                wire:confirm="Êtes-vous sûr de vouloir vider votre panier ?"
                                class="text-sm text-gray-600 hover:text-red-600 flex items-center gap-1"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Vider le panier
                            </button>
                            
                            <a href="{{ route('catalogue.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                                Continuer mes achats
                            </a>
                        </div>
                    </div>
                    
                    <!-- Adresse de livraison -->
                    <div class="mt-6 bg-white rounded-lg shadow-sm border overflow-hidden">
                        <div class="p-4 border-b">
                            <h2 class="text-lg font-medium text-gray-900">Adresse de livraison</h2>
                        </div>
                        
                        <div class="p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Nom et Prénom -->
                                <div class="col-span-2 md:col-span-1">
                                    <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                                    <input 
                                        type="text" 
                                        id="nom" 
                                        wire:model.live="adresseLivraison.nom"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        required
                                        placeholder="Votre nom"
                                    >
                                    @error('adresseLivraison.nom')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="col-span-2 md:col-span-1">
                                    <label for="prenoms" class="block text-sm font-medium text-gray-700 mb-1">Prénoms *</label>
                                    <input 
                                        type="text" 
                                        id="prenoms" 
                                        wire:model.live="adresseLivraison.prenoms"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        required
                                        placeholder="Vos prénoms"
                                    >
                                    @error('adresseLivraison.prenoms')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Email et Téléphone -->
                                <div class="col-span-2 md:col-span-1">
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                    <input 
                                        type="email" 
                                        id="email" 
                                        wire:model.live="adresseLivraison.email"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        required
                                        placeholder="votre@email.com"
                                    >
                                    @error('adresseLivraison.email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="col-span-2 md:col-span-1">
                                    <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                                    <input 
                                        type="tel" 
                                        id="telephone" 
                                        wire:model.live="adresseLivraison.telephone"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        required
                                        placeholder="+2250102030405 ou 0102030405"
                                        pattern="(\+225|0)[0-9]{10}$"
                                    >
                                    @error('adresseLivraison.telephone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @else
                                        <p class="mt-1 text-xs text-gray-500">Format: +2250102030405 ou 0102030405</p>
                                    @enderror
                                </div>
                                
                                <!-- Adresse -->
                                <div class="col-span-2">
                                    <label for="adresse" class="block text-sm font-medium text-gray-700 mb-1">Adresse complète *</label>
                                    <input 
                                        type="text" 
                                        id="adresse" 
                                        wire:model.live="adresseLivraison.adresse"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        required
                                        placeholder="N° de rue, avenue, quartier..."
                                    >
                                    @error('adresseLivraison.adresse')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Complément d'adresse -->
                                <div class="col-span-2">
                                    <label for="complement" class="block text-sm font-medium text-gray-700 mb-1">Complément d'adresse</label>
                                    <input 
                                        type="text" 
                                        id="complement" 
                                        wire:model.live="adresseLivraison.complement"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        placeholder="Bâtiment, étage, code d'accès..."
                                    >
                                    @error('adresseLivraison.complement')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Code postal et Ville -->
                                <div class="col-span-2 md:col-span-1">
                                    <label for="code_postal" class="block text-sm font-medium text-gray-700 mb-1">Code postal</label>
                                    <input 
                                        type="text" 
                                        id="code_postal" 
                                        wire:model.live="adresseLivraison.code_postal"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        placeholder="Ex: 01 BP 1234"
                                    >
                                    @error('adresseLivraison.code_postal')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="col-span-2 md:col-span-1">
                                    <label for="ville" class="block text-sm font-medium text-gray-700 mb-1">Ville *</label>
                                    <select 
                                        id="ville" 
                                        wire:model.live="adresseLivraison.ville"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        required
                                    >
                                        <option value="">Sélectionnez votre ville</option>
                                        <option value="Dakar">Dakar</option>
                                        <option value="Abidjan">Abidjan</option>
                                        <option value="Yamoussoukro">Yamoussoukro</option>
                                        <option value="Bouaké">Bouaké</option>
                                        <option value="San-Pédro">San-Pédro</option>
                                        <option value="Korhogo">Korhogo</option>
                                        <option value="Man">Man</option>
                                        <option value="Autre">Autre</option>
                                    </select>
                                    @error('adresseLivraison.ville')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="col-span-2">
                                    <label for="pays" class="block text-sm font-medium text-gray-700 mb-1">Pays *</label>
                                    <select 
                                        id="pays" 
                                        wire:model.live="adresseLivraison.pays"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        required
                                    >
                                        <option value="">Sélectionnez votre pays</option>
                                        <option value="Côte d'Ivoire" selected>Côte d'Ivoire</option>
                                        <option value="Bénin">Bénin</option>
                                        <option value="Burkina Faso">Burkina Faso</option>
                                        <option value="Cap-Vert">Cap-Vert</option>
                                        <option value="Gambie">Gambie</option>
                                        <option value="Ghana">Ghana</option>
                                        <option value="Guinée">Guinée</option>
                                        <option value="Guinée-Bissau">Guinée-Bissau</option>
                                        <option value="Libéria">Libéria</option>
                                        <option value="Mali">Mali</option>
                                        <option value="Niger">Niger</option>
                                        <option value="Nigeria">Nigeria</option>
                                        <option value="Sénégal">Sénégal</option>
                                        <option value="Sierra Leone">Sierra Leone</option>
                                        <option value="Togo">Togo</option>
                                        <option value="Autre">Autre pays</option>
                                    </select>
                                    @error('adresseLivraison.pays')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Instructions spéciales -->
                                <div class="col-span-2">
                                    <label for="commentaire" class="block text-sm font-medium text-gray-700 mb-1">
                                        Instructions pour la livraison (optionnel)
                                        <span class="text-gray-500 font-normal">(Porte, code d'accès, repère, etc.)</span>
                                    </label>
                                    <textarea 
                                        id="commentaire" 
                                        wire:model.live="commentaire"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        rows="3"
                                        placeholder="Précisez ici toutes les informations utiles pour le livreur..."
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Récapitulatif de la commande -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
                        <div class="p-4 border-b">
                            <h2 class="text-lg font-medium text-gray-900">Récapitulatif</h2>
                        </div>
                        
                        <div class="p-4 border-b">
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600">Sous-total</span>
                                <span class="font-medium">{{ number_format($sousTotal ?? 0, 0, ',', ' ') }} FCFA</span>
                            </div>
                            
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600">Frais de livraison</span>
                                <span class="font-medium">
                                    @if(($fraisLivraison ?? 0) > 0)
                                        {{ number_format($fraisLivraison, 0, ',', ' ') }} FCFA
                                    @else
                                        Gratuit
                                    @endif
                                </span>
                            </div>
                            
                            <div class="flex justify-between py-2 text-lg font-bold">
                                <span>Total</span>
                                <span>{{ number_format($total ?? 0, 0, ',', ' ') }} FCFA</span>
                            </div>
                            
                            <div class="mt-6">
                                <p class="text-sm text-gray-500 mb-2">Moyen de paiement</p>
                                <input type="hidden" name="paiement" value="livraison">
                                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                                    <h3 class="text-lg font-medium text-blue-800 mb-2">Méthode de paiement</h3>
                                    <p class="text-blue-700 flex items-center">
                                        <svg class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Paiement à la livraison (seule méthode disponible pour le moment)
                                    </p>
                                </div>
                            
                            <div class="mt-6">
                                <button 
                                    type="button" 
                                    wire:click="confirmerCommande"
                                    wire:loading.attr="disabled"
                                    wire:target="confirmerCommande"
                                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                                >
                                    <span wire:loading.remove wire:target="confirmerCommande">
                                        Confirmer la commande
                                    </span>
                                    <span wire:loading wire:target="confirmerCommande">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Traitement...
                                    </span>
                                </button>
                            </div>
                            
                            <p class="mt-3 text-xs text-gray-500 text-center">
                                En passant commande, vous acceptez nos 
                                <a href="#" class="text-blue-600 hover:text-blue-800">conditions générales de vente</a> 
                                et notre 
                                <a href="#" class="text-blue-600 hover:text-blue-800">politique de confidentialité</a>.
                            </p>
                        </div>
                        
                        <div class="p-4 bg-gray-50">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-sm text-gray-600">Paiement sécurisé</span>
                            </div>
                            <div class="flex items-center mt-2">
                                <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-sm text-gray-600">Livraison rapide</span>
                            </div>
                            <div class="flex items-center mt-2">
                                <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-sm text-gray-600">Service client 7j/7</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Fonction pour afficher une notification
    function showNotification(data) {
        console.log('Notification:', data);
        
        // Utilisation de SweetAlert2 si disponible
        if (typeof Swal !== 'undefined') {
            const options = {
                toast: true,
                position: 'top-end',
                icon: data.type || 'info',
                title: data.message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            };
            
            // Ajout des options pour les produits si nécessaire
            if (data.product) {
                options.imageUrl = data.product.image || '';
                options.imageWidth = 80;
                options.imageHeight = 80;
                options.imageAlt = data.product.name || '';
                options.html = `${data.message}<br><small>${data.product.name || ''}</small>`;
            }
            
            Swal.fire(options);
        }
    }

    // Écoute des événements Livewire
    document.addEventListener('livewire:initialized', () => {
        try {
            // Écoute des événements de notification via Livewire 3
            Livewire.on('notify', (data) => {
                showNotification(data);
            });
            
            // Gestion des erreurs globales (méthode alternative pour Livewire 3)
            window.addEventListener('livewire:error', (event) => {
                console.error('Livewire error:', event.detail);
                showNotification({
                    type: 'error',
                    message: 'Une erreur est survenue. Veuillez réessayer.'
                });
                // Empêche la propagation de l'événement d'erreur
                event.preventDefault();
                event.stopPropagation();
            });
            
            // Gestion des requêtes échouées (méthode Livewire 3)
            Livewire.on('failed', (response) => {
                console.error('Livewire request failed:', response);
                showNotification({
                    type: 'error',
                    message: 'Une erreur est survenue. Veuillez réessayer.'
                });
            });
        } catch (error) {
            console.error('Error initializing Livewire listeners:', error);
        }
    });
    
    // Gestion des erreurs de chargement de Livewire
    window.addEventListener('livewire:load-error', (event) => {
        console.error('Livewire load error:', event.detail);
        showNotification({
            type: 'error',
            message: 'Erreur de chargement. Veuillez recharger la page.'
        });
    });
</script>
@endpush
