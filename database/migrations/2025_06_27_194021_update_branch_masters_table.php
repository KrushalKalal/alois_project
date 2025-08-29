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
        Schema::table('branch_masters', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->tinyInteger('branch_status')->default(1);
            $table->dropForeign(['company_id']);
            $table->foreign('company_id')
                ->references('id')
                ->on('company_masters')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branch_masters', function (Blueprint $table) {
            $table->dropColumn('branch_status');
            $table->string('name')->unique()->change();
            $table->dropForeign(['company_id']);
            $table->foreign('company_id')
                ->references('id')
                ->on('company_masters')
                ->onDelete('cascade');
        });
    }
};
