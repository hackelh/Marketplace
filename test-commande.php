<?php

use App\Models\User;
use App\Models\Commande;
use App\Models\Tissu;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illware\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Démarrer une transaction
    DB::beginTransaction();

    // Récupérer un utilisateur de test
    $user = User::first();
    if (!$user) {
        throw new Exception("Aucun utilisateur trouvé dans la base de données");
    }

    // Créer une commande de test
    $commande = new Commande([
        'reference' => 'TEST-' . now()->format('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6)),
        'total' => 100.00,
        'sous_total' => 90.00,
        'frais_livraison' => 10.00,
        'mode_livraison' => 'standard',
        'mode_paiement' => 'test',
        'statut' => 'en_attente',
        'commentaire' => 'Commande de test',
        'adresse_livraison' => json_encode([
            'nom' => 'Test',
            'prenom' => 'Utilisateur',
            'adresse' => '123 Rue de Test',
            'code_postal' => '75000',
            'ville' => 'Paris',
            'pays' => 'France',
            'telephone' => '0123456789',
            'email' => 'test@example.com'
        ]),
        'payment_status' => 'paye',
        'user_id' => $user->id,
    ]);

    // Sauvegarder la commande
    $commande->save();
    
    // Récupérer un tissu de test
    $tissu = Tissu::first();
    if (!$tissu) {
        throw new Exception("Aucun tissu trouvé dans la base de données");
    }

    // Ajouter un article à la commande
    $commande->items()->create([
        'tissu_id' => $tissu->id,
        'quantite' => 2,
        'prix_unitaire' => 45.00,
        'total' => 90.00,
    ]);

    // Valider la transaction
    DB::commit();

    echo "Commande créée avec succès : " . $commande->reference . "\n";
    echo "ID de la commande : " . $commande->id . "\n";

} catch (Exception $e) {
    // Annuler la transaction en cas d'erreur
    DB::rollBack();
    
    echo "Erreur : " . $e->getMessage() . "\n";
    echo "Fichier : " . $e->getFile() . " (ligne " . $e->getLine() . ")\n";
    echo "Trace : \n" . $e->getTraceAsString() . "\n";
}
