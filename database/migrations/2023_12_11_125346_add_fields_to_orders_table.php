<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('payment_state')->after('status')->nullable()->default(null)->comment('Статус оплаты');
            $table->boolean('final')->after('payment_state')->nullable()->default(null)->comment('Финальный статус');
            $table->boolean('processed_paid')->after('final')->nullable()->default(null)->comment('Статус оплаты обработан');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_state');
            $table->dropColumn('final');
            $table->dropColumn('processed_paid');
        });
    }
};
