<?php

namespace App\Livewire\Client;

use Livewire\Component;
use App\Models\Tissu;
use App\Models\Commande;
use App\Models\CommandeItem;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use App\Mail\CommandeConfirmee;
use App\Notifications\NouvelleCommandeNotification;

class PanierController extends Component
{
    public Collection $panierItems;
    public Collection $produits;
    public float $total = 0;
    public float $sousTotal = 0;
    public float $fraisLivraison = 0;
    public bool $panierVide = true;
    public array $quantites = [];
    public array $adresseLivraison = [];
    public string $modePaiement = 'livraison';
    public string $commentaire = '';

    /**
     * Initialise les propriétés du composant
     */
    public function boot()
    {
        $this->panierItems = collect();
        $this->produits = collect();
    }

    protected $listeners = [
        'panierMisAJour' => 'mount',
        'refreshPanier' => '$refresh'
    ];

    public function mount()
    {
        try {
            // Initialisation des propriétés
            $this->panierItems = collect();
            $this->produits = collect();
            $this->total = 0;
            $this->sousTotal = 0;
            $this->fraisLivraison = 0;
            $this->panierVide = true;
            $this->quantites = [];
            
            // Chargement du panier et de l'adresse
            $this->chargerPanier();
            $this->initAdresseLivraison();
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'initialisation du panier: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            $this->ajouterErreur('Une erreur est survenue lors du chargement de votre panier. Veuillez réessayer.');
            
            // Réinitialisation en cas d'erreur
            $this->panierItems = collect();
            $this->produits = collect();
            $this->panierVide = true;
        }
    }

    protected function initAdresseLivraison(): void
    {
        $user = Auth::user();
        $this->adresseLivraison = [
            'nom' => $user->name,
            'email' => $user->email,
            'telephone' => $user->telephone ?? '',
            'adresse' => $user->adresse ?? '',
            'code_postal' => $user->code_postal ?? '',
            'ville' => $user->ville ?? '',
            'pays' => $user->pays ?? 'Sénégal',
        ];
    }

    protected function chargerPanier(): void
    {
        \Log::info('Début de la méthode chargerPanier()');
        try {
            // Récupération du panier avec une valeur par défaut vide si non défini
            $panier = Session::get('panier');
            if (!is_array($panier)) {
                $panier = [];
                Session::put('panier', $panier);
            }
            
            // Journalisation sécurisée du contenu du panier
            if (config('app.debug')) {
                \Log::debug('Contenu du panier :', ['panier' => $panier]);
            }
            
            $this->panierVide = empty($panier);
            \Log::info(sprintf('Panier %s', $this->panierVide ? 'vide' : 'non vide'));

            if ($this->panierVide) {
                \Log::info('Panier vide, initialisation des valeurs par défaut');
                $this->produits = collect();
                $this->panierItems = collect();
                $this->sousTotal = 0;
                $this->fraisLivraison = 0;
                $this->total = 0;
                return;
            }

            \Log::info('Chargement des produits depuis la base de données');
            // Charger les produits avec les relations nécessaires
            $produitsQuery = Tissu::with(['images', 'categorie']);
            \Log::info('Requête SQL : ' . $produitsQuery->toSql());
            \Log::info('IDs des produits dans le panier : ' . json_encode(array_keys($panier)));
            
            $this->produits = $produitsQuery->whereIn('id', array_keys($panier))->get();
            \Log::info('Nombre de produits chargés : ' . $this->produits->count());
            
            $this->produits = $this->produits->keyBy('id');

            // Créer une collection d'articles du panier avec les quantités
            $this->panierItems = collect($panier)->map(function ($quantite, $produitId) {
                \Log::info("Traitement du produit ID: $produitId, Quantité: $quantite");
                
                $produit = $this->produits->get($produitId);
                \Log::info('Produit récupéré : ' . ($produit ? 'Oui' : 'Non'));
                
                if (!$produit) {
                    \Log::warning("Produit ID $produitId non trouvé dans la base de données, retrait du panier");
                    $this->retirerDuPanier($produitId);
                    return null;
                }

                try {
                    $prix = $produit->en_promotion ? $produit->prix_promotion : $produit->prix;
                    $quantite = max(1, (int)$quantite);
                    $sousTotal = $prix * $quantite;
                    $estDisponible = $produit->estEnStock($quantite);
                    $imageUrl = $produit->images->first() ? $produit->images->first()->getUrl('thumb') : asset('images/default-tissu.jpg');
                    $lien = route('catalogue.show', $produit->slug ?? $produit->id);
                    
                    \Log::info("Détails du produit - ID: $produit->id, Nom: $produit->titre, Prix: $prix, Quantité: $quantite, Sous-total: $sousTotal");
                    
                    return [
                        'id' => $produit->id,
                        'slug' => $produit->slug,
                        'reference' => $produit->reference,
                        'nom' => $produit->titre,
                        'prix' => $prix,
                        'prix_initial' => $produit->prix,
                        'quantite' => $quantite,
                        'sous_total' => $sousTotal,
                        'disponible' => $estDisponible,
                        'stock_disponible' => $produit->quantite,
                        'image' => $imageUrl,
                        'lien' => $lien,
                        'en_promotion' => $produit->en_promotion,
                        'prix_promotion' => $produit->prix_promotion,
                    ];
                } catch (\Exception $e) {
                    \Log::error("Erreur lors du traitement du produit ID $produitId: " . $e->getMessage());
                    \Log::error($e->getTraceAsString());
                    return null;
                }
            })->filter()->values();

            \Log::info('Calcul des totaux du panier');
            $this->calculerTotaux();
            \Log::info('Méthode chargerPanier() terminée avec succès');
            
        } catch (\Exception $e) {
            $errorMessage = 'Erreur lors du chargement de votre panier: ' . $e->getMessage();
            $this->ajouterErreur($errorMessage);
            \Log::error($errorMessage);
            \Log::error('Fichier: ' . $e->getFile() . ' Ligne: ' . $e->getLine());
            \Log::error($e->getTraceAsString());
            
            // Initialiser les collections vides en cas d'erreur
            $this->produits = collect();
            $this->panierItems = collect();
            $this->sousTotal = 0;
            $this->fraisLivraison = 0;
            $this->total = 0;
        }
    }

    protected function calculerTotaux(): void
    {
        $this->sousTotal = $this->panierItems->sum('sous_total');
        $this->calculerFraisLivraison();
        $this->total = $this->sousTotal + $this->fraisLivraison;
    }
    
    protected function calculerFraisLivraison(): void
    {
        // Frais de livraison gratuits à partir de 50 000 FCFA
        $this->fraisLivraison = $this->sousTotal >= 50000 ? 0 : 3000;
    }

    // Méthodes de gestion du panier
    public function ajouter($produitId, $quantite = 1): void
    {
        $produit = Tissu::find($produitId);
        
        if (!$produit) {
            $this->ajouterErreur('Produit introuvable.');
            return;
        }

        $panier = Session::get('panier', []);
        $quantiteExistante = $panier[$produitId] ?? 0;
        $nouvelleQuantite = $quantiteExistante + $quantite;

        if ($nouvelleQuantite > $produit->quantite) {
            $this->ajouterErreur("Stock insuffisant pour {$produit->titre}. Stock disponible : {$produit->quantite}");
            return;
        }

        $panier[$produitId] = $nouvelleQuantite;
        Session::put('panier', $panier);
        
        // Émettre l'événement avec le bon nom (kebab-case pour la compatibilité JavaScript)
        $this->dispatch('panier-mis-a-jour');
        $this->mount();
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Produit ajouté au panier',
            'product' => [
                'id' => $produit->id,
                'name' => $produit->titre,
                'price' => $produit->prix_actuel,
                'image' => $produit->images->first()?->getUrl('thumb') ?? asset('images/default-tissu.jpg'),
                'url' => route('catalogue.show', $produit->slug ?? $produit->id)
            ]
        ]);
    }

    public function incrementer($produitId): void
    {
        $this->mettreAJour($produitId, ($this->quantites[$produitId] ?? 0) + 1);
    }

    public function decrementer($produitId): void
    {
        $this->mettreAJour($produitId, max(1, ($this->quantites[$produitId] ?? 1) - 1));
    }

    public function mettreAJour($produitId, $quantite = null): void
    {
        $quantite = (int)($quantite ?? $this->quantites[$produitId] ?? 1);
        
        if ($quantite < 1) {
            $this->supprimer($produitId);
            return;
        }

        $produit = Tissu::find($produitId);
        
        if (!$produit) {
            $this->retirerDuPanier($produitId);
            return;
        }

        if ($quantite > $produit->quantite) {
            $this->ajouterErreur("Stock insuffisant pour {$produit->titre}. Stock disponible : {$produit->quantite}");
            return;
        }

        $panier = Session::get('panier', []);
        $panier[$produitId] = $quantite;
        Session::put('panier', $panier);
        
        $this->emit('panierMiseAJour');
        $this->mount();
    }

    public function supprimer($produitId): void
    {
        $this->retirerDuPanier($produitId);
        $this->emit('panierMiseAJour');
        $this->mount();
    }

    protected function retirerDuPanier($produitId): void
    {
        $panier = Session::get('panier', []);
        unset($panier[$produitId]);
        Session::put('panier', $panier);
    }

    public function vider(): void
    {
        Session::forget('panier');
        $this->emit('panierMiseAJour');
        $this->mount();
        
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => 'Votre panier a été vidé'
        ]);
    }



    /**
     * Valide et confirme une commande avec paiement à la livraison
     */
    public function confirmerCommande()
    {
        // 1. Validation du panier
        if ($this->panierVide) {
            $this->ajouterErreur('Votre panier est vide.');
            return;
        }

        // 2. Validation de l'adresse de livraison
        $validation = $this->validerAdresseLivraison();
        if (!$validation['valide']) {
            $this->ajouterErreur($validation['message']);
            return;
        }

        // 3. Vérification des stocks et disponibilité
        $produitsHorsStock = [];
        $produitsIndisponibles = [];
        
        // Vérifier la disponibilité des produits avant de commencer la transaction
        foreach ($this->panierItems as $item) {
            $produit = Tissu::find($item['id']);
            
            if (!$produit || !$produit->is_active) {
                $produitsIndisponibles[] = $item['nom'];
                continue;
            }

            if ($produit->quantite < $item['quantite']) {
                $produitsHorsStock[] = [
                    'nom' => $produit->titre,
                    'stock_disponible' => $produit->quantite,
                    'quantite_demandee' => $item['quantite']
                ];
            }
        }

        // Gestion des erreurs de stock/disponibilité
        if (!empty($produitsIndisponibles)) {
            $this->ajouterErreur(
                'Certains produits ne sont plus disponibles : ' . implode(', ', $produitsIndisponibles)
            );
            return;
        }

        if (!empty($produitsHorsStock)) {
            $messages = [];
            foreach ($produitsHorsStock as $produit) {
                $messages[] = "{$produit['nom']} (stock: {$produit['stock_disponible']}, demandé: {$produit['quantite_demandee']})";
            }
            $this->ajouterErreur("Stock insuffisant pour : " . implode(', ', $messages));
            return;
        }

        // 4. Démarrer une transaction pour assurer l'intégrité des données
        DB::beginTransaction();

        try {
            // S'assurer que les totaux sont à jour
            $this->calculerTotaux();

            // Créer la commande avec paiement à la livraison
            $commande = new Commande();
            $commande->reference = $this->genererReferenceCommande();
            $commande->user_id = Auth::id();
            $commande->statut = Commande::STATUS_PENDING;
            $commande->payment_status = Commande::PAYMENT_STATUS_PENDING;
            $commande->payment_method = 'a_la_livraison';
            $commande->sous_total = $this->sousTotal;
            $commande->frais_livraison = $this->fraisLivraison;
            $commande->total = $this->total;
            $commande->adresse_livraison = $this->adresseLivraison;
            $commande->commentaire = $this->commentaire ?? null;

            // Journalisation pour le débogage
            \Log::info('Création de la commande', [
                'sous_total' => $commande->sous_total,
                'frais_livraison' => $commande->frais_livraison,
                'total' => $commande->total,
                'items_count' => $this->panierItems->count()
            ]);

            $commande->save();

            // 5. Ajouter les articles de la commande et mettre à jour les stocks
            foreach ($this->panierItems as $item) {
                $produit = Tissu::find($item['id']);
                
                if (!$produit) {
                    throw new \Exception("Le produit avec l'ID {$item['id']} n'existe plus.");
                }

                // Créer l'article de commande
                $commandeItem = $commande->items()->create([
                    'tissu_id' => $produit->id,
                    'quantity' => $item['quantite'],
                    'unit_price' => $item['prix'],
                    'total_price' => $item['sous_total'],
                    'tissu_name' => $item['nom'],
                    'tissu_image' => $item['image'],
                    'options' => json_encode([
                        'reference' => $item['reference'] ?? '',
                        'taille' => $item['taille'] ?? null,
                        'couleur' => $item['couleur'] ?? null
                    ])
                ]);

                // Mettre à jour le stock de manière atomique
                $produit->decrement('quantite', $item['quantite']);

                // Enregistrer l'historique des modifications de stock
                if ($produit->quantite <= $produit->quantite_alerte) {
                    // Déclencher une alerte de stock bas si nécessaire
                    event(new StockBas($produit));
                }
            }

            // 6. Mettre à jour les statistiques de l'utilisateur
            $this->mettreAJourStatistiquesUtilisateur();
            
            // 7. Valider la transaction
            DB::commit();
            
            // 8. Envoyer les notifications
            $this->envoyerNotifications($commande);
            
            // 9. Vider le panier et réinitialiser les totaux
            Session::forget('panier');
            $this->panierItems = collect();
            $this->sousTotal = 0;
            $this->fraisLivraison = 0;
            $this->total = 0;
            $this->panierVide = true;
            
            // 10. Rediriger vers la page de confirmation avec un message de succès
            return redirect()->route('commandes.show', $commande->reference)
                ->with([
                    'success' => 'Votre commande #' . $commande->reference . ' a été enregistrée avec succès !',
                    'order_reference' => $commande->reference,
                    'order_total' => number_format($commande->total, 0, ',', ' ') . ' FCFA',
                    'order_created' => true
                ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la confirmation de la commande : ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            $this->ajouterErreur('Une erreur est survenue lors du traitement de votre commande. Veuillez réessayer ou nous contacter si le problème persiste.');
            
            return back()->withInput();
        }
    }
    
    /**
     * Valide les informations de livraison
     */
    protected function validerAdresseLivraison(): array
    {
        $regles = [
            'nom' => 'required|string|max:100',
            'prenoms' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'telephone' => [
                'required',
                'string',
                'regex:/^(\+225|0)[0-9]{8,15}$/'
            ],
            'adresse' => 'required|string|max:255',
            'complement' => 'nullable|string|max:255',
            'ville' => 'required|string|max:100',
            'pays' => 'required|string|max:100',
            'code_postal' => 'nullable|string|max:20',
        ];

        $messages = [
            'required' => 'Le champ :attribute est obligatoire.',
            'email' => 'L\'adresse email n\'est pas valide.',
            'telephone.regex' => 'Le format du numéro de téléphone est invalide. Exemple: +2250102030405 ou 0102030405',
            'max' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
        ];

        $validateur = Validator::make($this->adresseLivraison, $regles, $messages);

        if ($validateur->fails()) {
            return [
                'valide' => false,
                'message' => $validateur->errors()->first()
            ];
        }
        
        // Nettoyer et formater les données
        $this->adresseLivraison = array_map('trim', $this->adresseLivraison);
        $this->adresseLivraison['telephone'] = $this->formatTelephone($this->adresseLivraison['telephone']);
        
        return ['valide' => true];
    }
    
    /**
     * Formate un numéro de téléphone
     */
    protected function formatTelephone($telephone)
    {
        // Supprimer tous les caractères non numériques
        $telephone = preg_replace('/[^0-9+]/', '', $telephone);
        
        // Si le numéro commence par 0, le remplacer par +225
        if (strpos($telephone, '0') === 0) {
            $telephone = '+225' . substr($telephone, 1);
        }
        // Si le numéro commence par 225, ajouter le +
        elseif (strpos($telephone, '225') === 0) {
            $telephone = '+' . $telephone;
        }
        
        return $telephone;
    }
    
    protected function genererReferenceCommande(): string
    {
        return 'CMD-' . strtoupper(Str::random(8)) . '-' . now()->format('Ymd');
    }
    
    protected function mettreAJourStatistiquesUtilisateur(): void
    {
        $user = Auth::user();
        $user->increment('nombre_commandes');
        $user->total_achats += $this->total;
        $user->save();
    }
    
    /**
     * Envoie les notifications de commande
     */
    protected function envoyerNotifications(Commande $commande): void
    {
        try {
            // 1. Envoyer un email au client
            $this->envoyerEmailConfirmation($commande);
            
            // 2. Notifier l'équipe commerciale
            $this->notifierEquipeCommerciale($commande);
            
            // 3. Notifier le service logistique si nécessaire
            if ($commande->frais_livraison > 0) {
                $this->notifierLogistique($commande);
            }
            
            // 4. Envoyer un SMS au client (optionnel, à implémenter avec un service SMS)
            $this->envoyerSMSConfirmation($commande);
            
        } catch (\Exception $e) {
            // Logger l'erreur mais ne pas interrompre le processus de commande
            \Log::error('Erreur lors de l\'envoi des notifications : ' . $e->getMessage());
        }
    }
    
    /**
     * Envoie un email de confirmation au client
     */
    protected function envoyerEmailConfirmation(Commande $commande): void
    {
        try {
            // Utiliser la notification Laravel
            $commande->user->notify(new \App\Notifications\CommandeConfirmee($commande));
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'envoi de l\'email de confirmation : ' . $e->getMessage());
        }
    }

    protected function getAdresseLivraison(): array
    {
        $user = Auth::user();
        
        return [
            'nom' => $user->name,
            'email' => $user->email,
            'telephone' => $user->telephone ?? '',
            'adresse' => $user->adresse ?? '',
            'code_postal' => $user->code_postal ?? '',
            'ville' => $user->ville ?? '',
            'pays' => $user->pays ?? 'Sénégal',
        ];
    }

    /**
     * Ajoute un message d'erreur à la session et déclenche une notification
     * 
     * @param string|array $message Message d'erreur ou tableau d'erreurs
     * @param string $type Type de notification (error, warning, success, info)
     */
    protected function ajouterErreur($message, string $type = 'error'): void
    {
        // Convertir les tableaux en chaîne lisible
        if (is_array($message)) {
            $message = implode(' ', array_filter($message, 'is_string'));
        }
        
        // S'assurer que le message est une chaîne
        $message = is_string($message) ? $message : 'Une erreur inconnue est survenue';
        
        // Journalisation de l'erreur
        if ($type === 'error') {
            \Log::error('Erreur panier: ' . $message);
        } else {
            \Log::warning('Avertissement panier: ' . $message);
        }
        
        // Ajout du message à la session
        session()->flash($type, $message);
        
        // Déclenchement de la notification Livewire
        $this->dispatch('notify', [
            'type' => $type,
            'message' => $message
        ]);
    }

    /**
     * Renvoie le nombre d'articles dans le panier
     */
    public function count()
    {
        $panier = Session::get('panier', []);
        return response()->json([
            'count' => array_sum(array_column($panier, 'quantite'))
        ]);
    }

    public function render()
    {
        return view('livewire.client.panier')
            ->layout('layouts.app', [
                'title' => 'Mon Panier',
                'description' => 'Votre panier d\'achat sur ' . config('app.name')
            ]);
    }
}
