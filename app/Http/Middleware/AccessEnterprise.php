<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AccessEnterprise
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next): Response | RedirectResponse
    {
        $remote_addr = $_SERVER['REMOTE_ADDR'] ?? '';
        $is_admin = ($remote_addr === config('app.admin_ip') || $remote_addr === config('app.homestead_ip'));
        if (! $is_admin) {
            return redirect(RouteServiceProvider::HOME);
        }

        return $next($request);
    }
}
