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
        // Récupérer tous les vendeurs
        $vendeurs = User::where('role', 'vendeur')->get();
        if ($vendeurs->isEmpty()) {
            $this->command->info("Aucun vendeur trouvé. Créez d'abord un vendeur.");
            return;
        }

        // Récupérer un client (unique) pour les tests
        $client = User::where('role', 'client')->first();
        if (!$client) {
            $this->command->info("Aucun client trouvé. Créez d'abord un client.");
            return;
        }

        foreach ($vendeurs as $vendeur) {
            // Récupérer des tissus pour ce vendeur
            $tissus = Tissu::where('user_id', $vendeur->id)->get();
            if ($tissus->isEmpty()) {
                $this->command->info("Aucun tissu pour le vendeur ID {$vendeur->id}. Passage.");
                continue;
            }

            // Jeu de commandes (en cours + validées) pour ce vendeur
            $commandes = [
                [
                    'client_id' => $client->id,
                    'vendeur_id' => $vendeur->id,
                    'statut' => 'en_attente',
                    'montant_total' => 15000.00,
                    'date_commande' => now()->subDays(6),
                    'date_livraison_prevue' => now()->addDays(7),
                    'adresse_livraison' => '123 Rue de la Paix, Dakar',
                    'notes' => 'Livraison en urgence',
                    'methode_paiement' => 'Orange Money',
                    'statut_paiement' => 'en_attente',
                ],
                [
                    'client_id' => $client->id,
                    'vendeur_id' => $vendeur->id,
                    'statut' => 'en_preparation',
                    'montant_total' => 22000.00,
                    'date_commande' => now()->subDays(8),
                    'date_livraison_prevue' => now()->addDays(3),
                    'adresse_livraison' => '789 Boulevard de la République, Dakar',
                    'notes' => 'Commande pour mariage',
                    'methode_paiement' => 'Moov Money',
                    'statut_paiement' => 'partiel',
                ],
                [
                    'client_id' => $client->id,
                    'vendeur_id' => $vendeur->id,
                    'statut' => 'en_couture',
                    'montant_total' => 17500.00,
                    'date_commande' => now()->subDays(10),
                    'date_livraison_prevue' => now()->addDays(2),
                    'adresse_livraison' => '12 Cité Keur Gorgui, Dakar',
                    'notes' => 'Tailleurs en cours',
                    'methode_paiement' => 'Espèces',
                    'statut_paiement' => 'partiel',
                ],
                [
                    'client_id' => $client->id,
                    'vendeur_id' => $vendeur->id,
                    'statut' => 'livree',
                    'montant_total' => 30500.00,
                    'date_commande' => now()->subDays(12),
                    'date_livraison_prevue' => now()->subDays(2),
                    'date_livraison_effective' => now()->subDay(),
                    'adresse_livraison' => 'Parcelles Assainies, U22',
                    'notes' => 'Livrée avec succès',
                    'methode_paiement' => 'Carte bancaire',
                    'statut_paiement' => 'paye',
                ],
                [
                    'client_id' => $client->id,
                    'vendeur_id' => $vendeur->id,
                    'statut' => 'terminee',
                    'montant_total' => 41250.00,
                    'date_commande' => now()->subDays(20),
                    'date_livraison_prevue' => now()->subDays(9),
                    'date_livraison_effective' => now()->subDays(8),
                    'adresse_livraison' => 'Grand Yoff, Dakar',
                    'notes' => 'Commande clôturée',
                    'methode_paiement' => 'Wave',
                    'statut_paiement' => 'paye',
                ],
            ];

            foreach ($commandes as $commandeData) {
                // numéro unique à chaque run
                $commandeData['numero_commande'] = $this->generateNumeroUnique();
                $commande = Commande::create($commandeData);

                // Ajouter 1-2 détails par commande
                $items = rand(1, 2);
                for ($i = 0; $i < $items; $i++) {
                    $tissu = $tissus->random();
                    $quantite = rand(1, 5);
                    $commande->details()->create([
                        'tissu_id' => $tissu->id,
                        'quantite' => $quantite,
                        'prix_unitaire' => $tissu->prix,
                        'prix_total' => $tissu->prix * $quantite,
                        'notes' => 'Tissu de qualité',
                    ]);
                }
            }
        }

        $this->command->info('Commandes de test créées avec succès pour tous les vendeurs !');
    }

    /**
     * Génère un numéro de commande unique du type CMDYYYYMMDD-XXXX
     */
    protected function generateNumeroUnique(): string
    {
        $prefix = 'CMD' . now()->format('Ymd') . '-';
        do {
            $numero = $prefix . random_int(1000, 9999);
        } while (Commande::where('numero_commande', $numero)->exists());
        return $numero;
    }
}
