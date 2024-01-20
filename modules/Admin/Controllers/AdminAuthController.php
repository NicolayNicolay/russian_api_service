<?php

namespace Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Modules\Users\Models\User;

class AdminAuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/login';

    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect(route('admin.index'));
        }
        return view('admin.auth.login');
    }

    /**
     * Show the application loginprocess.
     *
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        // Validate the form data
        $validateFields = $this->validate($request, [
            'email'    => 'required|email',
            'password' => 'required|min:4',
        ]);

        if (! User::where('email', $validateFields['email'])->exists()) {
            return \Response::json(
                [
                    'code'     => 401,
                    'redirect' => route('admin.register'),
                    'msg'      => 'Пользователя с таким email не существует',
                ],
                401
            );
        }

        if (Auth::attempt($validateFields, $request->get('remember'))) {
            return \Response::json(
                [
                    'code'     => 200,
                    'redirect' => route('admin.index'),
                    'msg'      => 'Авторизация успешна',
                ],
                200
            );
        }

        return \Response::json(
            [
                'code'     => 401,
                'redirect' => route('admin.login'),
                'msg'      => 'Ошибка авторизации, проверьте правильность введенных данных',
            ],
            401
        );
    }

    /**
     * Show the application logout.
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function logout()
    {
        Auth::guard('web')->logout();
        return redirect(route('admin.login'));
    }
}
