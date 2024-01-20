<?php

declare(strict_types=1);

namespace Modules\Roles\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Roles\Models\Role;

class RoleService
{
    public function __construct(
        protected Request $request
    ) {
    }

    /**
     * Метод возвращает текущее поле и направление сортировки
     */
    public function getOrderByParams(): array
    {
        $availableFields = [
            'id',
            'role_name',
            'updated_at',
        ];

        $orderBy = $this->request->input('orderBy', 'id');
        if (! in_array($orderBy, $availableFields)) {
            $orderBy = 'id';
        }

        return [
            'orderBy'        => $orderBy,
            'orderDirection' => $this->request->input('orderDirection', 'asc') === 'asc' ? 'asc' : 'desc',
        ];
    }

    public function validateFields($fields)
    {
        $rules = [
            'name' => 'required|max:255',
            'display_name' => 'max:255',
            'description' => 'max:255',
        ];

        $customAttributes = [
            'name' => 'Название роли',
            'display_name' => 'Псевдоним',
            'description' => 'Описание роли',
        ];

        $customMessages = [
            'required' => 'Поле ":attribute" является обязательным.',
            'min'      => 'Поле ":attribute" должно содержать минимум :min символа.',
            'max'      => 'Поле ":attribute" должно содержать максимум :max символов.',
        ];

        return Validator::make($fields, $rules, $customMessages, $customAttributes);
    }

    /**
     * Метод проверяет, закреплена ли роль за пользователем
     */
    public function isAttached($user_id, $role_id): bool
    {
        $value = DB::table('role_user')
            ->where('user_id', $user_id)
            ->where('role_id', $role_id)
            ->exists();

        return $value;
    }

    /**
     * Метод проверяет текущую роль пользователя
     */
    public function getCurrentRoleID($user_id): int
    {
        $value = DB::table('role_user')
            ->where('user_id', $user_id)
            ->first();

        return $value->role_id ?? 0;
    }
}
