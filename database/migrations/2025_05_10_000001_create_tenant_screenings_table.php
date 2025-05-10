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
        Schema::create('tenant_screenings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landlord_id')->constrained('users');
            $table->foreignId('tenant_id')->constrained('users');
            $table->foreignId('unit_id')->constrained('units');
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');
            $table->boolean('credit_check_passed')->nullable();
            $table->boolean('background_check_passed')->nullable();
            $table->boolean('eviction_check_passed')->nullable();
            $table->boolean('employment_verified')->nullable();
            $table->boolean('income_verified')->nullable();
            $table->string('document_path')->nullable();
            $table->json('report_data')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_screenings');
    }
};