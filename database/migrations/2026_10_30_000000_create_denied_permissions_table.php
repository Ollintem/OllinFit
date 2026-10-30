<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Permisos DENEGADOS por empleado.
 *
 * Spatie no soporta restar permisos que otorga un rol. Esta tabla guarda,
 * por usuario, los permisos que deben quedar bloqueados aunque su rol los
 * conceda. El Gate::before de AppServiceProvider la consulta para denegar.
 *
 * Permiso efectivo = (permisos del rol ∪ permisos directos) − denegados.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('denied_permissions', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->primary(['user_id', 'permission_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('denied_permissions');
    }
};
