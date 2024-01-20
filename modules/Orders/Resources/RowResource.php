<?php

declare(strict_types=1);

namespace Modules\Orders\Resources;

use Carbon\Carbon;
use Modules\System\Resources\AbstractResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RowResource extends AbstractResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $arr = [
            'code'                => $this->data[0],
            'lot_number'          => $this->getLotNumber(),
            'order_number'        => $this->getOrderNumber(),
            'season_id'           => $this->data[12],
            'fio'                 => $this->data[1] ?? '',
            'index'               => $this->data[2] ?? '',
            'geo'                 => $this->data[3] ?? '',
            'district'            => $this->data[4] ?? '',
            'address'             => $this->data[5] ?? '',
            'fio_relatives'       => $this->data[6] ?? '',
            'phone_relatives'     => $this->data[7] ?? '',
            'info'                => $this->data[8] ?? '',
            'notes'               => $this->data[9] ?? '',
            'track'               => $this->data[11] ?? '',
            'updated_user_id'     => $this->data[13] ?? 1,
            'last_status_updated' => Carbon::now()->format('Y-m-d H:i:s'),
        ];
        if ($this->data[13]) {
            $arr['created_user_id'] = $this->data[13] != 0 ? $this->data[13] : \Auth::user()->id;
        }
        return $arr;
    }

    //Получение номера заказа
    public function getOrderNumber(): string
    {
        //Перед обработкой убираем случайные пробелы
        $str = trim((string) $this->data[0]);
        return mb_substr($str, -4, 4);
    }

    //Получение номера партии
    public function getLotNumber(): string
    {
        //Перед обработкой убираем случайные пробелы
        $str = trim((string) $this->data[0]);
        $needLength = strlen($str) - 4;
        return mb_substr($str, 0, $needLength);
    }
}
