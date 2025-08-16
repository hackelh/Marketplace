<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-semibold mb-6">Tableau de bord client</h2>
                
                @if($recentOrders->count() > 0)
                    <div class="mb-10">
                        <h3 class="text-xl font-semibold mb-4">Vos commandes récentes</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Commande</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recentOrders as $order)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $order->reference }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $order->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ number_format($order->total, 0, ',', ' ') }} FCFA
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $order->statut === 'livré' ? 'bg-green-100 text-green-800' : 
                                                       ($order->statut === 'annulé' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                                    {{ ucfirst($order->statut) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('commandes.show', $order->id) }}" class="text-indigo-600 hover:text-indigo-900">Voir détails</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('commandes.index') }}" class="text-indigo-600 hover:text-indigo-900">Voir toutes les commandes</a>
                        </div>
                    </div>
                @else
                    <div class="mb-10 p-6 bg-blue-50 rounded-lg">
                        <h3 class="text-lg font-medium text-blue-800 mb-2">Bienvenue sur votre tableau de bord !</h3>
                        <p class="text-blue-700">Vous n'avez pas encore passé de commande. Parcourez notre catalogue pour découvrir nos produits.</p>
                        <div class="mt-4">
                            <a href="{{ route('catalogue.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Découvrir le catalogue
                            </a>
                        </div>
                    </div>
                @endif
                
                @if($suggestedProducts->count() > 0)
                    <div>
                        <h3 class="text-xl font-semibold mb-4">Produits recommandés pour vous</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                @foreach($suggestedProducts as $product)
                                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                                        @if(optional($product->images)->isNotEmpty())
                                            <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->titre }}" class="w-full h-48 object-cover">
                                        @else
                                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                                <span class="text-gray-400">Aucune image</span>
                                            </div>
                                        @endif
                                        <div class="p-4">
                                            <h4 class="font-semibold text-lg mb-2">{{ $product->titre }}</h4>
                                            <p class="text-gray-600 mb-2">{{ number_format($product->prix, 0, ',', ' ') }} FCFA</p>
                                            <div class="text-xs text-gray-500 mb-2">
                                                ID: {{ $product->id }}<br>
                                                Slug: {{ $product->slug ?? 'null' }}
                                            </div>
                                            @if($product->slug)
                                                <a href="{{ route('catalogue.show', $product->slug) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Voir le produit</a>
                                            @else
                                                <a href="{{ route('catalogue.show', $product->id) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Voir le produit (par ID)</a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4 text-center">
                                <a href="{{ route('catalogue.index') }}" class="text-indigo-600 hover:text-indigo-900">Voir plus de produits</a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
