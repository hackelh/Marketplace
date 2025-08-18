<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriqueTissu extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse
     */
    protected $fillable = [
        'tissu_id',
        'user_id',
        'type_mouvement',
        'quantite_avant',
        'quantite_apres',
        'quantite_mouvement',
        'motif',
        'reference',
        'notes',
    ];

    /**
     * Les attributs qui doivent être castés
     */
    protected $casts = [
        'quantite_avant' => 'integer',
        'quantite_apres' => 'integer',
        'quantite_mouvement' => 'integer',
    ];

    /**
     * Un historique appartient à un tissu
     */
    public function tissu(): BelongsTo
    {
        return $this->belongsTo(Tissu::class);
    }

    /**
     * Un historique appartient à un utilisateur
     */
    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Types de mouvements disponibles
     */
    public static function getTypesMouvement(): array
    {
        return [
            'ajout' => 'Ajout de stock',
            'vente' => 'Vente',
            'ajustement' => 'Ajustement de stock',
            'retour' => 'Retour client',
            'perte' => 'Perte/Dégât',
            'inventaire' => 'Inventaire',
            'creation' => 'Création du tissu',
            'modification' => 'Modification du tissu',
        ];
    }

    /**
     * Obtenir le libellé du type de mouvement
     */
    public function getTypeLibelleAttribute(): string
    {
        $types = self::getTypesMouvement();
        return $types[$this->type_mouvement] ?? $this->type_mouvement;
    }

    /**
     * Obtenir la couleur selon le type de mouvement
     */
    public function getTypeCouleurAttribute(): string
    {
        $couleurs = [
            'ajout' => 'success',
            'vente' => 'danger',
            'ajustement' => 'warning',
            'retour' => 'info',
            'perte' => 'danger',
            'inventaire' => 'primary',
            'creation' => 'success',
            'modification' => 'info',
        ];

        return $couleurs[$this->type_mouvement] ?? 'secondary';
    }

    /**
     * Obtenir l'icône selon le type de mouvement
     */
    public function getTypeIconeAttribute(): string
    {
        $icones = [
            'ajout' => 'bi-plus-circle',
            'vente' => 'bi-cart-minus',
            'ajustement' => 'bi-gear',
            'retour' => 'bi-arrow-return-left',
            'perte' => 'bi-exclamation-triangle',
            'inventaire' => 'bi-clipboard-check',
            'creation' => 'bi-plus-square',
            'modification' => 'bi-pencil-square',
        ];

        return $icones[$this->type_mouvement] ?? 'bi-circle';
    }

    /**
     * Scope pour filtrer par type de mouvement
     */
    public function scopeParType($query, $type)
    {
        return $query->where('type_mouvement', $type);
    }

    /**
     * Scope pour filtrer par tissu
     */
    public function scopeParTissu($query, $tissuId)
    {
        return $query->where('tissu_id', $tissuId);
    }

    /**
     * Scope pour filtrer par période
     */
    public function scopeParPeriode($query, $debut, $fin)
    {
        return $query->whereBetween('created_at', [$debut, $fin]);
    }
}