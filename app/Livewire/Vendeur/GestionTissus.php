<?php

namespace App\Livewire\Vendeur;

use App\Models\Tissu;
use App\Models\Categorie;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class GestionTissus extends Component
{
    use WithPagination, WithFileUploads;

    // Propriétés pour les filtres
    public $recherche = '';
    public $categorieSelectionnee = '';
    public $triPar = 'recent';

    // Propriétés pour les modals
    public $showAjouterModal = false;
    public $showModifierModal = false;
    public $showSupprimerModal = false;

    // Propriétés pour le formulaire
    public $tissuId;
    public $nom = '';
    public $description = '';
    public $prix = '';
    public $couleur = '';
    public $stock = '';
    public $origine = '';
    public $composition = '';
    public $categorie_id = '';
    public $image;
    public $disponible = true;

    // Propriétés pour l'affichage
    public $categories;

    protected $rules = [
        'nom' => 'required|string|max:255',
        'description' => 'required|string',
        'prix' => 'required|numeric|min:0',
        'couleur' => 'required|string|max:100',
        'stock' => 'required|integer|min:0',
        'origine' => 'nullable|string|max:100',
        'composition' => 'nullable|string|max:255',
        'categorie_id' => 'required|exists:categories,id',
        'image' => 'nullable|image|max:2048', // 2MB max
    ];

    public function mount()
    {
        $this->categories = Categorie::orderBy('nom')->get();
    }

    // Réinitialiser la pagination lors des filtres
    public function updatingRecherche()
    {
        $this->resetPage();
    }

    public function updatingCategorieSelectionnee()
    {
        $this->resetPage();
    }

    public function updatingTriPar()
    {
        $this->resetPage();
    }

    // Ouvrir le modal d'ajout
    public function ouvrirAjouterModal()
    {
        $this->resetForm();
        $this->showAjouterModal = true;
    }

    // Ouvrir le modal de modification
    public function ouvrirModifierModal($tissuId)
    {
        $tissu = Tissu::findOrFail($tissuId);
        
        $this->tissuId = $tissu->id;
        $this->nom = $tissu->nom;
        $this->description = $tissu->description;
        $this->prix = $tissu->prix;
        $this->couleur = $tissu->couleur;
        $this->stock = $tissu->stock;
        $this->origine = $tissu->origine;
        $this->composition = $tissu->composition;
        $this->categorie_id = $tissu->categorie_id;
        $this->disponible = $tissu->disponible;
        
        $this->showModifierModal = true;
    }

    // Ouvrir le modal de suppression
    public function ouvrirSupprimerModal($tissuId)
    {
        $this->tissuId = $tissuId;
        $this->showSupprimerModal = true;
    }

    // Ajouter un tissu
    public function ajouterTissu()
    {
        $this->validate();

        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('tissus', 'public');
        }

        Tissu::create([
            'nom' => $this->nom,
            'description' => $this->description,
            'prix' => $this->prix,
            'couleur' => $this->couleur,
            'stock' => $this->stock,
            'origine' => $this->origine,
            'composition' => $this->composition,
            'categorie_id' => $this->categorie_id,
            'image' => $imagePath,
            'disponible' => $this->disponible,
            'user_id' => auth()->id(),
        ]);

        $this->showAjouterModal = false;
        $this->resetForm();
        session()->flash('success', 'Tissu ajouté avec succès !');
    }

    // Modifier un tissu
    public function modifierTissu()
    {
        $this->validate();

        $tissu = Tissu::findOrFail($this->tissuId);

        $imagePath = $tissu->image;
        if ($this->image) {
            // Supprimer l'ancienne image si elle existe
            if ($tissu->image) {
                \Storage::disk('public')->delete($tissu->image);
            }
            $imagePath = $this->image->store('tissus', 'public');
        }

        $tissu->update([
            'nom' => $this->nom,
            'description' => $this->description,
            'prix' => $this->prix,
            'couleur' => $this->couleur,
            'stock' => $this->stock,
            'origine' => $this->origine,
            'composition' => $this->composition,
            'categorie_id' => $this->categorie_id,
            'image' => $imagePath,
            'disponible' => $this->disponible,
        ]);

        $this->showModifierModal = false;
        $this->resetForm();
        session()->flash('success', 'Tissu modifié avec succès !');
    }

    // Supprimer un tissu
    public function supprimerTissu()
    {
        $tissu = Tissu::findOrFail($this->tissuId);
        
        // Supprimer l'image si elle existe
        if ($tissu->image) {
            \Storage::disk('public')->delete($tissu->image);
        }
        
        $tissu->delete();
        
        $this->showSupprimerModal = false;
        session()->flash('success', 'Tissu supprimé avec succès !');
    }

    // Basculer la disponibilité
    public function toggleDisponibilite($tissuId)
    {
        $tissu = Tissu::findOrFail($tissuId);
        $tissu->update(['disponible' => !$tissu->disponible]);
        
        $status = $tissu->disponible ? 'disponible' : 'indisponible';
        session()->flash('success', "Tissu marqué comme {$status} !");
    }

    // Réinitialiser le formulaire
    private function resetForm()
    {
        $this->tissuId = null;
        $this->nom = '';
        $this->description = '';
        $this->prix = '';
        $this->couleur = '';
        $this->stock = '';
        $this->origine = '';
        $this->composition = '';
        $this->categorie_id = '';
        $this->image = null;
        $this->disponible = true;
    }

    // Fermer les modals
    public function fermerModals()
    {
        $this->showAjouterModal = false;
        $this->showModifierModal = false;
        $this->showSupprimerModal = false;
        $this->resetForm();
    }

    // Obtenir les tissus du vendeur connecté
    public function getTissusProperty()
    {
        $query = Tissu::with('categorie')
            ->where('user_id', auth()->id());

        // Filtre par recherche
        if (!empty($this->recherche)) {
            $query->where(function ($q) {
                $q->where('nom', 'like', '%' . $this->recherche . '%')
                  ->orWhere('description', 'like', '%' . $this->recherche . '%')
                  ->orWhere('couleur', 'like', '%' . $this->recherche . '%');
            });
        }

        // Filtre par catégorie
        if (!empty($this->categorieSelectionnee)) {
            $query->where('categorie_id', $this->categorieSelectionnee);
        }

        // Tri
        switch ($this->triPar) {
            case 'nom':
                $query->orderBy('nom', 'asc');
                break;
            case 'prix_asc':
                $query->orderBy('prix', 'asc');
                break;
            case 'prix_desc':
                $query->orderBy('prix', 'desc');
                break;
            case 'stock':
                $query->orderBy('stock', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        return $query->paginate(10);
    }

    // Obtenir les statistiques
    public function getStatistiquesProperty()
    {
        $userId = auth()->id();
        
        return [
            'total_tissus' => Tissu::where('user_id', $userId)->count(),
            'tissus_disponibles' => Tissu::where('user_id', $userId)->where('disponible', true)->count(),
            'stock_total' => Tissu::where('user_id', $userId)->sum('stock'),
            'valeur_stock' => Tissu::where('user_id', $userId)->selectRaw('SUM(prix * stock) as total')->first()->total ?? 0,
        ];
    }

    public function render()
    {
        return view('livewire.vendeur.gestion-tissus', [
            'tissus' => $this->tissus,
            'statistiques' => $this->statistiques,
        ]);
    }
}
