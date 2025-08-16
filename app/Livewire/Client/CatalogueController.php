<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tissu;
use App\Models\Categorie;

class CatalogueController extends Component
{
    use WithPagination;

    public $search = '';
    public $categorie = '';
    public $minPrice = '';
    public $maxPrice = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'categorie' => ['except' => ''],
        'minPrice' => ['except' => ''],
        'maxPrice' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc']
    ];

    protected $listeners = ['panier-mis-a-jour' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategorie()
    {
        $this->resetPage();
    }

    public function updatingMinPrice()
    {
        $this->resetPage();
    }

    public function updatingMaxPrice()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'categorie', 'minPrice', 'maxPrice']);
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortBy = $field;
    }

    public function addToCart($tissuId)
    {
        $tissu = Tissu::with('images')->findOrFail($tissuId);

        if ($tissu->quantite <= 0) {
            $this->dispatch('notify', [
                'type' => 'error',
                'title' => 'Rupture de stock',
                'message' => 'Ce produit est actuellement en rupture de stock.',
                'duration' => 3000,
                'icon' => 'x-circle'
            ]);
            return;
        }

        $panier = session()->get('panier', []);
        $alreadyInCart = isset($panier[$tissu->id]);

        if ($alreadyInCart) {
            $panier[$tissu->id]['quantite']++;
            $message = 'Quantité mise à jour dans le panier';
        } else {
            $panier[$tissu->id] = [
                'id' => $tissu->id,
                'titre' => $tissu->titre,
                'prix' => $tissu->prix,
                'quantite' => 1,
                'image' => $tissu->images->isNotEmpty() 
                    ? asset('storage/' . $tissu->images->first()->image_path) 
                    : asset('images/placeholder.svg'),
                'slug' => $tissu->slug,
            ];
            $message = 'Produit ajouté au panier';
        }

        session()->put('panier', $panier);

        $this->dispatch('panier-mis-a-jour');
        
        // Préparation de l'URL de l'image
        $imageUrl = null;
        
        if ($tissu->images->isNotEmpty()) {
            $imagePath = $tissu->images->first()->image_path;
            $imageUrl = asset('storage/' . $imagePath);
            \Log::info('Image path:', [
                'original' => $imagePath,
                'url' => $imageUrl,
                'exists' => file_exists(public_path('storage/' . $imagePath))
            ]);
        } else {
            \Log::info('Aucune image trouvée pour le produit', ['tissu_id' => $tissu->id]);
        }
        
        // Notification améliorée
        $this->dispatch('notify', [
            'type' => 'success',
            'title' => '✅ ' . $tissu->titre,
            'message' => $message,
            'duration' => 4000,
            'action' => [
                'text' => 'Voir le panier',
                'url' => route('panier.index')
            ],
            'image' => $imageUrl
        ]);
        
        // Log pour débogage
        \Log::info('Notification envoyée', [
            'tissu_id' => $tissu->id,
            'titre' => $tissu->titre,
            'image_url' => $imageUrl
        ]);
    }

    public function render()
    {
        $query = Tissu::query()
            ->where('disponible', true)
            ->where('is_published', true)
            ->where('quantite', '>', 0);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('titre', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->categorie) {
            $query->where('categorie_id', $this->categorie);
        }

        if ($this->minPrice !== '') { 
            $query->where('prix', '>=', $this->minPrice); 
        }
        if ($this->maxPrice !== '') { 
            $query->where('prix', '<=', $this->maxPrice); 
        }

        $tissus = $query->orderBy($this->sortBy, $this->sortDirection)
                        ->with('categorie')
                        ->paginate(12);

        $categories = Categorie::orderBy('nom')->get();

        return view('livewire.client.catalogue', [
            'tissus' => $tissus,
            'categories' => $categories,
            'minPriceRange' => Tissu::min('prix') ?? 0,
            'maxPriceRange' => Tissu::max('prix') ?? 1000,
        ]);
    }
}
