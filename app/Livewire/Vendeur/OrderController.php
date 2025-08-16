<?php

namespace App\Livewire\Vendeur;

use App\Models\Order;
use App\Models\OrderItem;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class OrderController extends Component
{
    use WithPagination;

    public $perPage = 10;
    public $search = '';
    public $statusFilter = '';
    public $dateFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'dateFilter' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    // Options pour les filtres
    public $statusOptions = [
        '' => 'Tous les statuts',
        'pending' => 'En attente',
        'processing' => 'En cours de traitement',
        'shipped' => 'Expédiée',
        'delivered' => 'Livrée',
        'cancelled' => 'Annulée',
    ];

    // Options de tri
    public $sortOptions = [
        'created_at_desc' => 'Date (plus récent)',
        'created_at_asc' => 'Date (plus ancien)',
        'total_desc' => 'Montant (décroissant)',
        'total_asc' => 'Montant (croissant)',
    ];

    // Récupérer les commandes du vendeur
    public function getOrders()
    {
        $query = Order::whereHas('items', function($q) {
                $q->whereHas('tissu', function($q) {
                    $q->where('user_id', Auth::id());
                });
            })
            ->with(['items.tissu', 'user'])
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('order_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function($q) {
                            $q->where('name', 'like', '%' . $this->search . '%')
                              ->orWhere('email', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->statusFilter, function($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->dateFilter, function($query) {
                $date = now()->subDays((int)$this->dateFilter);
                $query->where('created_at', '>=', $date);
            });

        // Gestion du tri
        if ($this->sortField) {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        return $query->paginate($this->perPage);
    }

    // Mettre à jour le statut d'une commande (méthode interne)
    protected function updateOrderStatus($orderId, $status, $redirectToShow = false)
    {
        $order = Order::whereHas('items.tissu', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->findOrFail($orderId);

        $order->update(['status' => $status]);
        
        // Si la commande est annulée, remettre les produits en stock
        if ($status === 'cancelled') {
            $this->restoreStock($order);
        }
        
        $message = 'Le statut de la commande a été mis à jour avec succès.';
        
        if ($redirectToShow) {
            session()->flash('message', $message);
            return redirect()->route('vendeur.commandes.show', $order->id);
        }
        
        session()->flash('message', $message);
        return null;
    }

    // Restaurer le stock des articles d'une commande annulée
    protected function restoreStock(Order $order)
    {
        foreach ($order->items as $item) {
            if ($item->tissu) {
                $item->tissu->incrementerStock($item->quantity);
            }
        }
    }

    // Obtenir le montant total des commandes pour le vendeur
    public function getTotalSales()
    {
        return Order::whereHas('items.tissu', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->where('status', '!=', 'cancelled')
            ->sum('total');
    }

    // Obtenir le nombre de commandes pour le vendeur
    public function getOrderCount()
    {
        return Order::whereHas('items.tissu', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->count();
    }

    // Obtenir le nombre de produits vendus
    public function getProductsSoldCount()
    {
        try {
            if (!Auth::check()) {
                \Log::warning('Tentative d\'accès à getProductsSoldCount() sans authentification');
                return 0;
            }
            
            $userId = Auth::id();
            \Log::info('Début de getProductsSoldCount()', ['user_id' => $userId]);
            
            // Vérifier d'abord s'il y a des OrderItem sans tissu valide
            $count = 0;
            
            // Méthode plus robuste avec une jointure directe pour éviter les problèmes de relation
            $count = \DB::table('order_items')
                ->join('tissus', 'order_items.tissu_id', '=', 'tissus.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('tissus.user_id', $userId)
                ->where('orders.status', '!=', 'cancelled')
                ->sum('order_items.quantity');
            
            \Log::info('Résultat de getProductsSoldCount()', ['count' => $count, 'user_id' => $userId]);
            
            return $count;
            
        } catch (\Exception $e) {
            \Log::error('Erreur critique dans getProductsSoldCount()', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id() ?? 'non authentifié'
            ]);
            
            // En cas d'erreur, on retourne 0 pour éviter de casser l'affichage
            return 0;
        }
    }

    // Gérer le changement de tri
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        
        $this->sortField = $field;
    }

    // Réinitialiser les filtres
    public function resetFilters()
    {
        $this->reset(['search', 'statusFilter', 'dateFilter']);
        $this->resetPage();
    }

    /**
     * Afficher les détails d'une commande spécifique
     */
    public function show($orderId)
    {
        $order = Order::whereHas('items.tissu', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->with(['items.tissu', 'user'])
            ->findOrFail($orderId);
            
        return view('livewire.vendeur.orders.show', [
            'order' => $order,
            'statusOptions' => $this->statusOptions
        ]);
    }
    
    /**
     * Mettre à jour le statut d'une commande (accessible depuis la liste des commandes)
     */
    public function updateStatus($orderId, $status)
    {
        return $this->updateOrderStatus($orderId, $status, false);
    }
    
    /**
     * Mettre à jour le statut d'une commande depuis la page de détail
     */
    public function updateStatusFromShow($orderId, $status)
    {
        return $this->updateOrderStatus($orderId, $status, true);
    }
    
    public function render()
    {
        return view('livewire.vendeur.order-controller', [
            'orders' => $this->getOrders(),
            'totalSales' => $this->getTotalSales(),
            'orderCount' => $this->getOrderCount(),
            'productsSold' => $this->getProductsSoldCount(),
            'statusOptions' => $this->statusOptions,
        ]);
    }
}
