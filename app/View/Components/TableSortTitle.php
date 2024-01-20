<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\View\Component;

class TableSortTitle extends Component
{
    public string $url;
    public string $orderDirection;
    public bool $isActive;

    public function __construct(
        public string $name,
        public string $orderBy,
        public bool $isDefault = false
    ) {
        $request = app(Request::class);
        $this->orderDirection = $request->input('orderDirection', 'asc');
        $this->isActive = $request->input('orderBy') === $this->orderBy || (! $request->has('orderBy') && $this->isDefault);

        $this->url = $request->fullUrlWithQuery(
            [
                'orderBy'        => $this->orderBy,
                'orderDirection' => $this->orderDirection === 'asc' ? 'desc' : 'asc',
            ]
        );
    }

    public function render(): View
    {
        return view('components.table-sort-title');
    }
}
