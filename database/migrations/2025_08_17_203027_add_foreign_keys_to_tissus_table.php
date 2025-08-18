<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tissus', function (Blueprint $table) {
            // S’assurer que les colonnes existent
            if (!Schema::hasColumn('tissus', 'categorie_id') || !Schema::hasColumn('tissus', 'user_id')) {
                return;
            }

            // Ajouter les FKs (sans tenter de supprimer au préalable)
            $table->foreign('categorie_id')
                ->references('id')->on('categories')
                ->cascadeOnDelete();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tissus', function (Blueprint $table) {
            try { $table->dropForeign(['categorie_id']); } catch (\Throwable $e) {}
            try { $table->dropForeign(['user_id']); } catch (\Throwable $e) {}
        });
    }
};