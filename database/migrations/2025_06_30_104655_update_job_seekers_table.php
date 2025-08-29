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

            $table->dropForeign(['consultant_id']);
            $table->dropColumn('consultant_id');


            $table->string('consultant_code')->nullable()->after('recruiter_id');
            $table->string('consultant_name')->nullable()->after('consultant_code');
            $table->unsignedBigInteger('checker_id')->nullable()->after('updated_by');
            $table->enum('job_seeker_type', ['Temporary', 'Permanent'])->default('Temporary')->after('checker_id');


            $table->foreign('checker_id')->references('id')->on('employees')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_seekers', function (Blueprint $table) {

            $table->unsignedBigInteger('consultant_id')->nullable()->after('recruiter_id');
            $table->foreign('consultant_id')->references('id')->on('consultants')->onDelete('restrict');


            $table->dropColumn('consultant_code');
            $table->dropColumn('consultant_name');
            $table->dropForeign(['checker_id']);
            $table->dropColumn('checker_id');
            $table->dropColumn('job_seeker_type');



        });
    }
};
