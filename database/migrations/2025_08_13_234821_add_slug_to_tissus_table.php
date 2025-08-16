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
        Schema::table('tissus', function (Blueprint $table) {
            $table->string('slug')->after('titre')->unique()->nullable();
        });
        
        // Générer les slugs pour les enregistrements existants
        \App\Models\Tissu::all()->each(function ($tissu) {
            $tissu->save(); // Cela va déclencher la génération du slug
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tissus', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
