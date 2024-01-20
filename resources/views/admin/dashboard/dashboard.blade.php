@extends('layouts.admin')

@section('content')
    <div class="bd-head">
        {{ Breadcrumbs::render('admin.dashboard') }}
    </div>

    <dashboard-component :seasons="{{$seasons}}"></dashboard-component>
@endsection
