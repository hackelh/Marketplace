<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Tissu;
use App\Models\OrderItem;
use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $order;
    protected $tissu;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer un utilisateur client
        $this->user = User::factory()->create([
            'role' => 'client'
        ]);
        
        // Créer un produit (tissu)
        $this->tissu = Tissu::factory()->create([
            'nom' => 'Tissu de test',
            'prix' => 10000, // 10 000 FCFA
            'quantite' => 10
        ]);
        
        // Créer une commande pour l'utilisateur
        $this->order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'TEST' . now()->timestamp,
            'status' => 'pending',
            'subtotal' => 10000,
            'shipping' => 2000,
            'tax' => 1000,
            'total' => 13000,
            'payment_status' => 'pending',
            'payment_method' => 'cash_on_delivery',
        ]);
        
        // Ajouter un article à la commande
        OrderItem::create([
            'order_id' => $this->order->id,
            'tissu_id' => $this->tissu->id,
            'quantity' => 1,
            'unit_price' => 10000,
        ]);
    }

    /** @test */
    public function un_utilisateur_connecte_peut_voir_ses_commandes()
    {
        $this->actingAs($this->user);
        
        $response = $this->get(route('commandes.index'));
        
        $response->assertStatus(200);
        $response->assertSeeLivewire('client.order-controller');
        $response->assertSee($this->order->order_number);
    }
    
    /** @test */
    public function un_utilisateur_ne_peut_voir_que_ses_propres_commandes()
    {
        // Créer un autre utilisateur
        $otherUser = User::factory()->create(['role' => 'client']);
        
        // Tenter d'accéder à la commande avec l'autre utilisateur
        $this->actingAs($otherUser);
        
        $response = $this->get(route('commandes.show', $this->order->order_number));
        
        $response->assertStatus(404);
    }
    
    /** @test */
    public function un_utilisateur_peut_voir_les_details_de_sa_commande()
    {
        $this->actingAs($this->user);
        
        $response = $this->get(route('commandes.show', $this->order->order_number));
        
        $response->assertStatus(200);
        $response->assertSee('Détails de la commande');
        $response->assertSee('Tissu de test');
        $response->assertSee('10 000 FCFA');
    }
    
    /** @test */
    public function les_onglets_filtrent_correctement_les_commandes()
    {
        $this->actingAs($this->user);
        
        // Tester l'onglet "En attente"
        Livewire::test('client.order-controller')
            ->set('activeTab', 'pending')
            ->assertViewHas('orders', function($orders) {
                return $orders->count() === 1 && 
                       $orders->first()->status === 'pending';
            });
            
        // Tester l'onglet "Toutes"
        Livewire::test('client.order-controller')
            ->set('activeTab', 'toutes')
            ->assertViewHas('orders', function($orders) {
                return $orders->count() === 1;
            });
    }
    
    /** @test */
    public function un_utilisateur_peut_annuler_une_commande_en_attente()
    {
        $this->actingAs($this->user);
        
        // Vérifier que la commande est bien en attente
        $this->assertEquals('pending', $this->order->fresh()->status);
        
        // Simuler l'annulation de la commande
        Livewire::test('client.order-controller')
            ->call('annulerCommande', $this->order->id);
            
        // Vérifier que le statut a été mis à jour
        $this->assertEquals('cancelled', $this->order->fresh()->status);
    }
    
    /** @test */
    public function un_utilisateur_ne_peut_pas_annuler_une_commande_deja_expediee()
    {
        // Mettre à jour le statut de la commande à "expédiée"
        $this->order->update(['status' => 'shipped']);
        
        $this->actingAs($this->user);
        
        // Tenter d'annuler la commande
        $response = Livewire::test('client.order-controller')
            ->call('annulerCommande', $this->order->id);
            
        // Vérifier que le statut n'a pas changé
        $this->assertEquals('shipped', $this->order->fresh()->status);
    }
}
