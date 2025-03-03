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
                $dayOfWeek = $baseDate->dayOfWeek;
                
                $start = Carbon::parse($report->start_time)->setDateFrom($baseDate);
                $end = Carbon::parse($report->end_time)->setDateFrom($baseDate);
                
                if($report->is_overnight) {
                    $end->addDay();
                }

                // Definisikan jam kerja normal
                $normalStart = Carbon::parse('08:45')->setDateFrom($baseDate);
                $normalEnd = Carbon::parse($dayOfWeek == 6 ? '13:00' : '17:00')->setDateFrom($baseDate);

                // Hitung jam kerja dan lembur
                if($report->work_day_type === 'Hari Libur' || $dayOfWeek == 0) {
                    // Untuk hari libur/minggu:
                    // - Semua jam masuk hitungan lembur
                    // - Tidak ada jam kerja normal
                    $totalWorkHours += 0;
                    $overtimeHours = $start->diffInMinutes($end) / 60;
                    $totalOvertimeHours += $overtimeHours;
                } else {
                    // Untuk hari kerja normal:
                    // 1. Hitung jam kerja normal (08:45 - 17:00 atau 13:00 untuk Sabtu)
                    $workStart = $start->copy();
                    $workEnd = $end->copy();

                    // Sesuaikan waktu kerja ke jam normal jika di luar jam normal
                    if($workStart->lt($normalStart)) {
                        $workStart = $normalStart->copy();
                    }
                    if($workEnd->gt($normalEnd)) {
                        $workEnd = $normalEnd->copy();
                    }

                    // Hitung total jam kerja normal
                    if($workEnd->gt($workStart)) {
                        $totalWorkHours += $workStart->diffInMinutes($workEnd) / 60;
                    }

                    // 2. Hitung jam lembur
                    $overtimeHours = 0;

                    // Lembur pagi (sebelum jam normal)
                    if($start->lt($normalStart)) {
                        $overtimeHours += $start->diffInMinutes($normalStart) / 60;
                    }

                    // Lembur sore (setelah jam normal)
                    if($end->gt($normalEnd)) {
                        $overtimeHours += $normalEnd->diffInMinutes($end) / 60;
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