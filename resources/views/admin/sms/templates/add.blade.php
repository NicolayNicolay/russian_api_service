@extends('layouts.admin')

@section('content')
    <div class="bd-head">
        {{ Breadcrumbs::render('admin.sms.templates.add') }}
    </div>
    <div class="row">
        <div class="col-lg-6">
            <template-form backurl="{{ route('templates.index') }}"></template-form>
        </div>
    </div>
@endsection
