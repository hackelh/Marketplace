<?php

namespace App\Livewire\Vendeur;

use Livewire\Component;
use App\Models\Categorie;
use Livewire\WithPagination;

class GestionCategories extends Component
{
    use WithPagination;

    // Propriétés pour les modals
    public $showAjouterModal = false;
    public $showModifierModal = false;
    public $showSupprimerModal = false;

    // Propriétés pour les formulaires
    public $nom = '';
    public $description = '';
    public $couleur_hex = '';
    
    // ID de la catégorie en cours d'édition/suppression
    public $categorieId = null;

    // Propriétés pour les filtres
    public $recherche = '';
    public $triPar = 'recent';

    protected $rules = [
        'nom' => 'required|string|max:255|unique:categories,nom',
        'description' => 'required|string|max:500',
        'couleur_hex' => 'required|string|size:7',
    ];

    protected $messages = [
        'nom.required' => 'Le nom de la catégorie est obligatoire.',
        'nom.unique' => 'Cette catégorie existe déjà.',
        'description.required' => 'La description est obligatoire.',
        'couleur_hex.required' => 'La couleur est obligatoire.',
        'couleur_hex.size' => 'Format de couleur invalide (ex: #FF6B35).',
    ];

    public function render()
    {
        $categories = Categorie::query()
            ->when($this->recherche, function ($query) {
                $query->where('nom', 'like', '%' . $this->recherche . '%')
                      ->orWhere('description', 'like', '%' . $this->recherche . '%');
            })
            ->when($this->triPar === 'nom', function ($query) {
                $query->orderBy('nom', 'asc');
            })
            ->when($this->triPar === 'recent', function ($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->paginate(10);

        $statistiques = [
            'total_categories' => Categorie::count(),
            'categories_utilisees' => Categorie::has('tissus')->count(),
        ];

        return view('livewire.vendeur.gestion-categories', [
            'categories' => $categories,
            'statistiques' => $statistiques,
        ]);
    }

    public function ouvrirAjouterModal()
    {
        $this->resetForm();
        $this->showAjouterModal = true;
    }

    public function ouvrirModifierModal($id)
    {
        $categorie = Categorie::findOrFail($id);
        $this->categorieId = $id;
        $this->nom = $categorie->nom;
        $this->description = $categorie->description;
        $this->couleur_hex = $categorie->couleur_hex;
        $this->showModifierModal = true;
    }

    public function ouvrirSupprimerModal($id)
    {
        $this->categorieId = $id;
        $this->showSupprimerModal = true;
    }

    public function fermerModals()
    {
        $this->showAjouterModal = false;
        $this->showModifierModal = false;
        $this->showSupprimerModal = false;
        $this->resetForm();
    }

    public function ajouterCategorie()
    {
        $this->validate();

        Categorie::create([
            'nom' => $this->nom,
            'description' => $this->description,
            'couleur_hex' => $this->couleur_hex,
        ]);

        session()->flash('success', 'Catégorie ajoutée avec succès !');
        $this->fermerModals();
        $this->resetPage();
    }

    public function modifierCategorie()
    {
        $this->validate([
            'nom' => 'required|string|max:255|unique:categories,nom,' . $this->categorieId,
            'description' => 'required|string|max:500',
            'couleur_hex' => 'required|string|size:7',
        ]);

        $categorie = Categorie::findOrFail($this->categorieId);
        $categorie->update([
            'nom' => $this->nom,
            'description' => $this->description,
            'couleur_hex' => $this->couleur_hex,
        ]);

        session()->flash('success', 'Catégorie modifiée avec succès !');
        $this->fermerModals();
    }

    public function supprimerCategorie()
    {
        $categorie = Categorie::findOrFail($this->categorieId);
        
        // Vérifier si la catégorie est utilisée
        if ($categorie->tissus()->count() > 0) {
            session()->flash('error', 'Impossible de supprimer cette catégorie car elle est utilisée par des tissus.');
            $this->fermerModals();
            return;
        }

        $categorie->delete();
        session()->flash('success', 'Catégorie supprimée avec succès !');
        $this->fermerModals();
        $this->resetPage();
    }

    private function resetForm()
    {
        $this->nom = '';
        $this->description = '';
        $this->couleur_hex = '#FF6B35';
        $this->categorieId = null;
        $this->resetErrorBag();
    }

    public function updatingRecherche()
    {
        $this->resetPage();
    }

    public function updatingTriPar()
    {
        $this->resetPage();
    }
}
