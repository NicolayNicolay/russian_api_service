<?php

namespace Modules\Roles\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Modules\Roles\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->input('count', config('app.per_page'));

        $roles = Role::orderBy('id', 'ASC')
            ->paginate($perPage)
            ->appends(request()->query());

        return view('admin.roles.roles', [
            'roles' => $roles,
        ]);
    }

    public function roleForm(Request $request): View
    {
        return view('admin.roles.role', [
            'id' => (int) $request->id,
        ]);
    }

    public function delete($id)
    {
        // Удаление роли
        Role::where('id', '=', $id)->delete();
        return redirect(route('admin.roles'))
            ->with(
                [
                    'info'  => "Роль удалена",
                    'alert' => 'warning',
                ]
            );
    }
}
