<?php

namespace App\Livewire\Client;

use Livewire\Component;
use App\Models\Tissu;
use App\Models\Favori;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class TissuShow extends Component
{
    use WithPagination;
    
    public $tissu;
    public $quantite = 1;
    public $selectedImageIndex = 0;
    public $estFavori = false;
    
    protected $listeners = ['favoriUpdated' => 'verifierFavori'];
    
    public function mount(Tissu $tissu)
    {
        $this->tissu = $tissu->load('images', 'categorie');
        $this->verifierFavori();
    }
    
    public function selectImage($index)
    {
        $this->selectedImageIndex = $index;
    }
    
    public function incrementerQuantite()
    {
        $this->quantite++;
    }
    
    public function decrementerQuantite()
    {
        if ($this->quantite > 1) {
            $this->quantite--;
        }
    }
    
    public function ajouterAuPanier()
    {
        // Vérifier si le produit est en stock
        if ($this->tissu->quantite < $this->quantite) {
            session()->flash('error', 'La quantité demandée n\'est pas disponible en stock.');
            return;
        }
        
        // Récupérer le panier de la session ou en créer un nouveau
        $panier = session()->get('panier', []);
        
        // Vérifier si le produit est déjà dans le panier
        if (isset($panier[$this->tissu->id])) {
            $panier[$this->tissu->id]['quantite'] += $this->quantite;
        } else {
            $panier[$this->tissu->id] = [
                'id' => $this->tissu->id,
                'nom' => $this->tissu->nom,
                'prix' => $this->tissu->prix,
                'quantite' => $this->quantite,
                'image' => $this->tissu->images->isNotEmpty() ? $this->tissu->images->first()->getUrl('thumb') : null,
                'slug' => $this->tissu->slug,
            ];
        }
        
        // Mettre à jour la session
        session()->put('panier', $panier);
        
        // Émettre un événement pour mettre à jour le compteur du panier
        $this->dispatch('panier-mis-a-jour');
        
        // Message de succès
        session()->flash('success', 'Le produit a été ajouté à votre panier.');
        
        // Réinitialiser la quantité
        $this->quantite = 1;
    }
    
    /**
     * Vérifie si le tissu est dans les favoris de l'utilisateur connecté.
     */
    public function verifierFavori(): void
    {
        $this->estFavori = auth()->check() 
            ? Favori::where('user_id', auth()->id())
                ->where('tissu_id', $this->tissu->id)
                ->exists()
            : false;
    }
    
    /**
     * Ajoute ou supprime le tissu des favoris.
     */
    public function toggleFavori(): void
    {
        if (!auth()->check()) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'Vous devez être connecté pour ajouter aux favoris.'
            ]);
            return;
        }
        
        if ($this->estFavori) {
            Favori::where('user_id', auth()->id())
                ->where('tissu_id', $this->tissu->id)
                ->delete();
                
            $message = 'Le tissu a été retiré de vos favoris.';
        } else {
            Favori::create([
                'user_id' => auth()->id(),
                'tissu_id' => $this->tissu->id,
            ]);
            
            $message = 'Le tissu a été ajouté à vos favoris.';
        }
        
        $this->estFavori = !$this->estFavori;
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $message
        ]);
        
        // Émettre un événement pour mettre à jour d'autres composants si nécessaire
        $this->dispatch('favoriUpdated');
    }
    
    public function render()
    {
        // Tissus similaires (même catégorie)
        $tissusSimilaires = Tissu::where('categorie_id', $this->tissu->categorie_id)
            ->where('id', '!=', $this->tissu->id)
            ->where('is_published', true)
            ->where('quantite', '>', 0)
            ->with('images')
            ->take(4)
            ->get();
            
        return view('livewire.client.tissu-show', [
            'tissu' => $this->tissu,
            'tissusSimilaires' => $tissusSimilaires,
        ]);
    }
}
