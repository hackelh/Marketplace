<?php

namespace App\Livewire\Client\Commandes;

use Livewire\Component;
use App\Models\Commande;
use Illuminate\Support\Facades\Auth;

class Show extends Component
{
    public $order;
    public $reference;
    public $notFound = false;

    public function mount($reference)
    {
        $this->reference = $reference;
        $this->loadOrder();
    }

    public function loadOrder()
    {
        $this->order = Commande::with(['items', 'user'])
            ->where('reference', $this->reference)
            ->where('user_id', Auth::id())
            ->first();

        if (!$this->order) {
            $this->notFound = true;
            return;
        }

        // Décoder l'adresse de livraison si c'est une chaîne JSON
        if (is_string($this->order->adresse_livraison)) {
            $this->order->adresse_livraison = json_decode($this->order->adresse_livraison, true);
        }

        // Décoder les options des articles si nécessaire
        foreach ($this->order->items as $item) {
            if (is_string($item->options)) {
                $item->options = json_decode($item->options, true);
            }
        }
    }

    public function render()
    {
        if ($this->notFound) {
            return view('livewire.client.commandes.not-found')
                ->layout('layouts.client');
        }

        return view('livewire.client.commandes.show')
            ->layout('layouts.client');
    }
}
