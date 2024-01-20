<?php

declare(strict_types=1);

namespace Modules\Seasons\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Orders\Services\OrderService;
use Modules\Seasons\Forms\SeasonForm;
use Modules\Seasons\Models\Seasons;

class SeasonController extends Controller
{
    private $season_id = 0;

    private Seasons $seasons;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Seasons $seasons)
    {
        $this->middleware('auth');
        $route = Route::current();
        if ($route !== null && ! empty($route->parameters['id'])) {
            $this->season_id = (int) $route->parameters['id'];
        }
        $this->seasons = $seasons;
    }

    public function index(): Factory | View | Application
    {
        return view('admin.seasons.list');
    }

    public function add(): Factory | View | Application
    {
        return view('admin.seasons.add');
    }

    public function edit(int $id = 0): Factory | View | Application
    {
        return view('admin.seasons.edit', [
            'id' => $id,
        ]);
    }

    /**
     * Метод для получения списка сезонов
     *
     * @param Request $request
     * @return mixed
     */
    public function seasonList(Request $request): mixed
    {
        $per_page = config('app.per_page');
        return Seasons::orderBy('sort')->paginate($per_page);
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $messages = [
            'required' => 'Поле ":attribute" обязательно для заполнения',
            'unique'   => 'Данный сезон уже создан.',
        ];

        $check_id = $request->input('id', 0);
        $season_form = new SeasonForm($check_id);
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
            $season = (new Seasons())->findOrFail($check_id);
            $season->update($fields);
        } else {
            $season = (new Seasons())->create($fields);
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
        $season = $this->seasons->find($id);
        $season?->delete();
    }

    /**
     * Получение параметров формы
     *
     * @return array
     */
    public function getFormParams(): array
    {
        $season_form = new SeasonForm($this->season_id);
        $form['form'] = $season_form->form()->getArray();
        $form['action'] = '/admin/seasons/store';
        if (! empty($this->season_id)) {
            $form['title'] = 'Редактировать сезон';
            $form['type'] = 'edit';
        } else {
            $form['title'] = 'Добавить сезон';
            $form['type'] = 'add';
            $form['form'] = $season_form->form()->getArray();
        }

        return $form;
    }
}
