@extends('layouts.admin')

@section('content')
    <div class="bd-head">
        {{ Breadcrumbs::render('admin.sms.templates.edit') }}
    </div>
    <div class="row">
        <div class="col-lg-6">
            <template-form :id="{{$id}}" backurl="{{ route('templates.index') }}"></template-form>
        </div>
    </div>
@endsection
