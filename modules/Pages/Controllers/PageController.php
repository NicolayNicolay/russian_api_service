<?php

namespace Modules\Pages\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

class PageController
{
    public function index(): Redirector | Application | RedirectResponse
    {
        return redirect('/admin');
    }
}
