<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model
{
    use SoftDeletes;

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'slug',
        'description',
        'image',
        'est_actif',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'est_actif' => 'boolean',
    ];

    /**
     * Obtenir les tissus associés à cette catégorie.
     */
    public function tissus(): HasMany
    {
        return $this->hasMany(Tissu::class);
    }

    /**
     * Obtenir le nom de la catégorie avec mise en forme.
     *
     * @return string
     */
    public function getNomFormateAttribute(): string
    {
        return ucfirst($this->nom);
    }
    
    /**
     * Crée une nouvelle instance de la factory pour le modèle.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory<static>
     */
    protected static function newFactory()
    {
        return \Database\Factories\CategorieFactory::new();
    }
}
