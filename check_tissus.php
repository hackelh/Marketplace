<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illware\Contracts\Console\Kernel::class);

$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArrayInput(['command' => 'tinker']),
    new Symfony\Component\Console\Output\ConsoleOutput()
);

// Vérifier la connexion à la base de données
try {
    $count = \DB::table('tissus')->count();
    echo "Nombre de tissus dans la base de données : " . $count . "\n";
    
    // Afficher les premiers tissus
    $tissus = \DB::table('tissus')->take(2)->get();
    echo "Exemple de tissus : " . json_encode($tissus, JSON_PRETTY_PRINT) . "\n";
    
} catch (\Exception $e) {
    echo "Erreur lors de l'accès à la base de données : " . $e->getMessage() . "\n";
    echo "Fichier : " . $e->getFile() . "\n";
    echo "Ligne : " . $e->getLine() . "\n";
    echo "Trace : " . $e->getTraceAsString() . "\n";
}

$kernel->terminate($input, $status);

exit($status);
