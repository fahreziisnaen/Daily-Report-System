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

        $query = User::with(['reports' => function($query) use ($month, $year) {
            $query->whereMonth('report_date', $month)
                ->whereYear('report_date', $year);
        }]);

        $users = $query->get()->map(function($user) {
            $totalWorkHours = 0;
            $totalOvertimeHours = 0;

            foreach($user->reports as $report) {
                $baseDate = Carbon::parse($report->report_date);
                $dayOfWeek = $baseDate->dayOfWeek; // 1 = Monday, 6 = Saturday
                
                $start = Carbon::parse($report->start_time)->setDateFrom($baseDate);
                $end = Carbon::parse($report->end_time)->setDateFrom($baseDate);
                if($report->is_overnight) {
                    $end->addDay();
                }

                // Debug log
                \Log::info('Time calculation for report', [
                    'user' => $user->name,
                    'date' => $baseDate->format('Y-m-d'),
                    'day_of_week' => $dayOfWeek,
                    'start' => $start->format('Y-m-d H:i'),
                    'end' => $end->format('Y-m-d H:i')
                ]);

                // Hitung total jam kerja
                $totalWorkHours += $start->diffInMinutes($end) / 60;

                // Hitung jam lembur
                if($report->is_overtime) {
                    // Hari Minggu atau status Hari Libur
                    if($dayOfWeek == 0 || $report->work_day_type === 'Hari Libur') {
                        // Semua jam dihitung lembur
                        $overtimeHours = $start->diffInMinutes($end) / 60;
                    } else {
                        // Logika normal untuk Senin-Sabtu
                        $normalStart = Carbon::parse('08:45')->setDateFrom($baseDate);
                        $normalEnd = Carbon::parse($dayOfWeek == 6 ? '13:00' : '17:00')->setDateFrom($baseDate);
                        
                        $overtimeHours = 0;
                        
                        // Hitung lembur pagi dan sore
                        if($start->lt($normalStart)) {
                            $overtimeHours += $start->diffInMinutes($normalStart) / 60;
                        }
                        if($end->gt($normalEnd)) {
                            $overtimeHours += $normalEnd->diffInMinutes($end) / 60;
                        }
                    }
                    
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