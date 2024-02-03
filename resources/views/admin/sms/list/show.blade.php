@extends('layouts.admin')

@section('content')
    <div class="bd-head">
        {{ Breadcrumbs::render('admin.sms.list.show', $id) }}
    </div>
    <div class="row">
        <div class="col-lg-6">
            <sms-show :id="{{$id}}" backurl="{{ route('list.index') }}" :message="'{{ session('info') ? session('info'): '' }}'" />
        </div>
    </div>
@endsection
