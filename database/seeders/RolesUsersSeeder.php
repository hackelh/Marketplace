<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Vendeur
        User::query()->firstOrCreate(
            ['email' => 'vendeur@marketplace.sn'],
            [
                'name' => 'Vendeur Démo',
                'password' => Hash::make('password'),
                'role' => 'vendeur',
                'email_verified_at' => now(),
            ]
        );

        // Client
        User::query()->firstOrCreate(
            ['email' => 'client@marketplace.sn'],
            [
                'name' => 'Client Démo',
                'password' => Hash::make('password'),
                'role' => 'client',
                'email_verified_at' => now(),
            ]
        );

        // Tailleur
        User::query()->firstOrCreate(
            ['email' => 'tailleur@marketplace.sn'],
            [
                'name' => 'Tailleur Démo',
                'password' => Hash::make('password'),
                'role' => 'tailleur',
                'email_verified_at' => now(),
            ]
        );
    }
}
