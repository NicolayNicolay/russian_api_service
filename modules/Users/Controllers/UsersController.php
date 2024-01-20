<?php

namespace Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\Roles\Models\Role;
use Modules\Roles\Services\RoleService;
use Modules\Users\Models\User;

class UsersController extends Controller
{
    public function index(Request $request, RoleService $roleService)
    {
        $perPage = (int) $request->input('count', config('app.per_page'));
        $users = User::leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
            ->leftJoin('roles', 'role_user.role_id', '=', 'roles.id')
            ->selectRAW('users.id as id, users.name as name, users.email as email,
                roles.name as role_name, roles.display_name as display_name')
            ->paginate($perPage);
        $filterShow = false;
        return view('admin.users.list', compact('users', 'filterShow'));
    }

    public function editView(Request $request, RoleService $roleService)
    {
        $user = User::where('id', $request->id)->first();

        $user->role_id = $roleService->getCurrentRoleID($user['id']);

        $roles = Role::get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function edit(Request $request, RoleService $roleService)
    {
        $form = $request->post('form');
        $roleID = $request->post('role_id');

        $rules = [
            'name'     => 'required',
            'email'    => 'required|email',
            'password' => 'nullable|min:4',
        ];

        $customAttributes = [
            'name' => 'ФИО',
            'email' => 'Email',
            'password' => 'Новый пароль',
        ];

        $customMessages = [
            'required' => 'Поле :attribute является обязательным.',
            'email'    => 'Поле :attribute должно содержать email.',
            'min'      => 'Пароль должен содержать минимум 4 символа.',
        ];

        $validator = Validator::make($form, $rules, $customMessages, $customAttributes);

        if ($validator->fails()) {
            return \Response::json(
                [
                    'code' => 401,
                    'msg'  => $validator->errors(),
                ],
                401
            );
        }

        $userValidData = $validator->valid();

        // Убираем пустые поля и id что бы исключить его не правильное обновление
        foreach ($userValidData as $key => $val) {
            unset($userValidData['id']);

            if (empty($val)) {
                unset($userValidData[$key]);
            }
        }

        if (! empty($userValidData['password'])) {
            $userValidData['password'] = Hash::make($userValidData['password']);
        }

        // Обновляем пользователя
        User::where('id', $form['id'])
            ->update($userValidData);
        $user = User::where('id', $form['id'])->first();

        // Обновляем роль пользователя
        if (!empty($roleID)) {
            $currentRoleID = $roleService->getCurrentRoleID($user['id']);
            $exist = $roleService->isAttached($user['id'], $roleID);
            if (!$exist && (int)$roleID !== $currentRoleID) {
                $user->detachRole($currentRoleID);
                $user->attachRole($roleID);
            }
        }

        return \Response::json(
            [
                'code' => 200,
                'user' => $user,
                'msg'  => ['success' => ['Данные пользователя успешно изменены']],
            ]
        );
    }

    public function addView()
    {
        $roles = Role::get();

        return view('admin.users.add', ['roles' => $roles]);
    }

    public function add(Request $request)
    {
        $form = $request->post('form');
        $roleID = $request->post('role_id');

        $rules = [
            'name'     => 'required',
            'email'    => 'required|email',
            'password' => 'nullable|min:4',
        ];

        $customAttributes = [
            'name' => 'ФИО',
            'email' => 'Email',
            'password' => 'Новый пароль',
        ];

        $customMessages = [
            'required' => 'Поле :attribute является обязательным.',
            'email'    => 'Поле :attribute должно содержать email.',
            'min'      => 'Пароль должен содержать минимум 4 символа.',
        ];

        $validator = Validator::make($form, $rules, $customMessages, $customAttributes);

        if ($validator->fails()) {
            return \Response::json(
                [
                    'code' => 401,
                    'msg'  => $validator->errors(),
                ],
                401
            );
        }

        $user = User::create($validator->valid());

        if (! empty($roleID)) {
            $user->attachRole($roleID);
        }

        if ($user) {
            return \Response::json(
                [
                    'code'     => 200,
                    'redirect' => route(
                        'admin.users',
                        [
                            'info'  => "Пользователь успешно добавлен",
                            'alert' => 'success',
                        ]
                    ),
                    'msg'      => ['success' => ['Пользователь успешно зарегистрирован']],
                ]
            );
        }

        return \Response::json(
            [
                'code'     => 401,
                'redirect' => route(
                    'admin.register',
                    [
                        'info'  => "Ошибка при добавлении пользователя",
                        'alert' => 'danger',
                    ]
                ),
                'msg'      => ['error' => ['Произошла ошибка при регистрации пользователя']],
            ],
            401
        );
    }

    public function delete($id)
    {
        $user = User::where('id', '=', $id)->first();
        if ($user->hasRole('admin')) {
            return redirect()->back()
                ->with(['info' => "Нельзя удалить пользователя с ролью Администратор!", 'alert' => 'warning']);
        }
        $user->delete();
        DB::table('role_user')
            ->where('user_id', '=', $id)
            ->delete();

        if (auth()->user()->id === $id) {
            Auth::guard('web')->logout();
            return redirect(route('admin.login'));
        }

        return redirect()->back()
            ->with(['info' => "Пользователь c ID: {$id} был удалён!", 'alert' => 'warning']);
    }
}
