@extends('layouts.admin')

@section('content')
    <div class="bd-head">
        {{ Breadcrumbs::render('admin.historyAdmin') }}
    </div>
    <div class="row mt-4">
        <div class="col-lg-8">
            <div class="parts-wrapper table-scroll">
                <table class="table table-parts table-sm table-bordered table-hover">
                    <thead class="table-light">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Выгрузил</th>
                        <th scope="col">Файл</th>
                        <th scope="col">Когда</th>
                    </tr>
                    </thead>
                    <tbody class="parts">
                    @foreach($data as $object)
                        <tr>
                            <td><a href="/admin/history/show/{{$object->id}}"> {{ $object->id }}</a></td>
                            <td><a href="/admin/history/show/{{$object->id}}">{{ $object->user->name }}</a></td>
                            <td>
                                <a href="{{ $object->full_path }}" download="Скачать файл">{{ $object->file->name }}</a>
                            </td>
                            <td>{{ $object->created_at }}</td>
                        </tr>
                    @endforeach
                    @if($data->count() === 0)
                        <tr>
                            <td colspan="4">
                                История пуста
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>

                {{ $data->links('layouts.pagination') }}
            </div>
        </div>
    </div>
@endsection
