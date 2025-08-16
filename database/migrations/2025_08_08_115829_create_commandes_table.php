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
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_commande')->unique();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vendeur_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tailleur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('statut', [
                'en_attente',
                'en_preparation', 
                'en_couture',
                'pret',
                'livree',
                'terminee',
                'annulee'
            ])->default('en_attente');
            $table->decimal('montant_total', 10, 2);
            $table->datetime('date_commande');
            $table->datetime('date_livraison_prevue')->nullable();
            $table->datetime('date_livraison_effective')->nullable();
            $table->text('adresse_livraison')->nullable();
            $table->text('notes')->nullable();
            $table->string('methode_paiement')->nullable();
            $table->enum('statut_paiement', ['en_attente', 'paye', 'partiel'])->default('en_attente');
            $table->timestamps();
            
            // Index pour optimiser les recherches
            $table->index(['vendeur_id', 'statut']);
            $table->index(['client_id', 'statut']);
            $table->index('date_commande');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
