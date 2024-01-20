<?php

declare(strict_types=1);

namespace Modules\Orders\Resources;

use Modules\System\Resources\AbstractResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnloadResource extends AbstractResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            $this->data->season['name'],
            $this->data->lot_number,
            $this->data->code,
            $this->data->fio,
            $this->data->index,
            $this->data->geo,
            $this->data->district,
            $this->data->address,
            $this->data->fio_relatives,
            $this->data->phone_relatives,
            $this->data->track . ' ',
            $this->data->status_name ? $this->data->status_name['stage'] : '',
            $this->data->status_name ? $this->data->status_name['state'] : '',
            $this->data->date_operation,
            $this->data->payment_status_name ? $this->data->payment_status_name['name'] : '',
            $this->data->payment_sum,
            $this->data->payment_date,
            $this->data->payment_place_index,
            $this->data->created_at,
            $this->data->updated_at,
        ];
    }
}
