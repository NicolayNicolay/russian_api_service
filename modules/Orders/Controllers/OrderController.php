<?php

declare(strict_types=1);

namespace Modules\Orders\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Orders\Forms\FilesForm;
use Modules\Orders\Models\Orders;
use Modules\Orders\Services\OrderService;
use Modules\Orders\Services\OrderUploadService;
use Modules\Orders\Services\UnloadService;
use PhpOffice\PhpSpreadsheet\Writer\Exception;

class OrderController extends Controller
{
    private $order_id = 0;
    private Orders $orders;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Orders $orders)
    {
        $this->middleware('auth');
        $route = Route::current();
        if ($route !== null && ! empty($route->parameters['id'])) {
            $this->order_id = (int) $route->parameters['id'];
        }
        $this->orders = $orders;
    }

    public function index(): Factory | View | Application
    {
        return view('admin.orders.list', [
            'seasons'  => OrderService::getSeasons(),
            'statuses' => OrderService::getStatuses(),
        ]);
    }

    public function add(): Factory | View | Application
    {
        return view('admin.orders.add');
    }

    public function ordersList(Request $request)
    {
        $per_page = $request->post('counts') ?? config('app.per_page');
        $filter = $request->post('filter');
        $sorting = $request->post('sorting');
        $states = config('tracking.payment_state');
        $states[] = config('tracking.avail_payment');
        return [
            'orders'        => $this->orders
                ->filter($filter)
                ->with('season', 'userCreated', 'userUpdated')
                ->ordersSort($sorting)
                ->paginate($per_page),
            'payment_state' => $states,
        ];
    }

    public function getOrderFormParams(): array
    {
        return (new FilesForm())->form()->getArray();
    }

    public function storeOrder(Request $request): array
    {
        return (new OrderUploadService($request))->save();
    }

    public function show(): Application | Factory | View
    {
        return view('admin.orders.show', [
            'data' => $this->orders->findOrFail($this->order_id),
        ]);
    }

    public function destroy(int $id): void
    {
        $season = $this->orders->find($id);
        $season?->delete();
    }

    /**
     * @throws Exception
     */
    public function getOrdersXls(Request $request): array
    {
        $filter = $request->post('filter');
        $result = $this->orders->filter($filter)->with('season', 'userCreated', 'userUpdated')->get();
        return (new UnloadService($result))->generate();
    }
}
