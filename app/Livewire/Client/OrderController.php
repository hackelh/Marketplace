<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Component
{
    use WithPagination;
    
    /**
     * Propriétés publiques
     */
    public $activeTab = 'toutes';
    public $perPage = 10;
    public $tabs = [];
    public $isCancelling = false;
    public $order;
    
    // Options de pagination
    public $paginationOptions = [5, 10, 25, 50];

    // Écouteurs d'événements Livewire
    protected $listeners = ['orderUpdated' => 'refreshTabs'];

    /**
     * Initialisation du composant
     */
    public function mount()
    {
        $this->initTabs();
    }

    /**
     * Change l'onglet actif
     */
    public function changeTab($tabId)
    {
        $this->activeTab = $tabId;
        $this->resetPage();
    }

    /**
     * Initialise les onglets avec leurs compteurs et statuts
     */
    protected function initTabs()
    {
        $userId = Auth::id();

        $this->tabs = [
            'toutes' => [
                'label' => 'Toutes',
                'count' => Order::where('user_id', $userId)->count(),
                'status' => null
            ],
            'pending' => [
                'label' => 'En attente',
                'count' => Order::where('user_id', $userId)->where('status', 'pending')->count(),
                'status' => 'pending'
            ],
            'processing' => [
                'label' => 'En cours',
                'count' => Order::where('user_id', $userId)->where('status', 'processing')->count(),
                'status' => 'processing'
            ],
            'shipped' => [
                'label' => 'Expédiée',
                'count' => Order::where('user_id', $userId)->where('status', 'shipped')->count(),
                'status' => 'shipped'
            ],
            'delivered' => [
                'label' => 'Livrée',
                'count' => Order::where('user_id', $userId)->where('status', 'delivered')->count(),
                'status' => 'delivered'
            ],
            'cancelled' => [
                'label' => 'Annulée',
                'count' => Order::where('user_id', $userId)->where('status', 'cancelled')->count(),
                'status' => 'cancelled'
            ],
        ];
    }

    /**
     * Rafraîchit les onglets et la pagination
     */
    public function refreshTabs()
    {
        $this->initTabs();
        $this->resetPage();
    }

    /**
     * Récupère les commandes selon l'onglet actif
     */
    protected function getOrdersByTab()
    {
        $query = Order::with(['items', 'items.tissu'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        if ($this->activeTab !== 'toutes') {
            $query->where('status', $this->tabs[$this->activeTab]['status']);
        }

        return $query->paginate($this->perPage);
    }

    /**
     * Affiche les détails d'une commande spécifique
     */
    public function show($orderNumber)
    {
        $this->order = Order::with(['items', 'items.tissu'])
            ->where('user_id', Auth::id())
            ->where('order_number', $orderNumber)
            ->firstOrFail();
            
        return view('livewire.client.order-show', [
            'order' => $this->order
        ]);
    }
    
    /**
     * Rendu de la vue
     */
    /**
     * Annuler une commande
     */
    public function annulerCommande($orderId)
    {
        try {
            $order = Order::where('user_id', Auth::id())
                        ->where('id', $orderId)
                        ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_PROCESSING])
                        ->firstOrFail();
            
            $order->update([
                'status' => Order::STATUS_CANCELLED,
                'cancelled_at' => now()
            ]);
            
            // Émettre un événement pour rafraîchir les onglets
            $this->emit('orderUpdated');
            
            // Afficher un message de succès
            session()->flash('message', 'La commande a été annulée avec succès.');
            
            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'annulation de la commande: ' . $e->getMessage());
            
            // Afficher un message d'erreur
            session()->flash('error', 'Impossible d\'annuler cette commande.');
            
            return false;
        }
    }
    
    /**
     * Rendu de la vue
     */
    public function render()
    {
        $orders = $this->getOrdersByTab();
        
        return view('livewire.client.order-index', [
            'orders' => $orders
        ]);
    }
}
