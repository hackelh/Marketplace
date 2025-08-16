<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-8 rounded" role="alert">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <p class="font-bold">{{ session('success') }}</p>
                </div>
                <p class="mt-2">Votre commande #{{ $order->reference }} a bien été enregistrée. Vous recevrez un email de confirmation sous peu.</p>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border overflow-hidden mb-8">
            <div class="p-6 border-b">
                <h1 class="text-2xl font-bold text-gray-800">Récapitulatif de votre commande #{{ $order->reference }}</h1>
                <p class="text-gray-600 mt-1">
                    Passée le {{ $order->created_at->format('d/m/Y à H:i') }} | 
                    Statut : 
                    <span class="font-medium {{ $order->statut === 'en_attente' ? 'text-yellow-600' : ($order->statut === 'en_cours' ? 'text-blue-600' : 'text-green-600') }}">
                        {{ ucfirst(str_replace('_', ' ', $order->statut)) }}
                    </span>
                </p>
            </div>

            <div class="p-6 border-b">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Détails de la commande</h2>
                
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex items-start justify-between py-3">
                            <div class="flex items-start space-x-4">
                                <img src="{{ $item->tissu_image ?? asset('images/default-tissu.jpg') }}" alt="{{ $item->tissu_name }}" class="w-20 h-20 object-cover rounded">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $item->tissu_name }}</h3>
                                    <p class="text-sm text-gray-500">Réf: {{ $item->options['reference'] ?? 'N/A' }}</p>
                                    @if(isset($item->options['couleur']) || isset($item->options['taille']))
                                        <div class="text-sm text-gray-500 mt-1">
                                            @if(isset($item->options['couleur']))
                                                <span>Couleur: {{ $item->options['couleur'] }}</span>
                                            @endif
                                            @if(isset($item->options['taille']))
                                                <span class="ml-2">Taille: {{ $item->options['taille'] }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-gray-900">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</p>
                                <p class="text-sm text-gray-500">x{{ $item->quantity }}</p>
                                <p class="font-medium mt-1">{{ number_format($item->total_price, 0, ',', ' ') }} FCFA</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 pt-6 border-t">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sous-total</span>
                            <span class="font-medium">{{ number_format($order->sous_total, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Frais de livraison</span>
                            <span class="font-medium">
                                @if($order->frais_livraison > 0)
                                    {{ number_format($order->frais_livraison, 0, ',', ' ') }} FCFA
                                @else
                                    Gratuit
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between text-lg font-bold pt-2 mt-2 border-t">
                            <span>Total</span>
                            <span>{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 border-b">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Adresse de livraison</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-medium text-gray-900">Adresse de facturation</h3>
                        <p class="mt-1 text-gray-600">
                            {{ $order->adresse_livraison['prenoms'] }} {{ $order->adresse_livraison['nom'] }}<br>
                            {{ $order->adresse_livraison['adresse'] }}<br>
                            @if(!empty($order->adresse_livraison['complement']))
                                {{ $order->adresse_livraison['complement'] }}<br>
                            @endif
                            {{ $order->adresse_livraison['code_postal'] ?? '' }} {{ $order->adresse_livraison['ville'] }}<br>
                            {{ $order->adresse_livraison['pays'] }}
                        </p>
                        <p class="mt-2">
                            <span class="text-gray-600">Téléphone :</span>
                            <span class="font-medium">{{ $order->adresse_livraison['telephone'] }}</span>
                        </p>
                        <p>
                            <span class="text-gray-600">Email :</span>
                            <span class="font-medium">{{ $order->adresse_livraison['email'] }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-gray-50">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Méthode de paiement</h2>
                <div class="flex items-center p-4 bg-white rounded-lg border">
                    <div class="p-3 bg-blue-50 rounded-full">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-medium text-gray-900">Paiement à la livraison</h3>
                        <p class="text-sm text-gray-500">Vous paierez à la réception de votre commande</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex flex-col sm:flex-row justify-between items-center">
            <a href="{{ route('catalogue.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mb-4 sm:mb-0">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Retour au catalogue
            </a>
            <div class="flex space-x-4">
                <a href="{{ route('commandes.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                    </svg>
                    Voir mes commandes
                </a>
                <a href="#" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Télécharger la facture
                </a>
            </div>
        </div>
    </div>
</div>
