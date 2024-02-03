<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Admin
Breadcrumbs::for('admin', function (BreadcrumbTrail $trail) {
    $trail->push('Административная панель');
});

// Dashboard
Breadcrumbs::for('admin.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin');
    $trail->push('Главная', route('admin.pages.index'));
});

// Roles
Breadcrumbs::for('admin.roles', function (BreadcrumbTrail $trail) {
    $trail->parent('admin');
    $trail->push('Роли', route('admin.roles'));
});

// Roles > Add
Breadcrumbs::for('admin.roles.add', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.roles');
    $trail->push('Добавить роль', route('admin.roles.form', ''));
});

// Roles > Edit
Breadcrumbs::for('admin.roles.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.roles');
    $trail->push("Редактировать роль", route('admin.roles.form', ''));
});


// Users
Breadcrumbs::for('admin.users', function (BreadcrumbTrail $trail) {
    $trail->parent('admin');
    $trail->push('Пользователи', route('admin.users'));
});


// User edit
Breadcrumbs::for('admin.user.edit', function (BreadcrumbTrail $trail, $user_id) {
    $trail->parent('admin.users');
    $trail->push("Данные пользователя {$user_id}", route('admin.user.edit', ''));
});

// User add
Breadcrumbs::for('admin.user.add', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.users');
    $trail->push("Новый пользователь", route('admin.user.add'));
});
// Seasons
Breadcrumbs::for('admin.seasons', function (BreadcrumbTrail $trail) {
    $trail->parent('admin');
    $trail->push('Сезоны', route('seasons.index'));
});
Breadcrumbs::for('admin.seasons.add', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.seasons');
    $trail->push('Добавить сезон', route('seasons.add'));
});
Breadcrumbs::for('admin.seasons.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.seasons');
    $trail->push('Изменить сезон', route('seasons.edit'));
});
//Orders
Breadcrumbs::for('admin.orders', function (BreadcrumbTrail $trail) {
    $trail->parent('admin');
    $trail->push('Заказы', route('orders.index'));
});
Breadcrumbs::for('admin.orders.add', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.orders');
    $trail->push('Загрузить заказы', route('orders.add'));
});
Breadcrumbs::for('admin.orders.show', function (BreadcrumbTrail $trail, $order_id) {
    $trail->parent('admin.orders');
    $trail->push("Заказ №{$order_id}", route('orders.show', ''));
});
//History
Breadcrumbs::for('admin.historyAdmin', function (BreadcrumbTrail $trail) {
    $trail->parent('admin');
    $trail->push('История выгрузок', route('historyAdmin.index'));
});
Breadcrumbs::for('admin.history', function (BreadcrumbTrail $trail) {
    $trail->parent('admin');
    $trail->push('История выгрузок', route('history.index'));
});
Breadcrumbs::for('admin.history.show', function (BreadcrumbTrail $trail, $name) {
    $trail->parent('admin.history');
    $trail->push($name, route('history.show', ''));
});
//Dashboard
Breadcrumbs::for('admin.dashboard', function (BreadcrumbTrail $trail) {
    $trail->parent('admin');
    $trail->push('Дашборд', route('dashboard.index'));
});
// Templates
Breadcrumbs::for('admin.sms', function (BreadcrumbTrail $trail) {
    $trail->parent('admin');
    $trail->push('СМС', route('sms.sections'));
});
Breadcrumbs::for('admin.sms.templates', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.sms');
    $trail->push('Шаблоны', route('templates.index'));
});
Breadcrumbs::for('admin.sms.templates.add', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.sms.templates');
    $trail->push('Добавить шаблон', route('templates.add'));
});
Breadcrumbs::for('admin.sms.templates.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.sms.templates');
    $trail->push('Изменить шаблон', route('templates.edit'));
});
Breadcrumbs::for('admin.sms.list', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.sms');
    $trail->push('СМС Рассылки', route('list.index'));
});
Breadcrumbs::for('admin.sms.list.show', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('admin.sms.list');
    $trail->push('Рассылка №' . $id, route('list.show'));
});
