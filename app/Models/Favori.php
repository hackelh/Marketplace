<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favori extends Model
{
    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'tissu_id',
    ];

    /**
     * Obtient l'utilisateur qui a ajouté le favori.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtient le tissu mis en favori.
     */
    public function tissu(): BelongsTo
    {
        return $this->belongsTo(Tissu::class);
    }

    /**
     * Vérifie si un tissu est dans les favoris d'un utilisateur.
     *
     * @param int $userId
     * @param int $tissuId
     * @return bool
     */
    public static function estFavori(int $userId, int $tissuId): bool
    {
        return self::where('user_id', $userId)
            ->where('tissu_id', $tissuId)
            ->exists();
    }

    /**
     * Ajoute un tissu aux favoris d'un utilisateur.
     *
     * @param int $userId
     * @param int $tissuId
     * @return Favori
     */
    public static function ajouterFavori(int $userId, int $tissuId): Favori
    {
        return self::firstOrCreate([
            'user_id' => $userId,
            'tissu_id' => $tissuId,
        ]);
    }

    /**
     * Supprime un tissu des favoris d'un utilisateur.
     *
     * @param int $userId
     * @param int $tissuId
     * @return bool
     */
    public static function supprimerFavori(int $userId, int $tissuId): bool
    {
        return (bool) self::where('user_id', $userId)
            ->where('tissu_id', $tissuId)
            ->delete();
    }
}
