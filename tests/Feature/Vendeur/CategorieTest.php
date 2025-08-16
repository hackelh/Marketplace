<?php

namespace Tests\Feature\Vendeur;

use App\Models\Categorie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CategorieTest extends TestCase
{
    use RefreshDatabase;

    private User $vendeur;
    private User $client;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Afficher un message de débogage
        fwrite(STDERR, print_r("\n=== DÉBUT DE SETUP ===\n", TRUE));
        
        try {
            // Afficher les informations sur l'environnement de test
            fwrite(STDERR, print_r("Environnement: " . app()->environment() . "\n", TRUE));
            fwrite(STDERR, print_r("URL: " . config('app.url') . "\n", TRUE));
            
            // Créer un vendeur et un client en utilisant les méthodes de la factory
            fwrite(STDERR, print_r("\nCréation des utilisateurs de test...\n", TRUE));
            
            $this->vendeur = User::factory()->vendeur()->create();
            $this->client = User::factory()->client()->create();
            
            // Afficher les détails des utilisateurs créés
            fwrite(STDERR, print_r("\nUtilisateurs créés avec succès :\n", TRUE));
            fwrite(STDERR, print_r("- Vendeur ID: " . $this->vendeur->id . "\n", TRUE));
            fwrite(STDERR, print_r("  Email: " . $this->vendeur->email . "\n", TRUE));
            fwrite(STDERR, print_r("  Rôle: " . $this->vendeur->role . "\n", TRUE));
            fwrite(STDERR, print_r("  Vérification isVendeur: " . ($this->vendeur->isVendeur() ? 'true' : 'false') . "\n", TRUE));
            
            fwrite(STDERR, print_r("- Client ID: " . $this->client->id . "\n", TRUE));
            fwrite(STDERR, print_r("  Email: " . $this->client->email . "\n", TRUE));
            fwrite(STDERR, print_r("  Rôle: " . $this->client->role . "\n", TRUE));
            
            // Vérifier que les utilisateurs sont bien enregistrés dans la base de données
            fwrite(STDERR, print_r("\nVérification de l'existence des utilisateurs dans la base de données :\n", TRUE));
            
            $vendeurFromDb = User::find($this->vendeur->id);
            $clientFromDb = User::find($this->client->id);
            
            fwrite(STDERR, print_r("- Vendeur trouvé: " . ($vendeurFromDb ? 'Oui' : 'Non') . "\n", TRUE));
            fwrite(STDERR, print_r("- Client trouvé: " . ($clientFromDb ? 'Oui' : 'Non') . "\n", TRUE));
            
            if ($vendeurFromDb) {
                fwrite(STDERR, print_r("Rôle du vendeur dans la base: " . $vendeurFromDb->role . "\n", TRUE));
                fwrite(STDERR, print_r("Vérification isVendeur: " . ($vendeurFromDb->isVendeur() ? 'true' : 'false') . "\n", TRUE));
            }
            
            // Configurer le stockage factice pour les tests
            fwrite(STDERR, print_r("\nConfiguration du stockage factice...\n", TRUE));
            Storage::fake('public');
            fwrite(STDERR, print_r("Stockage factice configuré avec succès.\n", TRUE));
            
            // Tester l'authentification manuellement
            $auth = auth();
            $isAuthenticated = $auth->check();
            fwrite(STDERR, print_r("\nÉtat de l'authentification dans setUp: " . ($isAuthenticated ? 'Authentifié' : 'Non authentifié') . "\n", TRUE));
            
            if ($isAuthenticated) {
                fwrite(STDERR, print_r("Utilisateur actuellement authentifié: " . $auth->user()->email . " (ID: " . $auth->id() . ")\n", TRUE));
            }
            
            fwrite(STDERR, print_r("\n=== FIN DE SETUP ===\n\n", TRUE));
            
        } catch (\Exception $e) {
            fwrite(STDERR, print_r("\nERREUR DANS SETUP :\n", TRUE));
            fwrite(STDERR, print_r("Message: " . $e->getMessage() . "\n", TRUE));
            fwrite(STDERR, print_r("Fichier: " . $e->getFile() . " (ligne " . $e->getLine() . ")\n", TRUE));
            fwrite(STDERR, print_r("Trace: " . $e->getTraceAsString() . "\n", TRUE));
            throw $e;
        }
    }

    /** @test */
    public function vendeur_peut_voir_la_liste_des_categories()
    {
        // Afficher des informations de débogage sur l'utilisateur vendeur
        fwrite(STDERR, print_r("\n=== DÉBUT DU TEST vendeur_peut_voir_la_liste_des_categories ===\n", TRUE));
        
        // Afficher les détails de l'utilisateur vendeur avant actingAs
        fwrite(STDERR, print_r("Détails de l'utilisateur vendeur avant actingAs:\n", TRUE));
        fwrite(STDERR, print_r("- ID: " . $this->vendeur->id . "\n", TRUE));
        fwrite(STDERR, print_r("- Email: " . $this->vendeur->email . "\n", TRUE));
        fwrite(STDERR, print_r("- Rôle: " . $this->vendeur->role . "\n", TRUE));
        fwrite(STDERR, print_r("- Vérification isVendeur: " . ($this->vendeur->isVendeur() ? 'true' : 'false') . "\n", TRUE));
        
        // Vérifier l'état de l'authentification avant d'essayer de s'authentifier
        $auth = auth();
        fwrite(STDERR, print_r("\n[1] État de l'authentification avant actingAs: " . ($auth->check() ? 'Authentifié' : 'Non authentifié') . "\n", TRUE));
        
        if ($auth->check()) {
            fwrite(STDERR, print_r("Utilisateur actuellement authentifié: " . $auth->user()->email . " (ID: " . $auth->id() . ")\n", TRUE));
        }
        
        // S'authentifier explicitement avec l'utilisateur vendeur
        fwrite(STDERR, print_r("\n[2] Appel de actingAs avec l'utilisateur vendeur...\n", TRUE));
        
        try {
            // Utiliser la méthode actingAs avec le driver de session
            $response = $this->actingAs($this->vendeur, 'web');
            fwrite(STDERR, print_r("[3] actingAs exécuté sans erreur.\n", TRUE));
            
            // Vérifier l'état de l'authentification après actingAs
            $auth = auth('web');
            fwrite(STDERR, print_r("\n[4] État de l'authentification après actingAs (web guard): " . ($auth->check() ? 'Authentifié' : 'Non authentifié') . "\n", TRUE));
            
            if ($auth->check()) {
                fwrite(STDERR, print_r("Utilisateur authentifié: " . $auth->user()->email . " (ID: " . $auth->id() . ")\n", TRUE));
                fwrite(STDERR, print_r("Rôle de l'utilisateur: " . $auth->user()->role . "\n", TRUE));
                fwrite(STDERR, print_r("Vérification isVendeur: " . ($auth->user()->isVendeur() ? 'true' : 'false') . "\n", TRUE));
            } else {
                fwrite(STDERR, print_r("ERREUR: L'utilisateur n'est toujours pas authentifié après actingAs!\n", TRUE));
                
                // Essayer de s'authentifier manuellement
                fwrite(STDERR, print_r("\nTentative d'authentification manuelle...\n", TRUE));
                
                try {
                    auth('web')->login($this->vendeur);
                    fwrite(STDERR, print_r("Authentification manuelle réussie avec login().\n", TRUE));
                    
                    // Vérifier à nouveau après la tentative manuelle
                    $auth = auth('web');
                    fwrite(STDERR, print_r("État de l'authentification après login manuel: " . ($auth->check() ? 'Authentifié' : 'Non authentifié') . "\n", TRUE));
                    
                    if ($auth->check()) {
                        fwrite(STDERR, print_r("Utilisateur authentifié: " . $auth->user()->email . " (ID: " . $auth->id() . ")\n", TRUE));
                        fwrite(STDERR, print_r("Rôle de l'utilisateur: " . $auth->user()->role . "\n", TRUE));
                    }
                } catch (\Exception $e) {
                    fwrite(STDERR, print_r("Échec de l'authentification manuelle avec login(): " . $e->getMessage() . "\n", TRUE));
                    fwrite(STDERR, print_r("Fichier: " . $e->getFile() . " (ligne " . $e->getLine() . ")\n", TRUE));
                }
            }
            
            // Vérifier que l'utilisateur est bien authentifié avec assertAuthenticatedAs
            fwrite(STDERR, print_r("\nVérification avec assertAuthenticatedAs...\n", TRUE));
            $this->assertAuthenticatedAs($this->vendeur, 'web');
            
            // Créer des catégories de test
            $categories = \App\Models\Categorie::factory(3)->create();
            
            // Afficher les noms des catégories créées pour le débogage
            $categoryNames = $categories->pluck('nom')->toArray();
            fwrite(STDERR, print_r("Catégories créées: " . implode(', ', $categoryNames) . "\n", TRUE));

            // Tester l'accès au contrôleur Livewire
            fwrite(STDERR, print_r("Tentative d'accès au contrôleur Livewire...\n", TRUE));
            
            $test = Livewire::actingAs($this->vendeur)
                ->test(\App\Livewire\Vendeur\CategorieController::class);
                
            // Afficher le statut de la réponse
            fwrite(STDERR, print_r("Statut de la réponse: " . $test->status() . "\n", TRUE));
            
            // Afficher les erreurs de validation s'il y en a
            if ($test->hasErrors()) {
                fwrite(STDERR, print_r("Erreurs de validation: " . print_r($test->errors(), true) . "\n", TRUE));
            }
            
            // Afficher le contenu de la réponse pour le débogage
            fwrite(STDERR, print_r("Contenu de la réponse: " . $test->html() . "\n", TRUE));
            
            // Vérifier que les catégories sont bien affichées
            $test->assertSeeInOrder($categoryNames);
            
        } catch (\Exception $e) {
            fwrite(STDERR, print_r("ERREUR lors de l'appel à actingAs: " . $e->getMessage() . "\n", TRUE));
            fwrite(STDERR, print_r("Fichier: " . $e->getFile() . " (ligne " . $e->getLine() . ")\n", TRUE));
            fwrite(STDERR, print_r("Trace: " . $e->getTraceAsString() . "\n", TRUE));
            
            // Relancer l'exception pour ne pas masquer l'erreur
            throw $e;
        }
    }

    /** @test */
    public function client_ne_peut_pas_acceder_aux_categories_du_vendeur()
    {
        $this->actingAs($this->client)
            ->get('/vendeur/categories')
            ->assertStatus(403);
    }

    /** @test */
    public function vendeur_peut_creer_une_categorie()
    {
        $this->actingAs($this->vendeur);

        $image = UploadedFile::fake()->image('categorie.jpg');

        Livewire::test(\App\Livewire\Vendeur\CategorieController::class)
            ->set('nom', 'Nouvelle Catégorie')
            ->set('description', 'Description de la catégorie')
            ->set('est_actif', true)
            ->set('image', $image)
            ->call('store');

        $this->assertDatabaseHas('categories', [
            'nom' => 'Nouvelle Catégorie',
            'slug' => 'nouvelle-categorie',
            'description' => 'Description de la catégorie',
            'est_actif' => true,
        ]);

        // Vérifier que l'image a été stockée
        Storage::disk('public')->assertExists('categories/' . $image->hashName());
    }

    /** @test */
    public function vendeur_peut_modifier_une_categorie()
    {
        $categorie = Categorie::factory()->create();
        
        $this->actingAs($this->vendeur);

        Livewire::test(\App\Livewire\Vendeur\CategorieController::class)
            ->call('edit', $categorie->id)
            ->set('nom', 'Catégorie Modifiée')
            ->set('description', 'Description modifiée')
            ->set('est_actif', false)
            ->call('store');

        $this->assertDatabaseHas('categories', [
            'id' => $categorie->id,
            'nom' => 'Catégorie Modifiée',
            'slug' => 'categorie-modifiee',
            'description' => 'Description modifiée',
            'est_actif' => false,
        ]);
    }

    /** @test */
    public function vendeur_peut_changer_le_statut_dune_categorie()
    {
        $categorie = Categorie::factory()->create(['est_actif' => true]);
        
        $this->actingAs($this->vendeur);

        Livewire::test(\App\Livewire\Vendeur\CategorieController::class)
            ->call('toggleStatus', $categorie->id);

        $this->assertDatabaseHas('categories', [
            'id' => $categorie->id,
            'est_actif' => false,
        ]);
    }

    /** @test */
    public function vendeur_peut_supprimer_une_categorie_non_utilisee()
    {
        $categorie = Categorie::factory()->create();
        
        $this->actingAs($this->vendeur);

        Livewire::test(\App\Livewire\Vendeur\CategorieController::class)
            ->call('delete', $categorie->id);

        $this->assertDatabaseMissing('categories', [
            'id' => $categorie->id,
        ]);
    }

    /** @test */
    public function vendeur_ne_peut_pas_supprimer_une_categorie_utilisee_par_des_tissus()
    {
        $categorie = Categorie::factory()
            ->hasTissus(1)
            ->create();
        
        $this->actingAs($this->vendeur);

        Livewire::test(\App\Livewire\Vendeur\CategorieController::class)
            ->call('delete', $categorie->id);

        // La catégorie doit toujours exister
        $this->assertDatabaseHas('categories', [
            'id' => $categorie->id,
        ]);
    }

    /** @test */
    public function validation_du_formulaire_de_categorie()
    {
        $this->actingAs($this->vendeur);

        // Test avec un nom vide
        Livewire::test(\App\Livewire\Vendeur\CategorieController::class)
            ->set('nom', '')
            ->call('store')
            ->assertHasErrors(['nom' => 'required']);

        // Test avec un nom trop long
        Livewire::test(\App\Livewire\Vendeur\CategorieController::class)
            ->set('nom', str_repeat('a', 256))
            ->call('store')
            ->assertHasErrors(['nom' => 'max']);

        // Test avec un fichier trop volumineux
        $largeFile = UploadedFile::fake()->image('large.jpg')->size(3000);
        Livewire::test(\App\Livewire\Vendeur\CategorieController::class)
            ->set('image', $largeFile)
            ->call('store')
            ->assertHasErrors(['image' => 'max']);

        // Test avec un type de fichier invalide
        $invalidFile = UploadedFile::fake()->create('document.pdf', 1000);
        Livewire::test(\App\Livewire\Vendeur\CategorieController::class)
            ->set('image', $invalidFile)
            ->call('store')
            ->assertHasErrors(['image' => 'image']);
    }
}
