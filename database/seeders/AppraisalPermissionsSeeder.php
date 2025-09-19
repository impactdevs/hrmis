<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AppraisalPermissionsSeeder extends Seeder
{
    /**
     * Create the appraisal permissions and assign them to existing roles.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create appraisal permissions if they don't exist
        $permissions = [
            'approve appraisal',
            'view appraisal', 
            'edit appraisal',
            'create appraisal',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Get existing roles and assign permissions
        $roles = [
            'Staff' => ['create appraisal', 'view appraisal', 'edit appraisal'],
            'Executive Secretary' => ['approve appraisal', 'view appraisal', 'edit appraisal'],
            'HR' => ['approve appraisal', 'view appraisal', 'edit appraisal'],
            'Head of Division' => ['approve appraisal', 'view appraisal', 'edit appraisal'],
            'Assistant Executive Secretary' => ['approve appraisal', 'view appraisal', 'edit appraisal'],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                // Remove existing permissions and add new ones to avoid duplicates
                $role->syncPermissions($rolePermissions);
                $this->command->info("Updated permissions for role: {$roleName}");
            } else {
                $this->command->warn("Role not found: {$roleName}");
            }
        }

        $this->command->info('Appraisal permissions have been created and assigned to roles.');
    }
}
