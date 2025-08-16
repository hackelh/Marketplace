<?php

namespace App\Livewire\Admin;

use App\Models\Commande;
use Livewire\Component;
use Livewire\WithPagination;

class OrdersIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statut = '';
    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'statut' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatut(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $q = Commande::query()
            ->with(['client:id,name,email', 'vendeur:id,name,email', 'tailleur:id,name,email']);

        if ($this->search !== '') {
            $term = "%{$this->search}%";
            $q->where(function ($s) use ($term) {
                $s->where('numero_commande', 'like', $term)
                  ->orWhereHas('client', fn($c) => $c->where('name', 'like', $term)->orWhere('email', 'like', $term))
                  ->orWhereHas('vendeur', fn($v) => $v->where('name', 'like', $term)->orWhere('email', 'like', $term))
                  ->orWhereHas('tailleur', fn($t) => $t->where('name', 'like', $term)->orWhere('email', 'like', $term));
            });
        }
        if ($this->statut !== '') {
            $q->where('statut', $this->statut);
        }

        $commandes = $q->orderByDesc('date_commande')->paginate($this->perPage);

        return view('livewire.components.admin.orders-index', [
            'commandes' => $commandes,
        ]);
    }
}
