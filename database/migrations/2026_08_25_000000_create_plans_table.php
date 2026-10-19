<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ej. Mensual, Semestral
            $table->decimal('price', 8, 2); // Ej. 500.00
            $table->text('description')->nullable(); 
            $table->integer('duration_days'); // Ej. 30, 180
            $table->string('highlight_text')->nullable(); // Ej. "Ahorro del 16% vs mensual"
            $table->string('color')->default('blue'); // Para la franja superior (blue, orange, green, etc)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};