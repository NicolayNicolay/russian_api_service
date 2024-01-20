<?php

declare(strict_types=1);

namespace Modules\System\Forms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

abstract class AbstractForm
{
    /** @var array Форма */
    public array $form = [];

    /** @var array Правила валидации формы */
    public array $validation_rules = [];

    /** @var array Заполненные поля */
    public array $completed_fields = [];

    /** @var int Идентификатор сущности, с которой ведется работа */
    public int $entity_id = 0;

    /** @var Model|null Данные сущности */
    public ?Model $entity_data = null;

    /**
     * Метод должен возвращать массив, содержащий форму
     *
     * @return self
     */
    abstract public function form(): AbstractForm;

    /**
     * Метод получает значения из полей
     *
     * @param string $field_name
     * @param $default_value
     * @return mixed
     */
    protected function getFieldValue(string $field_name, $default_value = ''): mixed
    {
        if (empty($this->entity_data)) {
            return $default_value;
        }
        $fields = $this->getFieldsDefinition();
        $key = array_search($field_name, $fields, true);
        if (! empty($key)) {
            return $this->entity_data->$key;
        }
        return $default_value;
    }

    /**
     * Метод для получения формы
     *
     * @return array
     */
    public function getArray(): array
    {
        return $this->form;
    }

    /**
     * Метод возвращает правила валидации для формы
     *
     * @return array
     */
    public function getValidationRules(): array
    {
        $this->collectValidationRules($this->form);
        return $this->validation_rules;
    }

    /**
     * Метод собирает правила валидации из формы
     *
     * @param $form
     */
    public function collectValidationRules(array $form): void
    {
        foreach ($form as $item) {
            if (! is_array($item)) {
                continue;
            }
            if (Arr::has($item, ['validation_rule', 'id', 'name'])) {
                if (! empty($item['validation_rule'])) {
                    $this->validation_rules[$item['name']] = $item['validation_rule'];
                }
            } else {
                $this->collectValidationRules($item);
            }
        }
    }
    public function getFieldsDefinition()
    {
    }
    /**
     * Метод собирает имена полей в которых хранятся значения в форме
     *
     * @param array $form
     * @return void
     */
    public function getFieldsNames(array $form): void
    {
        foreach ($form as $item) {
            if (! is_array($item)) {
                continue;
            }
            if (Arr::has($item, ['value', 'id', 'name'])) {
                $this->completed_fields[$item['name']] = $item['value'];
            } else {
                $this->getFieldsNames($item);
            }
        }
    }
    public function getRequestFields($request): array
    {
        $result = [];
        foreach ($request->all() as $item) {
            if (! is_array($item)) {
                continue;
            }
            $result[$item['name']] = $item['value'];
        }
        return $result;
    }
    /**
     * Метод собирает значения полей из запроса
     *
     * @return array
     */
    public function getFieldsFromRequest(): array
    {
        $this->getFieldsNames($this->form);

        /** @var Request $request */
        $request = app(Request::class);

        $fields_completed = [];

        $fields = $this->getFieldsDefinition();
        // Собираем поля из запроса
        foreach ($fields as $field => $path) {
            if (array_key_exists($path, $this->completed_fields)) {
                $fields_completed[$field] = $request->input($path)['value'];
            }
        }
        return $fields_completed;
    }
}
