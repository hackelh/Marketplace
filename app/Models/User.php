<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Les tissus favoris de l'utilisateur.
     */
    public function favoris(): BelongsToMany
    {
        return $this->belongsToMany(Tissu::class, 'favoris', 'user_id', 'tissu_id')
            ->withTimestamps();
    }

    /**
     * Obtenir les commandes de l'utilisateur.
     */
    public function commandes(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    // Constantes pour les rôles
    public const ROLE_ADMIN = 'admin';
    public const ROLE_VENDEUR = 'vendeur';
    public const ROLE_TAILLEUR = 'tailleur';
    public const ROLE_CLIENT = 'client';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Vérifier le rôle de l'utilisateur
    public function hasRole($role): bool
    {
        return $this->role === $role;
    }

    // Vérifier si l'utilisateur est administrateur
    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    // Vérifier si l'utilisateur est un vendeur
    public function isVendeur(): bool
    {
        return $this->hasRole(self::ROLE_VENDEUR);
    }

    // Vérifier si l'utilisateur est un tailleur
    public function isTailleur(): bool
    {
        return $this->hasRole(self::ROLE_TAILLEUR);
    }

    // Vérifier si l'utilisateur est un client
    public function isClient(): bool
    {
        return $this->hasRole(self::ROLE_CLIENT) || $this->role === null;
    }
}
