<?php

namespace App\Livewire\Client;

use Livewire\Component;
use App\Models\Tissu;

class ProductDetailController extends Component
{
    public Tissu $product;

    public function mount($tissu)
    {
        if (is_numeric($tissu)) {
            $this->product = Tissu::with(['categorie', 'vendeur', 'images'])->findOrFail($tissu);
        } else {
            $this->product = Tissu::with(['categorie', 'vendeur', 'images'])
                                ->where('slug', $tissu)
                                ->firstOrFail();
        }
    }

    public function addToCart($tissuId)
    {
        $tissu = Tissu::findOrFail($tissuId);

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
                'image' => $tissu->image ? asset('storage/' . $tissu->image) : asset('images/placeholder.svg'),
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
        return view('livewire.client.product-detail', [
            'product' => $this->product
        ]);
    }
}
