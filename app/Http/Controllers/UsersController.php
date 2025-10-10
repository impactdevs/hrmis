<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Requests\StoreUserRequest;


class UsersController extends Controller
{
    public function index()
    {
        $roles = Role::with('users')->get();
        return view('users.index', compact('roles'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'array',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
        ]);

        // Normalize email if provided
        if ($request->has('email')) {
            $user->email = strtolower(trim($request->email));
        }

        // Sync roles
        $user->syncRoles($request->roles);

        return redirect()->route('users.index')->with('success', 'Roles updated successfully.');
    }

    public function store(StoreUserRequest $request)
    {
        // Email is already normalized by the FormRequest
        $user = User::create($request->validated());

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }
}
