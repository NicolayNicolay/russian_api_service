<?php

declare(strict_types=1);

namespace Modules\System\Resources;

use Modules\System\Resources\ResourceInterface;

class AbstractResource implements ResourceInterface
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
