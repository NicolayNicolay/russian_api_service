<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Controllers\AdminAuthController;
use Modules\Dashboard\Controllers\DashboardController;
use Modules\Files\Controllers\FileController;
use Modules\History\Controllers\HistoryController;
use Modules\Orders\Controllers\OrderController;
use Modules\Pages\Controllers\PageController;
use Modules\Seasons\Controllers\SeasonController;
use Modules\Sms\Controllers\SmsController;
use Modules\Sms\Controllers\TemplateController;
use Modules\Users\Controllers\UsersController;
use Modules\Roles\Controllers\RoleController;
use Modules\Roles\Controllers\RoleFormController;

// Админка
Route::get('/', [PageController::class, 'index'])->name('admin.index')->middleware(['auth']);
Route::group(['prefix' => 'admin'], function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::get('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});

Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::group(['prefix' => 'dashboard'], static function () {
        Route::post('/get_data', [DashboardController::class, 'getList'])->name('dashboard.getList');
    });
    //Сезоны
    Route::group(['prefix' => 'seasons'], static function () {
        Route::get('/', [SeasonController::class, 'index'])->name('seasons.index');
        Route::get('/add', [SeasonController::class, 'add'])->name('seasons.add');
        Route::get('/edit/{id?}', [SeasonController::class, 'edit'])->name('seasons.edit');
        Route::get('/seasonList', [SeasonController::class, 'seasonList'])->name('seasons.seasonList');
        Route::get('/get_form/{id?}', [SeasonController::class, 'getFormParams'])->name('seasons.get_form');
        Route::post('/store', [SeasonController::class, 'store'])->name('seasons.store');
        Route::get('/remove/{id?}', [SeasonController::class, 'destroy'])->name('seasons.remove_form');
    });
    //Шаблоны смс
    Route::group(['prefix' => 'sms'], static function () {
        Route::get('/', [PageController::class, 'smsSections'])->name('sms.sections');
        Route::post('/store', [SmsController::class, 'store'])->name('sms.store');
        Route::group(['prefix' => 'templates'], static function () {
            Route::get('/', [TemplateController::class, 'index'])->name('templates.index');
            Route::get('/add', [TemplateController::class, 'add'])->name('templates.add');
            Route::get('/edit/{id?}', [TemplateController::class, 'edit'])->name('templates.edit');
            Route::get('/apiList', [TemplateController::class, 'apiList'])->name('templates.apiList');
            Route::get('/get_form/{id?}', [TemplateController::class, 'getFormParams'])->name('templates.get_form');
            Route::post('/store', [TemplateController::class, 'store'])->name('templates.store');
            Route::get('/remove/{id?}', [TemplateController::class, 'destroy'])->name('templates.remove_form');
        });
        Route::group(['prefix' => 'list'], static function () {
            Route::get('/', [SmsController::class, 'index'])->name('list.index');
            Route::get('/show/{id?}', [SmsController::class, 'show'])->name('list.show');
            Route::get('/get_data/{id?}', [SmsController::class, 'getData'])->name('list.getData');
            Route::get('/apiList', [SmsController::class, 'apiList'])->name('list.apiList');
            Route::get('/success/{id?}', [SmsController::class, 'success'])->name('list.success');
            Route::get('/remove/{id?}', [SmsController::class, 'destroy'])->name('list.remove');
            Route::post('/removeElement', [SmsController::class, 'destroyElement'])->name('templates.destroyElement');
        });
    });
    //Заказы
    Route::group(['prefix' => 'orders'], static function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/add', [OrderController::class, 'add'])->name('orders.add');
        Route::get('/get_order_form', [OrderController::class, 'getOrderFormParams'])->name('orders.getOrderFormParams');
        Route::post('/store_order', [OrderController::class, 'storeOrder'])->name('orders.storeOrder');
        Route::post('/getOrdersXls', [OrderController::class, 'getOrdersXls'])->name("getOrdersXls");
        Route::post('/ordersList', [OrderController::class, 'ordersList'])->name('orders.ordersList');
        Route::get('/show/{id?}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/remove/{id?}', [OrderController::class, 'destroy'])->name('orders.remove_form');
        Route::group(
            ['prefix' => 'files'],
            static function () {
                Route::post('/uploadFiles', [FileController::class, 'uploadFile'])->name('files.uploadFiles');
            }
        );
    });
    Route::group(['prefix' => 'historyAdmin', 'middleware' => 'enterprise'], static function () {
        Route::get('/', [HistoryController::class, 'indexAdmin'])->name('historyAdmin.index');
    });
    //История заказов
    Route::group(['prefix' => 'history'], static function () {
        Route::get('/', [HistoryController::class, 'index'])->name('history.index');
        Route::get('/show/{id?}', [HistoryController::class, 'show'])->name('history.show');
        Route::get('/get_data/{id?}', [HistoryController::class, 'getData'])->name('history.getData');
    });
    Route::group(['middleware' => 'permission:users'], function () {
        //Роли
        Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles');
        Route::get('/role/form/{id?}', [RoleController::class, 'roleForm'])->name('admin.roles.form');
        Route::get('/role/get-form/{id}', [RoleFormController::class, 'getForm']);
        Route::post('/role/submit', [RoleFormController::class, 'submit']);
        Route::get('/role/{id}/delete', [RoleController::class, 'delete']);
        //Пользователи
        Route::get('/users', [UsersController::class, 'index'])->name('admin.users');
        Route::get('/user/{id}/edit', [UsersController::class, 'editView'])->name('admin.user.edit');
        Route::post('/user/save', [UsersController::class, 'edit']);
        Route::get('/user/add', [UsersController::class, 'addView'])->name('admin.user.add');
        Route::post('/user/add', [UsersController::class, 'add']);
        Route::get('/user/{id}/delete', [UsersController::class, 'delete']);
    });
});

Route::get('/test', function () {
});
