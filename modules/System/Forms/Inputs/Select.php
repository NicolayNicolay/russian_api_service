<?php

declare(strict_types=1);

namespace Modules\System\Forms\Inputs;

class Select extends Input
{
    /** @var mixed */
    public $items = [];

    /**
     * Метод для установки элементов селекта
     *
     * @param callable $items_loader
     * @return Select
     */
    public function setItems(callable $items_loader): Select
    {
        $this->items = $items_loader();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(): array
    {
        if ($this->access_denied) {
            return [];
        }

        $arr = parent::get();
        $arr['type'] = 'select';
        $arr['items'] = $this->items;

        return $arr;
    }
}
