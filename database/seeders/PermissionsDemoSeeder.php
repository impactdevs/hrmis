<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsDemoSeeder extends Seeder
{
    /**
     * Create the initial roles and permissions.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        //create a role
        $role3 = 'Super Admin';

        //create $role1
        $role1 = Role::create(['name' => $role3]);
        // gets all permissions via Gate::before rule; see AuthServiceProvider

        $user = \App\Models\User::factory()->create([
            'name' => 'NSENGIYUMVA WILBERFORCE',
            'email' => 'nsengiyumvawilberforce@gmail.com',
        ]);
        $user->assignRole($role3);
    }
}
