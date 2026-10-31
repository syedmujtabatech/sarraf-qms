<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('counter_number'); // e.g. "01", "02", "P1"
            $table->string('name'); // e.g. "Counter 01 - Cash & Clearing"
            $table->string('type')->default('general'); // general, priority_dedicated, cash, inquiries
            $table->enum('status', ['offline', 'online', 'serving', 'break'])->default('offline');
            $table->foreignId('current_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('current_token_id')->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counters');
    }
};
