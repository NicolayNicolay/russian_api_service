<?php

namespace Modules\Dashboard\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Orders\Models\Orders;
use Modules\Orders\Services\OrderService;

class DashboardController
{
    private Orders $orders;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Orders $orders)
    {
        $this->orders = $orders;
    }

    public function index()
    {
        $seasons = (new OrderService())->getSeasons();
        return view('admin.dashboard.dashboard', [
            'seasons' => $seasons,
        ]);
    }

    public function getList(Request $request)
    {
        $filter = $request->post('filter');
        if ($filter['date_start']) {
            $date = $filter['date_start'];
            unset($filter['date_start']);
            $filter['payment_date_start'] = $date;
        }
        if ($filter['date_end']) {
            $date = $filter['date_end'];
            unset($filter['date_end']);
            $filter['payment_date_end'] = $date;
        }
        $data['payments'] = [
            'total'         => $this->orders
                ->filter(array_merge(['payment_state' => 3], $filter))
                ->sum('payment_sum'),
            'total_count'   => $this->orders
                ->paymentDashboard()
                ->filter($filter)
                ->count(),
            'index_344000'  => $this->orders
                ->filter(array_merge(['payment_state' => 3, 'payment_place_index' => '344000'], $filter))
                ->sum('payment_sum'),
            'index_344999'  => $this->orders
                ->filter(array_merge(['payment_state' => 3, 'payment_place_index' => '344999'], $filter))
                ->sum('payment_sum'),
            'index_another' => $this->orders
                ->filter(array_merge(['payment_state' => 3, 'not_payment_place_index' => ['344999', '344000']], $filter))
                ->sum('payment_sum'),
            'reception'     => $this->orders
                ->filter(array_merge(['payment_state' => 1], $filter))
                ->sum('payment_sum'),
            'another_state' => $this->orders
                ->filter(array_merge(['not_payment_state' => [1, 3]], $filter))
                ->sum('payment_sum'),
        ];
        if (array_key_exists('payment_date_start', $filter)) {
            $date = $filter['payment_date_start'];
            unset($filter['payment_date_start']);
            $filter['date_operation_start'] = $date;
        }
        if (array_key_exists('payment_date_end', $filter)) {
            $date = $filter['payment_date_end'];
            unset($filter['payment_date_end']);
            $filter['date_operation_end'] = $date;
        }
        $data['not_payment'] = [
            'total'   => $this->orders
                ->filter(array_merge(['not_payment_state_and_null' => [3]], $filter))
                ->count(),
            'on_post' => $this->orders
                ->whereIn('status', ['8_2_0', '8_25_0', '8_24_0'])
                ->filter($filter)
                ->count(),
            'on_way'  => $this->orders
                ->whereNotIn('status', ['8_2_0', '8_25_0', '8_24_0'])
                ->where('status', 'like', '8_%')
                ->filter($filter)
                ->count(),
        ];
        $data['not_payment']['on_another'] = $data['not_payment']['total'] - ($data['not_payment']['on_post'] + $data['not_payment']['on_way']);
        if ($this->orders->filter($filter)->count() == 0) {
            $data['percentage'] = 0;
        } else {
            $data['percentage'] = round(($data['payments']['total_count'] / $this->orders->filter($filter)->count()) * 100, 2);
        }
        return $data;
    }
}
