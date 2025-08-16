<?php

namespace App\Livewire\Client;

use Livewire\Component;
use App\Models\Tissu;

class CatalogueShow extends Component
{
    public $product;

    public function mount($tissu)
    {
        $this->product = Tissu::with(['categorie', 'images'])
                            ->where('slug', $tissu)
                            ->orWhere('id', $tissu)
                            ->firstOrFail();
    }

    public function addToCart($productId)
    {
        $tissu = Tissu::findOrFail($productId);
        
        if ($tissu->quantite <= 0) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Ce produit n\'est plus en stock.'
            ]);
            return;
        }

        $panier = session()->get('panier', []);
        
        if (isset($panier[$tissu->id])) {
            $panier[$tissu->id]['quantite']++;
        } else {
            $panier[$tissu->id] = [
                'id' => $tissu->id,
                'titre' => $tissu->titre,
                'prix' => $tissu->prix,
                'quantite' => 1,
                'image' => $tissu->images->isNotEmpty() ? asset('storage/' . $tissu->images->first()->image_path) : asset('images/placeholder.svg'),
                'slug' => $tissu->slug,
            ];
        }
        
        session()->put('panier', $panier);
        $this->dispatch('panier-mis-a-jour');
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Le produit a été ajouté à votre panier.'
        ]);
    }

    public function render()
    {
        return view('livewire.client.catalogue-show');
    }
}
