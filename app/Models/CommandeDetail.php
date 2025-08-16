<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommandeDetail extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse
     */
    protected $fillable = [
        'commande_id',
        'tissu_id',
        'quantite',
        'prix_unitaire',
        'prix_total',
        'notes',
    ];

    /**
     * Les attributs qui doivent être castés
     */
    protected $casts = [
        'prix_unitaire' => 'decimal:2',
        'prix_total' => 'decimal:2',
        'quantite' => 'integer',
    ];

    /**
     * Un détail appartient à une commande
     */
    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class);
    }

    /**
     * Un détail concerne un tissu
     */
    public function tissu(): BelongsTo
    {
        return $this->belongsTo(Tissu::class);
    }
} 