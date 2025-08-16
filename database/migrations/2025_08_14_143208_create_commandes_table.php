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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reference')->unique();
            $table->decimal('total', 10, 2);
            $table->enum('statut', ['en_attente', 'payee', 'expediee', 'livree', 'annulee'])->default('en_attente');
            $table->text('adresse_livraison');
            $table->string('mode_livraison')->default('standard');
            $table->text('commentaire')->nullable();
            $table->string('payment_method_id')->nullable();
            $table->string('payment_status')->default('en_attente');
            $table->string('tracking_number')->nullable();
            $table->string('transporteur')->nullable();
            $table->timestamps();
        });
        
        // Table intermédiaire pour les articles de commande
        Schema::create('commande_tissu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained()->onDelete('cascade');
            $table->foreignId('tissu_id')->constrained()->onDelete('cascade');
            $table->integer('quantite');
            $table->decimal('prix_unitaire', 10, 2);
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commande_tissu');
        Schema::dropIfExists('commandes');
    }
};
