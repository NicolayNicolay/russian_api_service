<?php

declare(strict_types=1);

namespace Modules\Sms\Forms;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Modules\Sms\Models\SmsTemplates;
use Modules\System\Forms\AbstractForm;
use Modules\System\Forms\Inputs\InputText;
use Modules\System\Forms\Inputs\InputTextarea;

class TemplateForm extends AbstractForm
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
        $this->entity_data = (new SmsTemplates())->find($this->entity_id);
    }

    /**
     * Метод собирает массив формы для создания/редактирования
     *
     * @param int $id
     * @return $this
     */
    public function form(int $id = 0): self
    {
        $this->form = [
            'id'     => $this->entity_id,
            'name'   => (new InputText())
                ->setLabel('Название')
                ->setValidationRule((empty($this->entity_data) ? 'required|unique:sms_templates,name' : 'required|unique:sms_templates,name,' . $this->entity_data->id . ',id'))
                ->setValue($this->getFieldValue('name'))
                ->setNameAndId('name')
                ->get(),
            'text'   => (new InputTextarea())
                ->setLabel('Текст')
                ->setValidationRule('required')
                ->setValue($this->getFieldValue('text'))
                ->setNameAndId('text')
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
     * @return mixed|string
     */
    public function getFieldsDefinition(): mixed
    {
        return config('sms.templates.fields');
    }

    public function validationAttributes()
    {
        return config('sms.templates.attr');
    }

    /**
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
