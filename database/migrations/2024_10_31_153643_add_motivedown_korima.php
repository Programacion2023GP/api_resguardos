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
            if (!Schema::hasColumn('korima', 'motive_down')) {
                $table->string('motive_down')->nullable();
            }
            if (!Schema::hasColumn('korima', 'autorized')) {
                $table->boolean('autorized')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('korima', function (Blueprint $table) {
            if (!Schema::hasColumn('korima', 'motive_down')) {
                $table->string('motive_down')->nullable();
            }
            if (!Schema::hasColumn('korima', 'autorized')) {
                $table->boolean('autorized')->nullable();
            }
        });
        
    }
};
