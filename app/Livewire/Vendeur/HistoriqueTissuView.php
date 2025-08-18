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

    // Formulaire de mouvement
    public string $mouvementType = 'ajout'; // ajout | vente | ajustement
    public $quantite = null;               // pour ajout/vente
    public $nouvelleQuantite = null;       // pour ajustement
    public ?string $motif = null;
    public ?string $reference = null;
    public ?string $notes = null;
    public ?string $successMessage = null;

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

    public function enregistrerMouvement(): void
    {
        // Validation de base selon le type
        $rules = [
            'mouvementType' => 'required|in:ajout,vente,ajustement',
            'motif' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ];

        if (in_array($this->mouvementType, ['ajout', 'vente'])) {
            $rules['quantite'] = 'required|integer|min:1';
        } else { // ajustement
            $rules['nouvelleQuantite'] = 'required|integer|min:0';
        }

        $this->validate($rules);

        $tissu = Tissu::where('user_id', auth()->id())->findOrFail($this->tissuId);
        $avant = (int) $tissu->stock;
        $mouvement = 0;
        $apres = $avant;

        if ($this->mouvementType === 'ajout') {
            $mouvement = (int) $this->quantite; // positif
            $apres = $avant + $mouvement;
        } elseif ($this->mouvementType === 'vente') {
            $mouvement = -abs((int) $this->quantite); // négatif
            // Empêcher stock négatif
            if ($avant + $mouvement < 0) {
                $this->addError('quantite', 'Quantité vendue supérieure au stock disponible.');
                return;
            }
            $apres = $avant + $mouvement;
        } else { // ajustement à une nouvelle quantité
            $apres = (int) $this->nouvelleQuantite;
            $mouvement = $apres - $avant; // peut être + ou -
        }

        // Mettre à jour le stock
        $tissu->stock = $apres;
        $tissu->save();

        // Enregistrer l'historique
        HistoriqueTissu::create([
            'tissu_id' => $tissu->id,
            'user_id' => auth()->id(),
            'type_mouvement' => $this->mouvementType,
            'quantite_avant' => $avant,
            'quantite_apres' => $apres,
            'quantite_mouvement' => $mouvement,
            'motif' => $this->motif,
            'reference' => $this->reference,
            'notes' => $this->notes,
        ]);

        // Rafraîchir l'état local
        $this->tissu = $tissu->fresh();
        $this->resetPage();
        $this->successMessage = 'Mouvement enregistré avec succès.';

        // Réinitialiser le formulaire
        $this->quantite = null;
        $this->nouvelleQuantite = null;
        $this->motif = null;
        $this->reference = null;
        $this->notes = null;
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