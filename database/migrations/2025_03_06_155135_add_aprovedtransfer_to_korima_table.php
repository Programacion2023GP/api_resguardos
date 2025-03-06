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
        Schema::table('korima', function (Blueprint $table) {
            $table->boolean('aproved_transfer')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('korima', function (Blueprint $table) {
            $table->dropColumn('aproved_transfer'); // Agregar el campo archivist
        });
    }
};
