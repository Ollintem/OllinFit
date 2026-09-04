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
    Schema::create('members', function (Blueprint $table) {
        $table->id();
        $table->string('folio')->unique(); // Identificador único automático
        $table->string('name');
        $table->string('last_name');
        $table->string('phone')->nullable();
        $table->string('photo_path')->nullable(); // Para guardar la foto
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
