<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Project;
use App\Models\Report;
use App\Models\ReportDetail;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create Roles
        $adminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $employeeRole = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);

        // Create Permissions
        $permissions = [
            'view own reports',
            'create reports',
            'edit own reports',
            'view all reports',
            'manage all reports',
            'manage users',
            'manage settings'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign Permissions to Roles
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

        // Create Users
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@fc-network.com',
                'homebase' => 'Surabaya',
                'password' => '$2y$12$3bwJbJz5CMjA5gGG4LKc4.JYMRJzaOw4jqgWsC8tmRtwskR7dnjzS',
                'role' => 'Super Admin'
            ],
            [
                'name' => 'Ingrid Melyana',
                'email' => 'ingrid.melyana@fc-network.com',
                'homebase' => 'Surabaya',
                'password' => '$2y$12$K4U8OT/HTw9.XEZxSQU1UuTtnmFdwiGFOTNBamKsoBZ8b8PPsYwIu',
                'role' => 'Super Admin'
            ],
            [
                'name' => 'Fahrezi Isnaen Fauzan',
                'email' => 'fahrezi.fauzan@fc-network.com',
                'homebase' => 'Surabaya',
                'password' => '$2y$12$XZxwqM02dAxHbymREDmVpOMSAOFXFff49bXMSEAc2/libsgzRGK1e',
                'role' => 'Super Admin'
            ],
            [
                'name' => 'Aditya Nata Nael',
                'email' => 'aditya.natanael@fc-netwok.com',
                'homebase' => 'Jakarta',
                'password' => '$2y$12$HmID.T8pgkBL5Rlb8W/54u6g7nORana73NAI7v5H.08elhX08noBC',
                'role' => 'employee'
            ],
            [
                'name' => 'Subandi Wahyono',
                'email' => 'bandi@fc-network.com',
                'homebase' => 'Surabaya',
                'password' => '$2y$12$urKwBpECYWUHChto2S5ijOuuOapzPUkRFtOWlw1EU2iezGio8W/5u',
                'role' => 'employee'
            ],
            [
                'name' => 'Harista Januarianto',
                'email' => 'harista@fc-network.com',
                'homebase' => 'Surabaya',
                'password' => '$2y$12$AU/igupPYFhtB9/plGwTwO0XeQLsTgCl0B9KaxK3c6vPUCPTxznZi',
                'role' => 'Super Admin'
            ],
            [
                'name' => 'Sastro Haris',
                'email' => 'sastro@fc-network.com',
                'homebase' => 'Surabaya',
                'password' => '$2y$12$e.bw85AUt6ktE2kFNarc8OQgtdYPo46xlTa3QuISGLZ9UVuib6202',
                'role' => 'Super Admin'
            ],
            [
                'name' => 'Kukuh Maruto Putra',
                'email' => 'kukuh.maruto@fc-network.com',
                'homebase' => 'Jakarta',
                'password' => '$2y$12$H6eAodWw4fG8WoyFf9yO9eciYdrrjBS/fc9U7eXc5/hksLNtjmpDW',
                'role' => 'employee'
            ],
            [
                'name' => 'Dedy Setiawan',
                'email' => 'ods.setiawandedy@fc-network.com',
                'homebase' => 'Jakarta',
                'password' => '$2y$12$wzYjWS4dtfM//22vRfQRe.Rtmmgx7mh8xPnlfYrIguuDU0OPqQkru',
                'role' => 'employee'
            ],
            [
                'name' => 'tess',
                'email' => 'tes@fc-network.com',
                'homebase' => 'Surabaya',
                'password' => '$2y$12$ayiA0.qEyRvNPN59LbDYlO94qli7ZclZGZW7ZRnB0tW9GDXor7hKe',
                'role' => 'employee'
            ]
        ];

        foreach ($users as $userData) {
            // Check if user exists
            $user = User::where('email', $userData['email'])->first();
            
            if (!$user) {
                // Create new user if doesn't exist
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'homebase' => $userData['homebase'],
                    'password' => $userData['password']
                ]);
            }

            // Sync role (will remove other roles and assign this one)
            $user->syncRoles([$userData['role']]);
        }

        // Create Projects
        $projects = [
            [
                'code' => 'KPI-JK-001',
                'name' => 'Sistem Network RU IV Cilacap & RU VI Balongan',
                'customer' => 'PT Kilang Pertamina Internasional',
                'status' => 'Berjalan',
                'created_at' => '2025-03-18 02:11:00',
                'updated_at' => '2025-03-18 02:11:00'
            ],
            [
                'code' => 'PTM-JK-037',
                'name' => 'LAN & Data Center SCU',
                'customer' => 'Persero',
                'status' => 'Berjalan',
                'created_at' => '2025-03-18 02:11:13',
                'updated_at' => '2025-03-18 02:11:13'
            ],
            [
                'code' => 'IPI-SB-001',
                'name' => 'Internal',
                'customer' => 'IPI',
                'status' => 'Berjalan',
                'created_at' => '2025-03-18 02:11:23',
                'updated_at' => '2025-03-18 02:11:23'
            ]
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }

        // Create Reports and Report Details
        $reports = [
            [
                'id' => 1,
                'user_id' => 2,
                'report_date' => '2025-03-18',
                'project_code' => 'PTM-JK-037',
                'location' => 'Surabaya',
                'start_time' => '17:00:00',
                'end_time' => '04:00:00',
                'is_overnight' => 1,
                'is_overtime' => 1,
                'is_shift' => 0,
                'work_day_type' => 'Hari Kerja',
                'created_at' => '2025-03-18 02:12:10',
                'updated_at' => '2025-03-18 06:44:40',
                'updated_by' => 1,
                'details' => [
                    [
                        'description' => 'Meeting Koordinasi dengan Persero',
                        'status' => 'Selesai',
                        'created_at' => '2025-03-18 06:44:40',
                        'updated_at' => '2025-03-18 06:44:40'
                    ]
                ]
            ],
            [
                'id' => 2,
                'user_id' => 10,
                'report_date' => '2025-03-11',
                'project_code' => 'IPI-SB-001',
                'location' => 'Surabaya',
                'start_time' => '08:00:00',
                'end_time' => '20:00:00',
                'is_overnight' => 0,
                'is_overtime' => 1,
                'is_shift' => 0,
                'work_day_type' => 'Hari Kerja',
                'created_at' => '2025-03-20 03:58:05',
                'updated_at' => '2025-03-20 03:58:05',
                'updated_by' => null,
                'details' => [
                    [
                        'description' => 'tess',
                        'status' => 'Selesai',
                        'created_at' => '2025-03-20 03:58:05',
                        'updated_at' => '2025-03-20 03:58:05'
                    ]
                ]
            ],
            [
                'id' => 3,
                'user_id' => 10,
                'report_date' => '2025-03-14',
                'project_code' => 'PTM-JK-037',
                'location' => 'Jakarta',
                'start_time' => '13:00:00',
                'end_time' => '20:00:00',
                'is_overnight' => 0,
                'is_overtime' => 0,
                'is_shift' => 0,
                'work_day_type' => 'Hari Kerja',
                'created_at' => '2025-03-20 03:58:57',
                'updated_at' => '2025-03-20 03:58:57',
                'updated_by' => null,
                'details' => [
                    [
                        'description' => 'aaaaaaaaa',
                        'status' => 'Selesai',
                        'created_at' => '2025-03-20 03:58:57',
                        'updated_at' => '2025-03-20 03:58:57'
                    ]
                ]
            ],
            [
                'id' => 4,
                'user_id' => 10,
                'report_date' => '2025-03-12',
                'project_code' => 'PTM-JK-037',
                'location' => 'Surabaya',
                'start_time' => '08:45:00',
                'end_time' => '17:00:00',
                'is_overnight' => 0,
                'is_overtime' => 0,
                'is_shift' => 0,
                'work_day_type' => 'Hari Kerja',
                'created_at' => '2025-03-20 03:59:52',
                'updated_at' => '2025-03-20 04:48:48',
                'updated_by' => 1,
                'details' => [
                    [
                        'description' => 'sssssss',
                        'status' => 'Selesai',
                        'created_at' => '2025-03-20 04:48:48',
                        'updated_at' => '2025-03-20 04:48:48'
                    ]
                ]
            ],
            [
                'id' => 5,
                'user_id' => 10,
                'report_date' => '2025-03-15',
                'project_code' => 'IPI-SB-001',
                'location' => 'Surabaya',
                'start_time' => '08:30:00',
                'end_time' => '13:00:00',
                'is_overnight' => 0,
                'is_overtime' => 1,
                'is_shift' => 0,
                'work_day_type' => 'Hari Kerja',
                'created_at' => '2025-03-20 04:52:42',
                'updated_at' => '2025-03-20 04:54:52',
                'updated_by' => 10,
                'details' => [
                    [
                        'description' => 'potr',
                        'status' => 'Selesai',
                        'created_at' => '2025-03-20 04:54:52',
                        'updated_at' => '2025-03-20 04:54:52'
                    ]
                ]
            ]
        ];

        foreach ($reports as $reportData) {
            $details = $reportData['details'];
            unset($reportData['details']);
            
            $report = Report::create($reportData);
            
            foreach ($details as $detail) {
                $report->details()->create($detail);
            }
        }
    }
}
