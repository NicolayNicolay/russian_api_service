<?php

namespace Modules\Roles\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Roles\Models\Role;
use Modules\Roles\Services\PermissionService;
use Modules\Roles\Services\RoleService;

class RoleFormController extends Controller
{
    public function submit(Request $request, RoleService $roleService, PermissionService $permissionService)
    {
        $form = $request->post('form');
        $permissions = $request->post('permissions');
        // Валидация полей формы
        $validator = $roleService->validateFields($form);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $error) {
                $errors[] = $error[0];
            }
            return \Response::json(
                [
                    'msg' => $errors,
                ],
                401
            );
        }
        if (array_key_exists('id', $form) && ! empty($form['id'])) {
            // Обновляем роль
            $role = Role::findOrFail($form['id']);
            $role->update($validator->valid());
            // Переопределяем права
            foreach ($permissions as $permission) {
                $exist = $permissionService->isAttached($permission['id'], $role->id);
                if (!empty($permission['value']) && !$exist) {
                        $role->attachPermission($permission['id']);
                } elseif (empty($permission['value']) && $exist) {
                    $role->detachPermission($permission['id']);
                }
            }
            $msg = 'Роль изменена';
        } else {
            // Создаём новую роль
            $newrole = Role::create($validator->valid());
            // Закрепляем права
            foreach ($permissions as $permission) {
                if (!empty($permission['value'])) {
                    $newrole->attachPermission($permission['id']);
                }
            }
            $msg = 'Роль добавлена';
        }
        $request->session()->flash('info', $msg);
        $request->session()->flash('alert', 'success');

        return \Response::json(
            [
                'code' => 200,
                'redirect' => '/admin/roles',
            ]
        );
    }

    public function getForm(Request $request, PermissionService $permissionService)
    {
        if (! empty($request->id)) {
            // Находим по ID роль и её разрешения
            $role = Role::find($request->id);
            if (! empty($role)) {
                $formPermissions = $permissionService->getPermissionForm($request->id);
                return \Response::json(
                    [
                        'code' => 200,
                        'form' => $role,
                        'permissions' => $formPermissions
                    ]
                );
            } else {
                return \Response::json(
                    [
                        'code' => 401,
                        'msg'  => 'Роль с ID ' . $request->id . ' не найдена',
                    ],
                    401
                );
            }
        } else {
            // Значения по умолчанию для новой роли
            $form['name'] = '';
            $form['display_name'] = '';
            $form['description'] = '';

            $formPermissions = $permissionService->getPermissionForm();

            return \Response::json(
                [
                    'code' => 200,
                    'form' => $form,
                    'permissions' => $formPermissions
                ]
            );
        }
    }
}
