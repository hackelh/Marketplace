<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Tissu;

class Categorie extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse
     */
    protected $fillable = [
        'nom',
        'description',
        'couleur_hex',
    ];

    /**
     * Une catégorie a plusieurs tissus
     */
    public function tissus(): HasMany
    {
        return $this->hasMany(Tissu::class);
    }

    /**
     * Obtenir le nombre de tissus disponibles dans cette catégorie
     */
    public function getTissusDisponiblesCountAttribute(): int
    {
        return $this->tissus()->where('disponible', true)->count();
    }
}
