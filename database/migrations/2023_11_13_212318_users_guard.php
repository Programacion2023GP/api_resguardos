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
            $table->string('stock_number');
            $table->string('type')->nullable();
            $table->string('description');
            $table->string('brand');
            $table->string('state');
            $table->string('serial');
            $table->string('airlne');
            $table->integer('payroll');
            $table->string('group');
            $table->date('date');
            $table->string('employeed');
            $table->string('observations')->nullable();
            $table->foreignId('user_id')->constrained('users','id');
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
        Schema::dropIfExists('users_guards');
    }
};
