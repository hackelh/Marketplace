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
        Schema::table('commandes', function (Blueprint $table) {
            // Ajouter les champs manquants
            $table->decimal('sous_total', 10, 2)->after('total');
            $table->decimal('frais_livraison', 10, 2)->default(0)->after('sous_total');
            $table->string('mode_paiement')->after('frais_livraison');
            $table->string('payment_method_id')->nullable()->change();
            $table->string('payment_status', 50)->default('en_attente')->change();
            $table->text('adresse_livraison')->nullable()->change();
            $table->text('adresse_facturation')->nullable()->after('adresse_livraison');
            $table->enum('statut', ['en_attente', 'en_cours', 'payee', 'expediee', 'livree', 'annulee'])->default('en_attente')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropColumn(['sous_total', 'frais_livraison', 'mode_paiement', 'adresse_facturation']);
        });
        
        // Remettre les colonnes à leur état d'origine si nécessaire
        Schema::table('commandes', function (Blueprint $table) {
            $table->string('payment_method_id')->change();
            $table->string('payment_status')->change();
            $table->text('adresse_livraison')->change();
            $table->enum('statut', ['en_attente', 'payee', 'expediee', 'livree', 'annulee'])->default('en_attente')->change();
        });
    }
};
