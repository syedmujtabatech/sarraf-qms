<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_code')->unique();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('priority_category_id')->constrained()->cascadeOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_cnic')->nullable();
            $table->string('customer_email')->nullable();
            $table->date('appointment_date');
            $table->string('time_slot'); // e.g. "10:00 AM - 10:30 AM"
            $table->enum('status', ['booked', 'checked_in', 'completed', 'cancelled'])->default('booked');
            $table->foreignId('token_id')->nullable()->constrained('tokens')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
