<?php

namespace App\Livewire\Vendeur;

use Livewire\Component;
use App\Models\Tissu;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $layout = 'components.layouts.app';
    
    public $stats = [
        'total_products' => 0,
        'total_sales' => 0,
        'total_revenue' => 0,
        'low_stock_items' => 0,
        'unique_customers' => 0,
        'conversion_rate' => 0,
        'average_cart' => 0,
        'sales_trend' => 0, // Pourcentage de variation
        'revenue_trend' => 0, // Pourcentage de variation
        'customer_trend' => 0, // Pourcentage de variation
        'conversion_trend' => 0, // Pourcentage de variation
    ];

    public $recentOrders = [];
    public $topProducts = [];
    public $salesData = [];
    public $revenueData = [];
    public $labels = [];
    public $period = '30d';
    public $chartType = 'line';
    public $chartView = 'sales';
    public $orderStatusFilter = 'all';
    
    // Propriétés pour la pagination des commandes
    public $ordersPerPage = 5;
    public $currentPage = 1;
    public $hasMoreOrders = true;

    public function mount()
    {
        $this->loadAllData();
    }
    
    protected $listeners = ['tissu-created' => 'refreshDashboard'];
    
    public function loadAllData()
    {
        $this->loadStats();
        $this->loadRecentOrders();
        $this->loadTopProducts();
        $this->loadSalesData();
    }

    private function loadStats()
    {
        $userId = auth()->id();
        $cacheKey = 'vendor_stats_' . $userId . '_' . $this->period;
        
        $this->stats = cache()->remember($cacheKey, now()->addMinutes(15), function() use ($userId) {
            $currentPeriod = $this->getPeriodDates($this->period);
            $previousPeriod = $this->getPreviousPeriodDates($this->period);
            
            $stats = [
                'total_products' => 0,
                'total_sales' => 0,
                'total_revenue' => 0,
                'low_stock_items' => 0,
                'unique_customers' => 0,
                'conversion_rate' => 0,
                'average_cart' => 0,
                'sales_trend' => 0,
                'revenue_trend' => 0,
                'customer_trend' => 0,
                'conversion_trend' => 0,
            ];
            
            // Statistiques de base
            $stats['total_products'] = Tissu::where('user_id', $userId)->count();
            $stats['low_stock_items'] = Tissu::where('user_id', $userId)
                ->where('quantite', '<=', 5)
                ->count();
            
            // Statistiques de vente pour la période actuelle
            $currentStats = $this->getSalesStats($userId, $currentPeriod['start'], $currentPeriod['end']);
            $previousStats = $this->getSalesStats($userId, $previousPeriod['start'], $previousPeriod['end']);
            
            $stats['total_sales'] = $currentStats['total_orders'] ?? 0;
            $stats['total_revenue'] = $currentStats['total_revenue'] ?? 0;
            $stats['unique_customers'] = $currentStats['unique_customers'] ?? 0;
            
            // Calcul des tendances
            $stats['sales_trend'] = $this->calculateTrend(
                $previousStats['total_orders'] ?? 0,
                $currentStats['total_orders'] ?? 0
            );
            
            $stats['revenue_trend'] = $this->calculateTrend(
                $previousStats['total_revenue'] ?? 0,
                $currentStats['total_revenue'] ?? 0
            );
            
            $stats['customer_trend'] = $this->calculateTrend(
                $previousStats['unique_customers'] ?? 0,
                $currentStats['unique_customers'] ?? 0
            );
            
            // Calcul du panier moyen
            $stats['average_cart'] = $currentStats['total_orders'] > 0 
                ? round($currentStats['total_revenue'] / $currentStats['total_orders'], 2)
                : 0;
            
            // Calcul du taux de conversion (basé sur les sessions et les commandes)
            $sessions = $this->estimateSessions($currentStats['unique_customers']);
            $stats['conversion_rate'] = $sessions > 0 
                ? round(($currentStats['total_orders'] / $sessions) * 100, 2)
                : 0;
                
            $previousSessions = $this->estimateSessions($previousStats['unique_customers'] ?? 0);
            $previousConversion = $previousSessions > 0 
                ? round((($previousStats['total_orders'] ?? 0) / $previousSessions) * 100, 2)
                : 0;
                
            $stats['conversion_trend'] = $this->calculateTrend(
                $previousConversion,
                $stats['conversion_rate']
            );
            
            return $stats;
        });
    }

    private function loadRecentOrders()
    {
        $query = Order::whereHas('items.tissu', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->with(['user', 'items.tissu']);
            
        // Appliquer le filtre de statut si nécessaire
        if ($this->orderStatusFilter !== 'all') {
            $query->where('status', $this->orderStatusFilter);
        }
            
        // Calculer l'offset pour la pagination
        $offset = ($this->currentPage - 1) * $this->ordersPerPage;
        
        $orders = $query->latest()
            ->skip($offset)
            ->take($this->ordersPerPage)
            ->get();
            
        // Vérifier s'il y a plus de commandes à charger
        $this->hasMoreOrders = $orders->count() === $this->ordersPerPage;
        
        // Si c'est la première page, réinitialiser la liste des commandes
        // Sinon, ajouter les nouvelles commandes à la liste existante
        $newOrders = $orders->map(function($order) {
            // Définir les couleurs en fonction du statut
            $statusColors = [
                'pending' => 'bg-yellow-100 text-yellow-800',
                'processing' => 'bg-blue-100 text-blue-800',
                'completed' => 'bg-green-100 text-green-800',
                'cancelled' => 'bg-red-100 text-red-800',
                'shipped' => 'bg-indigo-100 text-indigo-800',
                'delivered' => 'bg-purple-100 text-purple-800',
                'refunded' => 'bg-gray-100 text-gray-800',
            ];
            
            $statusColor = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
            
            return [
                'id' => $order->id,
                'customer' => $order->user->name,
                'email' => $order->user->email,
                'date' => $order->created_at->format('d/m/Y H:i'),
                'amount' => number_format($order->total, 2, ',', ' '),
                'status' => $order->status,
                'status_label' => ucfirst($order->status),
                'status_color' => $statusColor,
                'item_count' => $order->items->count(),
                'items' => $order->items->map(function($item) {
                    return [
                        'name' => $item->tissu->titre,
                        'quantity' => $item->quantity,
                        'price' => number_format($item->price, 2, ',', ' ')
                    ];
                })->toArray()
            ];
        })->toArray();
        
        // Si c'est la première page, réinitialiser la liste des commandes
        // Sinon, ajouter les nouvelles commandes à la liste existante
        if ($this->currentPage === 1) {
            $this->recentOrders = $newOrders;
        } else {
            $this->recentOrders = array_merge($this->recentOrders, $newOrders);
        }
    }
    
    public function loadMoreOrders()
    {
        $this->currentPage++;
        $this->loadRecentOrders();
        
        // Émettre un événement pour indiquer que le chargement est terminé
        $this->dispatch('orders-loaded');
    }
    
    public function getHasMoreOrdersProperty()
    {
        return $this->hasMoreOrders;
    }

    private function loadTopProducts()
    {
        $this->topProducts = Tissu::where('user_id', auth()->id())
            ->withCount(['orderItems as sales_count' => function($query) {
                $query->select(DB::raw('sum(quantity)'));
            }])
            ->orderBy('sales_count', 'desc')
            ->take(5)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->titre,
                    'sales' => $product->sales_count ?? 0,
                    'revenue' => number_format(($product->sales_count ?? 0) * $product->prix, 2, ',', ' ') . ' €',
                    'stock' => $product->quantite,
                    'status' => $product->quantite > 0 ? 'En stock' : 'Rupture'
                ];
            });
    }

    private function loadSalesData()
    {
        $period = $this->getPeriodDates($this->period);
        $startDate = $period['start'];
        $endDate = $period['end'];
        
        \Log::debug('Chargement des données de vente', [
            'période' => $this->period,
            'date_début' => $startDate->toDateString(),
            'date_fin' => $endDate->toDateString()
        ]);
        
        // Utiliser le cache pour les données de vente
        $cacheKey = 'vendor_sales_data_' . auth()->id() . '_' . $this->period;
        
        $data = cache()->remember($cacheKey, now()->addMinutes(15), function() use ($startDate, $endDate) {
            // Données de vente pour la période sélectionnée
            $sales = Order::whereHas('items.tissu', function($query) {
                    $query->where('user_id', auth()->id());
                })
                ->select([
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(total) as revenue'),
                    DB::raw('COUNT(DISTINCT user_id) as customers')
                ])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', '!=', 'cancelled')
                ->groupBy('date')
                ->orderBy('date')
                ->get();
            
            // Générer toutes les dates de la période
            $dates = collect();
            $currentDate = $startDate->copy();
            $dateFormat = $this->getDateFormatForPeriod($this->period);
            
            while ($currentDate <= $endDate) {
                $dateKey = $currentDate->format('Y-m-d');
                $sale = $sales->firstWhere('date', $dateKey);
                
                $dates->push([
                    'date' => $currentDate->format($dateFormat),
                    'sales' => $sale->count ?? 0,
                    'revenue' => $sale->revenue ?? 0,
                    'customers' => $sale->customers ?? 0,
                    'full_date' => $currentDate->format('d/m/Y')
                ]);
                
                $currentDate->addDay();
            }
            
            return $dates;
        });
        
        $this->labels = $data->pluck('date');
        
        switch ($this->chartView) {
            case 'sales':
                $this->salesData = $data->pluck('sales');
                break;
            case 'revenue':
                $this->salesData = $data->pluck('revenue');
                break;
            case 'customers':
                $this->salesData = $data->pluck('customers');
                break;
            default:
                $this->salesData = $data->pluck('sales');
        }
        
        $this->revenueData = $data->pluck('revenue');
        
        \Log::debug('Données du graphique générées', [
            'labels_count' => $this->labels->count(),
            'sales_count' => $this->salesData->count(),
            'revenue_count' => $this->revenueData->count(),
            'preview_labels' => $this->labels->take(3),
            'preview_sales' => $this->salesData->take(3),
            'preview_revenue' => $this->revenueData->take(3)
        ]);
    }

    /**
     * Rafraîchir les données du tableau de bord
     */
    public function refreshDashboard()
    {
        // Effacer le cache des statistiques et des données de vente
        $this->reset('stats', 'recentOrders', 'topProducts', 'salesData', 'revenueData', 'labels');
        cache()->forget('vendor_stats_' . auth()->id());
        cache()->forget('vendor_sales_data_' . auth()->id() . '_' . $this->period);
        
        // Recharger toutes les données
        $this->loadAllData();
        
        // Émettre un événement pour indiquer que le rafraîchissement est terminé
        $this->dispatch('dashboardRefreshed');
    }

    private function getPeriodDates($period)
    {
        $endDate = now();
        
        switch ($period) {
            case '7d':
                $startDate = now()->subDays(7);
                break;
            case '30d':
                $startDate = now()->subDays(30);
                break;
            case '90d':
                $startDate = now()->subDays(90);
                break;
            case '12m':
                $startDate = now()->subMonths(12);
                break;
            default:
                $startDate = now()->subDays(30);
        }
        
        return [
            'start' => $startDate->startOfDay(),
            'end' => $endDate->endOfDay()
        ];
    }
    
    private function getPreviousPeriodDates($period)
    {
        $currentPeriod = $this->getPeriodDates($period);
        $days = $currentPeriod['start']->diffInDays($currentPeriod['end']);
        
        return [
            'start' => $currentPeriod['start']->copy()->subDays($days),
            'end' => $currentPeriod['start']->copy()->subSecond()
        ];
    }
    
    private function calculateTrend($previous, $current)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return round((($current - $previous) / $previous) * 100, 1);
    }
    
    private function getSalesStats($userId, $startDate, $endDate)
    {
        return Order::whereHas('items.tissu', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->select([
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total) as total_revenue'),
                DB::raw('COUNT(DISTINCT user_id) as unique_customers')
            ])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->first()
            ->toArray();
    }
    
    private function estimateSessions($uniqueCustomers)
    {
        // Estimation basée sur un taux de conversion moyen de 2-3% pour les e-commerces
        $conversionRate = 0.025; // 2.5%
        
        if ($conversionRate > 0) {
            return (int) ($uniqueCustomers / $conversionRate);
        }
        
        return 0;
    }
    
    private function getDateFormatForPeriod($period)
    {
        switch ($period) {
            case '7d':
            case '30d':
                return 'd M';
            case '90d':
                return 'M Y';
            case '12m':
                return 'M Y';
            default:
                return 'd M Y';
        }
    }
    
    /**
     * Méthode appelée lorsque la propriété period est mise à jour
     */
    public function updatedPeriod()
    {
        $this->reset('currentPage');
        $this->loadAllData();
    }
    
    public function render()
    {
        // Log des données pour le débogage
        \Log::debug('Dashboard data', [
            'labels_count' => count($this->labels),
            'sales_count' => count($this->salesData),
            'revenue_count' => count($this->revenueData),
            'period' => $this->period,
            'chart_view' => $this->chartView
        ]);
        
        return view('livewire.vendeur.dashboard', [
            'stats' => $this->stats,
            'recentOrders' => $this->recentOrders,
            'topProducts' => $this->topProducts,
            'salesData' => $this->salesData,
            'revenueData' => $this->revenueData,
            'labels' => $this->labels ?? [],
        ]);
    }
}
