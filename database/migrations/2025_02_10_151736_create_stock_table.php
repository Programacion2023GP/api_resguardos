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
        Schema::create('stock', function (Blueprint $table) {
            $table->id();
            $table->string('picture')->nullable();
            $table->string('stock_number');
            $table->string('description')->nullable();

            $table->string('brand')->nullable();
            $table->string('serial')->nullable();
             

             $table->string('group');

            $table->string('observations')->nullable();
            $table->string('motive')->nullable();
            // $table->foreignId('user_id')->constrained('users','id');
            $table->boolean('active')->default(true);
            $table->foreignId('type_id')->nullable()->constrained('types', 'id');
            $table->foreignId('state_id')->nullable()->constrained('states', 'id');

            $table->string('number_korima')->nullable();
            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock');
    }
};
