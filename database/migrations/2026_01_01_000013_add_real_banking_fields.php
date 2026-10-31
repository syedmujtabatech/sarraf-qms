<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add region to branches
        Schema::table('branches', function (Blueprint $table) {
            $table->foreignId('region_id')->nullable()->after('id')->constrained('regions')->nullOnDelete();
        });

        // 2. Add role_id and region_id to users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('password')->constrained('roles')->nullOnDelete();
            $table->foreignId('region_id')->nullable()->after('branch_id')->constrained('regions')->nullOnDelete();
        });

        // 3. Add cash drawer & break details to counters
        Schema::table('counters', function (Blueprint $table) {
            $table->string('break_reason')->nullable()->after('status'); // "Lunch Break", "Cash Reconciliation", "Prayer Break", "Vault Transfer"
            $table->decimal('opening_cash_balance', 14, 2)->default(0.00)->after('break_reason');
            $table->decimal('current_cash_drawer', 14, 2)->default(0.00)->after('opening_cash_balance');
        });

        // 4. Add SLA Escalation & Manager Override to tokens
        Schema::table('tokens', function (Blueprint $table) {
            $table->boolean('is_escalated')->default(false)->after('status'); // Flagged if waiting exceeds SBP SLA
            $table->boolean('manager_override')->default(false)->after('is_escalated'); // VIP fast-tracked by BM
            $table->string('override_reason')->nullable()->after('manager_override');
        });
    }

    public function down(): void
    {
        Schema::table('tokens', function (Blueprint $table) {
            $table->dropColumn(['is_escalated', 'manager_override', 'override_reason']);
        });

        Schema::table('counters', function (Blueprint $table) {
            $table->dropColumn(['break_reason', 'opening_cash_balance', 'current_cash_drawer']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['region_id']);
            $table->dropColumn(['role_id', 'region_id']);
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropColumn(['region_id']);
        });
    }
};
