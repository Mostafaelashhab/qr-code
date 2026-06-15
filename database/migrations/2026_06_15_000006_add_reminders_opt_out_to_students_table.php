<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table): void {
            // When true, the guardian has opted out and receives no reminders.
            $table->boolean('reminders_opt_out')->default(false)->after('guardian_phone');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table): void {
            $table->dropColumn('reminders_opt_out');
        });
    }
};
