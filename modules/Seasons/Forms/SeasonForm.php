<?php

declare(strict_types=1);

namespace Modules\Seasons\Forms;

use Modules\Files\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Modules\Seasons\Models\Seasons;
use Modules\System\Forms\AbstractForm;
use Modules\System\Forms\Inputs\InputCheckbox;
use Modules\System\Forms\Inputs\InputText;

class SeasonForm extends AbstractForm
{
    /** @var array Форма */
    public array $form = [];

    /** @var array Правила валидации формы */
    public array $validation_rules = [];

    /** @var array Заполненные поля */
    public array $completed_fields = [];

    public function __construct(int $entity_id = 0)
    {
        $this->entity_id = $entity_id;
        $this->getEntity();
    }

    protected function getEntity(): void
    {
        $this->entity_data = (new Seasons())->find($this->entity_id);
    }

    /**
     * Метод собирает массив формы для создания/редактирования
     *
     * @param int $season_id
     * @return $this
     */
    public function form(int $season_id = 0): self
    {
        $this->form = [
            'id'     => $this->entity_id,
            'active' => (new InputCheckbox())
                ->setLabel('Активность')
                ->setNameAndId('active')
                ->setValue($this->getFieldValue('active') != false || $this->getFieldValue('active') != null)
                ->get(),
            'name'   => (new InputText())
                ->setLabel('Название')
                ->setValidationRule((empty($this->entity_data) ? 'required|unique:seasons,name' : 'required|unique:seasons,name,' . $this->entity_data->id . ',id'))
                ->setValue($this->getFieldValue('name'))
                ->setNameAndId('name')
                ->get(),
            'sort'   => (new InputText())
                ->setLabel('Сортировка')
                ->setValidationRule('required')
                ->setValue($this->getFieldValue('sort') ? $this->getFieldValue('sort') : 500)
                ->setNameAndId('sort')
                ->get(),

        ];
        // Добавляем дополнительные служебные поля для существующих объектов
        if (is_object($this->entity_data)) {
            $this->form['id'] = $this->entity_data->id;
        }

        return $this;
    }

    /**
     * Метод получает значения из полей
     *
     * @param $field_name
     * @param $default_value
     * @return mixed|string
     */
    public function getFieldsDefinition(): mixed
    {
        return config('season.fields');
    }

    public function validationAttributes()
    {
        return config('season.attr');
    }

    /**
     * @throws ValidationException
     */
    public function validate($request, $rules, $message = []): array | MessageBag
    {
        $validator = Validator::make(
            data:             $this->getRequestFields($request),
            rules:            $rules,
            messages:         $message,
            customAttributes: $this->validationAttributes()
        );
        if ($validator->fails()) {
            return $validator->errors();
        }
        return [];
    }
}
