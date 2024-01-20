@extends('layouts.admin')

@section('content')
    <div class="bd-head">
        {{ Breadcrumbs::render('admin.user.add') }}
    </div>

    <div class="row">
        <div class="col-lg-6">
            <user-add-component :roles="{{ $roles }}" backurl="{{ route('admin.users') }}"/>
        </div>
    </div>
@endsection
