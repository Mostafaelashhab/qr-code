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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('currency', 8)->default('EGP')->after('logo_path');
            $table->string('timezone', 64)->default('Africa/Cairo')->after('currency');
            $table->decimal('default_monthly_fee', 10, 2)->nullable()->after('timezone');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['currency', 'timezone', 'default_monthly_fee']);
        });
    }
};
