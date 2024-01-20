@extends('layouts.admin')

@section('content')
    <div class="bd-head">
        {{ Breadcrumbs::render('admin.users') }}
    </div>
    <div class="row">
        {{--        <div class="col-lg-2">--}}
        {{--            <div class="filter-container">--}}
        {{--                <div class="show-filter">--}}
        {{--                    <button class="btn btn-light btn-sm w-100" data-bs-toggle="collapse" data-bs-target="#filter-panel" aria-expanded="true" aria-controls="filter-panel">--}}
        {{--                        <i class="fas fa-filter me-1"></i>--}}
        {{--                        Фильтр--}}
        {{--                    </button>--}}
        {{--                </div>--}}
        {{--            </div>--}}
        {{--        </div>--}}

        <div class="col-6 col-lg-2">
            <a class="btn btn-primary btn-sm w-100" href="{{ route('admin.user.add') }}">
                <i class="fas fa-plus me-1"></i>
                Добавить {{ !empty($info) ? $info : '' }}
            </a>
        </div>
    </div>

    {{--    <div class="row mt-2">--}}
    {{--        <div class="col-lg-6">--}}
    {{--            <div id="filter-panel" class="accordion-collapse collapse mt-3 {{ $filterShow ? 'show' : '' }}" aria-labelledby="panelsStayOpen-headingOne">--}}
    {{--                <div class="card">--}}
    {{--                    <div class="card-body">--}}
    {{--                        <form action="">--}}
    {{--                            <div class="row">--}}
    {{--                                <div class="col-lg-6">--}}
    {{--                                    <div class="mb-3">--}}
    {{--                                        <label for="filterPart" class="form-label">E-mail</label>--}}
    {{--                                        <input type="text" class="form-control form-control-sm" id="filterPart" name="email" value="{{ (!empty($_GET['email'])) ? $_GET['email'] : "" }}">--}}
    {{--                                    </div>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}

    {{--                            <div class="row">--}}
    {{--                                <div class="col-lg-6">--}}
    {{--                                    <div class="row">--}}
    {{--                                        <div class="col-lg-6">--}}
    {{--                                            <button class="btn btn-primary btn-block" type="submit">--}}
    {{--                                                --}}{{--                        <i class="fas fa-plus me-2"></i>--}}
    {{--                                                Показать--}}
    {{--                                            </button>--}}
    {{--                                        </div>--}}
    {{--                                        <div class="col-lg-6">--}}
    {{--                                            <a class="btn btn-light btn-block" href="{{ route('admin.users') }}">--}}
    {{--                                                --}}{{--                        <i class="fas fa-plus me-2"></i>--}}
    {{--                                                Сбросить--}}
    {{--                                            </a>--}}
    {{--                                        </div>--}}
    {{--                                    </div>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                        </form>--}}
    {{--                    </div>--}}
    {{--                </div>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--    </div>--}}

    @if (session('info'))
        <div class="row mt-4">
            <div class="col-lg-8">
                <div class="alert alert-{{session('alert')}}" role="alert">
                    {{ session('info') }}
                </div>
            </div>
        </div>
    @endif

    <div class="row mt-4">
        <div class="col-lg-8">
            <div class="parts-wrapper table-scroll">
                <table class="table table-parts table-sm table-bordered table-hover">
                    <thead class="table-light">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">ФИО</th>
                        <th scope="col">Email</th>
                        <th scope="col">Роль</th>
                        <th scope="col">Действия</th>
                    </tr>
                    </thead>
                    <tbody class="parts">
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->display_name ?? $user->role_name }}</td>
                            <td>
                                <a class="me-2" href="/admin/user/{{ $user->id }}/edit" title="Редактировть">
                                    <i class="fa-solid fa-user-pen"></i>
                                </a>

                                @if (auth()->user()->id !== $user->id && $user->role_name !== 'admin')
                                    {{--                                    <a href="/admin/user/{{ $user->id }}/delete" title="Удалить">--}}
                                    {{--                                        <i class="fa-solid fa-trash"></i>--}}
                                    {{--                                    </a>--}}
                                    <user-delete-component user_id="{{ $user->id }}"/>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @if($users->count() === 0)
                        <tr>
                            <td colspan="5">
                                Пользователи не найдены
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>

                {{ $users->links('layouts.pagination') }}
            </div>
        </div>
    </div>
@endsection
