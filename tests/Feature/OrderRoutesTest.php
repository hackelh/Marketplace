<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderRoutesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function un_utilisateur_connecte_peut_acceder_a_la_liste_de_ses_commandes()
    {
        // Créer un utilisateur et une commande
        $user = User::factory()->create(['role' => 'client']);
        $order = Order::factory()->create(['user_id' => $user->id]);
        
        // Tenter d'accéder à la liste des commandes
        $response = $this->actingAs($user)
                         ->get(route('commandes.index'));
        
        // Vérifier que la réponse est réussie
        $response->assertStatus(200);
        $response->assertViewIs('livewire.client.order-controller');
    }
    
    /** @test */
    public function un_utilisateur_non_connecte_est_redirige_vers_la_page_de_connexion()
    {
        // Tenter d'accéder à la liste des commandes sans être connecté
        $response = $this->get(route('commandes.index'));
        
        // Vérifier la redirection vers la page de connexion
        $response->assertRedirect(route('login'));
    }
    
    /** @test */
    public function un_utilisateur_peut_voir_les_details_de_sa_commande()
    {
        // Créer un utilisateur et une commande
        $user = User::factory()->create(['role' => 'client']);
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'order_number' => 'TEST1234'
        ]);
        
        // Tenter d'accéder aux détails de la commande
        $response = $this->actingAs($user)
                         ->get(route('commandes.show', $order->order_number));
        
        // Vérifier que la réponse est réussie
        $response->assertStatus(200);
        $response->assertViewIs('livewire.client.order-controller');
    }
    
    /** @test */
    public function un_utilisateur_ne_peut_pas_voir_les_commandes_d_un_autre_utilisateur()
    {
        // Créer deux utilisateurs
        $user1 = User::factory()->create(['role' => 'client']);
        $user2 = User::factory()->create(['role' => 'client']);
        
        // Créer une commande pour le premier utilisateur
        $order = Order::factory()->create(['user_id' => $user1->id]);
        
        // Tenter d'accéder à la commande avec le deuxième utilisateur
        $response = $this->actingAs($user2)
                         ->get(route('commandes.show', $order->order_number));
        
        // Vérifier que l'accès est refusé (404 car la commande n'existe pas pour cet utilisateur)
        $response->assertStatus(404);
    }
}
