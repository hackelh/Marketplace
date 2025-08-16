<?php

namespace App\Livewire\Vendeur;

use App\Models\Commande;
use Livewire\Component;
use Livewire\WithPagination;

class CommandesValidees extends Component
{
    use WithPagination;

    public $recherche = '';
    public $statutFilter = '';
    public $dateFilter = '';

    protected $queryString = [
        'recherche' => ['except' => ''],
        'statutFilter' => ['except' => ''],
        'dateFilter' => ['except' => ''],
    ];

    public function updatingRecherche()
    {
        $this->resetPage();
    }

    public function updatingStatutFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function getCommandesProperty()
    {
        $query = Commande::with(['client', 'details.tissu'])
            ->where('vendeur_id', auth()->id())
            ->whereIn('statut', ['livree', 'terminee']);

        // Filtre par recherche
        if (!empty($this->recherche)) {
            $query->where(function ($q) {
                $q->where('numero_commande', 'like', '%' . $this->recherche . '%')
                  ->orWhereHas('client', function ($q2) {
                      $q2->where('name', 'like', '%' . $this->recherche . '%');
                  });
            });
        }

        // Filtre par statut
        if (!empty($this->statutFilter)) {
            $query->where('statut', $this->statutFilter);
        }

        // Filtre par date
        if (!empty($this->dateFilter)) {
            switch ($this->dateFilter) {
                case 'aujourd_hui':
                    $query->whereDate('date_livraison_effective', today());
                    break;
                case 'cette_semaine':
                    $query->whereBetween('date_livraison_effective', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'ce_mois':
                    $query->whereMonth('date_livraison_effective', now()->month);
                    break;
            }
        }

        return $query->orderBy('date_livraison_effective', 'desc')->paginate(10);
    }

    public function getStatistiquesProperty()
    {
        $userId = auth()->id();
        
        return [
            'total_validees' => Commande::where('vendeur_id', $userId)
                ->whereIn('statut', ['livree', 'terminee'])
                ->count(),
            'livrees' => Commande::where('vendeur_id', $userId)
                ->where('statut', 'livree')
                ->count(),
            'terminees' => Commande::where('vendeur_id', $userId)
                ->where('statut', 'terminee')
                ->count(),
            'montant_total' => Commande::where('vendeur_id', $userId)
                ->whereIn('statut', ['livree', 'terminee'])
                ->sum('montant_total'),
        ];
    }

    public function render()
    {
        return view('livewire.vendeur.commandes-validees', [
            'commandes' => $this->commandes,
            'statistiques' => $this->statistiques,
        ]);
    }
}
