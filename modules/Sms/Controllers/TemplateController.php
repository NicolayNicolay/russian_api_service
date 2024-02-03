<?php

declare(strict_types=1);

namespace Modules\Sms\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Modules\Sms\Forms\TemplateForm;
use Modules\Sms\Models\SmsTemplates;

class TemplateController extends Controller
{
    private int $template_id = 0;

    private SmsTemplates $templates;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(SmsTemplates $templates)
    {
        $this->middleware('auth');
        $route = Route::current();
        if ($route !== null && ! empty($route->parameters['id'])) {
            $this->template_id = (int) $route->parameters['id'];
        }
        $this->templates = $templates;
    }


    public function index(): Factory | View | Application
    {
        return view('admin.sms.templates.list');
    }

    public function add(): Factory | View | Application
    {
        return view('admin.sms.templates.add');
    }

    public function edit(int $id = 0): Factory | View | Application
    {
        return view('admin.sms.templates.edit', [
            'id' => $id,
        ]);
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
        return SmsTemplates::paginate($per_page);
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $messages = [
            'required' => 'Поле ":attribute" обязательно для заполнения',
        ];
        $check_id = $request->input('id', 0);
        $season_form = new TemplateForm($check_id);
        $form = $season_form->form();
        // Выполняем валидацию полей
        $validation_rules = $form->getValidationRules();
        $errors = $form->validate($request, $validation_rules, $messages);
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
        $fields = $form->getFieldsFromRequest();
        if ($check_id) {
            // Обновляем сезон
            $season = (new SmsTemplates())->findOrFail($check_id);
            $season->update($fields);
            $request->session()->flash('info', 'Запись успешно обновлена!');
            $request->session()->flash('alert', 'success');
        } else {
            $season = (new SmsTemplates())->create($fields);
            $request->session()->flash('info', 'Запись успешно добавлена!');
            $request->session()->flash('alert', 'success');
        }
        return [
            'id'      => $season->id,
            'success' => true,
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     */
    public function destroy(int $id): void
    {
        $element = $this->templates->find($id);
        $element?->delete();
    }

    /**
     * Получение параметров формы
     *
     * @return array
     */
    public function getFormParams(): array
    {
        $template_form = new TemplateForm($this->template_id);
        $form['form'] = $template_form->form()->getArray();
        $form['action'] = '/admin/sms/templates/store';
        if (! empty($this->season_id)) {
            $form['title'] = 'Редактировать шаблон';
            $form['type'] = 'edit';
        } else {
            $form['title'] = 'Добавить шаблон';
            $form['type'] = 'add';
            $form['form'] = $template_form->form()->getArray();
        }
        $form['description'] = config('sms.description_makro');

        return $form;
    }
}
