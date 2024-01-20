<?php

namespace Modules\System\Forms\Inputs;

class MultiInputs extends Input
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
        $arr['type'] = 'multiinputs';

        return $arr;
    }
}
