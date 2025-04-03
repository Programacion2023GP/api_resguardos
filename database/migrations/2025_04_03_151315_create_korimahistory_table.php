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
        Schema::create('korimahistory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('korima_id')->constrained('korima', 'id');
            $table->foreignId('userdown_id')->nullable()->constrained('users', 'id');
            $table->foreignId('userup_id')->constrained('users', 'id');
            $table->enum('movement', ['transfer', 'discharge']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('korimahistory');
    }
};
