@extends('layouts.admin')

@section('content')
    <div class="bd-head">
        {{ Breadcrumbs::render('admin.roles') }}
    </div>
    <div class="row">
        <div class="col-6 col-lg-2">
            <a class="btn btn-primary btn-sm w-100" href="{{ route('admin.roles.form') }}">
                <i class="fas fa-plus me-1"></i>
                Добавить
            </a>
        </div>
    </div>

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
            <div class="parts-wrapper">
                <table class="table table-parts table-sm table-bordered table-hover">
                    <thead class="table-light">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Название</th>
                        <th scope="col">Описание</th>
                        <th scope="col">Дата изменения</th>
                        <th scope="col">Действия</th>
                    </tr>
                    </thead>
                    <tbody class="parts">
                    @foreach($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td>{{ !empty($role->display_name) ? $role->display_name : $role->name }}</td>
                            <td>{{ $role->description }}</td>
                            <td>{{ date('d.m.Y H:i:s', strtotime($role->updated_at)) }}</td>
                            <td>
                                <a class="me-2" href="/admin/role/form/{{ $role->id }}" title="Подробнее">
                                    <i class="fa-solid fa-ellipsis"></i>
                                </a>
                                @if ($role->name !== 'admin')
                                    <role-delete-component role_id="{{ $role->id }}"></role-delete-component>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @if($roles->count() === 0)
                        <tr>
                            <td colspan="9">
                                Роли не найдены
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>

                <div class="row d-flex align-items-center mb-4">
                    <div class="col-md-10">
                        {{ $roles->links('layouts.pagination') }}
                    </div>
                    <div class="col-md-2">
                        <select-items-page-component
                            :current-count-items="{{ empty($_GET['count']) ? 0 : intval($_GET['count']) }}"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
