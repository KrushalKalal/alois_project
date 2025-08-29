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
            Schema::table('employees', function (Blueprint $table) {
                $table->enum('role', [
                    'maker',
                    'checker',
                    'po_maker',
                    'po_checker',
                    'finance_maker',
                    'finance_checker',
                    'backout_maker',
                    'backout_checker'
                ])->nullable()->change();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->enum('role', [
                'maker',
                'checker',
                'po_maker',
                'po_checker',
                'backout_maker',
                'backout_checker'
            ])->nullable()->change();
        });
    }
};
