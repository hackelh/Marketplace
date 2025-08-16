<div class="container mx-auto px-4 py-8">
    <!-- En-tête avec bouton de rafraîchissement -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Tableau de bord</h1>
            <p class="text-gray-600">Bienvenue sur votre espace vendeur, {{ auth()->user()->name }}.</p>
        </div>
        <button 
            wire:click="refreshDashboard"
            class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition"
        >
            <svg wire:loading wire:target="refreshDashboard" class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Rafraîchir</span>
        </button>
    </div>

    <!-- Cartes de statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total des produits -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Produits</h3>
                    <p class="text-2xl font-bold">{{ $stats['total_products'] }}</p>
                </div>
            </div>
        </div>

        <!-- Commandes totales -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Commandes</h3>
                    <p class="text-2xl font-bold">{{ $stats['total_sales'] }}</p>
                </div>
            </div>
        </div>

        <!-- Revenu total -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Revenu total</h3>
                    <p class="text-2xl font-bold">{{ number_format($stats['total_revenue'], 2, ',', ' ') }} €</p>
                </div>
            </div>
        </div>

        <!-- Clients uniques -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Clients uniques</h3>
                    <p class="text-2xl font-bold">{{ $stats['unique_customers'] }}</p>
                </div>
            </div>
        </div>

        <!-- Panier moyen -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Panier moyen</h3>
                    <p class="text-2xl font-bold">{{ number_format($stats['average_cart'], 2, ',', ' ') }} €</p>
                </div>
            </div>
        </div>

        <!-- Taux de conversion -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-pink-100 text-pink-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Taux de conversion</h3>
                    <p class="text-2xl font-bold">{{ $stats['conversion_rate'] }}%</p>
                </div>
            </div>
        </div>

        <!-- Produits en stock faible -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Stock faible</h3>
                    <p class="text-2xl font-bold">{{ $stats['low_stock_items'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et tableaux -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Graphique combiné ventes/revenus -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Activité des 30 derniers jours</h2>
                <div class="flex space-x-2">
                    <button 
                        id="toggleChartView" 
                        data-view="combined"
                        class="px-3 py-1 text-xs rounded-md bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition-colors"
                    >
                        Vue combinée
                    </button>
                    <button 
                        id="toggleChartType" 
                        data-type="bar"
                        class="px-3 py-1 text-xs rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Barres
                    </button>
                </div>
            </div>
            <div class="h-80">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Produits populaires -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Produits populaires</h2>
                <a href="{{ route('vendeur.tissus.index') }}" class="text-sm text-blue-600 hover:underline">Voir tout</a>
            </div>
            <div class="space-y-4">
                @forelse($topProducts as $product)
                    <button wire:click="$dispatch('edit-tissu', { id: {{ $product['id'] }} })" class="w-full text-left hover:bg-gray-50 -mx-2 p-2 rounded-md transition-colors">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-12 w-12 bg-gray-100 rounded-md overflow-hidden border border-gray-200">
                                @if(isset($product['image']) && $product['image'])
                                    <img src="{{ asset('storage/' . $product['image']) }}" alt="{{ $product['name'] }}" class="h-full w-full object-cover">
                                @else
                                    <div class="h-full w-full flex items-center justify-center bg-gray-100 text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4 flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $product['name'] }}</p>
                                <div class="flex items-center mt-1">
                                    <div class="h-2 flex-1 bg-gray-200 rounded-full overflow-hidden">
                                        @php
                                            $percentage = $product['sales'] > 0 ? min(100, ($product['sales'] / max(1, $stats['total_sales'])) * 100) : 0;
                                        @endphp
                                        <div class="h-full bg-indigo-600 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="ml-2 text-xs text-gray-500">{{ $product['sales'] }} ventes</span>
                                </div>
                            </div>
                            <div class="ml-4 text-right">
                                <p class="text-sm font-medium text-gray-900">{{ $product['revenue'] }}</p>
                                <p class="text-xs {{ $product['stock'] < 5 ? 'text-red-600' : 'text-gray-500' }}">
                                    {{ $product['stock'] }} en stock
                                </p>
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="text-center py-6">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune vente</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Aucun produit n'a été vendu pour le moment.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Dernières commandes -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">Dernières commandes</h2>
                <div class="flex space-x-2">
                    <button 
                        wire:click="loadMoreOrders" 
                        class="text-sm text-blue-600 hover:underline flex items-center"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="loadMoreOrders">
                            Voir plus
                        </span>
                        <span wire:loading wire:target="loadMoreOrders">
                            Chargement...
                        </span>
                    </button>
                    <span class="text-gray-400">|</span>
                    <a href="{{ route('vendeur.commandes.index') }}" class="text-sm text-blue-600 hover:underline">
                        Voir tout
                    </a>
                </div>
            </div>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($recentOrders as $order)
                <a href="{{ route('vendeur.commandes.show', $order['id']) }}" class="block hover:bg-gray-50 transition-colors duration-150">
                    <div class="p-6">
                        <div class="flex justify-between items-start">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2">
                                    <h3 class="font-medium text-gray-900 truncate">
                                        Commande #{{ $order['id'] }}
                                    </h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                        $order['status'] === 'completed' ? 'bg-green-100 text-green-800' : 
                                        ($order['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                        ($order['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                        ($order['status'] === 'processing' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')))
                                    }}">
                                        {{ ucfirst($order['status']) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">
                                    <span class="font-medium">{{ $order['customer'] }}</span> • 
                                    <span x-data="{ date: new Date('{{ $order['date'] }}').toLocaleDateString() }" x-text="date"></span>
                                </p>
                                <div class="mt-2 flex items-center text-sm text-gray-500">
                                    <span class="flex items-center">
                                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                        {{ $order['item_count'] }} {{ Str::plural('article', $order['item_count']) }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <p class="text-lg font-semibold text-gray-900">{{ $order['amount'] }}</p>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="p-6 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune commande</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Aucune commande n'a été passée pour le moment.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script>
    console.log('Script de graphique chargé');
    
    // Variables globales pour le graphique
    let salesChart = null;
    
    // Fonction pour initialiser le graphique
    function initializeChart() {
        console.log('Début de l\'initialisation du graphique');
        
        // Vérifier que le canvas existe
        const canvas = document.getElementById('salesChart');
        if (!canvas) {
            console.error('Élément canvas non trouvé');
            return;
        }
        
        // Utiliser les données du composant Livewire
        const salesData = {
            labels: @js($labels),
            sales: @js($salesData),
            revenue: @js($revenueData)
        };
        
        console.log('Données du graphique:', salesData);
        
        // Vérifier que les données sont valides
        if (!salesData.labels || !salesData.labels.length) {
            console.warn('Aucune donnée disponible pour le graphique');
            return;
        }
        
        // Vérifier que les données sont valides
        if (!salesData.labels || !salesData.labels.length) {
            console.warn('Aucune donnée disponible pour le graphique');
            return;
        }

        // Configuration du graphique
        let chartType = 'bar';
        let chartView = 'combined';
        let salesChart;

        // Initialisation du graphique
        function initChart() {
            const ctx = document.getElementById('salesChart')?.getContext('2d');
            if (!ctx) return; // S'assurer que l'élément existe
            
            // Détruire le graphique existant s'il y en a un
            if (salesChart) {
                salesChart.destroy();
            }

            const datasets = [];
            
            if (chartView === 'combined' || chartView === 'sales') {
                datasets.push({
                    label: 'Ventes',
                    data: salesData.sales || [],
                    backgroundColor: 'rgba(99, 102, 241, 0.5)',
                    borderColor: 'rgba(99, 102, 241, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    type: chartType === 'line' ? 'line' : 'bar',
                    yAxisID: 'y',
                    order: 1
                });
            }
            
            if (chartView === 'combined' || chartView === 'revenue') {
                datasets.push({
                    label: 'Revenu (€)',
                    data: salesData.revenue || [],
                    backgroundColor: 'rgba(16, 185, 129, 0.5)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    type: chartType === 'line' ? 'line' : 'bar',
                    yAxisID: chartView === 'combined' ? 'y1' : 'y',
                    order: chartView === 'combined' ? 2 : 0
                });
            }

            const config = {
                type: chartType === 'line' ? 'line' : 'bar',
                data: {
                    labels: salesData.labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        },
                        y: {
                            type: 'linear',
                            display: chartView !== 'revenue',
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Nombre de ventes'
                            },
                            grid: {
                                drawOnChartArea: chartView === 'combined' ? false : true
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: chartView === 'combined',
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Revenu (€)'
                            },
                            grid: {
                                drawOnChartArea: false
                            },
                            // Configuration pour l'échelle du revenu (diviser par 10 pour un meilleur affichage)
                            afterDataLimits: (scale) => {
                                scale.max = scale.max * 1.1; // Ajouter 10% d'espace en haut
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + ' €';
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += context.dataset.label.includes('€') 
                                            ? context.parsed.y.toFixed(2) + ' €' 
                                            : context.parsed.y;
                                    }
                                    return label;
                                }
                            }
                        },
                        legend: {
                            position: 'top',
                        },
                        datalabels: {
                            display: false
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeInOutQuart'
                    }
                },
                plugins: [ChartDataLabels]
            };

            salesChart = new Chart(ctx, config);
        }

        // Initialisation du graphique
        try {
            initChart();
            console.log('Graphique initialisé avec succès');
        } catch (error) {
            console.error('Erreur lors de l\'initialisation du graphique:', error);
        }
        
        // Gestionnaire d'événements pour le bouton de basculement de type de graphique
        document.getElementById('toggleChartType')?.addEventListener('click', function() {
            const typeButton = this;
            const currentType = typeButton.dataset.type;
            const newType = currentType === 'bar' ? 'line' : 'bar';
            
            // Mettre à jour le bouton
            typeButton.dataset.type = newType;
            typeButton.innerHTML = newType === 'bar' 
                ? '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>Barres'
                : '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l5-5 5 5m0 0l-5 5-5-5" /></svg>Lignes';
            
            // Changer le style du bouton
            if (newType === 'line') {
                typeButton.classList.remove('bg-gray-100', 'text-gray-700');
                typeButton.classList.add('bg-indigo-100', 'text-indigo-700');
            } else {
                typeButton.classList.remove('bg-indigo-100', 'text-indigo-700');
                typeButton.classList.add('bg-gray-100', 'text-gray-700');
            }
            
            // Mettre à jour le graphique
            chartType = newType;
            initChart();
        });

        // Gestionnaire d'événements pour le bouton de basculement de vue
        document.getElementById('toggleChartView')?.addEventListener('click', function() {
            const viewButton = this;
            const currentView = viewButton.dataset.view;
            let newView, newText;
            
            // Déterminer la nouvelle vue
            if (currentView === 'combined') {
                newView = 'sales';
                newText = 'Ventes';
            } else if (currentView === 'sales') {
                newView = 'revenue';
                newText = 'Revenu';
            } else {
                newView = 'combined';
                newText = 'Vue combinée';
            }
            
            // Mettre à jour le bouton
            viewButton.dataset.view = newView;
            viewButton.textContent = newText;
            
            // Mettre à jour le style du bouton
            if (newView === 'combined') {
                viewButton.classList.remove('bg-gray-100', 'text-gray-700');
                viewButton.classList.add('bg-indigo-100', 'text-indigo-700');
            } else {
                viewButton.classList.remove('bg-indigo-100', 'text-indigo-700');
                viewButton.classList.add('bg-gray-100', 'text-gray-700');
            }
            
            // Mettre à jour le graphique
            chartView = newView;
            initChart();
        });

        // Initialiser le graphique au chargement de la page
        initChart();

        // Rafraîchir le graphique lorsque les données Livewire sont mises à jour
        Livewire.on('statsUpdated', () => {
            // Ici, vous pourriez recharger les données depuis le serveur si nécessaire
            // Par exemple: salesData = @this.stats.chartData;
            initChart();
        });
    });

    // Fonction pour détruire le graphique existant
    function destroyChart() {
        if (salesChart) {
            console.log('Destruction du graphique existant');
            salesChart.destroy();
            salesChart = null;
        }
    }
    
    // Initialisation au chargement du DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM chargé, initialisation du graphique...');
            initializeChart();
        });
    } else {
        console.log('DOM déjà chargé, initialisation du graphique...');
        setTimeout(initializeChart, 100); // Petit délai pour s'assurer que tout est prêt
    }

    // Réinitialiser le graphique lors des mises à jour Livewire
    document.addEventListener('livewire:update', function() {
        console.log('Mise à jour Livewire détectée, réinitialisation du graphique...');
        destroyChart();
        setTimeout(initializeChart, 100); // Petit délai pour s'assurer que les données sont mises à jour
    });
    
    // Exposer la fonction de rafraîchissement pour un accès facile
    window.refreshChart = function() {
        console.log('Rafraîchissement manuel du graphique...');
        destroyChart();
        initializeChart();
    };
</script>
@endpush
