<?php

namespace App\Livewire\Client;

use Livewire\Component;
use App\Models\Tissu;

class ProductController extends Component
{
    public $product;

    protected $listeners = ['refreshProduct' => '$refresh'];

    public function mount($tissu)
    {
        // Récupérer le produit par son slug ou son ID
        $this->product = Tissu::where('slug', $tissu)
                             ->orWhere('id', $tissu)
                             ->with('categorie', 'images')
                             ->firstOrFail();
    }

    public function addToCart($productId)
    {
        $product = Tissu::findOrFail($productId);

        if ($product->quantite <= 0) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Ce produit est en rupture de stock.'
            ]);
            return;
        }

        $cart = session()->get('panier', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantite']++;
        } else {
            $cart[$product->id] = [
                'id' => $product->id,
                'titre' => $product->titre,
                'prix' => $product->prix,
                'quantite' => 1,
                'image' => $product->images->isNotEmpty() 
                    ? asset('storage/' . $product->images->first()->image_path) 
                    : asset('images/placeholder.svg'),
                'slug' => $product->slug,
            ];
        }

        // Mise à jour de la session
        session(['panier' => $cart]);
        
        // Déclenchement des événements Livewire
        $this->dispatch('panier-mis-a-jour');
        
        // Rafraîchissement du composant panier
        $this->dispatch('refreshPanier')->to(PanierController::class);
        
        // Notification utilisateur
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Produit ajouté au panier.'
        ]);
        
        // Rafraîchissement du composant actuel
        $this->product = $this->product->fresh(['categorie', 'images']);
    }

    public function render()
    {
        return view('livewire.client.product', [
            'product' => $this->product
        ]);
    }
}
