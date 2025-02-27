<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $employeeRole = Role::create(['name' => 'employee', 'guard_name' => 'web']);

        // Create permissions
        $viewOwnReports = Permission::create(['name' => 'view own reports', 'guard_name' => 'web']);
        $createReports = Permission::create(['name' => 'create reports', 'guard_name' => 'web']);
        $editOwnReports = Permission::create(['name' => 'edit own reports', 'guard_name' => 'web']);
        $viewAllReports = Permission::create(['name' => 'view all reports', 'guard_name' => 'web']);
        $manageAllReports = Permission::create(['name' => 'manage all reports', 'guard_name' => 'web']);
        $manageUsers = Permission::create(['name' => 'manage users', 'guard_name' => 'web']);
        $manageSettings = Permission::create(['name' => 'manage settings', 'guard_name' => 'web']);

        // Assign permissions to roles
        $employeeRole->givePermissionTo([
            'view own reports',
            'create reports',
            'edit own reports'
        ]);

        $adminRole->givePermissionTo([
            'view all reports',
            'manage all reports',
            'manage users',
            'manage settings'
        ]);
    }
} 