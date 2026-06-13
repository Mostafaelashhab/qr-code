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
            $table->foreignId('client_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('client_user')->after('email')->index();
            $table->string('phone')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('phone')->index();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn(['client_id', 'role', 'phone', 'is_active']);
        });
    }
};
