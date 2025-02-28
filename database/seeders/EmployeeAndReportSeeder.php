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
                'homebase' => 'Jakarta',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Mike Specialist',
                'email' => 'mike@example.com',
                'homebase' => 'Surabaya',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Sarah Technician',
                'email' => 'sarah@example.com',
                'homebase' => 'Medan',
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
        $locations = ['Site A', 'Site B', 'Site C'];
        $statuses = ['Selesai', 'Dalam Proses', 'Tertunda', 'Bermasalah'];

        // Skenario waktu kerja yang masuk akal
        $workScenarios = [
            // Normal (tidak lembur, tidak overnight)
            ['08:45', '17:00', false, false],
            // Lembur sore (lembur, tidak overnight)
            ['08:45', '19:30', false, true],
            // Lembur pagi (lembur, tidak overnight)
            ['06:30', '17:00', false, true],
            // Overnight (lembur dan overnight)
            ['14:00', '02:00', true, true],
            // Lembur sore panjang (lembur, tidak overnight)
            ['08:45', '21:00', false, true],
        ];

        $workDayTypes = ['Hari Kerja', 'Hari Libur'];

        // Create 5 reports for each employee
        for ($i = 0; $i < 5; $i++) {
            $date = Carbon::now()->subDays(rand(0, 30));
            $scenario = $workScenarios[array_rand($workScenarios)];
            
            $report = Report::create([
                'user_id' => $user->id,
                'report_date' => $date,
                'project_code' => $projects[array_rand($projects)],
                'location' => $locations[array_rand($locations)],
                'start_time' => $scenario[0],
                'end_time' => $scenario[1],
                'is_overnight' => $scenario[2],
                'is_overtime' => $scenario[3],
                'work_day_type' => $workDayTypes[array_rand($workDayTypes)],
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
            'Instalasi perangkat jaringan baru',
            'Maintenance router dan switch',
            'Konfigurasi firewall',
            'Troubleshooting koneksi client',
            'Update firmware perangkat',
            'Pengecekan performa jaringan',
            'Backup konfigurasi perangkat',
            'Implementasi VLAN',
            'Optimasi bandwidth',
            'Setup monitoring system'
        ];

        return $tasks[array_rand($tasks)];
    }
} 