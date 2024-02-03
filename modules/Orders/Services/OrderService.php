<?php

namespace Modules\Orders\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use LapayGroup\RussianPost\Entity\Order;
use Modules\Orders\Models\Orders;
use Modules\OrderTracking\Models\PostResponse;
use Modules\Seasons\Models\Seasons;
use Modules\Seasons\Resources\SeasonSelectResource;
use Modules\Seasons\Resources\SelectResource;

class OrderService
{
    public function createOrUpdate(array $fields, $user_id = 0): void
    {
        // Обновляем объект
        $order = (new Orders())->where('code', $fields['code'])->where('season_id', $fields['season_id']);
        $user_id = $user_id != 0 ? $user_id : \Auth::user()->id;
        if ($order->count()) {
            $order->update($fields);
        } else {
            $fields['created_user_id'] = $user_id;
            $fields['last_status_updated'] = Carbon::now()->format('Y-m-d H:i:s');
            (new Orders())->create($fields);
        }
    }

    public function createsOrUpdates(array $fields): void
    {
        // Обновляем объекты
        Orders::upsert($fields, ['code', 'season_id'], config('orders.fields'));
    }

    public static function getSeasons(): Collection
    {
        $discounts = (new Seasons())->all();
        return collect(SelectResource::collection($discounts));
    }

    public static function getStatuses(): Collection
    {
        $statuses = (new PostResponse())
            ->orderBy('stage', 'asc')
            ->orderBy('state', 'asc')
            ->get()
            ->toArray();

        return collect(array_values($statuses));
    }
}
