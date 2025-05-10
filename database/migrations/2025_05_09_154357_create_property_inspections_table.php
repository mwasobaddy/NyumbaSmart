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
        Schema::create('property_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('landlord_id')->constrained('users')->onDelete('cascade');
            $table->string('type'); // move_in, move_out, routine, maintenance
            $table->date('inspection_date');
            $table->string('status')->default('scheduled'); // scheduled, in_progress, completed
            $table->json('checklist_items'); // Store inspection checklist data
            $table->text('overall_condition')->nullable();
            $table->text('notes')->nullable();
            $table->json('image_paths')->nullable(); // Store paths to uploaded inspection images
            $table->boolean('tenant_signed')->default(false);
            $table->boolean('landlord_signed')->default(false);
            $table->timestamp('tenant_signed_at')->nullable();
            $table->timestamp('landlord_signed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_inspections');
    }
};
