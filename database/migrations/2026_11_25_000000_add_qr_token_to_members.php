<?php

use App\Models\Member;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Token aleatorio por socio: es el secreto que guarda el QR de acceso.
            // El folio (consecutivo) deja de usarse como código de acceso.
            $table->string('qr_token', 64)->nullable()->unique()->after('folio');
        });

        // Backfill de los socios existentes con un token aleatorio.
        Member::whereNull('qr_token')->chunkById(200, function ($socios) {
            foreach ($socios as $socio) {
                $socio->forceFill(['qr_token' => Str::random(40)])->saveQuietly();
            }
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropUnique(['qr_token']);
            $table->dropColumn('qr_token');
        });
    }
};
