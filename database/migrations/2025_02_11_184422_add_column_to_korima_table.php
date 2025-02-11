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
            $table->text('motivetransfer')->nullable();  // Cambia el tipo de campo según lo necesites

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('korima', function (Blueprint $table) {
            $table->string('motivetransfer');  // Cambia el tipo de campo según lo necesites

        });
    }
};
