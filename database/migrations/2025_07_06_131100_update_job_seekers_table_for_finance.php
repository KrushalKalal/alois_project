<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('job_seekers', function (Blueprint $table) {
            $table->unsignedBigInteger('finance_maker_id')->nullable()->after('po_checker_id');
            $table->unsignedBigInteger('finance_checker_id')->nullable()->after('finance_maker_id');

            $table->decimal('actual_billing_value', 10, 2)->nullable()->after('percentage_gp');
            $table->string('invoice_no')->nullable()->after('actual_billing_value');

            $table->enum('type_of_attrition', ['Voluntary', 'Involuntary'])->nullable()->after('backout_term_year');
            $table->text('reason_of_attrition')->nullable()->after('type_of_attrition');

            $table->foreign('finance_maker_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('finance_checker_id')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_seekers', function (Blueprint $table) {
            $table->dropForeign(['finance_maker_id']);
            $table->dropForeign(['finance_checker_id']);
            $table->dropColumn([
                'finance_maker_id',
                'finance_checker_id',
                'actual_billing_value',
                'invoice_no',
                'type_of_attrition',
                'reason_of_attrition'
            ]);
        });
    }
};
