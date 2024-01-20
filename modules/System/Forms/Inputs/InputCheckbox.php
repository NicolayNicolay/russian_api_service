<?php

namespace Modules\System\Forms\Inputs;

class InputCheckbox extends Input
{
    /**
     * @inheritDoc
     */
    public function get(): array
    {
        if ($this->access_denied) {
            return [];
        }

        $arr = parent::get();
        $arr['type'] = 'checkbox';

        return $arr;
    }
}
