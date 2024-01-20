@extends('layouts.admin')

@section('content')
    <div class="bd-head">
        {{ Breadcrumbs::render('admin.orders.add') }}
    </div>
    <div class="row">
        <div class="col-lg-6">
            <order-upload-form backurl="{{ route('orders.index') }}" />
        </div>
    </div>
@endsection
