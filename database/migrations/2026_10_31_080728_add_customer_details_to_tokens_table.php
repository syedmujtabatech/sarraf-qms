<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tokens', function (Blueprint $table) {
            $table->string('salutation')->default('Mr.')->after('served_by_user_id'); // Mr., Mrs., Ms., Dr., Haji, Syed, Sheikh
            $table->string('account_number')->nullable()->after('customer_email');
            $table->string('transaction_purpose')->nullable()->after('account_number'); // Cash Deposit, Cash Withdrawal, Cheque, KYC, Loan
            $table->decimal('expected_amount', 14, 2)->nullable()->after('transaction_purpose');
        });
    }

    public function down(): void
    {
        Schema::table('tokens', function (Blueprint $table) {
            $table->dropColumn(['salutation', 'account_number', 'transaction_purpose', 'expected_amount']);
        });
    }
};
