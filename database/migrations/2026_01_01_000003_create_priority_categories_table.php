<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('priority_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ur');
            $table->string('code')->unique(); // REGULAR, SENIOR, PENSIONER, PWD, VIP, PREGNANT
            $table->integer('weight_multiplier')->default(1); // Higher weight = higher queue priority
            $table->string('badge_color')->default('secondary');
            $table->string('icon')->default('bx-user');
            $table->text('regulatory_note')->nullable(); // e.g. "SBP BC-CPD Circular 01/2018"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('priority_categories');
    }
};
