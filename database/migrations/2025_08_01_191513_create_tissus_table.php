<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table des tissus avec toutes les informations nécessaires
     */
    public function up(): void
    {
        Schema::create('tissus', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // Nom du tissu
            $table->text('description'); // Description détaillée
            $table->decimal('prix', 10, 2); // Prix au mètre en FCFA
            $table->string('couleur'); // Couleur principale
            $table->string('image')->nullable(); // Chemin vers l'image
            $table->integer('stock')->default(0); // Quantité en stock (en mètres)
            $table->string('origine')->nullable(); // Pays/région d'origine
            $table->string('composition')->nullable(); // Composition du tissu (100% coton, etc.)
            $table->boolean('disponible')->default(true); // Disponibilité
            
            // Relations
            $table->foreignId('categorie_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Vendeur
            
            $table->timestamps();
            
            // Index pour optimiser les recherches
            $table->index(['disponible', 'categorie_id']);
            $table->index(['prix']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tissus');
    }
};
