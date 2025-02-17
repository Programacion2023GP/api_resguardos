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
            $table->integer('archivist')->nullable()->default(null); // Agregar el campo archivist
            $table->text('motivearchivist')->nullable(); // Agregar el campo archivist

        });
    }

    /**motivoarchivist
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('korima', function (Blueprint $table) {
            $table->dropColumn('archivist'); // Eliminar el campo archivist
            $table->dropColumn('motivearchivist'); // Agregar el campo archivist

        });
    }
};
