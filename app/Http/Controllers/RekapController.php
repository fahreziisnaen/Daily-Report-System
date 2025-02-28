<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $query = User::role('employee')
            ->with(['reports' => function($query) use ($month, $year) {
                $query->whereMonth('report_date', $month)
                    ->whereYear('report_date', $year);
            }]);

        $users = $query->get()->map(function($user) {
            $totalWorkHours = 0;
            $totalOvertimeHours = 0;

            foreach($user->reports as $report) {
                $baseDate = Carbon::parse($report->report_date);
                
                $start = Carbon::parse($report->start_time)->setDateFrom($baseDate);
                $end = Carbon::parse($report->end_time)->setDateFrom($baseDate);
                if($report->is_overnight) {
                    $end->addDay();
                }

                $normalStart = Carbon::parse('08:45')->setDateFrom($baseDate);
                $normalEnd = Carbon::parse('17:00')->setDateFrom($baseDate);

                // Debug log
                \Log::info('Time calculation for report', [
                    'user' => $user->name,
                    'date' => $baseDate->format('Y-m-d'),
                    'start' => $start->format('Y-m-d H:i'),
                    'end' => $end->format('Y-m-d H:i'),
                    'normal_start' => $normalStart->format('Y-m-d H:i'),
                    'normal_end' => $normalEnd->format('Y-m-d H:i')
                ]);

                // Hitung jam kerja normal
                if ($start->lte($normalEnd) && $end->gte($normalStart)) {
                    $workStart = Carbon::createFromTimestamp(max($start->timestamp, $normalStart->timestamp));
                    $workEnd = Carbon::createFromTimestamp(min($end->timestamp, $normalEnd->timestamp));
                    $workMinutes = $workStart->diffInMinutes($workEnd);  // Dari workStart ke workEnd
                    $workHours = $workMinutes / 60;
                    
                    \Log::info('Work hours calculation', [
                        'work_start' => $workStart->format('H:i'),
                        'work_end' => $workEnd->format('H:i'),
                        'work_minutes' => $workMinutes,
                        'work_hours' => $workHours
                    ]);
                    
                    $totalWorkHours += $workHours;
                }

                // Hitung lembur
                // 1. Lembur pagi (sebelum 8:45)
                if ($start->lt($normalStart)) {
                    $overtimeMinutes = $start->diffInMinutes($normalStart);  // 8:45 - waktu mulai
                    $overtimeHours = $overtimeMinutes / 60;
                    
                    \Log::info('Morning overtime calculation', [
                        'start' => $start->format('H:i'),
                        'normal_start' => $normalStart->format('H:i'),
                        'overtime_minutes' => $overtimeMinutes,
                        'overtime_hours' => $overtimeHours
                    ]);
                    
                    $totalOvertimeHours += $overtimeHours;
                }

                // 2. Lembur sore (setelah 17:00)
                if ($end->gt($normalEnd)) {
                    $overtimeMinutes = $normalEnd->diffInMinutes($end);  // waktu akhir - 17:00
                    $overtimeHours = $overtimeMinutes / 60;
                    
                    \Log::info('Evening overtime calculation', [
                        'normal_end' => $normalEnd->format('H:i'),
                        'end' => $end->format('H:i'),
                        'overtime_minutes' => $overtimeMinutes,
                        'overtime_hours' => $overtimeHours
                    ]);
                    
                    $totalOvertimeHours += $overtimeHours;
                }
            }

            $formatHoursAndMinutes = function($totalHours) {
                $hours = floor($totalHours);
                $minutes = round(($totalHours - $hours) * 60);
                return $hours . ' Jam ' . $minutes . ' Menit';
            };

            return [
                'name' => $user->name,
                'total_work_hours' => $formatHoursAndMinutes($totalWorkHours),
                'total_overtime_hours' => $formatHoursAndMinutes($totalOvertimeHours),
                'report_count' => $user->reports->count()
            ];
        });

        return view('rekap.index', [
            'users' => $users,
            'month' => $month,
            'year' => $year,
            'months' => $this->getMonths(),
            'years' => range(now()->year - 2, now()->year)
        ]);
    }

    private function getMonths()
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
    }
} 