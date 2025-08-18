<?php

namespace App\Livewire\Vendeur;

use App\Models\Categorie;
use App\Models\Tissu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class GestionTissus extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public int $total = 0;
    public int $available = 0;
    public int $stockSum = 0;
    public int $stockValue = 0;

    public $categories = [];

    // Filtres réactifs
    public string $search = '';
    public string $categorie = '';
    public string $disponible = '';
    public $prixMin = null;
    public $prixMax = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'categorie' => ['except' => ''],
        'disponible' => ['except' => ''],
        'prixMin' => ['except' => null],
        'prixMax' => ['except' => null],
    ];

    public function updating($name, $value)
    {
        // Réinitialise la pagination à chaque changement de filtre
        $this->resetPage();
    }

    public function mount(): void
    {
        abort_unless(auth()->user()?->isVendeur(), 403);

        $userId = auth()->id();

        // Statistiques globales du vendeur
        $this->total = Tissu::where('user_id', $userId)->count();
        $this->available = Tissu::where('user_id', $userId)->where('stock', '>', 0)->count();
        $this->stockSum = (int) Tissu::where('user_id', $userId)->sum('stock');
        $this->stockValue = (int) Tissu::where('user_id', $userId)->sum(DB::raw('prix * stock'));

        $this->categories = Categorie::select('id', 'nom')->orderBy('nom')->get();
    }

    public function supprimer(int $id): void
    {
        abort_unless(auth()->user()?->isVendeur(), 403);

        $userId = auth()->id();
        $tissu = Tissu::where('user_id', $userId)->findOrFail($id);

        // Delete associated image if exists
        if (!empty($tissu->image)) {
            try {
                Storage::disk('public')->delete($tissu->image);
            } catch (\Throwable $e) {
                // Ignore storage errors; proceed with DB delete
            }
        }

        $tissu->delete();

        // Recompute stats
        $this->total = Tissu::where('user_id', $userId)->count();
        $this->available = Tissu::where('user_id', $userId)->where('stock', '>', 0)->count();
        $this->stockSum = (int) Tissu::where('user_id', $userId)->sum('stock');
        $this->stockValue = (int) Tissu::where('user_id', $userId)->sum(DB::raw('prix * stock'));

        // Ensure pagination is valid after deletion
        $this->resetPage();

        session()->flash('success', 'Tissu supprimé avec succès !');
    }

    public function render()
    {
        abort_unless(auth()->user()?->isVendeur(), 403);

        $userId = auth()->id();

        $query = Tissu::with('categorie')
            ->where('user_id', $userId);

        if ($this->search !== '') {
            $s = '%' . $this->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('nom', 'like', $s)
                  ->orWhere('description', 'like', $s)
                  ->orWhere('couleur', 'like', $s);
            });
        }

        if ($this->categorie !== '') {
            $query->where('categorie_id', $this->categorie);
        }

        if ($this->disponible !== '') {
            if ($this->disponible === '1') {
                $query->where('stock', '>', 0);
            } elseif ($this->disponible === '0') {
                $query->where('stock', '<=', 0);
            }
        }

        if ($this->prixMin !== null && $this->prixMin !== '') {
            $query->where('prix', '>=', (float) $this->prixMin);
        }
        if ($this->prixMax !== null && $this->prixMax !== '') {
            $query->where('prix', '<=', (float) $this->prixMax);
        }

        $tissus = $query->orderByDesc('id')->paginate(10);

        return view('livewire.vendeur.gestion-tissus', [
            'total' => $this->total,
            'available' => $this->available,
            'stockSum' => $this->stockSum,
            'stockValue' => $this->stockValue,
            'categories' => $this->categories,
            'tissus' => $tissus,
        ]);
    }
}
