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
            //    Сумма, дате операции, индекс отделения
            $table->decimal('payment_sum')->after('payment_state')->nullable()->default(null)->comment('Сумма');
            $table->dateTime('payment_date')->nullable()->default(null)->after('payment_sum')->comment('Дата операции');
            $table->bigInteger('payment_place_index')->after('payment_date')->nullable()->default(null)->comment('Индекс отделения');
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
            $table->dropColumn('payment_sum');
            $table->dropColumn('payment_date');
            $table->dropColumn('payment_place_index');
        });
    }
};
