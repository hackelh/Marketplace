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
            $table->boolean('is_published')->default(false)->after('disponible');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tissus', function (Blueprint $table) {
            $table->dropColumn('is_published');
        });
    }
};
