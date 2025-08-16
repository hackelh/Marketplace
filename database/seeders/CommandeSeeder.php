<?php

namespace Database\Seeders;

use App\Models\Commande;
use App\Models\User;
use App\Models\Tissu;
use Illuminate\Database\Seeder;

class CommandeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer un vendeur
        $vendeur = User::where('role', 'vendeur')->first();
        
        if (!$vendeur) {
            $this->command->info('Aucun vendeur trouvé. Créez d\'abord un vendeur.');
            return;
        }

        // Récupérer un client
        $client = User::where('role', 'client')->first();
        
        if (!$client) {
            $this->command->info('Aucun client trouvé. Créez d\'abord un client.');
            return;
        }

        // Récupérer des tissus du vendeur
        $tissus = Tissu::where('user_id', $vendeur->id)->get();
        
        if ($tissus->isEmpty()) {
            $this->command->info('Aucun tissu trouvé pour ce vendeur. Créez d\'abord des tissus.');
            return;
        }

        // Créer quelques commandes de test
        $commandes = [
            [
                'numero_commande' => 'CMD' . now()->format('Ymd') . '0001',
                'client_id' => $client->id,
                'vendeur_id' => $vendeur->id,
                'statut' => 'en_attente',
                'montant_total' => 15000.00,
                'date_commande' => now()->subDays(2),
                'date_livraison_prevue' => now()->addDays(7),
                'adresse_livraison' => '123 Rue de la Paix, Dakar',
                'notes' => 'Livraison en urgence',
                'methode_paiement' => 'Orange Money',
                'statut_paiement' => 'en_attente',
            ],
            [
                'numero_commande' => 'CMD' . now()->format('Ymd') . '0002',
                'client_id' => $client->id,
                'vendeur_id' => $vendeur->id,
                'statut' => 'en_attente',
                'montant_total' => 8500.00,
                'date_commande' => now()->subDay(),
                'date_livraison_prevue' => now()->addDays(5),
                'adresse_livraison' => '456 Avenue Liberté, Dakar',
                'notes' => 'Tissu de qualité premium',
                'methode_paiement' => 'Wave',
                'statut_paiement' => 'paye',
            ],
            [
                'numero_commande' => 'CMD' . now()->format('Ymd') . '0003',
                'client_id' => $client->id,
                'vendeur_id' => $vendeur->id,
                'statut' => 'en_preparation',
                'montant_total' => 22000.00,
                'date_commande' => now()->subDays(5),
                'date_livraison_prevue' => now()->addDays(3),
                'adresse_livraison' => '789 Boulevard de la République, Dakar',
                'notes' => 'Commande pour mariage',
                'methode_paiement' => 'Moov Money',
                'statut_paiement' => 'partiel',
            ],
        ];

        foreach ($commandes as $commandeData) {
            $commande = Commande::create($commandeData);
            
            // Ajouter des détails de commande
            $tissu = $tissus->random();
            $quantite = rand(2, 5);
            
            $commande->details()->create([
                'tissu_id' => $tissu->id,
                'quantite' => $quantite,
                'prix_unitaire' => $tissu->prix,
                'prix_total' => $tissu->prix * $quantite,
                'notes' => 'Tissu de qualité',
            ]);
        }

        $this->command->info('Commandes de test créées avec succès !');
    }
}
