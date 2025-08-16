<?php

namespace App\Livewire\Client;

use App\Models\Favori;
use App\Models\Tissu;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class FavorisController extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 12;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $filters = [
        'categorie' => null,
        'couleur' => null,
        'prix_min' => null,
        'prix_max' => null,
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'filters.categorie' => ['except' => null],
        'filters.couleur' => ['except' => null],
        'filters.prix_min' => ['except' => null],
        'filters.prix_max' => ['except' => null],
    ];

    /**
     * Supprime un tissu des favoris.
     *
     * @param int $tissuId
     * @return void
     */
    public function supprimerFavori(int $tissuId): void
    {
        $favori = Favori::where('user_id', auth()->id())
            ->where('tissu_id', $tissuId)
            ->first();

        if ($favori) {
            $favori->delete();
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Le tissu a été retiré de vos favoris.'
            ]);
        }
    }

    /**
     * Supprime tous les favoris de l'utilisateur.
     *
     * @return void
     */
    public function viderFavoris(): void
    {
        Favori::where('user_id', auth()->id())->delete();
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Votre liste de favoris a été vidée.'
        ]);
    }

    /**
     * Ajoute ou supprime un favori.
     *
     * @param Tissu $tissu
     * @return void
     */
    public function toggleFavori(Tissu $tissu): void
    {
        if (!auth()->check()) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'Vous devez être connecté pour ajouter aux favoris.'
            ]);
            return;
        }

        $favori = Favori::where('user_id', auth()->id())
            ->where('tissu_id', $tissu->id)
            ->first();

        if ($favori) {
            $favori->delete();
            $message = 'Le tissu a été retiré de vos favoris.';
        } else {
            Favori::create([
                'user_id' => auth()->id(),
                'tissu_id' => $tissu->id,
            ]);
            $message = 'Le tissu a été ajouté à vos favoris.';
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $message
        ]);
    }

    /**
     * Réinitialise les filtres.
     *
     * @return void
     */
    public function resetFilters(): void
    {
        $this->reset('search', 'filters');
        $this->resetPage();
    }

    /**
     * Change le nombre d'éléments par page.
     *
     * @param int $perPage
     * @return void
     */
    public function updatedPerPage(int $perPage): void
    {
        $this->perPage = $perPage;
        $this->resetPage();
    }

    /**
     * Rend la vue des favoris.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $query = auth()->user()->favoris()
            ->with(['categorie', 'vendeur'])
            ->when($this->search, function (Builder $query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('reference', 'like', "%{$search}%");
                });
            })
            ->when($this->filters['categorie'], function (Builder $query, $categorieId) {
                $query->where('categorie_id', $categorieId);
            })
            ->when($this->filters['couleur'], function (Builder $query, $couleur) {
                $query->where('couleur', $couleur);
            })
            ->when($this->filters['prix_min'], function (Builder $query, $prixMin) {
                $query->where('prix', '>=', $prixMin);
            })
            ->when($this->filters['prix_max'], function (Builder $query, $prixMax) {
                $query->where('prix', '<=', $prixMax);
            });

        $tissus = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        // Récupérer les filtres disponibles
        $categories = \App\Models\Categorie::whereHas('tissus', function ($query) {
            $query->whereIn('id', function($q) {
                $q->select('tissu_id')
                  ->from('favoris')
                  ->where('user_id', auth()->id());
            });
        })->get();

        $couleurs = Tissu::whereIn('id', function($query) {
            $query->select('tissu_id')
                  ->from('favoris')
                  ->where('user_id', auth()->id());
        })
        ->whereNotNull('couleur')
        ->distinct()
        ->pluck('couleur')
        ->filter()
        ->sort();

        return view('livewire.client.favoris', [
            'tissus' => $tissus,
            'categories' => $categories,
            'couleurs' => $couleurs,
        ]);
    }
}
