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

            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['created_by', 'updated_by']);


            $table->unsignedBigInteger('maker_id')->nullable()->after('checker_id');
            $table->unsignedBigInteger('po_maker_id')->nullable()->after('maker_id');
            $table->unsignedBigInteger('po_checker_id')->nullable()->after('po_maker_id');
            $table->unsignedBigInteger('backout_maker_id')->nullable()->after('po_checker_id');
            $table->unsignedBigInteger('backout_checker_id')->nullable()->after('backout_maker_id');
            $table->integer('process_status')->default(0)->after('form_status');


            $table->foreign('maker_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('po_maker_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('po_checker_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('backout_maker_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('backout_checker_id')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_seekers', function (Blueprint $table) {
            // Restore created_by and updated_by
            $table->unsignedBigInteger('created_by')->nullable()->after('sources');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Drop new fields and their foreign keys
            $table->dropForeign(['maker_id']);
            $table->dropForeign(['po_maker_id']);
            $table->dropForeign(['po_checker_id']);
            $table->dropForeign(['backout_maker_id']);
            $table->dropForeign(['backout_checker_id']);
            $table->dropColumn([
                'maker_id',
                'po_maker_id',
                'po_checker_id',
                'backout_maker_id',
                'backout_checker_id',
                'process_status'
            ]);
        });
    }
};
