<?php

namespace Modules\System\Forms\Inputs;

/**
 * Class Input - абстрактный класс с обобщенными для всех полей методами
 */
abstract class Input
{
    public string $name = '';

    /** @var mixed */
    public $value = '';

    public string $label = '';

    /** @var mixed */
    public $id = '';

    public bool $read_only = false;

    public bool $access_denied = false;

    /** @var mixed */
    public $validation_rule = '';

    public array $custom_fields = [];

    public bool $required = false;

    public bool $disabled = false;

    /**
     * Устанавливаем свойства id и name с одинаковым значением
     *
     * @param string $value
     * @return self
     */
    public function setNameAndId(string $value): self
    {
        $this->name = $value;
        $this->id = $value;
        return $this;
    }

    public function setDisabled(bool $value): self
    {
        $this->disabled = $value;
        return $this;
    }

    /**
     * Устанавливаем свойства required и name с одинаковым значением
     *
     * @param bool $value
     * @return self
     */
    public function setRequired(bool $value): self
    {
        $this->required = $value;
        return $this;
    }

    /**
     * Имя поля
     *
     * @param string $value
     * @return self
     */
    public function setName(string $value): self
    {
        $this->name = $value;
        return $this;
    }

    /**
     * Имя поля
     *
     * @param $value
     * @return self
     */
    public function setId($value): self
    {
        $this->id = $value;
        return $this;
    }

    /**
     * Label поля
     *
     * @param string $value
     * @return self
     */
    public function setLabel(string $value): self
    {
        $this->label = $value;
        return $this;
    }

    /**
     * Значение поля
     *
     * @param mixed $value
     * @return self
     */
    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @param callable $read_only
     * @return self
     */
    public function setReadOnly(callable $read_only): self
    {
        $this->read_only = $read_only();
        return $this;
    }

    /**
     * Пометка недоступности поля. Если флаг установлен, то поле класс должен возвращать пустой массив
     *
     * @param callable $access_denied
     * @return self
     */
    public function setAccessDenied(callable $access_denied): self
    {
        $this->access_denied = $access_denied();
        return $this;
    }

    /**
     * Добавление правила валидации
     *
     * @param string|array $value
     * @return self
     */
    public function setValidationRule($value): self
    {
        $this->validation_rule = $value;
        return $this;
    }

    /**
     * Метод устанавливает значение кастомного поля.
     * Имена кастомных полей не заменяют собой имена основных, а при совпадении будут использоваться основные поля
     *
     * @param string $field_name
     * @param $value
     * @return $this
     */
    public function setCustomField(string $field_name, $value): self
    {
        $this->custom_fields[$field_name] = $value;
        return $this;
    }

    /**
     * Метод должен реализовать сбор всех данных и подготовить поле для выдачи
     *
     * @return mixed
     */
    public function get()
    {
        return array_merge(
            $this->custom_fields,
            [
                'id'              => $this->id,
                'name'            => $this->name,
                'label'           => $this->label,
                'value'           => $this->value,
                'read_only'       => $this->read_only,
                'validation_rule' => $this->validation_rule,
                'disabled'        => $this->disabled,
            ]
        );
    }
}
