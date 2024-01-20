@extends('layouts.admin')

@section('content')
    <div class="bd-head">
        {{ Breadcrumbs::render('admin.orders.show', $data->code) }}
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="order">
                <div class="title mb-3">
                    Данные заказа
                </div>
                <div class="row">
                    <div class="col-md-12 table-scroll">
                        <table class="table table-order table-sm table-bordered">
                            <thead class="table-light">
                            <tr>
                                <th scope="col">Сезон</th>
                                <th scope="col">Номер партии</th>
                                <th scope="col">Номер заказа</th>
                                <th scope="col">ФИО</th>
                                <th scope="col">Номер телефона</th>
                                <th scope="col">Трек номер</th>
                                <th scope="col">Дата загрузки</th>
                                <th scope="col">Дата изменения</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>{{ $data->season->name }}</td>
                                <td>{{ $data->lot_number }}</td>
                                <td>{{ $data->code }}</td>
                                <td>{{ $data->fio }}</td>
                                <td>{{ $data->phone_relatives }}</td>
                                <td>{{ $data->track }}</td>
                                <td>{{ $data->created_at }}</td>
                                <td>{{ $data->updated_at }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 table-scroll">
                        <div class="title mb-3">
                            Дополнительная информация
                        </div>

                        <table class="table table-order table-sm table-bordered">
                            <thead class="table-light">
                            <tr>
                                <th scope="col">Почтовыйы индекс</th>
                                <th scope="col">Область, край, республика</th>
                                <th scope="col">Район отправления</th>
                                <th scope="col">Адрес</th>
                                <th scope="col">ФИО родителя клиента</th>
                                <th scope="col">Дополнительная информация</th>
                                <th scope="col">Примечания к заказу</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>{{ $data->index }}</td>
                                <td>{{ $data->geo }}</td>
                                <td>{{ $data->district }}</td>
                                <td>{{ $data->address }}</td>
                                <td>{{ $data->fio_relatives }}</td>
                                <td>{{ $data->info }}</td>
                                <td>{{ $data->notes }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 table-scroll">
                        <div class="title mb-3">
                            Информация о статусе
                        </div>

                        <table class="table table-order table-sm table-bordered">
                            <thead class="table-light">
                            <tr>
                                <th scope="col">Статус</th>
                                <th scope="col">Стадия</th>
                                <th scope="col">Код статуса</th>
                                <th scope="col">Код статуса оплаты</th>
                                <th scope="col">Расшифровка</th>
                                <th scope="col">Дата установки статуса</th>
                                <th scope="col">Дата синхронизации</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if ($data->status != null)
                                <tr>
                                    <td>{{ $data->status_name['state'] ?? "-" }}</td>
                                    <td>{{ $data->status_name['stage'] ?? "-" }}</td>
                                    <td>{{ $data->status }}</td>
                                    @if($data->payment_status_name)
                                        <td>{{ $data->payment_status_name['name'] }}</td>
                                        <td>{{ $data->payment_status_name['description'] }}</td>
                                    @else
                                        <td>Отсутствует</td>
                                        <td>-</td>
                                    @endif
                                    <td>{{ $data->created_at }}</td>
                                    <td>{{ $data->last_status_updated }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="5">Статус не установлен</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <p><small><a href="{{ url()->previous() }}"><i class="fa-solid fa-chevron-left me-1"></i>Вернуться к списку</a></small></p>
            </div>
        </div>
    </div>
@endsection
