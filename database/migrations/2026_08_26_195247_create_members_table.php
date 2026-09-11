<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            
            // Datos personales y de identificación
            $table->string('folio')->unique(); // Ej. MX-9021
            $table->string('name');
            $table->string('last_name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('profile_photo_path')->nullable();
            
            // Relación con el Plan Activo (Membresía)
            // Si eliminamos un plan, el socio no se borra, solo su plan_id queda en null
            $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();
            
            // Control de acceso
            $table->date('expiration_date')->nullable(); // Fecha de vencimiento
            $table->boolean('is_active')->default(true); // Para suspender socios manualmente si es necesario

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};