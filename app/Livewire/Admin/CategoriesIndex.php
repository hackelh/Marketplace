<?php

namespace App\Livewire\Admin;

use App\Models\Categorie;
use Livewire\Component;
use Livewire\WithPagination;

class CategoriesIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $couleur = '';
    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'couleur' => ['except' => ''],
    ];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingCouleur(): void { $this->resetPage(); }

    public function render()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $q = Categorie::query();

        if ($this->search !== '') {
            $term = "%{$this->search}%";
            $q->where(function ($s) use ($term) {
                $s->where('nom', 'like', $term)
                  ->orWhere('description', 'like', $term);
            });
        }
        if ($this->couleur !== '') {
            $q->where('couleur_hex', 'like', "%{$this->couleur}%");
        }

        $categories = $q->orderBy('nom')->paginate($this->perPage);

        return view('livewire.components.admin.categories-index', [
            'categories' => $categories,
        ]);
    }
}
