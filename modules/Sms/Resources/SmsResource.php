<?php

declare(strict_types=1);

namespace Modules\Sms\Resources;

use Carbon\Carbon;
use Modules\System\Resources\AbstractResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SmsResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'text'        => $this->text,
            'status'      => $this->status,
            'status_name' => $this->status_name,
            'class'       => $this->status_class,
            'phones'      => $this->getPhonesTable(),
        ];
    }

    public function getPhonesTable(): array
    {
        $text = $this->text;
        $replace_makro = config('sms.makro_replace');
        $res = [];
        foreach ($this->orders as $order) {
            $tmp_text = $text;
            foreach ($replace_makro as $key => $item) {
                if (strripos($tmp_text, $key)) {
                    if ($order[$item]) {
                        $tmp_text = str_replace($key, $order[$item], $tmp_text);
                    } else {
                        $tmp_text = str_replace($key, '', $tmp_text);
                    }
                }
            }
            $res[] = [
                'id'    => $order->id,
                'text'  => $tmp_text,
                'phone' => $order->phone_relatives,
            ];
        }
        return $res;
    }
}
