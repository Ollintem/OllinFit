<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cambia la duración del plan de "duration_days" a "duration_value" + "duration_unit".
     */
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->unsignedInteger('duration_value')->default(1)->after('price');
            $table->string('duration_unit')->default('days')->after('duration_value'); // days | months | years
        });

        // Backfill: los planes existentes pasan a expresarse en días (comportamiento idéntico).
        if (Schema::hasColumn('plans', 'duration_days')) {
            DB::table('plans')->update([
                'duration_value' => DB::raw('COALESCE(duration_days, 1)'),
                'duration_unit' => 'days',
            ]);

            Schema::table('plans', function (Blueprint $table) {
                $table->dropColumn('duration_days');
            });
        }
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->integer('duration_days')->default(30)->after('price');
        });

        // Reconstruir duration_days aproximado desde value + unit.
        DB::table('plans')->get()->each(function ($plan) {
            $factor = match ($plan->duration_unit) {
                'months' => 30,
                'years' => 365,
                default => 1,
            };
            DB::table('plans')->where('id', $plan->id)->update([
                'duration_days' => $plan->duration_value * $factor,
            ]);
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['duration_value', 'duration_unit']);
        });
    }
};
