<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use App\Models\OrderItem;

class Commande extends Model
{
    const STATUS_PENDING = 'en_attente';
    const STATUS_PROCESSING = 'en_traitement';
    const STATUS_SHIPPED = 'expediee';
    const STATUS_DELIVERED = 'livree';
    const STATUS_CANCELLED = 'annulee';

    const PAYMENT_STATUS_PENDING = 'en_attente';
    const PAYMENT_STATUS_PAID = 'payee';
    const PAYMENT_STATUS_FAILED = 'echouee';
    const PAYMENT_STATUS_REFUNDED = 'remboursee';

    protected $fillable = [
        'user_id', 'reference', 'total', 'statut', 'details', 'payment_status',
        'adresse_livraison', 'mode_livraison', 'frais_livraison', 'commentaire',
        'transporteur', 'date_expedition', 'date_livraison', 'tracking_number'
    ];

    protected $casts = [
        'details' => 'array',
        'adresse_livraison' => 'array',
        'total' => 'decimal:2',
        'frais_livraison' => 'decimal:2',
        'date_expedition' => 'datetime',
        'date_livraison' => 'datetime',
    ];

    protected $attributes = [
        'statut' => self::STATUS_PENDING,
        'payment_status' => self::PAYMENT_STATUS_PENDING,
        'frais_livraison' => 0,
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($commande) {
            if (empty($commande->reference)) {
                $commande->reference = static::generateReference();
            }
        });
    }

    public static function generateReference(): string
    {
        $reference = 'CMD-' . strtoupper(Str::random(8));
        
        // Vérifier l'unicité
        while (static::where('reference', $reference)->exists()) {
            $reference = 'CMD-' . strtoupper(Str::random(8));
        }
        
        return $reference;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function produits(): BelongsToMany
    {
        return $this->belongsToMany(Tissu::class, 'commande_items')
            ->withPivot(['quantite', 'prix_unitaire', 'total'])
            ->withTimestamps();
    }

    public function getSousTotalAttribute(): float
    {
        return $this->total - $this->frais_livraison;
    }

    public function getNombreArticlesAttribute(): int
    {
        return $this->items->sum('quantite');
    }

    public function marquerCommePayee(): bool
    {
        return $this->update([
            'payment_status' => self::PAYMENT_STATUS_PAID,
            'etat' => self::STATUS_PROCESSING
        ]);
    }

    public function marquerCommeExpediee(array $data = []): bool
    {
        return $this->update([
            'etat' => self::STATUS_SHIPPED,
            'transporteur' => $data['transporteur'] ?? null,
            'tracking_number' => $data['tracking_number'] ?? null,
            'date_expedition' => now(),
        ]);
    }

    public function marquerCommeLivree(): bool
    {
        return $this->update([
            'etat' => self::STATUS_DELIVERED,
            'date_livraison' => now(),
        ]);
    }

    public function annuler(string $raison = null): bool
    {
        return $this->update([
            'etat' => self::STATUS_CANCELLED,
            'commentaire' => $raison ?: 'Commande annulée par le client',
        ]);
    }

    public function getStatusCssClass(): string
    {
        return match($this->statut) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_PROCESSING => 'bg-blue-100 text-blue-800',
            self::STATUS_SHIPPED => 'bg-indigo-100 text-indigo-800',
            self::STATUS_DELIVERED => 'bg-green-100 text-green-800',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getPaymentStatusCssClass(): string
    {
        return match($this->payment_status) {
            self::PAYMENT_STATUS_PAID => 'bg-green-100 text-green-800',
            self::PAYMENT_STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::PAYMENT_STATUS_FAILED => 'bg-red-100 text-red-800',
            self::PAYMENT_STATUS_REFUNDED => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', self::STATUS_PENDING);
    }

    public function scopeEnTraitement($query)
    {
        return $query->where('statut', self::STATUS_PROCESSING);
    }

    public function scopeExpediees($query)
    {
        return $query->where('statut', self::STATUS_SHIPPED);
    }

    public function scopeLivrees($query)
    {
        return $query->where('statut', self::STATUS_DELIVERED);
    }

    public function scopeAnnulees($query)
    {
        return $query->where('statut', self::STATUS_CANCELLED);
    }

    public function estPayee(): bool
    {
        return $this->payment_status === self::PAYMENT_STATUS_PAID;
    }

    public function estAnnulee(): bool
    {
        return $this->statut === self::STATUS_CANCELLED;
    }

    public function estEnCours(): bool
    {
        return in_array($this->statut, [self::STATUS_PENDING, self::STATUS_PROCESSING, self::STATUS_SHIPPED]);
    }

    public function peutEtreAnnulee(): bool
    {
        return in_array($this->statut, [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }
}
