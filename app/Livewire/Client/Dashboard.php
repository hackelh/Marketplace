<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Tissu;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    use WithPagination;
    
    public $recentOrders;
    public $suggestedProducts;
    
    public function mount()
    {
        // Initialiser les collections vides
        $this->recentOrders = collect();
        $this->suggestedProducts = collect();
        
        // Charger les commandes récentes de l'utilisateur
        if (Auth::check()) {
            $this->recentOrders = Order::where('user_id', Auth::id())
                ->with('items.tissu')
                ->latest()
                ->take(5)
                ->get() ?? collect();
                
            // Charger des suggestions de produits basées sur l'historique ou aléatoires
            $this->suggestedProducts = Tissu::with(['images', 'categorie'])
                ->where('is_published', true)
                ->where('quantite', '>', 0)
                ->inRandomOrder()
                ->take(4)
                ->get() ?? collect();
                
            // Vérifier que les slugs sont bien générés
            $this->suggestedProducts->each(function($product) {
                if (empty($product->slug)) {
                    $product->save(); // Cela va forcer la génération du slug via le trait HasSlug
                }
                \Log::info('Product data:', [
                    'id' => $product->id,
                    'titre' => $product->titre,
                    'slug' => $product->slug,
                    'is_published' => $product->is_published,
                    'quantite' => $product->quantite
                ]);
            });
        }
    }
    
    public function render()
    {
        return view('livewire.client.dashboard');
    }
}
