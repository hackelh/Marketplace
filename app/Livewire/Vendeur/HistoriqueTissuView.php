<?php

namespace App\Livewire\Vendeur;

use App\Models\Tissu;
use App\Models\HistoriqueTissu;
use Livewire\Component;
use Livewire\WithPagination;

class HistoriqueTissuView extends Component
{
    use WithPagination;

    public $tissuId;
    public $tissu;
    public $typeFilter = '';
    public $dateDebut = '';
    public $dateFin = '';

    protected $queryString = [
        'typeFilter' => ['except' => ''],
        'dateDebut' => ['except' => ''],
        'dateFin' => ['except' => ''],
    ];

    public function mount($tissuId)
    {
        $this->tissuId = $tissuId;
        $this->tissu = Tissu::where('user_id', auth()->id())->findOrFail($tissuId);
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingDateDebut()
    {
        $this->resetPage();
    }

    public function updatingDateFin()
    {
        $this->resetPage();
    }

    public function getHistoriqueProperty()
    {
        $query = HistoriqueTissu::with('utilisateur')
            ->where('tissu_id', $this->tissuId);

        // Filtre par type
        if (!empty($this->typeFilter)) {
            $query->where('type_mouvement', $this->typeFilter);
        }

        // Filtre par date
        if (!empty($this->dateDebut)) {
            $query->whereDate('created_at', '>=', $this->dateDebut);
        }

        if (!empty($this->dateFin)) {
            $query->whereDate('created_at', '<=', $this->dateFin);
        }

        return $query->orderBy('created_at', 'desc')->paginate(15);
    }

    public function getStatistiquesProperty()
    {
        $historique = HistoriqueTissu::where('tissu_id', $this->tissuId);

        return [
            'total_mouvements' => $historique->count(),
            'ajouts' => $historique->where('type_mouvement', 'ajout')->sum('quantite_mouvement'),
            'ventes' => abs($historique->where('type_mouvement', 'vente')->sum('quantite_mouvement')),
            'ajustements' => $historique->where('type_mouvement', 'ajustement')->sum('quantite_mouvement'),
            'dernier_mouvement' => $historique->latest()->first(),
        ];
    }

    public function render()
    {
        return view('livewire.vendeur.historique-tissu-view', [
            'historique' => $this->historique,
            'statistiques' => $this->statistiques,
            'typesMouvement' => HistoriqueTissu::getTypesMouvement(),
        ]);
    }
}
