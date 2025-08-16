<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

/**
 * Trait pour initialiser l'application de test.
 */
trait CreatesApplication
{
    /**
     * Crée l'application de test.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // Configuration pour les tests
        config(['app.debug' => true]);
        
        // Utilisation de SQLite en mémoire pour les tests
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => ':memory:']);

        return $app;
    }
}
