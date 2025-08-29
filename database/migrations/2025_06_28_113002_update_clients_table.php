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
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'state',
                'city',
                'country',
                'phone2',
                'email2',
                'aadhaar',
                'pan',
                'po_copy',
                'extra_doc',
            ]);

            $table->renameColumn('phone1', 'phone');
            $table->renameColumn('email1', 'email');

            $table->unsignedBigInteger('company_id')->after('client_name')->nullable();
            $table->unsignedTinyInteger('client_status')->after('company_id')->default(0);

            $table->integer('loaded_cost')->after('client_status')->nullable();
            $table->integer('qualify_days')->after('loaded_cost')->nullable();

            $table->foreign('company_id')->references('id')->on('company_masters')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn(['company_id', 'client_status', 'loaded_cost', 'qualify_days']);

            $table->text('address')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('phone2', 20)->nullable()->unique();
            $table->string('email2')->nullable()->unique();
            $table->string('aadhaar')->nullable();
            $table->string('pan')->nullable();
            $table->string('po_copy')->nullable();
            $table->string('extra_doc')->nullable();

            $table->renameColumn('phone', 'phone1');
            $table->renameColumn('email', 'email1');
        });
    }
};
