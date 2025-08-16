<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Commande;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CommandeController extends Component
{
    use WithPagination;
    
    /**
     * Propriétés publiques
     */
    public $activeTab = 'toutes';
    public $perPage = 10;
    public $tabs = [];
    public $isCancelling = false;
    public $commande;
    
    // Options de pagination
    public $paginationOptions = [5, 10, 25, 50];

    // Écouteurs d'événements Livewire
    protected $listeners = ['commandeUpdated' => 'refreshTabs'];

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
                'count' => Commande::where('user_id', $userId)->count(),
                'status' => null
            ],
            'en_attente' => [
                'label' => 'En attente',
                'count' => Commande::where('user_id', $userId)->where('statut', 'en_attente')->count(),
                'status' => 'en_attente'
            ],
            'en_cours' => [
                'label' => 'En cours',
                'count' => Commande::where('user_id', $userId)->where('statut', 'en_cours')->count(),
                'status' => 'en_cours'
            ],
            'expediee' => [
                'label' => 'Expédiée',
                'count' => Commande::where('user_id', $userId)->where('statut', 'expediee')->count(),
                'status' => 'expediee'
            ],
            'livree' => [
                'label' => 'Livrée',
                'count' => Commande::where('user_id', $userId)->where('statut', 'livree')->count(),
                'status' => 'livree'
            ],
            'annulee' => [
                'label' => 'Annulée',
                'count' => Commande::where('user_id', $userId)->where('statut', 'annulee')->count(),
                'status' => 'annulee'
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
    protected function getCommandesByTab()
    {
        $query = Commande::with(['items', 'items.tissu', 'adresse'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        if ($this->activeTab !== 'toutes') {
            $query->where('statut', $this->activeTab);
        }

        return $query->paginate($this->perPage);
    }

    /**
     * Rendu de la vue
     */
    /**
     * Affiche les détails d'une commande spécifique
     */
    public function show($reference)
    {
        $this->commande = Commande::with(['items', 'items.tissu'])
            ->where('user_id', Auth::id())
            ->where('reference', $reference)
            ->firstOrFail();
            
        return view('livewire.client.commande-show', [
            'commande' => $this->commande
        ]);
    }
    
    /**
     * Rendu de la vue
     */
    public function render()
    {
        $commandes = $this->getCommandesByTab();
        
        return view('livewire.client.commande-index', [
            'commandes' => $commandes
        ]);
    }
}
