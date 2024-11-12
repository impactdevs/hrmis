<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }
    public function create()
    {
        $permissions = Permission::all(); // Assuming you have a Permission model
        return view('roles.create', compact('permissions'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array', // Ensure permissions are an array
        ]);

        $role = Role::create(['name' => $request->name]);

        // Sync permissions
        if ($request->permissions) {
            //convert the ids to ints
            $permissions = $this->convertPermissionsToInt($request->permissions);
            $role->syncPermissions($permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }


    public function edit(Role $role)
    {
        $allPermissions = Permission::all();
        return view('roles.edit', compact('role', 'allPermissions'));
    }

    /**
     * Update the specified role in the database.
     *
     * Validates the role's name to ensure it is unique, updates the role's name,
     * converts the provided permissions to integers, and syncs these permissions
     * with the role. Upon successful update, redirects to the roles index page
     * with a success message.
     *
     * @param Request $request The incoming HTTP request containing role data.
     * @param Role $role The role instance to be updated.
     * @return \Illuminate\Http\RedirectResponse A redirect response to the roles index page.
     */
    public function update(Request $request, Role $role)
    {
        $permissions = $this->convertPermissionsToInt($request->permissions);

        $role->syncPermissions($permissions);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }
    private function convertPermissionsToInt(array $permissions = []): array
    {
        return array_map('intval', $permissions);
    }


    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }

    public function addPermissions(Request $request, Role $role)
    {
        $request->validate(['permissions' => 'required|array']);
        $role->givePermissionTo($request->permissions);
        return redirect()->route('roles.index')->with('success', 'Permissions added to role successfully.');
    }

    public function removePermissions(Request $request, Role $role)
    {
        $request->validate(['permissions' => 'required|array']);
        $role->revokePermissionTo($request->permissions);
        return redirect()->route('roles.index')->with('success', 'Permissions removed from role successfully.');
    }

}
