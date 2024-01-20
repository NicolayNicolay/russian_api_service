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
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->comment('Код заказа, состоит из номера партии и уникального идентификатора заказа');
            $table->string('lot_number')->comment('Номер партии');
            $table->string('order_number')->comment('Номер заказа');
            $table->unsignedBigInteger('season_id')->comment('Сезон');
            $table->foreign('season_id')
                ->references('id')->on('seasons');
            $table->string('fio')->comment('ФИО клиента');
            $table->string('index')->nullable()->comment('Почтовый индекс');
            $table->text('geo')->nullable()->comment('Область, край, республика');
            $table->string('district')->nullable()->comment('Район отправления');
            $table->string('address')->nullable()->comment('Адрес');
            $table->text('fio_relatives')->nullable()->comment('ФИО родителя клиента');
            $table->string('phone_relatives')->nullable()->comment('Телефон родителя клиента');
            $table->string('info')->nullable()->comment('Дополнительная информация');
            $table->string('notes')->nullable()->comment('Примечания к заказу');
            $table->string('track')->nullable()->comment('Трек номер заказа');
            $table->string('status')->nullable()->comment('Статус заказа');
            $table->dateTime('last_status_updated')->nullable()->comment('Дата последнего обновления статуса заказа');
            $table->unsignedBigInteger('created_user_id')->comment('Пользователь выполнивший выгрузку (создание)');
            $table->foreign('created_user_id')
                ->references('id')->on('users');
            $table->unsignedBigInteger('updated_user_id')->comment('Пользователь выполнивший выгрузку (обновление)');
            $table->foreign('updated_user_id')
                ->references('id')->on('users');
            $table->unique(['code', 'season_id']);
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
        Schema::dropIfExists('orders');
    }
};
