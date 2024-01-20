<?php

declare(strict_types=1);

namespace Modules\History\Resources;

use Carbon\Carbon;
use Modules\System\Resources\AbstractResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends AbstractResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'         => $this->data->id,
            'user'       => $this->data->user,
            'file'       => $this->data->file,
            'status'     => $this->data->status,
            'processed'  => $this->data->processed,
            'count'      => $this->data->count_string,
            'season'     => $this->data->season,
            'start_work' => Carbon::createFromFormat('Y-m-d H:i:s', $this->data->created_at)->format('d-m-Y H:i:s')
        ];
    }
}
