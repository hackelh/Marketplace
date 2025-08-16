<?php

namespace App\Livewire\Vendeur;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Tissu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TissuController extends Component
{
    use WithFileUploads, WithPagination;

    public $titre, $description, $prix, $quantite, $categorie, $couleur, $matiere, $image;
    public $tissuId;
    public $isOpen = false;
    public $search = '';
    public $perPage = 10;

    protected $rules = [
        'titre' => 'required|string|max:255',
        'description' => 'required|string',
        'prix' => 'required|numeric|min:0',
        'quantite' => 'required|integer|min:0',
        'categorie' => 'required|string|max:255',
        'couleur' => 'required|string|max:100',
        'matiere' => 'required|string|max:100',
        'image' => 'nullable|image|max:2048',
    ];

    public function render()
    {
        $tissus = Tissu::where('user_id', auth()->id())
            ->when($this->search, function($query) {
                $query->where('titre', 'like', '%'.$this->search.'%')
                      ->orWhere('description', 'like', '%'.$this->search.'%')
                      ->orWhere('categorie', 'like', '%'.$this->search.'%');
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.vendeur.tissu-controller', [
            'tissus' => $tissus
        ]);
    }

    public function create()
    {
        \Log::info('Méthode create() appelée');
        $this->resetInputFields();
        \Log::info('Avant openModal() - isOpen:', ['isOpen' => $this->isOpen]);
        $this->openModal();
        \Log::info('Après openModal() - isOpen:', ['isOpen' => $this->isOpen]);
        $this->dispatch('isOpenUpdated', value: $this->isOpen);
    }

    public function updatedIsOpen($value)
    {
        $this->dispatch('isOpenUpdated', value: $value);
    }

    public function openModal()
    {
        \Log::info('Méthode openModal() appelée');
        $this->isOpen = true;
        \Log::info('openModal() - isOpen défini à:', ['isOpen' => $this->isOpen]);
        // Déclencher manuellement un événement pour forcer la mise à jour du DOM
        $this->dispatch('modalOpened', name: 'tissu-modal');
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    private function resetInputFields()
    {
        $this->titre = '';
        $this->description = '';
        $this->prix = '';
        $this->quantite = '';
        $this->categorie = '';
        $this->couleur = '';
        $this->matiere = '';
        $this->image = null;
        $this->tissuId = null;
    }

    public function store()
    {
        $this->validate();

        // Récupérer ou créer la catégorie avec un slug
        $categorie = \App\Models\Categorie::firstOrCreate(
            ['nom' => $this->categorie],
            [
                'slug' => \Illuminate\Support\Str::slug($this->categorie),
                'est_actif' => true
            ]
        );

        // Log pour déboguer la valeur de quantite
        \Log::info('Valeur de quantite avant création:', ['quantite' => $this->quantite]);

        $data = [
            'titre' => $this->titre,
            'description' => $this->description,
            'prix' => $this->prix,
            'quantite' => (int)$this->quantite, // S'assurer que c'est un entier
            'categorie_id' => $categorie->id,
            'couleur' => $this->couleur,
            'matiere' => $this->matiere,
            'user_id' => auth()->id(),
            'disponible' => $this->quantite > 0,
            'is_published' => true, // Par défaut, les produits sont publiés
        ];

        // Log pour vérifier les données avant sauvegarde
        \Log::info('Données avant sauvegarde:', $data);

        if ($this->image) {
            $data['image'] = $this->image->store('tissus', 'public');
        }

        $tissu = Tissu::updateOrCreate(['id' => $this->tissuId], $data);

        // Déclencher un événement Livewire pour rafraîchir le dashboard
        if (!$this->tissuId) {
            $this->dispatch('tissu-created');
        }

        session()->flash('message', 
            $this->tissuId ? 'Tissu mis à jour avec succès.' : 'Tissu créé avec succès.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $tissu = Tissu::with('categorie')->findOrFail($id);
        $this->tissuId = $id;
        $this->titre = $tissu->titre;
        $this->description = $tissu->description;
        $this->prix = $tissu->prix;
        $this->quantite = $tissu->quantite;
        $this->categorie = $tissu->categorie ? $tissu->categorie->nom : '';
        $this->couleur = $tissu->couleur;
        $this->matiere = $tissu->matiere;
        
        $this->openModal();
    }

    public function delete($id)
    {
        $tissu = Tissu::findOrFail($id);
        
        // Supprimer l'image si elle existe
        if ($tissu->image) {
            Storage::disk('public')->delete($tissu->image);
        }
        
        $tissu->delete();
        session()->flash('message', 'Tissu supprimé avec succès.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
