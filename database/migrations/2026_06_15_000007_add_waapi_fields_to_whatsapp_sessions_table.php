<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_sessions', function (Blueprint $table): void {
            // Provisioned by the super admin from the waapi dashboard. A session
            // is "provisioned" once device_uuid is set; the center can then link
            // its number by scanning the QR.
            $table->string('device_uuid')->nullable()->after('client_id');
            $table->string('app_key')->nullable()->after('device_uuid');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_sessions', function (Blueprint $table): void {
            $table->dropColumn(['device_uuid', 'app_key']);
        });
    }
};
