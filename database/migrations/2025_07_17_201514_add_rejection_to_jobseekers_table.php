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
            if (!Schema::hasColumn('job_seekers', 'reason_of_rejection')) {
                $table->text('reason_of_rejection')->nullable()->after('type_of_attrition');
            }

            $table->string('join_month', 7)->nullable()->change();
            $table->string('backout_term_month', 7)->nullable()->change();
            $table->string('po_end_month', 7)->nullable()->change();

            $table->integer('join_year')->nullable()->change();
            $table->integer('backout_term_year')->nullable()->change();
            $table->integer('po_end_year')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_seekers', function (Blueprint $table) {
            if (Schema::hasColumn('job_seekers', 'reason_of_rejection')) {
                $table->dropColumn('reason_of_rejection');
            }

            $table->date('join_month')->nullable()->change();
            $table->date('backout_term_month')->nullable()->change();
            $table->date('po_end_month')->nullable()->change();

            $table->string('join_year')->nullable()->change();
            $table->string('backout_term_year')->nullable()->change();
            $table->string('po_end_year')->nullable()->change();
        });
    }
};
