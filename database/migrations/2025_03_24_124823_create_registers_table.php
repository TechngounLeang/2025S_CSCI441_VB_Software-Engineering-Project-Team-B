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
        Schema::create('registers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('status', ['open', 'closed'])->default('closed');
            $table->decimal('cash_balance', 10, 2)->default(0);
            $table->decimal('counted_balance', 10, 2)->nullable();
            $table->decimal('balance_difference', 10, 2)->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->foreignId('opened_by')->nullable()->constrained('users');
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users');
            $table->integer('transaction_count')->default(0);
            $table->timestamps();
        });
        
        // Add register_id to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('register_id')->nullable()->constrained();
            $table->foreignId('cashier_id')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('register_id');
            $table->dropConstrainedForeignId('cashier_id');
        });
        
        Schema::dropIfExists('registers');
    }
};