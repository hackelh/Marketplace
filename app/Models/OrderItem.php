<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'tissu_id',
        'quantity',
        'unit_price',
        'total_price',
        'tissu_name',
        'tissu_image',
        'options',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'options' => 'array',
    ];

    /**
     * Obtenir la commande associée à cet article.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Obtenir le tissu associé à cet article de commande.
     */
    public function tissu(): BelongsTo
    {
        return $this->belongsTo(Tissu::class);
    }

    /**
     * Obtenir le vendeur du tissu.
     */
    public function vendeur()
    {
        return $this->tissu->vendeur();
    }

    /**
     * Le "booting" du modèle.
     */
    protected static function boot()
    {
        parent::boot();

        // Calculer le prix total avant de sauvegarder
        static::saving(function ($orderItem) {
            if (empty($orderItem->total_price) && $orderItem->unit_price && $orderItem->quantity) {
                $orderItem->total_price = $orderItem->unit_price * $orderItem->quantity;
            }
            
            // S'assurer que le nom du tissu est à jour
            if ($orderItem->tissu && empty($orderItem->tissu_name)) {
                $orderItem->tissu_name = $orderItem->tissu->titre;
            }
        });
    }
}
