<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    <!-- En-tête avec statistiques -->
    <div class="bg-white shadow-sm border-bottom">
        <div class="container-fluid py-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h1 class="h3 fw-bold mb-1">Commandes en cours</h1>
                    <div class="text-muted">Suivi des commandes en attente, en préparation et en couture</div>
                </div>
            </div>

            <!-- Statistiques - style Dashboard (Bootstrap/AdminLTE cards) -->
            <div class="row g-4 mb-2">
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-hourglass-split fs-3 text-primary"></i>
                            </div>
                            <div>
                                <div class="fs-5 fw-bold">{{ $statistiques['total_en_cours'] }}</div>
                                <div class="text-muted">Total en cours</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-clock fs-3 text-warning"></i>
                            </div>
                            <div>
                                <div class="fs-5 fw-bold">{{ $statistiques['en_attente'] }}</div>
                                <div class="text-muted">En attente</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-tools fs-3 text-info"></i>
                            </div>
                            <div>
                                <div class="fs-5 fw-bold">{{ $statistiques['en_preparation'] }}</div>
                                <div class="text-muted">En préparation</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-secondary bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-scissors fs-3 text-secondary"></i>
                            </div>
                            <div>
                                <div class="fs-5 fw-bold">{{ $statistiques['en_couture'] }}</div>
                                <div class="text-muted">En couture</div>
                            </div>
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
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                    <input type="text" wire:model.live="recherche" placeholder="Numéro commande, client..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select wire:model.live="statutFilter" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Tous</option>
                        <option value="en_attente">En attente</option>
                        <option value="en_preparation">En préparation</option>
                        <option value="en_couture">En couture</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <select wire:model.live="dateFilter" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Toutes</option>
                        <option value="aujourd_hui">Aujourd'hui</option>
                        <option value="cette_semaine">Cette semaine</option>
                        <option value="ce_mois">Ce mois</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="button"
                            wire:click="$set('recherche', '')"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 mr-2">
                        Effacer
                    </button>
                    <button type="button"
                            wire:click="$set('statutFilter', '')"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 mr-2">
                        Statut: Tous
                    </button>
                    <button type="button"
                            wire:click="$set('dateFilter', '')"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Date: Toutes
                    </button>
                </div>
            </div>
        </div>

        <!-- Liste des commandes -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Commandes</h3>
                <div class="text-sm text-gray-500">Montant total: <span class="font-semibold">{{ number_format($statistiques['montant_total'], 0, ',', ' ') }} CFA</span></div>
            </div>
            
            <div class="overflow-x-auto">
                @if($commandes->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commande</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Créée le</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
                                        {{ $commande->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex flex-wrap gap-2">
                                            <a href="{{ route('vendeur.commande.show', $commande->id) }}" class="px-2 py-1 text-xs rounded bg-indigo-600 text-white hover:bg-indigo-700">Voir détails</a>
                                            @if($commande->statut !== 'en_preparation')
                                                <button wire:click="changerStatut({{ $commande->id }}, 'en_preparation')" class="px-2 py-1 text-xs rounded bg-blue-600 text-white hover:bg-blue-700">Préparation</button>
                                            @endif
                                            @if($commande->statut !== 'en_couture')
                                                <button wire:click="changerStatut({{ $commande->id }}, 'en_couture')" class="px-2 py-1 text-xs rounded bg-purple-600 text-white hover:bg-purple-700">Couture</button>
                                            @endif
                                            <button wire:click="changerStatut({{ $commande->id }}, 'livree')" class="px-2 py-1 text-xs rounded bg-green-600 text-white hover:bg-green-700">Marquer livrée</button>
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
                        <i class="bi bi-inboxes text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune commande en cours</h3>
                        <p class="text-gray-500">Aucune commande en attente, en préparation ou en couture pour le moment.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
