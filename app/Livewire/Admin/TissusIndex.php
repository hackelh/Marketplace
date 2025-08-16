<?php

namespace App\Livewire\Admin;

use App\Models\Categorie;
use App\Models\Tissu;
use Livewire\Component;
use Livewire\WithPagination;

class TissusIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $categorie = '';
    public string $disponible = ''; // '', '1', '0'
    public ?float $prixMin = null;
    public ?float $prixMax = null;
    public int $perPage = 10;

    /** @var array<int,array{id:int,name:string}> */
    public array $categories = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'categorie' => ['except' => ''],
        'disponible' => ['except' => ''],
        'prixMin' => ['except' => null],
        'prixMax' => ['except' => null],
    ];

    public function mount(): void
    {
        // Précharger la liste des catégories (utilise colonne 'nom')
        $this->categories = Categorie::query()
            ->orderBy('nom')
            ->get(['id', 'nom'])
            ->map(fn($c) => ['id' => (int)$c->id, 'name' => (string)$c->nom])
            ->all();
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingCategorie(): void { $this->resetPage(); }
    public function updatingDisponible(): void { $this->resetPage(); }
    public function updatingPrixMin(): void { $this->resetPage(); }
    public function updatingPrixMax(): void { $this->resetPage(); }

    public function render()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $q = Tissu::query()->with(['categorie:id,nom', 'vendeur:id,name']);

        if ($this->search !== '') {
            $term = "%{$this->search}%";
            $q->where(function ($s) use ($term) {
                $s->where('nom', 'like', $term)
                  ->orWhere('couleur', 'like', $term)
                  ->orWhere('description', 'like', $term);
            });
        }
        if ($this->categorie !== '') {
            $q->where('categorie_id', $this->categorie);
        }
        if ($this->disponible !== '') {
            $q->where('disponible', $this->disponible === '1');
        }
        if ($this->prixMin !== null) {
            $q->where('prix', '>=', $this->prixMin);
        }
        if ($this->prixMax !== null) {
            $q->where('prix', '<=', $this->prixMax);
        }

        $tissus = $q->orderByDesc('created_at')->paginate($this->perPage);

        return view('livewire.components.admin.tissus-index', [
            'tissus' => $tissus,
        ]);
    }
}
