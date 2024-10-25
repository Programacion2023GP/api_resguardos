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
            $table->string('tag_picture')->nullable(); // Añadir la columna
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('korima', function (Blueprint $table) {
            $table->dropColumn('tag_picture'); // Eliminar la columna en caso de rollback
        });
    }
};
