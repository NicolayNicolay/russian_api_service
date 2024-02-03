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
        Schema::create('sms_orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sms_id')->comment('ID записи в таблице Sms');
            $table->bigInteger('order_id')->comment('ID записи в таблице Orders');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_orders');
    }
};
