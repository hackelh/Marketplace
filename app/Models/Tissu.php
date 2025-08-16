<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHistorique;

class Tissu extends Model
{
    use HasFactory, HasHistorique;

    /**
     * Les attributs qui peuvent être assignés en masse
     */
    protected $fillable = [
        'nom',
        'description',
        'prix',
        'couleur',
        'image',
        'stock',
        'origine',
        'composition',
        'disponible',
        'categorie_id',
        'user_id',
    ];

    /**
     * Les attributs qui doivent être castés
     */
    protected $casts = [
        'prix' => 'decimal:2',
        'disponible' => 'boolean',
        'stock' => 'integer',
    ];

    /**
     * Un tissu appartient à une catégorie
     */
    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }

    /**
     * Un tissu appartient à un vendeur (utilisateur)
     */
    public function vendeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Un tissu a plusieurs entrées d'historique
     */
    public function historique(): HasMany
    {
        return $this->hasMany(HistoriqueTissu::class);
    }

    /**
     * Scope pour filtrer les tissus disponibles
     */
    public function scopeDisponible(Builder $query): Builder
    {
        return $query->where('disponible', true)->where('stock', '>', 0);
    }

    /**
     * Scope pour filtrer par catégorie
     */
    public function scopeParCategorie(Builder $query, $categorieId): Builder
    {
        return $query->where('categorie_id', $categorieId);
    }

    /**
     * Scope pour filtrer par couleur
     */
    public function scopeParCouleur(Builder $query, $couleur): Builder
    {
        return $query->where('couleur', 'like', '%' . $couleur . '%');
    }

    /**
     * Scope pour filtrer par prix
     */
    public function scopeParPrix(Builder $query, $prixMin = null, $prixMax = null): Builder
    {
        if ($prixMin) {
            $query->where('prix', '>=', $prixMin);
        }
        if ($prixMax) {
            $query->where('prix', '<=', $prixMax);
        }
        return $query;
    }

    /**
     * Obtenir l'URL de l'image ou une image par défaut
     */
    public function getImageUrlAttribute(): string
    {
        return $this->image 
            ? asset('storage/' . $this->image)
            : asset('images/tissu-default.jpg');
    }

    /**
     * Vérifier si le tissu est en stock
     */
    public function getEnStockAttribute(): bool
    {
        return $this->stock > 0 && $this->disponible;
    }
}
