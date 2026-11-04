<?php

use App\Models\CashRegister;
use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Liga cada ingreso (venta de POS y pago de membresía) a la caja/turno en que
 * se cobró. Antes el turno se infería por "ventana de tiempo" (created_at >=
 * opened_at), lo cual solo funciona con una caja a la vez. Con este enlace el
 * corte es exacto por caja y queda preparado para cajas simultáneas a futuro.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('cash_register_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('cash_register_id')->nullable()->after('member_id')->constrained()->nullOnDelete();
        });

        // Backfill: a los ingresos del/los turno(s) abierto(s) les asignamos su caja
        // (por la ventana de tiempo, como se venían contando) para que la vista en vivo
        // quede consistente. Los históricos de turnos cerrados quedan en null.
        CashRegister::where('status', 'abierta')->get()->each(function (CashRegister $caja) {
            Sale::whereNull('cash_register_id')
                ->where('created_at', '>=', $caja->opened_at)
                ->update(['cash_register_id' => $caja->id]);

            Payment::whereNull('cash_register_id')
                ->where('created_at', '>=', $caja->opened_at)
                ->update(['cash_register_id' => $caja->id]);
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cash_register_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cash_register_id');
        });
    }
};
