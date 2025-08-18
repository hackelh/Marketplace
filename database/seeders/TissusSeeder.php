<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tissu;
use App\Models\User;
use App\Models\Categorie;

class TissusSeeder extends Seeder
{
    public function run(): void
    {
        $vendeur = User::where('email', 'vendeur@marketplace.sn')->first();
        if (!$vendeur) {
            $this->command?->warn('Vendeur introuvable, exécutez RolesUsersSeeder d\'abord.');
            return;
        }

        // Choisir une catégorie existante (Coton de préférence, sinon la première)
        $categorie = Categorie::where('nom', 'Coton')->first() ?: Categorie::first();
        if (!$categorie) {
            $this->command?->warn('Aucune catégorie trouvée, exécutez CategoriesSeeder d\'abord.');
            return;
        }

        $items = [
            [
                'nom' => 'Coton Premium 200g',
                'description' => 'Coton doux et résistant pour chemises et robes.',
                'prix' => 3500,
                'couleur' => 'Blanc',
                'stock' => 50,
                'origine' => 'Sénégal',
                'composition' => '100% coton',
                'disponible' => true,
            ],
            [
                'nom' => 'Soie Naturelle',
                'description' => 'Soie de haute qualité, légère et brillante.',
                'prix' => 12000,
                'couleur' => 'Ivoire',
                'stock' => 20,
                'origine' => 'Maroc',
                'composition' => '100% soie',
                'disponible' => true,
            ],
            [
                'nom' => 'Denim Indigo',
                'description' => 'Jean résistant pour pantalons et vestes.',
                'prix' => 6000,
                'couleur' => 'Indigo',
                'stock' => 80,
                'origine' => 'Turquie',
                'composition' => '98% coton, 2% élasthanne',
                'disponible' => true,
            ],
        ];

        foreach ($items as $data) {
            Tissu::firstOrCreate(
                ['nom' => $data['nom'], 'user_id' => $vendeur->id],
                array_merge($data, [
                    'categorie_id' => $categorie->id,
                    'user_id' => $vendeur->id,
                ])
            );
        }

        $this->command?->info('Tissus de démonstration créés/confirmés.');
    }
}
