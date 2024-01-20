<?php

namespace Modules\System\Forms\Inputs;

class Autocomplete extends Input
{
    public string $source_url = '';

    /** @var mixed */
    public $current_item = [];

    /**
     * @param string $value
     * @return $this
     */
    public function setSourceUrl(string $value): self
    {
        $this->source_url = $value;
        return $this;
    }

    /**
     * @param callable $current_item
     * @return Autocomplete
     */
    public function setCurrentItem(callable $current_item): self
    {
        $this->current_item = $current_item();
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
        return array_merge(
            $arr,
            [
                'type'            => 'text',
                'source_url'      => $this->source_url,
                'current_item'    => $this->current_item,
                'validation_rule' => $this->validation_rule,
            ]
        );
    }
}
