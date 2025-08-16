<?php

namespace Database\Seeders;

use App\Models\HistoriqueTissu;
use App\Models\Tissu;
use App\Models\User;
use Illuminate\Database\Seeder;

class HistoriqueTissuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer un vendeur et ses tissus
        $vendeur = User::where('role', 'vendeur')->first();
        
        if (!$vendeur) {
            $this->command->info('Aucun vendeur trouvé. Créez d\'abord un vendeur.');
            return;
        }

        $tissus = Tissu::where('user_id', $vendeur->id)->get();
        
        if ($tissus->isEmpty()) {
            $this->command->info('Aucun tissu trouvé pour ce vendeur. Créez d\'abord des tissus.');
            return;
        }

        foreach ($tissus as $tissu) {
            // Créer des mouvements d'historique pour chaque tissu
            $mouvements = [
                [
                    'type_mouvement' => 'creation',
                    'quantite_avant' => 0,
                    'quantite_apres' => 50,
                    'quantite_mouvement' => 50,
                    'motif' => 'Création du tissu',
                    'notes' => "Tissu créé : {$tissu->nom}",
                    'created_at' => now()->subDays(30),
                ],
                [
                    'type_mouvement' => 'ajout',
                    'quantite_avant' => 50,
                    'quantite_apres' => 80,
                    'quantite_mouvement' => 30,
                    'motif' => 'Réapprovisionnement',
                    'notes' => 'Nouveau stock reçu',
                    'created_at' => now()->subDays(25),
                ],
                [
                    'type_mouvement' => 'vente',
                    'quantite_avant' => 80,
                    'quantite_apres' => 75,
                    'quantite_mouvement' => -5,
                    'motif' => 'Vente client',
                    'reference' => 'CMD001',
                    'notes' => 'Vente de 5 mètres',
                    'created_at' => now()->subDays(20),
                ],
                [
                    'type_mouvement' => 'vente',
                    'quantite_avant' => 75,
                    'quantite_apres' => 70,
                    'quantite_mouvement' => -5,
                    'motif' => 'Vente client',
                    'reference' => 'CMD002',
                    'notes' => 'Vente de 5 mètres',
                    'created_at' => now()->subDays(15),
                ],
                [
                    'type_mouvement' => 'ajout',
                    'quantite_avant' => 70,
                    'quantite_apres' => 90,
                    'quantite_mouvement' => 20,
                    'motif' => 'Ajout manuel',
                    'notes' => 'Ajout via interface vendeur',
                    'created_at' => now()->subDays(10),
                ],
                [
                    'type_mouvement' => 'ajustement',
                    'quantite_avant' => 90,
                    'quantite_apres' => 85,
                    'quantite_mouvement' => -5,
                    'motif' => 'Inventaire',
                    'notes' => 'Correction après inventaire',
                    'created_at' => now()->subDays(5),
                ],
                [
                    'type_mouvement' => 'vente',
                    'quantite_avant' => 85,
                    'quantite_apres' => 80,
                    'quantite_mouvement' => -5,
                    'motif' => 'Vente client',
                    'reference' => 'CMD003',
                    'notes' => 'Vente de 5 mètres',
                    'created_at' => now()->subDays(2),
                ],
            ];

            foreach ($mouvements as $mouvement) {
                HistoriqueTissu::create([
                    'tissu_id' => $tissu->id,
                    'user_id' => $vendeur->id,
                    'type_mouvement' => $mouvement['type_mouvement'],
                    'quantite_avant' => $mouvement['quantite_avant'],
                    'quantite_apres' => $mouvement['quantite_apres'],
                    'quantite_mouvement' => $mouvement['quantite_mouvement'],
                    'motif' => $mouvement['motif'],
                    'reference' => $mouvement['reference'] ?? null,
                    'notes' => $mouvement['notes'],
                    'created_at' => $mouvement['created_at'],
                    'updated_at' => $mouvement['created_at'],
                ]);
            }

            // Mettre à jour le stock actuel du tissu
            $tissu->update(['stock' => 80]);
        }

        $this->command->info('Historique des tissus créé avec succès !');
    }
}
