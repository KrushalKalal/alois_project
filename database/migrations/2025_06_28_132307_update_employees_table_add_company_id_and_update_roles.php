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
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->after('name')->nullable();
            $table->foreign('company_id')->references('id')->on('company_masters')->onDelete('set null');
            $table->enum('role', ['maker', 'checker', 'po maker', 'po checker', 'backout maker', 'backout checker'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
            $table->enum('role', ['maker', 'checker'])->change();
        });
    }
};
