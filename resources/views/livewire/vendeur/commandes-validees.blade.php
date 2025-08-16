<div>
    <!-- En-tête avec statistiques -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Commandes Validées</h1>
                    <p class="text-gray-600">Historique de vos commandes livrées et terminées</p>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="bg-green-100 rounded-full p-2">
                            <i class="bi bi-check-circle text-green-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-600">Total validées</p>
                            <p class="text-lg font-semibold text-green-900">{{ $statistiques['total_validees'] }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="bg-blue-100 rounded-full p-2">
                            <i class="bi bi-truck text-blue-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-600">Livrées</p>
                            <p class="text-lg font-semibold text-blue-900">{{ $statistiques['livrees'] }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-purple-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="bg-purple-100 rounded-full p-2">
                            <i class="bi bi-flag-checkered text-purple-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-purple-600">Terminées</p>
                            <p class="text-lg font-semibold text-purple-900">{{ $statistiques['terminees'] }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 rounded-full p-2">
                            <i class="bi bi-currency-dollar text-yellow-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-yellow-600">Montant total</p>
                            <p class="text-lg font-semibold text-yellow-900">{{ number_format($statistiques['montant_total'], 0, ',', ' ') }} CFA</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filtres -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                    <input type="text" wire:model.live="recherche" placeholder="Numéro commande, client..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select wire:model.live="statutFilter" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Tous les statuts</option>
                        <option value="livree">Livrée</option>
                        <option value="terminee">Terminée</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date de livraison</label>
                    <select wire:model.live="dateFilter" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Toutes les dates</option>
                        <option value="aujourd_hui">Aujourd'hui</option>
                        <option value="cette_semaine">Cette semaine</option>
                        <option value="ce_mois">Ce mois</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button wire:click="$set('recherche', '')" 
                            wire:click="$set('statutFilter', '')" 
                            wire:click="$set('dateFilter', '')"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Réinitialiser
                    </button>
                </div>
            </div>
        </div>

        <!-- Liste des commandes -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Historique des Commandes Validées</h3>
            </div>
            
            <div class="overflow-x-auto">
                @if($commandes->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Commande
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Client
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Montant
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date de livraison
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($commandes as $commande)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $commande->numero_commande }}</div>
                                        <div class="text-sm text-gray-500">{{ $commande->details->count() }} article(s)</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $commande->client->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $commande->client->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ number_format($commande->montant_total, 0, ',', ' ') }} CFA</div>
                                        <div class="text-sm text-gray-500">{{ $commande->methode_paiement }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $commande->statut_couleur }}-100 text-{{ $commande->statut_couleur }}-800">
                                            {{ $commande->statut_libelle }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($commande->date_livraison_effective)
                                            {{ $commande->date_livraison_effective->format('d/m/Y H:i') }}
                                        @else
                                            <span class="text-gray-400">Non définie</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button class="text-indigo-600 hover:text-indigo-900">
                                                Voir détails
                                            </button>
                                            <button class="text-green-600 hover:text-green-900">
                                                Facture
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <div class="px-6 py-4">
                        {{ $commandes->links() }}
                    </div>
                @else
                    <div class="px-6 py-12 text-center">
                        <i class="bi bi-check-circle text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune commande validée</h3>
                        <p class="text-gray-500">Vous n'avez pas encore de commandes livrées ou terminées.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
