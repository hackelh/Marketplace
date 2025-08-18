<?php

namespace App\Livewire\Admin;

use App\Models\Categorie;
use App\Models\User;
use Livewire\Component;

class DashboardIndex extends Component
{
    public array $stats = [];

    public function mount(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        // Compteurs par rôle
        $admins   = User::where('role', 'admin')->count();
        $vendeurs = User::where('role', 'vendeur')->count();
        $tailleurs= User::where('role', 'tailleur')->count();
        $clients  = User::where('role', 'client')->count();
        $totalUsers = User::count();

        // Croissance (comptes créés sur périodes glissantes)
        $growth7  = User::where('created_at', '>=', now()->subDays(7))->count();
        $growth30 = User::where('created_at', '>=', now()->subDays(30))->count();

        // Qualité (ex: email vérifié)
        $verified = User::whereNotNull('email_verified_at')->count();

        $this->stats = [
            'users'       => $totalUsers,
            'admins'      => $admins,
            'vendeurs'    => $vendeurs,
            'tailleurs'   => $tailleurs,
            'clients'     => $clients,
            'categories'  => Categorie::count(),

            'role_counts' => [
                'admin'   => $admins,
                'vendeur' => $vendeurs,
                'tailleur'=> $tailleurs,
                'client'  => $clients,
            ],

            'growth_7d'   => $growth7,
            'growth_30d'  => $growth30,
            'verified_users' => $verified,
        ];
    }

    public function render()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $recentAdmins = User::where('role', 'admin')
            ->select('id', 'name', 'email', 'created_at')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('livewire.components.admin.dashboard-index', [
            'recentAdmins' => $recentAdmins,
            'stats' => $this->stats,
        ]);
    }
}
