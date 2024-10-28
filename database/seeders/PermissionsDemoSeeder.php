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

        // create permissions
        Permission::create(['name' => 'can view all employees']);
        Permission::create(['name' => 'can view all events']);
        Permission::create(['name' => 'can view all trainings']);
        Permission::create(['name' => 'can view all attendances']);
        Permission::create(['name' => 'can add an employee']);
        Permission::create(['name' => 'can add a training']);
        Permission::create(['name' => 'can add an event']);
        Permission::create(['name' => 'can view all leave requests']);
        Permission::create(['name' => 'can approve a leave']);
        Permission::create(['name' => 'can view appraisals']);
        Permission::create(['name' => 'can view settings']);
        Permission::create(['name' => 'can edit settings']);
        Permission::create(['name' => 'can view department']);

        $role3 = Role::create(['name' => 'Super Admin']);
        // gets all permissions via Gate::before rule; see AuthServiceProvider

        $user = \App\Models\User::factory()->create([
            'name' => 'NSENGIYUMVA WILBERFORCE',
            'email' => 'nsengiyumvawilberforce@gmail.com',
        ]);
        $user->assignRole($role3);
    }
}
