<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('job_seekers', function (Blueprint $table) {
            $table->string('currency')->nullable()->after('reason_of_attrition');
            $table->string('select_month')->nullable()->after('selection_date');
            $table->string('join_month')->nullable()->after('join_date');
            $table->string('join_year')->nullable()->after('join_month');
            $table->string('source')->nullable()->after('sources');
            $table->string('domain')->nullable()->after('source');
            $table->string('hire_status')->nullable()->after('po_end_year');
            $table->decimal('pay_rate_usd', 10, 2)->nullable()->after('pay_rate');
            $table->decimal('bill_rate_usd', 10, 2)->nullable()->after('bill_rate');
            $table->decimal('basic_pay_rate', 10, 2)->nullable()->after('bill_rate_usd');
            $table->decimal('benefits', 10, 2)->nullable()->after('basic_pay_rate');
            $table->decimal('gp_hour', 10, 2)->nullable()->after('gp_month');
            $table->decimal('gp_hour_usd', 10, 2)->nullable()->after('gp_hour');
            $table->decimal('ctc_offered', 10, 2)->nullable()->after('percentage_gp');
            $table->decimal('billing_value', 10, 2)->nullable()->after('ctc_offered');
            $table->decimal('loaded_gp', 10, 2)->nullable()->after('billing_value');
            $table->decimal('final_billing_value', 10, 2)->nullable()->after('loaded_gp');
            $table->string('bd_absconding_term')->nullable()->after('type_of_attrition');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_seekers', function (Blueprint $table) {
            $table->dropColumn([
                'currency',
                'select_month',
                'join_month',
                'join_year',
                'source',
                'domain',
                'hire_status',
                'pay_rate_usd',
                'bill_rate_usd',
                'basic_pay_rate',
                'benefits',
                'gp_hour',
                'gp_hour_usd',
                'ctc_offered',
                'billing_value',
                'loaded_gp',
                'final_billing_value',
                'bd_absconding_term'
            ]);
        });
    }
};
