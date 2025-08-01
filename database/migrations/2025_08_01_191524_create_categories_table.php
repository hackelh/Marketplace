<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table des catégories de tissus
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique(); // Nom de la catégorie (ex: Coton, Soie, Laine)
            $table->text('description')->nullable(); // Description de la catégorie
            $table->string('couleur_hex', 7)->default('#3B82F6'); // Couleur pour l'affichage (hex)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
