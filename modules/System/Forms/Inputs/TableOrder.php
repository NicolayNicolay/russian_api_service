<?php

namespace Modules\System\Forms\Inputs;

class TableOrder extends Input
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
        $arr['type'] = 'order';

        return $arr;
    }
}
