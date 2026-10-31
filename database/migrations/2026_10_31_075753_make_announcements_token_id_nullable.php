<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->foreignId('token_id')->nullable()->change();
            $table->string('token_number')->nullable()->change();
            $table->string('counter_number')->nullable()->change();
            $table->string('type')->default('call')->after('is_played'); // 'call', 'manager_broadcast', 'system'
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
