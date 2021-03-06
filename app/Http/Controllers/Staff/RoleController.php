<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Role::class, 'role');
    }

    public function index()
    {
        return view('staff.roles.index', [
            'roles' => Role::all()->map(function ($role) {
                $role->setRelation(
                    'permissions',
                    $role->permissions->groupBy(function ($permission) {
                        return ucwords(explode(':', $permission->name, 2)[0]);
                    })->map(function ($permissions, $group) {
                        return '[' . $group . ':' . $permissions->map(function ($permission) {
                            return ucwords(explode(':', $permission->name, 2)[1]);
                        })->implode(',') . ']';
                    })
                );
                return $role;
            }),
        ]);
    }

    public function create()
    {
        return view('staff.roles.create', [
            'permissions' => Permission::all()
                ->groupBy(static function ($permission) {
                    return ucwords(explode(':', $permission->name, 2)[0]);
                }),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:190', 'unique:roles'],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        Role::create([
            'name' => $request->name,
        ])->syncPermissions($request->permissions);

        flash('Role created successfully!')->success();

        return redirect()->route('staff.roles.index');
    }

    public function show(Role $role)
    {
        return view('staff.roles.show', [
            'role' => $role,
            'permissions' => Permission::all()
                ->groupBy(static function ($permission) {
                    return ucwords(explode(':', $permission->name, 2)[0]);
                }),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => ['sometimes', 'required', 'string', 'min:3', 'max:190', Rule::unique('roles', 'name')->ignore($role)],
            'permissions' => ['sometimes', 'required', 'array', 'min:1'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role->update([
            'name' => $request->name,
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        flash('Role updated successfully!')->success();

        return redirect()->back();
    }

    public function destroy(Role $role)
    {
        $role->delete();

        flash('Role deleted successfully!')->success();

        return redirect()->back();
    }
}
