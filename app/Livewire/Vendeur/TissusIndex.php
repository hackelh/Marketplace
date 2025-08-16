<?php

namespace App\Livewire\Vendeur;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Tissu;
use App\Models\Categorie;
use Illuminate\Support\Facades\Storage;

class TissusIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $categorieFilter = '';
    public $categories;
    public $confirmingDelete = false;
    public $tissuToDelete;

    public $editMode = false;
    public $tissuId;
    public $titre;
    public $description;
    public $prix;
    public $quantite;
    public $categorie_id;
    public $image;
    public $oldImage;
    public $couleur;
    public $matiere;
    public $largeur;
    public $poids;
    public $mots_cles;
    public $reference;

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'titre' => 'required|string|max:255',
        'description' => 'required|string',
        'prix' => 'required|numeric|min:0',
        'quantite' => 'required|integer|min:0',
        'categorie_id' => 'required|exists:categories,id',
        'image' => 'nullable|image|max:2048',
        'couleur' => 'nullable|string|max:100',
        'matiere' => 'nullable|string|max:100',
        'largeur' => 'nullable|numeric|min:0',
        'poids' => 'nullable|numeric|min:0',
        'mots_cles' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->categories = Categorie::all();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function showEditForm(Tissu $tissu)
    {
        $this->resetValidation();
        $this->editMode = true;
        $this->tissuId = $tissu->id;
        $this->titre = $tissu->titre;
        $this->description = $tissu->description;
        $this->prix = $tissu->prix;
        $this->quantite = $tissu->quantite;
        $this->categorie_id = $tissu->categorie_id;
        $this->oldImage = $tissu->image;
        $this->couleur = $tissu->couleur;
        $this->matiere = $tissu->matiere;
        $this->largeur = $tissu->largeur;
        $this->poids = $tissu->poids;
        $this->mots_cles = $tissu->mots_cles;
        $this->reference = $tissu->reference;
        
        $this->dispatch('scroll-to-edit');
    }

    public function updateTissu()
    {
        $validatedData = $this->validate();
        $tissu = Tissu::findOrFail($this->tissuId);

        // Gestion de l'image
        if ($this->image) {
            // Supprimer l'ancienne image si elle existe
            if ($this->oldImage && Storage::disk('public')->exists($this->oldImage)) {
                Storage::disk('public')->delete($this->oldImage);
            }
            $validatedData['image'] = $this->image->store('tissus', 'public');
        } else {
            $validatedData['image'] = $this->oldImage;
        }

        // Mise à jour du tissu
        $tissu->update($validatedData);

        $this->resetForm();
        session()->flash('success', 'Tissu mis à jour avec succès.');
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = true;
        $this->tissuToDelete = $id;
    }

    public function deleteTissu()
    {
        $tissu = Tissu::findOrFail($this->tissuToDelete);

        // Supprimer l'image associée
        if ($tissu->image && Storage::disk('public')->exists($tissu->image)) {
            Storage::disk('public')->delete($tissu->image);
        }

        $tissu->delete();
        $this->confirmingDelete = false;
        session()->flash('success', 'Tissu supprimé avec succès.');
    }

    private function resetForm()
    {
        $this->reset([
            'editMode', 'tissuId', 'titre', 'description', 'prix', 'quantite',
            'categorie_id', 'image', 'oldImage', 'couleur', 'matiere',
            'largeur', 'poids', 'mots_cles', 'reference'
        ]);
        $this->resetValidation();
    }

    public function render()
    {
        $tissus = Tissu::with('categorie')
            ->where('user_id', auth()->id())
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('titre', 'like', '%'.$this->search.'%')
                      ->orWhere('reference', 'like', '%'.$this->search.'%')
                      ->orWhere('description', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->categorieFilter, function($query) {
                $query->where('categorie_id', $this->categorieFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.vendeur.tissus-index', [
            'tissus' => $tissus,
        ]);
    }
}
