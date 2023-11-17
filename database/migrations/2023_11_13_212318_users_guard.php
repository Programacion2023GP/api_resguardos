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
        Schema::create('users_guards', function (Blueprint $table) {
            $table->id();
            $table->string('picture');
            $table->string('facture')->nullable();
            $table->string('emisor')->nullable();
            $table->string('description');
            $table->string('type');
            $table->integer('value');
            $table->string('name');
            $table->string('group');
            $table->integer('numberconsecutive');
            $table->integer('label');
            $table->integer('payroll');
            $table->boolean('active')->default(true);
            $table->foreignId('user_id')->constrained('users','id');

            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_guards');
    }
};
