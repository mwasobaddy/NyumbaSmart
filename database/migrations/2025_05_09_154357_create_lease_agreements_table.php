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
        Schema::create('lease_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('landlord_id')->constrained('users')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('rent_amount', 12, 2);
            $table->decimal('security_deposit', 12, 2);
            $table->text('terms_and_conditions')->nullable();
            $table->text('special_provisions')->nullable();
            $table->string('document_path')->nullable(); // Path to the stored PDF document
            $table->string('status')->default('draft'); // draft, sent, signed, active, expired, terminated
            $table->timestamp('signed_by_tenant_at')->nullable();
            $table->timestamp('signed_by_landlord_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lease_agreements');
    }
};
