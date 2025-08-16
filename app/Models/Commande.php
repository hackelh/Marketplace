<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commande extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse
     */
    protected $fillable = [
        'numero_commande',
        'client_id',
        'vendeur_id',
        'tailleur_id',
        'statut',
        'montant_total',
        'date_commande',
        'date_livraison_prevue',
        'date_livraison_effective',
        'adresse_livraison',
        'notes',
        'methode_paiement',
        'statut_paiement',
    ];

    /**
     * Les attributs qui doivent être castés
     */
    protected $casts = [
        'montant_total' => 'decimal:2',
        'date_commande' => 'datetime',
        'date_livraison_prevue' => 'datetime',
        'date_livraison_effective' => 'datetime',
    ];

    /**
     * Une commande appartient à un client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Une commande appartient à un vendeur
     */
    public function vendeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendeur_id');
    }

    /**
     * Une commande peut être traitée par un tailleur
     */
    public function tailleur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tailleur_id');
    }

    /**
     * Une commande a plusieurs détails
     */
    public function details(): HasMany
    {
        return $this->hasMany(CommandeDetail::class);
    }

    /**
     * Scope pour les commandes en attente
     */
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    /**
     * Scope pour les commandes en cours
     */
    public function scopeEnCours($query)
    {
        return $query->whereIn('statut', ['en_attente', 'en_preparation', 'en_couture']);
    }

    /**
     * Scope pour les commandes validées
     */
    public function scopeValidees($query)
    {
        return $query->whereIn('statut', ['livree', 'terminee']);
    }

    /**
     * Générer un numéro de commande unique
     */
    public static function genererNumeroCommande(): string
    {
        $prefix = 'CMD';
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', today())->count() + 1;
        
        return $prefix . $date . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Obtenir le statut en français
     */
    public function getStatutLibelleAttribute(): string
    {
        $statuts = [
            'en_attente' => 'En attente',
            'en_preparation' => 'En préparation',
            'en_couture' => 'En couture',
            'pret' => 'Prêt',
            'livree' => 'Livrée',
            'terminee' => 'Terminée',
            'annulee' => 'Annulée',
        ];

        return $statuts[$this->statut] ?? $this->statut;
    }

    /**
     * Obtenir la couleur du badge selon le statut
     */
    public function getStatutCouleurAttribute(): string
    {
        $couleurs = [
            'en_attente' => 'warning',
            'en_preparation' => 'info',
            'en_couture' => 'primary',
            'pret' => 'success',
            'livree' => 'success',
            'terminee' => 'success',
            'annulee' => 'danger',
        ];

        return $couleurs[$this->statut] ?? 'secondary';
    }
} 