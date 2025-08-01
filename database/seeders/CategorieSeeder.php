<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categorie;

class CategorieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'nom' => 'Wax',
                'description' => 'Tissus wax traditionnels avec motifs colorés et designs africains authentiques',
                'couleur_hex' => '#FF6B35'
            ],
            [
                'nom' => 'Bazin',
                'description' => 'Bazin riche brodé, tissu de prestige pour les grandes occasions',
                'couleur_hex' => '#4ECDC4'
            ],
            [
                'nom' => 'Coton',
                'description' => 'Tissus en coton naturel, confortables et respirants',
                'couleur_hex' => '#45B7D1'
            ],
            [
                'nom' => 'Soie',
                'description' => 'Tissus en soie fine, élégants et luxueux',
                'couleur_hex' => '#96CEB4'
            ],
            [
                'nom' => 'Bogolan',
                'description' => 'Tissu traditionnel malien teint à la boue, motifs géométriques',
                'couleur_hex' => '#FFEAA7'
            ],
            [
                'nom' => 'Kente',
                'description' => 'Tissu traditionnel ghanéen aux couleurs vives et motifs symboliques',
                'couleur_hex' => '#FD79A8'
            ],
            [
                'nom' => 'Ndop',
                'description' => 'Tissu camerounais indigo avec motifs blancs traditionnels',
                'couleur_hex' => '#6C5CE7'
            ],
            [
                'nom' => 'Raphia',
                'description' => 'Tissu naturel en fibres de raphia, texture unique',
                'couleur_hex' => '#A29BFE'
            ]
        ];

        foreach ($categories as $categorie) {
            Categorie::create($categorie);
        }
    }
}
