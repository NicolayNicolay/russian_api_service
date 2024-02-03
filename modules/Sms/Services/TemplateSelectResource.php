<?php

declare(strict_types=1);

namespace Modules\Sms\Services;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TemplateSelectResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'value' => $this->id,
            'text'  => $this->text,
        ];
    }
}
