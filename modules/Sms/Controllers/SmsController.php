<?php

declare(strict_types=1);

namespace Modules\Sms\Controllers;

use App\Http\Controllers\Controller;
use igorbunov\Smsc\Config\Config;
use igorbunov\Smsc\SmscJsonApi;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Modules\Sms\Forms\SmsForm;
use Modules\Sms\Jobs\SendSms;
use Modules\Sms\Models\Sms;
use Modules\Sms\Resources\SmsResource;
use Modules\Sms\Services\SmsService;

class SmsController extends Controller
{

    private Sms $sms;

    public int $sms_id = 0;

    public SmsForm $form;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Sms $sms)
    {
        $route = Route::current();
        if ($route !== null && ! empty($route->parameters['id'])) {
            $this->sms_id = (int) $route->parameters['id'];
        }
        $this->middleware(
            function ($request, $next) {
                $this->form = new SmsForm();
                return $next($request);
            }
        );
        $this->sms = $sms;
    }

    public function index(): Factory | View | Application
    {
        return view('admin.sms.list.list');
    }

    public function show(): Factory | View | Application
    {
        return view(
            'admin.sms.list.show',
            ['id' => $this->sms_id]
        );
    }

    /**
     * Метод для получения списка сезонов
     *
     * @param Request $request
     * @return mixed
     */
    public function apiList(Request $request): mixed
    {
        $per_page = config('app.per_page');
        return Sms::orderBy("id", 'desc')->paginate($per_page);
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request, SmsService $service)
    {
        $messages = [
            'required' => 'Поле ":attribute" обязательно для заполнения',
            'max'      => 'Максимальное кол-во символов для поля ":attribute" - 420',
        ];
        // Выполняем валидацию полей
        $validation_rules = $this->form->form()->getValidationRules();
        $errors = $this->form->validate($request, $validation_rules, $messages);
        if ($errors) {
            return \Response::json(
                [
                    'code' => 401,
                    'msg'  => $errors,
                ],
                401
            );
        }
        // Получаем данные формы
        $fields = $this->form->getRequestFields($request);
        $id = $service->create($fields);
        $request->session()->flash('info', 'Рассылка для выбранных пользователей добавлена успешно!');
        $request->session()->flash('alert', 'success');
        return [
            'id' => $id,
            'success' => true,
        ];
    }

    public function getData()
    {
        $element = $this->sms->find($this->sms_id);
        return collect(new SmsResource($element));
    }

    public function success(): void
    {
        $element = Sms::query()->find($this->sms_id);
        SendSms::dispatch($element);
        $element->update([
                             'status' => true,
                         ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(): void
    {
        $element = $this->sms->find($this->sms_id);
        $element->orders()->detach();
        $element?->delete();
    }

    public function destroyElement(Request $request)
    {
        $id = $request->post('id');
        $object_id = $request->post('orderId');
        $element = $this->sms->find($id);
        $element->orders()->detach([$object_id]);
    }

    /**
     * Получение параметров формы
     *
     * @return array
     */
    public function getFormParams(): array
    {
        return $this->form->form()->getArray();
    }
}
