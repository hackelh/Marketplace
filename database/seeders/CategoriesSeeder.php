<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categorie;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'nom' => 'Coton',
                'description' => 'Tissu naturel, respirant et polyvalent.',
                'couleur_hex' => '#22C55E',
            ],
            [
                'nom' => 'Soie',
                'description' => 'Tissu luxueux, doux et brillant.',
                'couleur_hex' => '#F59E0B',
            ],
            [
                'nom' => 'Laine',
                'description' => 'Chaud, idéal pour l’hiver.',
                'couleur_hex' => '#6B7280',
            ],
            [
                'nom' => 'Linge',
                'description' => 'Fibre naturelle, texture légère et fraîche.',
                'couleur_hex' => '#10B981',
            ],
            [
                'nom' => 'Jean (Denim)',
                'description' => 'Résistant, parfait pour pantalons et vestes.',
                'couleur_hex' => '#3B82F6',
            ],
        ];

        foreach ($items as $data) {
            Categorie::query()->firstOrCreate(
                ['nom' => $data['nom']],
                [
                    'description' => $data['description'] ?? null,
                    'couleur_hex' => $data['couleur_hex'] ?? '#3B82F6',
                ]
            );
        }
    }
}
