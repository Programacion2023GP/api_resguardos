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
        Schema::create('guards', function (Blueprint $table) {
            $table->id();
            $table->string('picture')->nullable();
            $table->string('stock_number');
            $table->string('type')->nullable();
            $table->string('description')->nullable();

            $table->string('brand')->nullable();
            $table->string('state')->nullable();
            $table->string('serial')->nullable();
             $table->string('airlane')->nullable();
            // $table->integer('payroll');
            $table->string('group');
            // $table->string('employeed');
            $table->string('observations')->nullable();
            // $table->foreignId('user_id')->constrained('users','id');
            $table->boolean('active')->default(true);

            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guards');
    }
};
