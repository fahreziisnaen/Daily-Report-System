<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Add default settings
        DB::table('settings')->insert([
            [
                'key' => 'auth_mode',
                'value' => 'local',
                'group' => 'authentication',
                'description' => 'Authentication mode (local/ldap)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'ldap_enabled',
                'value' => 'false',
                'group' => 'authentication',
                'description' => 'Enable/disable LDAP authentication',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create admin user
        if (!DB::table('users')->where('email', 'admin@example.com')->exists()) {
            DB::table('users')->insert([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create employee user for testing
        if (!DB::table('users')->where('email', 'employee@example.com')->exists()) {
            DB::table('users')->insert([
                'name' => 'Employee',
                'email' => 'employee@example.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Run role seeder
        $this->call([
            RoleSeeder::class,
            EmployeeAndReportSeeder::class,
        ]);

        // Assign roles to users
        $admin = \App\Models\User::where('email', 'admin@example.com')->first();
        $employee = \App\Models\User::where('email', 'employee@example.com')->first();
        
        if ($admin) {
            $admin->assignRole('admin');
        }
        if ($employee) {
            $employee->assignRole('employee');
        }
    }
}
