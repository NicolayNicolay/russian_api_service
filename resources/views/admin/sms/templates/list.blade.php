@extends('layouts.admin')

@section('content')
    <div class="bd-head">
        {{ Breadcrumbs::render('admin.sms.templates') }}
    </div>
    <div class="row">
        <div class="col-6 col-lg-2">
            <a class="btn btn-primary btn-sm w-100" href="/admin/sms/templates/add">
                <i class="fas fa-plus me-1"></i>
                Добавить
            </a>
        </div>
    </div>
    <templates-list :message="'{{ session('info') ? session('info'): '' }}'"></templates-list>
@endsection
