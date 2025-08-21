<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tissus', function (Blueprint $table) {
            if (Schema::hasColumn('tissus', 'couleur')) {
                $table->dropColumn('couleur');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tissus', function (Blueprint $table) {
            // Recreate the column if needed; choose nullable to avoid future breakage
            if (!Schema::hasColumn('tissus', 'couleur')) {
                $table->string('couleur')->nullable();
            }
        });
    }
};