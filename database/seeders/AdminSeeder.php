<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Crée le compte administrateur avec des identifiants fixes
     */
    public function run(): void
    {
        // Vérifier si l'admin existe déjà
        $adminExists = User::where('email', 'admin@marketplace.sn')->exists();
        
        if (!$adminExists) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'admin@marketplace.sn',
                'password' => Hash::make('marketplace123'),
                'role' => 'admin',
                'email_verified_at' => now(), // Admin pré-vérifié
            ]);
            
            $this->command->info('Compte admin créé avec succès !');
            $this->command->info('Email: admin@marketplace.sn');
            $this->command->info('Mot de passe: marketplace123');
        } else {
            $this->command->info('Le compte admin existe déjà.');
        }
    }
}
