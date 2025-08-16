<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Tissu extends Model
{
    use HasSlug;

    protected $fillable = [
        'titre', 'slug', 'description', 'prix', 'quantite', 'seuil_alerte',
        'categorie_id', 'couleur', 'matiere', 'image', 'largeur', 'poids',
        'disponible', 'user_id', 'is_published', 'mots_cles', 'reference',
        'promotion', 'prix_promotion', 'date_debut_promotion', 'date_fin_promotion',
        'vues', 'note_moyenne', 'nombre_avis'
    ];

    protected $casts = [
        'prix' => 'decimal:2',
        'prix_promotion' => 'decimal:2',
        'quantite' => 'integer',
        'seuil_alerte' => 'integer',
        'largeur' => 'decimal:1',
        'poids' => 'decimal:2',
        'disponible' => 'boolean',
        'is_published' => 'boolean',
        'en_promotion' => 'boolean',
        'date_debut_promotion' => 'datetime',
        'date_fin_promotion' => 'datetime',
        'vues' => 'integer',
        'note_moyenne' => 'decimal:1',
        'nombre_avis' => 'integer',
    ];

    protected $appends = ['prix_actuel', 'en_promotion', 'image_url'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tissu) {
            if (empty($tissu->reference)) {
                $tissu->reference = static::generateReference();
            }
            
            if (empty($tissu->slug)) {
                $tissu->slug = Str::slug($tissu->titre);
            }
        });

        static::updating(function ($tissu) {
            if ($tissu->isDirty('titre') && empty($tissu->slug)) {
                $tissu->slug = Str::slug($tissu->titre);
            }
            
            // Désactiver la promotion si la date est dépassée
            if ($tissu->en_promotion && $tissu->date_fin_promotion && $tissu->date_fin_promotion->isPast()) {
                $tissu->en_promotion = false;
                $tissu->prix_promotion = null;
            }
        });
    }

    public static function generateReference(): string
    {
        $reference = 'TIS-' . strtoupper(Str::random(6));
        
        while (static::where('reference', $reference)->exists()) {
            $reference = 'TIS-' . strtoupper(Str::random(6));
        }
        
        return $reference;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getPrixActuelAttribute()
    {
        return $this->en_promotion ? $this->prix_promotion : $this->prix;
    }

    public function getEnPromotionAttribute()
    {
        if (!$this->prix_promotion) return false;
        
        $now = now();
        $debut = $this->date_debut_promotion ?? $now->copy()->subDay();
        $fin = $this->date_fin_promotion ?? $now->copy()->addDay();
        
        return $now->between($debut, $fin);
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return Storage::url($this->image);
        }
        
        // Retourner une image par défaut si aucune image n'est définie
        return asset('images/default-tissu.jpg');
    }

    public function getStockStatusAttribute()
    {
        if ($this->quantite <= 0) {
            return 'rupture';
        }
        
        if ($this->quantite <= $this->seuil_alerte) {
            return 'alerte';
        }
        
        return 'disponible';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vendeur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')->orderBy('ordre');
    }

    public function commandes(): BelongsToMany
    {
        return $this->belongsToMany(Commande::class, 'commande_items')
            ->withPivot(['quantite', 'prix_unitaire', 'total'])
            ->withTimestamps();
    }
    
    /**
     * Obtenir les articles de commande associés à ce tissu.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'tissu_id');
    }

    public function favoris(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favoris')
            ->withTimestamps();
    }

    public function avis(): HasMany
    {
        return $this->hasMany(Avis::class);
    }

    public function estEnStock(int $quantite = 1): bool
    {
        return $this->disponible && $this->quantite >= $quantite;
    }

    public function decrementerStock(int $quantite = 1): bool
    {
        if (!$this->estEnStock($quantite)) {
            return false;
        }

        $this->decrement('quantite', $quantite);
        
        if ($this->quantite <= 0) {
            $this->disponible = false;
            $this->save();
        }
        
        return true;
    }

    public function incrementerStock(int $quantite = 1): bool
    {
        $wasAvailable = $this->disponible;
        $this->increment('quantite', $quantite);
        
        if (!$wasAvailable) {
            $this->disponible = true;
            $this->save();
        }
        
        return true;
    }

    public function mettreEnPromotion(float $prixPromotion, $dateFin = null, $dateDebut = null): bool
    {
        return $this->update([
            'prix_promotion' => $prixPromotion,
            'date_debut_promotion' => $dateDebut ?? now(),
            'date_fin_promotion' => $dateFin,
            'en_promotion' => true
        ]);
    }

    public function retirerPromotion(): bool
    {
        return $this->update([
            'prix_promotion' => null,
            'date_debut_promotion' => null,
            'date_fin_promotion' => null,
            'en_promotion' => false
        ]);
    }

    public function estFavori(User $user): bool
    {
        return $this->favoris()->where('user_id', $user->id)->exists();
    }

    public function incrementerVues(): void
    {
        $this->increment('vues');
    }

    public function scopeDisponibles($query)
    {
        return $query->where('disponible', true)
                    ->where('quantite', '>', 0)
                    ->where('is_published', true);
    }

    public function scopeEnPromotion($query)
    {
        $now = now();
        return $query->where('en_promotion', true)
                    ->where('prix_promotion', '>', 0)
                    ->where('date_debut_promotion', '<=', $now)
                    ->where(function($q) use ($now) {
                        $q->whereNull('date_fin_promotion')
                          ->orWhere('date_fin_promotion', '>=', $now);
                    });
    }

    public function scopeRechercher($query, $terme)
    {
        return $query->where('titre', 'LIKE', "%{$terme}%")
                    ->orWhere('description', 'LIKE', "%{$terme}%")
                    ->orWhere('reference', 'LIKE', "%{$terme}%");
    }
}
