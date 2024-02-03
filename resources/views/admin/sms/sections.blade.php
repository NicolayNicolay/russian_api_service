@extends('layouts.admin')

@section('content')
    <div class="bd-head">
        {{ Breadcrumbs::render('admin.sms') }}
    </div>
    <div class="content">
        <div class="row">
            <div class="col-lg-10">
                <div class="parts-wrapper">
                    <div class="card-wrapper">
                        <div id="red" class="column-item card-menu">
                            <div class="top">
                                <list-icon/>
                            </div>
                            <div class="bottom">
                                <a href="/admin/sms/list" class="d-flex justify-content-between align-items-center w-100">СМС Рассылки
                                    <arrow-link-icon></arrow-link-icon>
                                </a>
                            </div>
                        </div>
                        <div id="blue" class="column-item card-menu">
                            <div class="top">
                                <template-icon/>
                            </div>
                            <div class="bottom">
                                <a href="/admin/sms/templates/" class="d-flex justify-content-between align-items-center w-100">Шаблоны
                                    <arrow-link-icon/>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
