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

        // Create permissions first
        $permissions = [
            'approve appraisal',
            'view appraisal',
            'edit appraisal',
            'create appraisal',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        //create roles
        $role1 = 'Staff';
        $role2 = 'Executive Secretary';
        $role3 = 'HR';
        $role4 = 'Head of Division';
        $role5 = 'Assistant Executive Secretary';
        $role1 = Role::create(['name' => $role1]);
        $role2 = Role::create(['name' => $role2]);
        $role3 = Role::create(['name' => $role3]);
        $role4 = Role::create(['name' => $role4]);
        $role5 = Role::create(['name' => $role5]);

        // Assign permissions to roles
        // Staff can create, view, and edit their own appraisals
        $role1->givePermissionTo(['create appraisal', 'view appraisal', 'edit appraisal']);
        
        // Executive Secretary, HR, and Head of Division can approve appraisals
        $role2->givePermissionTo(['approve appraisal', 'view appraisal', 'edit appraisal']);
        $role3->givePermissionTo(['approve appraisal', 'view appraisal', 'edit appraisal']);
        $role4->givePermissionTo(['approve appraisal', 'view appraisal', 'edit appraisal']);
        
        // Assistant Executive Secretary has similar permissions to ES
        $role5->givePermissionTo(['approve appraisal', 'view appraisal', 'edit appraisal']);
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


        ///create corresponding employees
        $employee1 = \App\Models\Employee::factory()->create([
            'email' => $user1->email,
            'first_name' => 'STAFF',
            'last_name' => 'USER',
            'phone_number' => '0700000000',
            'user_id' => $user1->id,
        ]);

        $employee2 = \App\Models\Employee::factory()->create([
            'email' => $user2->email,
            'first_name' => 'EXECUTIVE',
            'last_name' => 'SECRETARY',
            'phone_number' => '0700000001',
            'user_id' => $user2->id,
        ]);

        $employee3 = \App\Models\Employee::factory()->create([
            'email' => $user3->email,
            'first_name' => 'HUMAN',
            'last_name' => 'RESOURCE',
            'phone_number' => '0700000002',
            'user_id' => $user3->id,
        ]);


        $employee4 = \App\Models\Employee::factory()->create([
            'email' => $user4->email,
            'first_name' => 'HEAD OF',
            'last_name' => 'DIVISION',
            'phone_number' => '0700000003',
            'user_id' => $user4->id,
        ]);

        $employee5 = \App\Models\Employee::factory()->create([
            'email' => $user5->email,
            'first_name' => 'Assistant Executive',
            'last_name' => 'Secretary',
            'phone_number' => '0700000004',
            'user_id' => $user5->id,
        ]);


    }
}
