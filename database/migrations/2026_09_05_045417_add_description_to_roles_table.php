<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // Agregamos la columna como nullable (opcional) justo después del nombre
            $table->string('description')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // Si revertimos la migración, borramos la columna
            $table->dropColumn('description');
        });
    }
};