<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token_number');
            $table->integer('sequence_number')->default(1);
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('priority_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('counter_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('served_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Customer Details (SBI / SBP requirements for history & tracking)
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_cnic')->nullable();
            $table->string('customer_email')->nullable();
            $table->enum('channel', ['kiosk', 'appointment', 'mobile', 'teller_manual'])->default('kiosk');
            
            // Queue Workflow Status
            $table->enum('status', [
                'waiting', 
                'called', 
                'serving', 
                'completed', 
                'on_hold', 
                'transferred', 
                'no_show', 
                'cancelled'
            ])->default('waiting');

            // SBP/SBI Priority algorithm score (higher comes first in FIFO + weight)
            $table->double('priority_score', 8, 2)->default(1.0);
            
            // SLA and Timing Timestamps
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamp('called_at')->nullable();
            $table->timestamp('serving_started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('waiting_time_seconds')->default(0);
            $table->integer('serving_time_seconds')->default(0);
            $table->integer('recall_count')->default(0);
            
            // Transaction audit details
            $table->text('notes')->nullable();
            $table->decimal('transaction_amount', 14, 2)->nullable();
            $table->string('transaction_reference')->nullable();
            
            // Transfers
            $table->foreignId('transferred_from_counter_id')->nullable()->constrained('counters')->nullOnDelete();
            $table->foreignId('transferred_to_counter_id')->nullable()->constrained('counters')->nullOnDelete();
            
            $table->timestamps();
            
            $table->index(['branch_id', 'status', 'created_at']);
            $table->index(['customer_cnic']);
            $table->index(['customer_phone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tokens');
    }
};
