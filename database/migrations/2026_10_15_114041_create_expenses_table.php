<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            // Para saber si es de la pestaña 1 o de la pestaña 2
            $table->enum('type', ['compra_proveedor', 'gasto_general']); 
            
            $table->string('concept'); // Ej: Pago de Luz, Compra de Aguas
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->default('Efectivo');
            
            // Relaciones
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete(); // A quién le compramos (si aplica)
            $table->foreignId('cash_register_id')->nullable()->constrained('cash_registers')->nullOnDelete(); // De qué turno salió el dinero
            $table->foreignId('user_id')->constrained('users'); // Quién registró el gasto
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};