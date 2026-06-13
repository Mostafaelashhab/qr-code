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
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->string('token', 40)->unique();
            $table->string('title');
            $table->unsignedInteger('duration_minutes')->default(30);
            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_to')->nullable();
            $table->boolean('shuffle')->default(true);
            $table->boolean('show_results')->default(true);
            $table->boolean('is_published')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};
