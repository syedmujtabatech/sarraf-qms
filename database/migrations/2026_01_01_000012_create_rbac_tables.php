<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Roles Table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g. super_admin, regional_manager, branch_manager, chief_teller, teller, cso, sbp_auditor, greeter
            $table->string('display_name'); // e.g. "Branch Operations Manager (BOM)"
            $table->string('badge_color')->default('primary');
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        // 2. Permissions Table
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g. view_dashboard, call_tokens, override_priority, manage_users
            $table->string('display_name'); // e.g. "Call & Dispatch Queue Tokens"
            $table->string('module'); // e.g. "Executive Overview", "Counter Operations", "Staff & Security", "Compliance & Audit"
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 3. Role-Permission Pivot Table
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
