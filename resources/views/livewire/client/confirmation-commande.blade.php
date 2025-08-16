<div class="container mx-auto px-4 py-8" x-data>
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 5000)" 
             class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Émettre un événement pour rafraîchir la liste des commandes
                window.livewire.emit('commande-cree');
                
                // Forcer le rechargement de la page après un court délai pour s'assurer que les données sont à jour
                setTimeout(() => {
                    window.livewire.emit('$refresh');
                }, 1000);
            });
        </script>
    @endif
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
                    <a href="{{ route('commandes.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Mes commandes</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Confirmation de commande</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="bg-green-50 px-6 py-4 border-b border-green-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-bold text-gray-900">Commande confirmée !</h2>
                        <p class="mt-1 text-sm text-gray-600">Merci pour votre achat. Votre commande a été enregistrée avec succès.</p>
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Récapitulatif de la commande</h3>
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Référence</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $commande->reference }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Date</dt>
                                <dd class="text-sm text-gray-900">{{ $commande->created_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Statut</dt>
                                <dd class="text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ ucfirst(str_replace('_', ' ', $commande->statut)) }}
                                    </span>
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Méthode de livraison</dt>
                                <dd class="text-sm text-gray-900">{{ ucfirst($commande->mode_livraison) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Mode de paiement</dt>
                                <dd class="text-sm text-gray-900">Paiement à la livraison</dd>
                            </div>
                        </dl>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Adresse de livraison</h3>
                        <address class="not-italic text-sm text-gray-600">
                            {{ $commande->adresse_livraison['prenom'] }} {{ $commande->adresse_livraison['nom'] }}<br>
                            @if(!empty($commande->adresse_livraison['societe']))
                                {{ $commande->adresse_livraison['societe'] }}<br>
                            @endif
                            {{ $commande->adresse_livraison['adresse'] }}<br>
                            @if(!empty($commande->adresse_livraison['complement']))
                                {{ $commande->adresse_livraison['complement'] }}<br>
                            @endif
                            {{ $commande->adresse_livraison['code_postal'] }} {{ $commande->adresse_livraison['ville'] }}<br>
                            {{ $commande->adresse_livraison['pays'] }}<br>
                            <div class="mt-2">
                                Téléphone : {{ $commande->adresse_livraison['telephone'] }}<br>
                                Email : {{ $commande->adresse_livraison['email'] }}
                            </div>
                        </address>
                    </div>
                </div>
                
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Détails de la commande</h3>
                    <div class="bg-gray-50 rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($commande->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    @if($item->tissu && $item->tissu->images->isNotEmpty())
                                                        <img class="h-10 w-10 rounded-md object-cover" src="{{ $item->tissu->images->first()->getUrl('thumb') }}" alt="{{ $item->tissu->nom }}">
                                                    @else
                                                        <div class="h-10 w-10 rounded-md bg-gray-200 flex items-center justify-center">
                                                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        @if($item->tissu)
                                                            <a href="{{ route('catalogue.show', $item->tissu->slug) }}" class="hover:text-blue-600">{{ $item->tissu->nom }}</a>
                                                        @else
                                                            {{ $item->nom }}
                                                        @endif
                                                    </div>
                                                    @if($item->tissu)
                                                        <div class="text-sm text-gray-500">{{ $item->tissu->categorie->nom ?? 'Non catégorisé' }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                            {{ number_format($item->prix_unitaire, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                            {{ $item->quantite }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                            {{ number_format($item->total, 0, ',', ' ') }} FCFA
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <th scope="row" colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-900">Sous-total</th>
                                    <td class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                                        {{ number_format($commande->sous_total, 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-900">Frais de livraison</th>
                                    <td class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                                        {{ number_format($commande->frais_livraison, 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                                <tr class="border-t border-gray-200">
                                    <th scope="row" colspan="3" class="px-6 py-3 text-right text-base font-bold text-gray-900">Total</th>
                                    <td class="px-6 py-3 text-right text-base font-bold text-gray-900">
                                        {{ number_format($commande->total, 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="text-sm text-gray-600">
                            <p>Un email de confirmation a été envoyé à <span class="font-medium text-gray-900">{{ $commande->adresse_livraison['email'] }}</span>.</p>
                            <p class="mt-1">Pour toute question concernant votre commande, n'hésitez pas à <a href="{{ route('contact') }}" class="text-blue-600 hover:text-blue-800">nous contacter</a>.</p>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ route('commandes.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Voir toutes mes commandes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Suivi de commande</h3>
            </div>
            <div class="px-6 py-4">
                <div class="flow-root">
                    <ul class="-mb-8">
                        @php
                            $steps = [
                                'en_attente' => [
                                    'title' => 'Commande reçue',
                                    'description' => 'Votre commande a été enregistrée et est en attente de traitement.',
                                    'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4'
                                ],
                                'en_preparation' => [
                                    'title' => 'En préparation',
                                    'description' => 'Votre commande est en cours de préparation par nos équipes.',
                                    'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'
                                ],
                                'expediee' => [
                                    'title' => 'Expédiée',
                                    'description' => 'Votre commande a été expédiée. Vous recevrez bientôt un email avec les informations de suivi.',
                                    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'
                                ],
                                'livree' => [
                                    'title' => 'Livrée',
                                    'description' => 'Votre commande a été livrée. Nous espérons que vous serez satisfait de votre achat !',
                                    'icon' => 'M5 13l4 4L19 7'
                                ]
                            ];
                            
                            $currentStep = array_search($commande->statut, array_keys($steps));
                            if ($currentStep === false) $currentStep = 0;
                            else $currentStep++;
                        @endphp
                        
                        @foreach($steps as $status => $step)
                            @php
                                $isCurrent = $commande->statut === $status;
                                $isCompleted = $currentStep > array_search($status, array_keys($steps));
                                $isUpcoming = $currentStep < array_search($status, array_keys($steps));
                            @endphp
                            
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white {{ $isCompleted ? 'bg-green-500' : ($isCurrent ? 'bg-blue-500' : 'bg-gray-300') }}">
                                                @if($isCompleted)
                                                    <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                @else
                                                    <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] ?? '' }}" />
                                                    </svg>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm {{ $isCurrent || $isCompleted ? 'text-gray-900 font-medium' : 'text-gray-500' }}">
                                                    {{ $step['title'] }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $step['description'] }}
                                                </p>
                                            </div>
                                            @if($isCurrent)
                                                <div class="text-right text-sm whitespace-nowrap text-blue-600">
                                                    En cours
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
