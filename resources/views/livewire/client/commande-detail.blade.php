<div class="container mx-auto px-4 py-8">
    @if($commande)
        <div class="max-w-7xl mx-auto">
            <!-- Bouton retour -->
            <div class="mb-6">
                <a href="{{ route('commandes.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Retour à mes commandes
                </a>
            </div>

            <!-- En-tête de la commande -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Commande #{{ $commande->reference }}</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Passée le {{ $commande->created_at->format('d/m/Y à H:i') }}
                        @if($commande->updated_at->gt($commande->created_at))
                            <br>Dernière mise à jour : {{ $commande->updated_at->format('d/m/Y à H:i') }}
                        @endif
                    </p>
                </div>
                <div class="mt-4 md:mt-0 flex flex-col items-end">
                    <span class="px-4 py-2 rounded-full text-sm font-medium {{ $commande->getStatusCssClass() }} mb-2">
                        {{ ucfirst(str_replace('_', ' ', $commande->etat)) }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $commande->getPaymentStatusCssClass() }}">
                        {{ $commande->payment_status === 'payee' ? 'Payée' : 'En attente de paiement' }}
                    </span>
                </div>
            </div>

            <!-- Alertes et notifications -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                {{ session('error') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Détails de la commande -->
                <div class="lg:col-span-2">
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Détails de la commande
                            </h3>
                        </div>
                        <div class="border-t border-gray-200">
                            <dl>
                                <div class="bg-gray-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Numéro de commande
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-medium">
                                        {{ $commande->reference }}
                                    </dd>
                                </div>
                                <div class="bg-white px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Date de commande
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $commande->created_at->format('d/m/Y à H:i') }}
                                    </dd>
                                </div>
                                @if($commande->date_expedition)
                                    <div class="bg-gray-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">
                                            Date d'expédition
                                        </dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                            {{ $commande->date_expedition->format('d/m/Y') }}
                                            @if($commande->transporteur)
                                                <span class="text-gray-500">via {{ $commande->transporteur }}</span>
                                                @if($commande->tracking_number)
                                                    <div class="mt-1">
                                                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                                            N° de suivi: {{ $commande->tracking_number }}
                                                        </span>
                                                    </div>
                                                @endif
                                            @endif
                                        </dd>
                                    </div>
                                @endif
                                @if($commande->date_livraison)
                                    <div class="bg-white px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">
                                            Date de livraison
                                        </dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                            {{ $commande->date_livraison->format('d/m/Y') }}
                                        </dd>
                                    </div>
                                @endif
                                <div class="bg-gray-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Mode de paiement
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        Paiement à la livraison
                                        @if($commande->payment_status === 'payee')
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Payé le {{ $commande->updated_at->format('d/m/Y') }}
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Articles de la commande -->
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                Articles commandés
                            </h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Produit
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Prix unitaire
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Qté
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Total
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($commande->items as $item)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-16 w-16">
                                                        @if($item->tissu && $item->tissu->images->isNotEmpty())
                                                            <img class="h-16 w-16 rounded-md object-cover" src="{{ asset('storage/' . $item->tissu->images->first()->url) }}" alt="{{ $item->tissu->titre }}">
                                                        @else
                                                            <div class="h-16 w-16 rounded-md bg-gray-200 flex items-center justify-center">
                                                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                </svg>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            @if($item->tissu)
                                                                <a href="{{ route('catalogue.show', $item->tissu) }}" class="text-indigo-600 hover:text-indigo-800">
                                                                    {{ $item->tissu->titre }}
                                                                </a>
                                                            @else
                                                                Produit supprimé
                                                            @endif
                                                        </div>
                                                        @if($item->tissu)
                                                            <div class="text-xs text-gray-500 mt-1">
                                                                Réf: {{ $item->tissu->reference }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                                {{ number_format($item->prix_unitaire, 0, ',', ' ') }} FCFA
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                                {{ $item->quantite }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                                {{ number_format($item->prix_unitaire * $item->quantite, 0, ',', ' ') }} FCFA
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                                            Sous-total
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                            {{ number_format($commande->total - $commande->frais_livraison, 0, ',', ' ') }} FCFA
                                        </td>
                                    </tr>
                                    @if($commande->frais_livraison > 0)
                                        <tr>
                                            <td colspan="3" class="px-6 py-2 text-right text-sm font-medium text-gray-900 border-b border-gray-200">
                                                Frais de livraison
                                            </td>
                                            <td class="px-6 py-2 whitespace-nowrap text-right text-sm font-medium text-gray-900 border-b border-gray-200">
                                                {{ number_format($commande->frais_livraison, 0, ',', ' ') }} FCFA
                                            </td>
                                        </tr>
                                    @endif
                                    <tr class="bg-gray-100">
                                        <td colspan="3" class="px-6 py-4 text-right text-base font-bold text-gray-900">
                                            Total TTC
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-base font-bold text-gray-900">
                                            {{ number_format($commande->total, 0, ',', ' ') }} FCFA
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Récapitulatif et actions -->
                <div class="lg:col-span-1">
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Récapitulatif
                            </h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <dl class="space-y-4">
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-600">Sous-total</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ number_format($commande->total - $commande->frais_livraison, 0, ',', ' ') }} FCFA</dd>
                                </div>
                                @if($commande->frais_livraison > 0)
                                    <div class="flex justify-between border-t border-gray-200 pt-4">
                                        <dt class="text-sm font-medium text-gray-600">Frais de livraison</dt>
                                        <dd class="text-sm font-medium text-gray-900">{{ number_format($commande->frais_livraison, 0, ',', ' ') }} FCFA</dd>
                                    </div>
                                @endif
                                <div class="flex justify-between border-t border-gray-200 pt-4">
                                    <dt class="text-base font-bold text-gray-900">Total TTC</dt>
                                    <dd class="text-base font-bold text-gray-900">{{ number_format($commande->total, 0, ',', ' ') }} FCFA</dd>
                                </div>
                            </dl>

                            <!-- Actions -->
                            <div class="mt-6 space-y-4">
                                @if($commande->etat === 'expediee' && !$commande->date_livraison)
                                    <button type="button"
                                            wire:click="marquerCommeRecue('{{ $commande->reference }}')"
                                            onclick="return confirm('Confirmez-vous avoir bien reçu votre commande ?')"
                                            class="w-full flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Marquer comme reçue
                                    </button>
                                @endif

                                @if($commande->peutEtreAnnulee())
                                    <button type="button"
                                            wire:click="annuler('{{ $commande->reference }}')"
                                            onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?')"
                                            class="w-full flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Annuler la commande
                                    </button>
                                @endif

                                <a href="{{ route('catalogue.index') }}" class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    Continuer mes achats
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Adresse de livraison -->
                    @if(isset($commande->adresse_livraison) && is_array($commande->adresse_livraison) && !empty($commande->adresse_livraison))
                        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Adresse de livraison
                                </h3>
                            </div>
                            <div class="px-4 py-5 sm:p-6">
                                <address class="not-italic text-sm text-gray-700">
                                    <div class="font-medium">{{ $commande->adresse_livraison['prenom'] }} {{ $commande->adresse_livraison['nom'] }}</div>
                                    <div>{{ $commande->adresse_livraison['adresse'] }}</div>
                                    @if(isset($commande->adresse_livraison['complement']))
                                        <div>{{ $commande->adresse_livraison['complement'] }}</div>
                                    @endif
                                    <div>{{ $commande->adresse_livraison['code_postal'] }} {{ $commande->adresse_livraison['ville'] }}</div>
                                    <div>{{ $commande->adresse_livraison['pays'] }}</div>
                                    <div class="mt-2">
                                        <span class="font-medium">Tél:</span> {{ $commande->adresse_livraison['telephone'] }}
                                        @if(isset($commande->adresse_livraison['email']))
                                            <br><span class="font-medium">Email:</span> {{ $commande->adresse_livraison['email'] }}
                                        @endif
                                    </div>
                                </address>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-4 mt-8">
                <a href="{{ route('commandes.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    &larr; Retour à la liste des commandes
                </a>
                @if($commande->statut === 'en_attente')
                    <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Payer maintenant
                    </button>
                @endif
            </div>
        </div>
    @else
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">Commande introuvable</h3>
                <p class="mt-1 text-sm text-gray-500">La commande que vous recherchez n'existe pas ou vous n'avez pas les droits pour la consulter.</p>
                <div class="mt-6">
                    <a href="{{ route('commandes.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Retour à mes commandes
                    </a>
                </div>
            </div>
        </div>
    @endif

