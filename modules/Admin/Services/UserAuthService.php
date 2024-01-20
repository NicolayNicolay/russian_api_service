<?php

declare(strict_types=1);

namespace Modules\Admin\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Orders\Models\Orders;

class UserAuthService
{
    public function __construct(
        protected Request $request
    ) {
    }

    public function checkAdmin(): bool
    {
        return Auth::check() && Auth::user()->hasRole(['admin']);
    }
}
