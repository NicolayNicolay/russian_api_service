@extends('layouts.admin')

@section('content')
    <div class="bd-head">
        {{ Breadcrumbs::render('admin.history.show', $data->file->name) }}
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="history">
                <div class="title mb-3">
                    Данные заказа
                </div>
                <history-component :id="{{$data->id}}"/>
                <p><small><a href="{{ url()->previous() }}"><i class="fa-solid fa-chevron-left me-1"></i>Вернуться к списку</a></small></p>
            </div>
        </div>
    </div>
@endsection
