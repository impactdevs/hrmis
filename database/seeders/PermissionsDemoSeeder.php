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
        $role1 = 'Staff';
        $role2 = 'Executive Secretary';
        $role3 = 'HR';
        $role4 = 'Head of Division';
        $role5 = 'Assistant Executive Secretary';
        $role1 = Role::create(['name' => $role1]);
        // $role2 = Role::create(['name' => $role2]);
        // $role3 = Role::create(['name' => $role3]);
        $role4 = Role::create(['name' => $role4]);
        $role5 = Role::create(['name' => $role5]);
        // gets all permissions via Gate::before rule; see AuthServiceProvider
        $user1 = \App\Models\User::factory()->create([
            'name' => 'STAFF USER',
            'email' => 'staff@uncst.com',
        ]);
        $user1->assignRole($role1);

        $user2 = \App\Models\User::factory()->create([
            'name' => 'EXECUTIVE SECRETARY',
            'email' => 'executivesecretary@uncst.com',
        ]);
        $user2->assignRole($role2);

        $user3 = \App\Models\User::factory()->create([
            'name' => 'HUMAN RESOURCE',
            'email' => 'humanresource@uncst.com',
        ]);
        $user3->assignRole($role3);

        $user4 = \App\Models\User::factory()->create([
            'name' => 'HEAD OF DIVISION',
            'email' => 'headofdivision@uncst.com',
        ]);
        $user4->assignRole($role4);

        $user5 = \App\Models\User::factory()->create([
            'name' => 'Assistant Executive Secretary',
            'email' => 'assistantexecutivesecretary@uncst.com',
        ]);
        $user5->assignRole($role5);
    }
}
