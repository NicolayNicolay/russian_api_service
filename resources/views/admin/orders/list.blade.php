@extends('layouts.admin')

@section('content')
    <div class="bd-head">
        {{ Breadcrumbs::render('admin.orders') }}
    </div>
    <orders-list :message="'{{ session('info') ? session('info'): '' }}'" :statuses="{{ $statuses }}" :seasons="{{ $seasons }}"/>
@endsection
