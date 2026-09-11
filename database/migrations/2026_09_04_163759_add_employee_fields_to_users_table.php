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
    Schema::table('users', function (Blueprint $table) {
        $table->string('last_name')->nullable()->after('name');
        $table->string('phone')->nullable()->after('email');
        $table->date('hire_date')->nullable()->after('phone'); 
        $table->string('profile_photo_path')->nullable()->after('hire_date'); 
        $table->boolean('is_active')->default(true)->after('profile_photo_path');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['last_name', 'phone', 'hire_date', 'profile_photo_path', 'is_active']);
    });
}
};
