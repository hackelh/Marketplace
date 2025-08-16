<?php

namespace App\Livewire\Vendeur;

use App\Models\Commande;
use Livewire\Component;
use Livewire\WithPagination;

class CommandesEnCours extends Component
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

    public function changerStatut($commandeId, $nouveauStatut)
    {
        $commande = Commande::where('vendeur_id', auth()->id())->findOrFail($commandeId);
        $commande->update(['statut' => $nouveauStatut]);
        
        session()->flash('success', 'Statut de la commande mis à jour avec succès !');
    }

    public function getCommandesProperty()
    {
        $query = Commande::with(['client', 'details.tissu'])
            ->where('vendeur_id', auth()->id())
            ->whereIn('statut', ['en_attente', 'en_preparation', 'en_couture']);

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
                    $query->whereDate('created_at', today());
                    break;
                case 'cette_semaine':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'ce_mois':
                    $query->whereMonth('created_at', now()->month);
                    break;
            }
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function getStatistiquesProperty()
    {
        $userId = auth()->id();
        
        return [
            'total_en_cours' => Commande::where('vendeur_id', $userId)
                ->whereIn('statut', ['en_attente', 'en_preparation', 'en_couture'])
                ->count(),
            'en_attente' => Commande::where('vendeur_id', $userId)
                ->where('statut', 'en_attente')
                ->count(),
            'en_preparation' => Commande::where('vendeur_id', $userId)
                ->where('statut', 'en_preparation')
                ->count(),
            'en_couture' => Commande::where('vendeur_id', $userId)
                ->where('statut', 'en_couture')
                ->count(),
            'montant_total' => Commande::where('vendeur_id', $userId)
                ->whereIn('statut', ['en_attente', 'en_preparation', 'en_couture'])
                ->sum('montant_total'),
        ];
    }

    public function render()
    {
        return view('livewire.vendeur.commandes-en-cours', [
            'commandes' => $this->commandes,
            'statistiques' => $this->statistiques,
        ]);
    }
}
