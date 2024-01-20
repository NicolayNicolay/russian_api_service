<?php

declare(strict_types=1);

namespace Modules\System\Resources;

interface ResourceInterface
{
    public function toArray(): array;
}
