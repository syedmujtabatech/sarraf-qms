<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('token_id')->constrained()->cascadeOnDelete();
            $table->string('token_number');
            $table->string('counter_number');
            $table->string('voice_text_ur'); // e.g. "ٹوکن نمبر A-102، کاؤنٹر نمبر 3 پر تشریف لائیں"
            $table->string('voice_text_en'); // e.g. "Token Number A-102, please proceed to Counter 3"
            $table->boolean('is_played')->default(false);
            $table->timestamps();
            
            $table->index(['branch_id', 'is_played', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
