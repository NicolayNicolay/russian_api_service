@extends('layouts.admin')

@section('content')
    <div class="bd-head">
        {{ Breadcrumbs::render('admin.sms.list') }}
    </div>
    <sms-list/>
@endsection
