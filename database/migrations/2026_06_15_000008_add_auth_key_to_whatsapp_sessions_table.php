<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_sessions', function (Blueprint $table): void {
            // Each center is its own waapi account with its own authkey.
            $table->string('auth_key')->nullable()->after('client_id');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_sessions', function (Blueprint $table): void {
            $table->dropColumn('auth_key');
        });
    }
};
