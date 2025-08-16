<?php

namespace App\Livewire\Vendeur;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Categorie;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategorieController extends Component
{
    use WithPagination, WithFileUploads;
    
    /**
     * Vérifie que l'utilisateur est un vendeur lors du chargement initial du composant.
     *
     * @return void
     */
    /**
     * Vérifie les autorisations lors du chargement initial du composant.
     *
     * @return void
     */
    public function mount()
    {
        $this->authorize('viewAny', Categorie::class);
    }

    public $nom, $description, $image, $est_actif = true;
    public $categorie_id;
    public $isModalOpen = false;
    public $isSubmitting = false;
    public $search = '';
    public $sortField = 'nom';
    public $sortDirection = 'asc';

    protected function rules()
    {
        return [
            'nom' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'nom')->ignore($this->categorie_id)
            ],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048', 'mimes:jpeg,png,jpg,gif'],
            'est_actif' => ['boolean'],
        ];
    }
    
    protected $messages = [
        'nom.required' => 'Le nom de la catégorie est requis.',
        'nom.unique' => 'Ce nom de catégorie est déjà utilisé.',
        'image.image' => 'Le fichier doit être une image valide.',
        'image.max' => 'L\'image ne doit pas dépasser 2 Mo.',
        'image.mimes' => 'Le fichier doit être de type : jpeg, png, jpg ou gif.',
    ];
    
    public function render()
    {
        $categories = Categorie::where('nom', 'like', '%' . $this->search . '%')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.vendeur.categorie-controller', [
            'categories' => $categories,
        ]);
    }

    public function sortBy($field)
    {
        $this->sortDirection = $this->sortField === $field 
            ? $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc'
            : 'asc';

        $this->sortField = $field;
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetErrorBag();
        $this->resetValidation();
        $this->isSubmitting = false;
    }

    private function resetInputFields()
    {
        $this->categorie_id = null;
        $this->nom = '';
        $this->description = '';
        $this->image = null;
        $this->est_actif = true;
    }

    /**
     * Enregistre ou met à jour une catégorie.
     *
     * @return void
     */
    public function store()
    {
        $this->isSubmitting = true;
        $this->validate();

        try {
            $this->authorize('create', Categorie::class);
            
            $data = [
                'nom' => $this->nom,
                'slug' => Str::slug($this->nom),
                'description' => $this->description,
                'est_actif' => $this->est_actif,
            ];

            // Gestion de l'upload de l'image
            if ($this->image) {
                // Supprimer l'ancienne image si elle existe
                if ($this->categorie_id) {
                    $oldCategorie = Categorie::find($this->categorie_id);
                    if ($oldCategorie && $oldCategorie->image) {
                        \Storage::disk('public')->delete($oldCategorie->image);
                    }
                }
                $data['image'] = $this->image->store('categories', 'public');
            }

            // Création ou mise à jour
            if ($this->categorie_id) {
                $categorie = Categorie::findOrFail($this->categorie_id);
                $this->authorize('update', $categorie);
                $categorie->update($data);
                $message = 'Catégorie mise à jour avec succès !';
            } else {
                $categorie = Categorie::create($data);
                $message = 'Catégorie créée avec succès !';
            }

            // Réinitialisation et fermeture du modal
            $this->resetForm();
            $this->closeModal();
            
            // Notification de succès
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $message
            ]);
            
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Action non autorisée.'
            ]);
        } catch (\Exception $e) {
            // Nettoyage en cas d'erreur
            if (isset($data['image']) && file_exists(storage_path('app/public/' . $data['image']))) {
                \Storage::disk('public')->delete($data['image']);
            }
            
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Une erreur est survenue : ' . $e->getMessage()
            ]);
        } finally {
            $this->isSubmitting = false;
        }
    }

    public function edit($id)
    {
        $categorie = Categorie::findOrFail($id);
        $this->categorie_id = $id;
        $this->nom = $categorie->nom;
        $this->description = $categorie->description;
        $this->est_actif = $categorie->est_actif;
        $this->openModal();
    }

    /**
     * Bascule le statut actif/inactif d'une catégorie.
     *
     * @param int $id
     * @return void
     */
    public function toggleStatus($id)
    {
        try {
            $categorie = Categorie::findOrFail($id);
            $this->authorize('update', $categorie);
            
            $categorie->update(['est_actif' => !$categorie->est_actif]);
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Statut de la catégorie mis à jour avec succès !'
            ]);
            
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Action non autorisée.'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Une erreur est survenue lors de la mise à jour du statut : ' . $e->getMessage()
            ]);
        }
    }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
    
    /**
     * Réinitialise le formulaire et les erreurs.
     *
     * @return void
     */
    public function resetForm()
    {
        $this->reset([
            'categorie_id',
            'nom',
            'description',
            'image',
            'est_actif',
            'isSubmitting'
        ]);
        
        $this->resetErrorBag();
        $this->resetValidation();
    }
    
    /**
     * Supprime l'image de la catégorie.
     *
     * @return void
     */
    /**
     * Supprime l'image d'une catégorie
     *
     * @param int|null $categorieId ID de la catégorie (optionnel, utilise la catégorie courante si non fourni)
     * @return void
     */
    public function removeImage($categorieId = null)
    {
        try {
            // Vérifier les autorisations générales
            $this->authorize('update', Categorie::class);
            
            // Utiliser l'ID fourni en paramètre ou celui de la propriété
            $categorieId = $categorieId ?? $this->categorie_id;
            
            // Vérifier si un ID de catégorie est disponible
            if (!$categorieId) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Aucune catégorie sélectionnée.'
                ]);
                return;
            }
            
            // Récupérer la catégorie avec une seule requête
            $categorie = Categorie::findOrFail($categorieId);
            
            // Vérifier les autorisations pour cette catégorie spécifique
            $this->authorize('update', $categorie);
            
            // Vérifier si la catégorie a une image
            if (empty($categorie->image)) {
                $this->dispatch('notify', [
                    'type' => 'info',
                    'message' => 'Cette catégorie ne possède pas d\'image.'
                ]);
                return;
            }
            
            // Supprimer l'image physique si elle existe
            $imagePath = storage_path('app/public/' . $categorie->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            
            // Mettre à jour la base de données
            $categorie->update([
                'image' => null,
                'updated_at' => now()
            ]);
            
            // Réinitialiser la propriété image si c'est la catégorie courante
            if ($this->categorie_id == $categorieId) {
                $this->reset('image');
            }
            
            // Rafraîchir les données
            $this->resetPage();
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Image supprimée avec succès.'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Catégorie non trouvée.'
            ]);
            
            \Log::error('Catégorie non trouvée lors de la suppression d\'image', [
                'categorie_id' => $categorieId ?? 'null',
                'error' => $e->getMessage()
            ]);
            
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Vous n\'êtes pas autorisé à effectuer cette action.'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Une erreur est survenue lors de la suppression de l\'image : ' . $e->getMessage()
            ]);
            
            // Enregistrer l'erreur dans les logs
            \Log::error('Erreur lors de la suppression de l\'image de la catégorie', [
                'error' => $e->getMessage(),
                'categorie_id' => $categorieId ?? 'null',
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
