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
        Schema::table('company_masters', function (Blueprint $table) {
            $table->json('to_emails')->nullable();
            $table->json('cc_emails')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_masters', function (Blueprint $table) {
            $table->dropColumn(['to_emails', 'cc_emails']);
        });
    }
};
