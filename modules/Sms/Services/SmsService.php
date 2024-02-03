<?php

namespace Modules\Sms\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\Orders\Models\Orders;
use Modules\Sms\Models\Sms;
use Modules\Sms\Models\SmsTemplates;
use Illuminate\Http\Request;

class SmsService
{
    public function create($fields): int
    {
        $request = app(Request::class);
        $send_all = $request->post('sendAll');
        $filter = $request->post('filter');
        //Если выбран вариант рассылки всем - найдем подходящие под условия фильтра заказы
        $sms = Sms::create($fields);
        if ($send_all) {
            $ids = Orders::query()->filter($filter)->pluck('id');
        } else {
            $ids = $request->post('ids');
        }
        //Создадим связанные записи
        $sms->orders()->sync($ids);
        return $sms->id;
    }

    public static function getTemplates(): Collection
    {
        $data = (new SmsTemplates())->all();
        return collect(TemplateSelectResource::collection($data));
    }
}
