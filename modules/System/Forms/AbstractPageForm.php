<?php

declare(strict_types=1);

namespace Modules\System\Forms;

use Modules\Files\Models\AboutPage;
use Modules\Files\Models\AboutPagePhotos;
use Modules\Files\Models\TmpPhoto;
use Modules\Files\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use JetBrains\PhpStorm\Pure;

abstract class AbstractPageForm extends AbstractForm
{
    public User $user;

    public AboutPagePhotos $photos;

    public TmpPhoto $tmpPhoto;

    public function __construct(int $entity_id = 0)
    {
        $this->entity_id = $entity_id;
        $user = \Auth::user();
        if ($user) {
            $this->user = $user;
        }
        $this->photos = (new AboutPagePhotos());
        $this->tmpPhoto = (new TmpPhoto());
        $this->getEntity();
    }

    protected function getEntity(): void
    {
        $this->entity_data = (new AboutPage())->find($this->entity_id);
    }

    public function collectValidationRules(mixed $form): void
    {
        foreach ($form['sections'] as $items) {
            foreach ($items['items'] as $index => $item) {
                if (! is_array($item)) {
                    continue;
                }
                if (Arr::has($item, ['validation_rule', 'id', 'name'])) {
                    if (! empty($item['validation_rule'])) {
                        $this->validation_rules[$items['id'] . '_' . $index] = explode("|", $item['validation_rule']);
                    }
                }
            }
        }
    }

    protected function getFieldAboutValue(mixed $field_name, string $name, $default_value = ''): mixed
    {
        if (empty($this->entity_data)) {
            return $default_value;
        } else {
            if ($this->entity_data && $this->entity_data->$field_name) {
                if (is_string($this->entity_data->$field_name)) {
                    if (array_key_exists($name, json_decode($this->entity_data->$field_name, true))) {
                        return json_decode($this->entity_data->$field_name, true)[$name];
                    }
                } else {
                    if (array_key_exists($name, $this->entity_data->$field_name)) {
                        return $this->entity_data->$field_name[$name];
                    }
                }
            }
        }
        return $default_value;
    }

    public function getRequestFieldsInputs($request): array
    {
        $result = [];
        foreach ($request->all()['sections'] as $items) {
            foreach ($items['items'] as $index => $item) {
                if (! is_array($item)) {
                    continue;
                }
                if ($index === 'multiinputs') {
                    $flag = false;
                    foreach ($item['value'] as $value) {
                        if ($value['title'] && $value['value']) {
                            $result[$items['id'] . '_' . $index] = 1;
                            $flag = true;
                        }
                    }
                    if (! $flag) {
                        $result[$items['id'] . '_' . $index] = null;
                    }
                    continue;
                }
                $result[$items['id'] . '_' . $index] = $item['value'];
            }
        }
        return $result;
    }

    public function getRequestFields($request): array
    {
        $result = [];
        foreach ($request->all()['sections'] as $items) {
            foreach ($items['items'] as $index => $item) {
                if (! is_array($item)) {
                    continue;
                }
                if ($index === 'multiinputs') {
                    $res = [];
                    foreach ($item['value'] as $value) {
                        if ($value['title'] && $value['value']) {
                            array_push($res, $value);
                        }
                    }
                    $result[$items['id']][$index] = $res;
                    continue;
                }
                $result[$items['id']][$index] = $item['value'];
            }
        }
        return $result;
    }

    public function getRequestFiles($request): array
    {
        $result = [];
        foreach ($request->all()['photo_sections'] as $key => $item) {
            $result[$key] = $item;
        }
        return $result;
    }

    #[Pure]
    public function getFieldsDefinition()
    {
        return config('about.fields');
    }

    #[Pure]
    public function validationAttributes()
    {
        return config('about.attr');
    }

    public function updateFormFiles($request, $id)
    {
        foreach ($request as $fields) {
            if ($fields['items']['new_files']) {
                foreach ($fields['items']['new_files'] as $field) {
                    $contents = Storage::get($field['response']['path']);
                    Storage::put('public/about_section/' . $id . '/' . $field['name'], $contents);
                    $this->photos->create([
                                              'path'       => '/storage/about_section/' . $id . '/' . $field['name'],
                                              'page_id'    => $id,
                                              'section_id' => $fields['section'],
                                          ]);
                    $tmp = $this->tmpPhoto->find($field['response']['id']);
                    $tmp?->delete();
                }
            }
        }
    }

    /**
     * Выполняем валидацию данных формы
     */
    public function validate($request, $rules)
    {
        $validator = Validator::make(
            data:             $this->getRequestFieldsInputs($request),
            rules:            $rules,
            customAttributes: $this->validationAttributes()
        );
        $validator->validated();
    }
}
