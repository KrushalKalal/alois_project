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
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['checker_emp_id']);
            $table->dropColumn('checker_emp_id');

            $table->unsignedBigInteger('checker_id')->nullable()->after('role');
            $table->foreign('checker_id')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Restore checker_emp_id
            $table->string('checker_emp_id')->nullable()->after('role');
            $table->foreign('checker_emp_id')->references('emp_id')->on('employees')->onDelete('set null');

            // Drop checker_id
            $table->dropForeign(['checker_id']);
            $table->dropColumn('checker_id');
        });
    }
};
