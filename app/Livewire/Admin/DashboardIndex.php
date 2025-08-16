<?php

namespace App\Livewire\Admin;

use App\Models\Categorie;
use App\Models\Commande;
use App\Models\Tissu;
use App\Models\User;
use Livewire\Component;

class DashboardIndex extends Component
{
    public array $stats = [];

    public function mount(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $this->stats = [
            'users' => User::count(),
            'commandes' => Commande::count(),
            'tissus' => Tissu::count(),
            'categories' => Categorie::count(),
            'en_attente' => Commande::where('statut', 'en_attente')->count(),
            'terminees' => Commande::where('statut', 'terminee')->count(),
        ];
    }

    public function render()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $recentOrders = Commande::with(['client:id,name', 'vendeur:id,name'])
            ->orderByDesc('date_commande')
            ->limit(8)
            ->get();

        return view('livewire.components.admin.dashboard-index', [
            'recentOrders' => $recentOrders,
            'stats' => $this->stats,
        ]);
    }
}
