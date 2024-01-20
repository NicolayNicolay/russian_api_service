<?php

declare(strict_types=1);

namespace Modules\Roles\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Roles\Models\Permission;

class PermissionService
{
    public function __construct(
        protected Request $request
    ) {
    }

    /**
     * Метод возвращает данные для формы выбора прав роли
     */
    public function getPermissionForm($id = 0): array
    {
        $formPermissions = [];
        $permissions = Permission::get();

        foreach ($permissions as $i => $permission) {
            $value = DB::table('permission_role')
                ->where('permission_id', $permission->id)
                ->where('role_id', $id)
                ->exists();
            $formPermissions[$i]['id'] = $permission->id;
            $formPermissions[$i]['name'] = $permission->name;
            $formPermissions[$i]['display_name'] = $permission->display_name;
            $formPermissions[$i]['description'] = $permission->description;
            $formPermissions[$i]['value'] = $value;
        }

        return $formPermissions;
    }

    /**
     * Метод проверяет, закреплено ли разрешение за ролью
     */
    public function isAttached($perm_id, $role_id): bool
    {
        $value = DB::table('permission_role')
            ->where('permission_id', $perm_id)
            ->where('role_id', $role_id)
            ->exists();

        return $value;
    }
}
