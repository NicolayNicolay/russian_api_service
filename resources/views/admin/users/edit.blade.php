@extends('layouts.admin')

@section('content')
    <div class="bd-head">
        {{ Breadcrumbs::render('admin.user.edit', $user->email) }}
    </div>

    <div class="row">
        <div class="col-lg-6">
            <user-edit-component :user="{{ $user }}" :roles="{{ $roles }}" backurl="{{ route('admin.users') }}"/>
        </div>
    </div>
@endsection
