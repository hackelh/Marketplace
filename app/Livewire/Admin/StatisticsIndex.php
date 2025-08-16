<?php

namespace App\Livewire\Admin;

use App\Models\Categorie;
use App\Models\Commande;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StatisticsIndex extends Component
{
    public array $byStatus = [];
    public array $monthlyRevenue = [];
    public array $topVendors = [];
    public array $categoriesDistribution = [];

    public function mount(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        // Commandes par statut
        $this->byStatus = Commande::query()
            ->select('statut', DB::raw('COUNT(*) as total'))
            ->groupBy('statut')
            ->pluck('total', 'statut')
            ->toArray();

        // Chiffre d'affaires mensuel (6 derniers mois)
        $this->monthlyRevenue = Commande::query()
            ->select(
                DB::raw("DATE_FORMAT(date_commande, '%Y-%m') as ym"),
                DB::raw('SUM(montant_total) as total')
            )
            ->whereNotNull('date_commande')
            ->groupBy('ym')
            ->orderBy('ym', 'desc')
            ->limit(6)
            ->get()
            ->reverse() // pour afficher du plus ancien au plus récent
            ->map(fn($r) => ['ym' => $r->ym, 'total' => (float) $r->total])
            ->values()
            ->all();

        // Top vendeurs par nombre de commandes (5)
        $this->topVendors = Commande::query()
            ->select('vendeur_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('vendeur_id')
            ->groupBy('vendeur_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                $user = User::find($row->vendeur_id, ['id', 'name']);
                return [
                    'id' => $row->vendeur_id,
                    'name' => $user?->name ?? 'Inconnu',
                    'total' => (int) $row->total,
                ];
            })
            ->all();

        // Répartition des tissus par catégorie
        $this->categoriesDistribution = Categorie::query()
            ->select('id', 'nom')
            ->withCount('tissus')
            ->orderBy('nom')
            ->get()
            ->map(fn($c) => [
                'id' => (int) $c->id,
                'nom' => (string) $c->nom,
                'tissus_count' => (int) $c->tissus_count,
            ])
            ->all();
    }

    public function render()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        return view('livewire.components.admin.statistics-index', [
            'byStatus' => $this->byStatus,
            'monthlyRevenue' => $this->monthlyRevenue,
            'topVendors' => $this->topVendors,
            'categoriesDistribution' => $this->categoriesDistribution,
        ]);
    }
}
