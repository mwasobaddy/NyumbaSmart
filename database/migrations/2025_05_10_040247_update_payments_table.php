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
        Schema::table('payments', function (Blueprint $table) {
            // Check if these columns don't exist before adding them
            if (!Schema::hasColumn('payments', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            }
            
            if (!Schema::hasColumn('payments', 'invoice_id')) {
                $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            }
            
            if (!Schema::hasColumn('payments', 'transaction_id')) {
                $table->string('transaction_id')->unique()->nullable();
            }
            
            if (!Schema::hasColumn('payments', 'phone')) {
                $table->string('phone')->nullable();
            }
            
            if (!Schema::hasColumn('payments', 'payment_method')) {
                $table->string('payment_method')->default('mpesa');
            }
            
            if (!Schema::hasColumn('payments', 'status')) {
                $table->string('status')->nullable();
            }
            
            if (!Schema::hasColumn('payments', 'description')) {
                $table->text('description')->nullable();
            }
            
            if (!Schema::hasColumn('payments', 'payment_date')) {
                $table->datetime('payment_date')->nullable();
            }
            
            if (!Schema::hasColumn('payments', 'amount')) {
                $table->decimal('amount', 10, 2)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('invoice_id');
            $table->dropColumn([
                'transaction_id',
                'phone',
                'payment_method',
                'status',
                'description',
                'payment_date'
            ]);
        });
    }
};
