<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('availability_payment')->after('processed_paid')->nullable()->default(null)->comment('Наличие наложенного платежа');
            $table->boolean('status_availability_payment')->after('availability_payment')->nullable()->default(null)->comment('Запрос на получение наличия наложенного платежа');
            $table->dateTime('availability_payment_date')->nullable()->default(null)->after('status_availability_payment')->comment('Дата проставления статуса запроса наложенного платежа');
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
            $table->dropColumn('availability_payment');
            $table->dropColumn('status_availability_payment');
            $table->dropColumn('availability_payment_date');
        });
    }
};
