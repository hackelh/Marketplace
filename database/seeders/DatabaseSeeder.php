<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\AdminSeeder;
use Database\Seeders\RolesUsersSeeder;
use Database\Seeders\CategoriesSeeder;
use Database\Seeders\TissusSeeder;
use Database\Seeders\CommandeSeeder;
use Database\Seeders\HistoriqueTissuSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ordre: utilisateurs (admin & rôles) -> catégories -> tissus -> commandes -> historique
        $this->call([
            AdminSeeder::class,
            RolesUsersSeeder::class,
            CategoriesSeeder::class,
            TissusSeeder::class,
            CommandeSeeder::class,
            HistoriqueTissuSeeder::class,
        ]);
    }
}
