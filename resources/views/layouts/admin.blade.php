<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!--  (c) 2022, разработано студией "IT-ENTERPRISE"  -->

    <link rel="apple-touch-icon" sizes="180x180" href="/images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon/favicon-16x16.png">
    <link rel="manifest" href="/images/favicon/site.webmanifest">
    <link rel="mask-icon" href="/images/favicon/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user_id" content="{{ Auth::user()->id }}">

    @if(isset($page_title))
        <title>{{ $page_title }}</title>
    @else
        <title>Административная панель | voenkor2022.ru</title>
    @endif

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body>

<div class="admin-wrapper" id="app">
    <div class="container-fluid p-0">
        <div class="bd-body">
            <div class="bd-sidebar slideout-menu slideout-menu-left" id="bd_sidebar">
                <a href="#" class="close-menu d-lg-none"><i aria-hidden="true" class="fa fa-times"></i></a>
                <div class="bd-logo">
                    <a href="{{ route('admin.index') }}" class="logo">Военкор<br/>
                        <span style="font-size: 18px">Трек-номера</span>
                    </a>
                </div>
                <div class="bd-login">
                    <div class="dropdown">
                        <div class="name">
                            {{ auth()->guard('web')->user()->name }}
                        </div>
                    </div>
                </div>
                <div class="scrollbar-outer">
                    <div class="bd-menu">
                        <ul class="nav flex-column">
                            <li class="nav-item {{ Request::is('admin') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.index') }}">
                                    <span class="icon text-primary">
                                        <i class="fa-solid fa-house"></i>
                                    </span>Дашборд
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/orders') || Request::is('admin/orders/*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('orders.index') }}">
                                    <span class="icon text-primary">
                                        <i class="fa-solid fa-list"></i>
                                    </span>Заказы
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/seasons') || Request::is('admin/seasons/*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('seasons.index') }}">
                                    <span class="icon text-primary">
                                        <i class="fa-solid fa-star"></i>
                                    </span>Сезоны
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/users') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.users') }}">
                                    <span class="icon text-primary">
                                        <i class="fa-solid fa-users"></i>
                                    </span>Пользователи
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/roles') ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.roles') }}">
                                    <span class="icon text-primary">
                                        <i class="fa-solid fa-cog"></i>
                                    </span>Роли
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/history') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('history.index') }}">
                                <span class="icon text-primary">
                                    <i class="fa-solid fa-users"></i>
                                </span>История загрузок
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link " href="{{ route('admin.logout') }}">
                                    <span class="icon text-primary">
                                        <i class="fas fa-sign-out-alt"></i>
                                    </span>Выйти
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <main class="bd-content" id="bd_content">
                <div class="bd-head-mobile">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12 text-center">
                                <div class="brand">
                                    <div class="logo">
                                        <a href="{{ route('admin.index') }}" class="logo">Военкор</br>
                                            <span style="font-size: 18px;">Трек-номера</span>
                                        </a>
                                    </div>
                                    <button type="button" class="navbar-toggler d-lg-none open-menu"><i class="fas fa-bars"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @yield('content')
            </main>
        </div>
    </div>

    <notifications group="top_messages" position="top center" classes="vue-notification" width="500px"></notifications>
    <notifications group="notifications"
                   :max="5"
                   classes="vue-notification"
                   width="500px"
                   position="bottom right"
                   style="margin-bottom: 2rem;"></notifications>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3" id="toast-alert-container">
    <div class="toast fade hide" role="alert" id="toastAlertSuccess" aria-live="assertive" aria-atomic="true">
        <div class="toast-body"></div>
    </div>

    <div class="toast fade hide" role="alert" id="toastAlertInfo" aria-live="assertive" aria-atomic="true">
        <div class="toast-body"></div>
    </div>

    <div class="toast fade hide" role="alert" id="toastAlertDanger" aria-live="assertive" aria-atomic="true">
        <div class="toast-body"></div>
    </div>
</div>

<script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
