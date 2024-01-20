@extends('layouts.admin')

@section('content')
    <div class="bd-head">
        {{ Breadcrumbs::render('admin.seasons.add') }}
    </div>
    <div class="row">
        <div class="col-lg-6">
            <season-form backurl="{{ route('seasons.index') }}"/>
        </div>
    </div>
@endsection
