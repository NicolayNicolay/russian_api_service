<?php

namespace Modules\Orders\Filters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Modules\Orders\Models\Orders;
use EloquentFilter\ModelFilter;

/**
 * @var Orders $builder
 */
class OrdersFilter extends ModelFilter
{
    public $relations = [];

    public function __construct($query, array $input = [], bool $relationsEnabled = true)
    {
        parent::__construct($query, $input, $relationsEnabled);
    }

    public function season(int $season): OrdersFilter
    {
        return $this->where('orders.season_id', $season);
    }

    public function code(string $code): OrdersFilter
    {
        return $this->where('orders.code', $code);
    }

    public function lotNumber(string $lot_number): OrdersFilter
    {
        return $this->where('orders.lot_number', $lot_number);
    }

    public function lotNumbers(string $numbers): OrdersFilter
    {
        $arr = explode(',', $numbers);
        $arr_range = [];
        $arr_numbers = [];
        foreach ($arr as $item) {
            if (str_contains($item, '-')) {
                $range = explode('-', preg_replace('/\s+/', '', $item));
                $arr_range[] = $range;
            } else {
                $arr_numbers[] = preg_replace('/\s+/', '', $item);
            }
        }
        return $this->where(
            function (Builder $query) use ($arr_range, $arr_numbers) {
                if (count($arr_range) > 0) {
                    foreach ($arr_range as $key => $range) {
                        if ($key === 0) {
                            $query->where(
                                function (Builder $query) use ($range) {
                                    $query->where('lot_number', '>=', $range[0])->where('lot_number', '<=', $range[1]);
                                }
                            );
                        } else {
                            $query->orWhere(
                                function (Builder $query) use ($range) {
                                    $query->where('lot_number', '>=', $range[0])->where('lot_number', '<=', $range[1]);
                                }
                            );
                        }
                    }
                }
                if (count($arr_numbers) > 0) {
                    if (count($arr_range) > 0) {
                        $query->orWhereIn('lot_number', $arr_numbers);
                    } else {
                        $query->whereIn('lot_number', $arr_numbers);
                    }
                }
            }
        );
    }

    public function geo(string $fio): OrdersFilter
    {
        return $this->where('orders.geo', 'like', '%' . $fio . '%');
    }

    public function address(string $fio): OrdersFilter
    {
        return $this->where('orders.address', 'like', '%' . $fio . '%');
    }

    public function payd(?bool $value): OrdersFilter
    {
        if ($value) {
            return $this->where('orders.payment_state', 3);
        } else {
            return $this->where('orders.payment_state', '!=', 3);
        }
    }

    public function fio(string $fio): OrdersFilter
    {
        return $this->where('orders.fio', 'like', '%' . $fio . '%');
    }

    public function phoneRelatives(string $phone_relatives): OrdersFilter
    {
        return $this->where('orders.phone_relatives', 'like', '%' . $phone_relatives . '%');
    }

    public function track(string $track): OrdersFilter
    {
        return $this->where('orders.track', $track);
    }

    public function status(string $status): OrdersFilter
    {
        return $this->where('orders.status', $status);
    }

    public function stage(string $stage): OrdersFilter
    {
        return $this->join('post_responses', 'orders.status', '=', 'post_responses.code')
            ->select('orders.*')
            ->where('post_responses.stage', $stage);
    }

    public function state(string $state): OrdersFilter
    {
        return $this->where('post_responses.state', $state);
    }

    public function createdAtStart(string $dateStart): OrdersFilter
    {
        return $this->where('created_at', '>=', $dateStart . ' 00:00:00');
    }

    public function createdAtEnd(string $dateEnd): OrdersFilter
    {
        return $this->where('created_at', '<=', $dateEnd . ' 00:00:00');
    }

    public function paymentDateStart(string $dateStart): OrdersFilter
    {
        return $this->where('payment_date', '>=', $dateStart . ' 00:00:00');
    }

    public function paymentDateEnd(string $dateEnd): OrdersFilter
    {
        return $this->where('payment_date', '<=', $dateEnd . ' 00:00:00');
    }

    public function paymentState(string $state): OrdersFilter
    {
        if ($state == 8) {
            return $this->whereNull('availability_payment')->where('status_availability_payment', true);
        } else {
            return $this->where('payment_state', $state);
        }
    }

    public function notPaymentState(array $indexes): OrdersFilter
    {
        return $this->whereNotIn('payment_state', $indexes);
    }

    public function notPaymentStateAndNull(array $indexes): OrdersFilter
    {
        return $this->where(
            function (Builder $query) use ($indexes) {
                $query->whereNotIn('payment_state', $indexes)->orWhere('payment_state', null);
            }
        );
    }

    public function paymentPlaceIndex(string $index): OrdersFilter
    {
        return $this->where('orders.payment_place_index', $index);
    }

    public function notPaymentPlaceIndex(array $indexes): OrdersFilter
    {
        return $this->whereNotIn('orders.payment_place_index', $indexes);
    }

    public function dateOperationStart(string $dateStart): OrdersFilter
    {
        return $this->where('date_operation', '>=', $dateStart . ' 00:00:00');
    }

    public function dateOperationEnd(string $dateEnd): OrdersFilter
    {
        return $this->where('date_operation', '<=', $dateEnd . ' 00:00:00');
    }

    public function updatedAtStart(string $dateStart): OrdersFilter
    {
        return $this->where('updated_at', '>=', $dateStart . ' 00:00:00');
    }

    public function updatedAtEnd(string $dateEnd): OrdersFilter
    {
        return $this->where('updated_at', '<=', $dateEnd . ' 00:00:00');
    }

    public function emptyTrack(): OrdersFilter
    {
        return $this->where('track', '');
    }
}
