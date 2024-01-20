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
        Schema::create('post_responses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->comment('Код ответа');
            $table->string('stage')->comment('Стадия');
            $table->string('state')->nullable()->comment('Состояние');
            $table->string('description')->comment('Описание');
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
        Schema::dropIfExists('post_responses');
    }
};
