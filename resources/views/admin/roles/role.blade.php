@extends('layouts.admin')

@section('content')
    <div class="bd-head">
        @if (empty($id))
            {{ Breadcrumbs::render('admin.roles.add') }}
        @else
            {{ Breadcrumbs::render('admin.roles.edit') }}
        @endif
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="filter-container">
                <role-form-component role_id="{{$id}}"></role-form-component>
            </div>
        </div>
    </div>
@endsection

