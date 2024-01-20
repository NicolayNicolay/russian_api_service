<?php

namespace Modules\History\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Modules\History\Models\HistoryUpload;
use Modules\History\Resources\HistoryResource;
use Modules\Orders\Models\Orders;

class HistoryController extends Controller
{
    private $history_id = 0;
    private HistoryUpload $histories;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(HistoryUpload $histories)
    {
        $this->middleware('auth');
        $route = Route::current();
        if ($route !== null && ! empty($route->parameters['id'])) {
            $this->history_id = (int) $route->parameters['id'];
        }
        $this->histories = $histories;
    }

    public function index(): Application | Factory | View
    {
        $per_page = config('app.per_page');
        return view('admin.history.list', [
            'data' => $this->histories->with('user', 'file')->paginate($per_page),
        ]);
    }

    public function indexAdmin(): Application | Factory | View
    {
        $per_page = config('app.per_page');
        return view('admin.historyAdmin.list', [
            'data' => $this->histories->with('user', 'file')->paginate($per_page),
        ]);
    }

    public function show(): Factory | View | Application
    {
        return view('admin.history.show', [
            'data' => $this->histories->with('user', 'file')->findOrFail($this->history_id),
        ]);
    }

    public function getData()
    {
        $res = $this->histories->with(['user'])->find($this->history_id);
        return collect((new HistoryResource($res))->toArray());
    }
}
