<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Report;
use Carbon\Carbon;

class EmployeeAndReportSeeder extends Seeder
{
    public function run()
    {
        // Create some employees
        $employees = [
            [
                'name' => 'John Engineer',
                'email' => 'john@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Mike Specialist',
                'email' => 'mike@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Sarah Technician',
                'email' => 'sarah@example.com',
                'password' => bcrypt('password'),
            ],
        ];

        foreach ($employees as $employee) {
            $user = User::create($employee);
            $user->assignRole('employee');

            // Create reports for each employee
            $this->createReportsForEmployee($user);
        }

        // Create an admin if not exists
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );
        $admin->assignRole('admin');
    }

    private function createReportsForEmployee($user)
    {
        $projects = ['PRJ-001', 'PRJ-002', 'PRJ-003', 'PRJ-004'];
        $locations = ['Site A', 'Site B', 'Site C', 'Workshop'];
        $statuses = ['Selesai', 'Dalam Proses', 'Tertunda', 'Bermasalah'];

        // Create 5 reports for each employee
        for ($i = 0; $i < 5; $i++) {
            $date = Carbon::now()->subDays(rand(0, 30));
            $startTime = sprintf('%02d:00', rand(7, 16));
            $endTime = sprintf('%02d:00', rand(17, 22));
            
            $report = Report::create([
                'user_id' => $user->id,
                'report_date' => $date,
                'project_code' => $projects[array_rand($projects)],
                'location' => $locations[array_rand($locations)],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'is_overnight' => rand(0, 1),
            ]);

            // Create 2-4 work details for each report
            $detailCount = rand(2, 4);
            for ($j = 0; $j < $detailCount; $j++) {
                $report->details()->create([
                    'description' => $this->getRandomWorkDescription($j + 1),
                    'status' => $statuses[array_rand($statuses)],
                ]);
            }
        }
    }

    private function getRandomWorkDescription($taskNumber)
    {
        $tasks = [
            'Melakukan inspeksi peralatan',
            'Perbaikan sistem kontrol',
            'Kalibrasi sensor',
            'Pemeliharaan rutin',
            'Troubleshooting masalah',
            'Instalasi komponen baru',
            'Testing dan komisioning',
            'Dokumentasi teknis',
            'Koordinasi dengan tim lapangan',
            'Review desain sistem'
        ];

        $details = [
            'menyelesaikan tahap awal',
            'melanjutkan proses',
            'finalisasi',
            'evaluasi hasil'
        ];

        $task = $tasks[array_rand($tasks)];
        $detail = $details[array_rand($details)];

        return "Task #{$taskNumber}: {$task} - {$detail}";
    }
} 