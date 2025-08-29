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
        Schema::create('job_seekers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('location_id');
            $table->string('hire_type');
            $table->unsignedBigInteger('business_unit_id');
            $table->unsignedBigInteger('am_id')->nullable();
            $table->unsignedBigInteger('dm_id')->nullable();
            $table->unsignedBigInteger('tl_id')->nullable();
            $table->unsignedBigInteger('recruiter_id')->nullable();
            $table->unsignedBigInteger('consultant_id')->nullable();
            $table->string('skill')->nullable();
            $table->string('sap_id')->nullable();
            $table->unsignedBigInteger('status_id');
                
            $table->unsignedBigInteger('client_id');
            $table->string('poc')->nullable();
            $table->string('client_reporting_manager')->nullable();
            $table->string('quarter')->nullable();
            $table->date('selection_date')->nullable();
            $table->date('offer_date')->nullable();
            $table->date('join_date')->nullable();
            $table->date('qly_date')->nullable();
            $table->date('backout_term_date')->nullable();
            $table->string('backout_term_month')->nullable();
            $table->year('backout_term_year')->nullable();
            $table->date('po_end_date')->nullable();
            $table->string('po_end_month')->nullable();
            $table->year('po_end_year')->nullable();
            $table->decimal('pay_rate', 10, 2);
            $table->decimal('loaded_cost', 10, 2);
            $table->decimal('pay_rate_1', 10, 2);
            $table->decimal('bill_rate', 10, 2);
            $table->decimal('gp_month', 10, 2);
            $table->decimal('otc', 10, 2)->nullable();
            $table->decimal('otc_split', 10, 2)->nullable();
            $table->decimal('msp_fees', 10, 2)->nullable();
            $table->decimal('final_gp', 10, 2);
            $table->decimal('percentage_gp', 10, 2);
            $table->string('end_client')->nullable();
            $table->string('lob')->nullable();
            $table->string('bo_type')->nullable();
            $table->text('remark1')->nullable();
            $table->text('remark2')->nullable();
            $table->string('sources')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('company_id')->references('id')->on('company_masters')->onDelete('restrict');
            $table->foreign('location_id')->references('id')->on('branch_masters')->onDelete('restrict');
            $table->foreign('business_unit_id')->references('id')->on('business_unit_masters')->onDelete('restrict');
            $table->foreign('am_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('dm_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('tl_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('recruiter_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('consultant_id')->references('id')->on('consultants')->onDelete('restrict');
            $table->foreign('status_id')->references('id')->on('status_masters')->onDelete('restrict');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_seekers');
    }
};
