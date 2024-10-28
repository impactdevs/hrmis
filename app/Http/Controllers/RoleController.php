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
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:roles,name']);
        Role::create(['name' => $request->name]);
        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $allPermissions = Permission::all();
        return view('roles.edit', compact('role', 'allPermissions'));
    }

    public function update(Request $request, Role $role)
    {
        // Validate the role name
        $request->validate(['name' => 'required|unique:roles,name,' . $role->id]);

        // Update the role name
        $role->update(['name' => $request->name]);

        // Sync permissions
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
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
