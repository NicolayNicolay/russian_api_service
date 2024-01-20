<?php

declare(strict_types=1);

namespace Modules\System\Forms\Inputs;

class InputTextHidden extends Input
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
        $arr['type'] = 'textHidden';

        return $arr;
    }
}
