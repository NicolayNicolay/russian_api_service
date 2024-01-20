<?php

namespace Modules\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Users\Models\User;

class AdminRegisterController extends Controller
{
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect(route('admin.index'));
        }
        return view('admin.register');
    }

    public function save(Request $request)
    {
        $rules = [
            'name'     => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:4',
        ];

        $customMessages = [
            'required' => 'Поле :attribute является обязательным.',
            'email'    => 'Поле :attribute должно содержать email.',
            'min'      => 'Пароль должен содержать минимум 4 символа.',
        ];

        $validator = Validator::make($request->post(), $rules, $customMessages);

        if ($validator->fails()) {
            return \Response::json(
                [
                    'code'     => 401,
                    'redirect' => route(
                        'admin.register'
                    ),
                    'msg'      => $validator->errors(),
                ],
                401
            );
        }

        if (User::where('email', $validator->valid()['email'])->exists()) {
            return \Response::json(
                [
                    'code'     => 401,
                    'redirect' => route(
                        'admin.register'
                    ),
                    'msg'      => ['email' => ['Такой пользователь уже существует']],
                ],
                401
            );
        }

        $user = User::create($validator->valid());

        if ($user) {
            auth()->login($user, $request->get('remember'));

            return \Response::json(
                [
                    'code'     => 200,
                    'redirect' => route(
                        'admin.index'
                    ),
                    'msg'      => ['success' => ['Пользователь успешно зарегистрирован']],
                ],
                200
            );
        }

        return \Response::json(
            [
                'code'     => 401,
                'redirect' => route(
                    'admin.register'
                ),
                'msg'      => ['error' => ['Произошла ошибка при регистрации пользователя']],
            ],
            401
        );
    }
}
