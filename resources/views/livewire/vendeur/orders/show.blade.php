<div class="container mx-auto px-4 py-8">
    <!-- En-tête avec bouton de retour -->
    <div class="mb-6">
        <a href="{{ route('vendeur.commandes.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Retour à la liste des commandes
        </a>
    </div>

    <!-- En-tête de la commande -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-8">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Commande #{{ $order->order_number }}
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Passée le {{ $order->created_at->format('d/m/Y à H:i') }}
                    </p>
                </div>
                <div class="mt-4 md:mt-0">
                    @php
                        $statusClasses = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'processing' => 'bg-blue-100 text-blue-800',
                            'shipped' => 'bg-purple-100 text-purple-800',
                            'delivered' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ][$order->status] ?? 'bg-gray-100 text-gray-800';
                        
                        $statusLabels = [
                            'pending' => 'En attente',
                            'processing' => 'En cours de traitement',
                            'shipped' => 'Expédiée',
                            'delivered' => 'Livrée',
                            'cancelled' => 'Annulée',
                        ];
                    @endphp
                    <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusClasses }}">
                        {{ $statusLabels[$order->status] ?? $order->status }}
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Détails de la commande -->
        <div class="border-t border-gray-200">
            <dl>
                <!-- Informations client -->
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Client
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="font-medium">{{ $order->shipping_name }}</div>
                        <div>{{ $order->shipping_email }}</div>
                        <div class="mt-1">
                            <span class="text-gray-700">Tél:</span> {{ $order->shipping_phone ?? 'Non fourni' }}
                        </div>
                    </dd>
                </div>
                
                <!-- Adresse de livraison -->
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Adresse de livraison
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div>{{ $order->shipping_address }}</div>
                        @if($order->shipping_address2)
                            <div>{{ $order->shipping_address2 }}</div>
                        @endif
                        <div>{{ $order->shipping_postal_code }} {{ $order->shipping_city }}</div>
                        <div>{{ $order->shipping_country }}</div>
                    </dd>
                </div>
                
                <!-- Informations de paiement -->
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Paiement
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="font-medium">
                            {{ ucfirst($order->payment_method) }}
                            @if($order->payment_status === 'paid')
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Payé
                                </span>
                            @else
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    En attente
                                </span>
                            @endif
                        </div>
                        @if($order->payment_reference)
                            <div class="mt-1 text-sm text-gray-500">
                                Référence: {{ $order->payment_reference }}
                            </div>
                        @endif
                    </dd>
                </div>
                
                <!-- Notes -->
                @if($order->notes)
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Notes
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $order->notes }}
                        </dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>

    <!-- Articles de la commande -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-8">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Articles commandés
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Article
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Prix unitaire
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Quantité
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($item->tissu && $item->tissu->image)
                                        <div class="flex-shrink-0 h-16 w-16">
                                            <img class="h-16 w-16 rounded-md object-cover" src="{{ asset('storage/' . $item->tissu->image) }}" alt="{{ $item->tissu_name }}">
                                        </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $item->tissu_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Réf: {{ $item->tissu_reference ?? 'N/A' }}
                                        </div>
                                        @if($item->options && count(json_decode($item->options, true)) > 0)
                                            <div class="mt-1 text-xs text-gray-500">
                                                @foreach(json_decode($item->options, true) as $key => $value)
                                                    <div>{{ ucfirst($key) }}: {{ $value }}</div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                {{ number_format($item->price, 2, ',', ' ') }} €
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                {{ number_format($item->price * $item->quantity, 2, ',', ' ') }} €
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                            Sous-total
                        </td>
                        <td class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                            {{ number_format($order->subtotal, 2, ',', ' ') }} €
                        </td>
                    </tr>
                    @if($order->shipping_cost > 0)
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                                Frais de livraison
                            </td>
                            <td class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                                {{ number_format($order->shipping_cost, 2, ',', ' ') }} €
                            </td>
                        </tr>
                    @endif
                    @if($order->discount > 0)
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                                Réduction
                            </td>
                            <td class="px-6 py-3 text-right text-sm font-medium text-red-600">
                                -{{ number_format($order->discount, 2, ',', ' ') }} €
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right text-lg font-bold text-gray-900 border-t border-gray-200">
                            Total
                        </td>
                        <td class="px-6 py-3 text-right text-lg font-bold text-gray-900 border-t border-gray-200">
                            {{ number_format($order->total, 2, ',', ' ') }} €
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-end space-x-4">
        <!-- Bouton d'impression -->
        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Imprimer
        </button>
        
        <!-- Menu déroulant pour changer le statut -->
        <div x-data="{ open: false }" class="relative inline-block text-left">
            <div>
                <button @click="open = !open" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" id="status-menu" aria-haspopup="true" x-bind:aria-expanded="open">
                    Changer le statut
                    <svg class="-mr-1 ml-2 h-5 w-5" x-description="Heroicon name: solid/chevron-down" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <div x-show="open" 
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="status-menu">
                    @foreach($statusOptions as $value => $label)
                        @if($value && $value !== $order->status)
                            <button wire:click="updateStatusFromShow('{{ $value }}')" 
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" 
                                    role="menuitem">
                                {{ $label }}
                            </button>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #printable, #printable * {
            visibility: visible;
        }
        #printable {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
    }
</style>
@endpush
