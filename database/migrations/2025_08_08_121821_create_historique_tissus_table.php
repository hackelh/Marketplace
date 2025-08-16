<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('historique_tissus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tissu_id')->constrained('tissus')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type_mouvement', [
                'ajout',
                'vente', 
                'ajustement',
                'retour',
                'perte',
                'inventaire',
                'creation',
                'modification'
            ]);
            $table->integer('quantite_avant');
            $table->integer('quantite_apres');
            $table->integer('quantite_mouvement'); // positif pour ajout, négatif pour diminution
            $table->string('motif')->nullable();
            $table->string('reference')->nullable(); // référence commande, facture, etc.
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Index pour optimiser les recherches
            $table->index(['tissu_id', 'created_at']);
            $table->index(['type_mouvement', 'created_at']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_tissus');
    }
};
