<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_id')->constrained('expenses')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products'); // Qué producto compramos
            $table->integer('quantity'); // Cuántas piezas entraron
            $table->decimal('unit_cost', 10, 2); // A cómo nos la vendió el proveedor
            $table->decimal('subtotal', 10, 2); // cantidad * unit_cost
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_items');
    }
};