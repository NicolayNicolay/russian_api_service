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
        Schema::create('api_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('api_id')->comment('ID Задания на почте России');
            $table->boolean('status')->default(false)->comment('Статус получения данных');
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
        Schema::dropIfExists('api_tasks');
    }
};
