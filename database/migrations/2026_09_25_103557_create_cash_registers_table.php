<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Quién abrió la caja
            $table->decimal('opening_amount', 10, 2); // Fondo inicial (cambio)
            $table->decimal('closing_amount', 10, 2)->nullable(); // Efectivo físico contado al cerrar
            $table->decimal('expected_amount', 10, 2)->nullable(); // Total esperado por el sistema
            $table->decimal('difference', 10, 2)->nullable(); // Sobrante o Faltante
            $table->enum('status', ['abierta', 'cerrada'])->default('abierta');
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_registers');
    }
};