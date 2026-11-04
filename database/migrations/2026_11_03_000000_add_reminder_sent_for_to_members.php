<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Guarda la fecha de vencimiento para la que ya se envió el recordatorio al
 * socio. Así el comando diario no reenvía el mismo aviso, y al renovar (cambia
 * expiration_date) el recordatorio se vuelve a habilitar automáticamente.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->date('reminder_sent_for')->nullable()->after('expiration_date');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('reminder_sent_for');
        });
    }
};
