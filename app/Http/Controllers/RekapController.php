<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
                'id' => $user->id,
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

    public function export(Request $request, User $user)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        try {
            // Load template
            $templatePath = storage_path('app/templates/exportlaporan.xlsx');
            if (!file_exists($templatePath)) {
                throw new \Exception('Template file tidak ditemukan di: ' . $templatePath);
            }

            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            // Set informasi user dan periode
            $sheet->setCellValue('B1', $user->name);
            $sheet->setCellValue('B2', Carbon::create($year, $month)->format('F Y'));

            // Ambil laporan user dan group by tanggal
            $reports = $user->reports()
                ->whereMonth('report_date', $month)
                ->whereYear('report_date', $year)
                ->orderBy('report_date')
                ->get()
                ->groupBy(function($report) {
                    return $report->report_date->format('Y-m-d');
                });

            // Mulai dari baris 5 (sesuai template)
            $row = 5;
            foreach ($reports as $date => $dateReports) {
                $startRow = $row;
                $lastProject = null;
                $lastStartTime = null;
                $lastEndTime = null;
                $lastLocation = null;
                $projectStartRow = $row;
                $startTimeStartRow = $row;
                $endTimeStartRow = $row;
                $locationStartRow = $row;
                
                foreach ($dateReports as $index => $report) {
                    $sheet->setCellValue('A' . $row, $report->report_date->format('d/m/Y'));
                    $sheet->setCellValue('B' . $row, $report->project_code);
                    $sheet->setCellValue('C' . $row, Carbon::parse($report->start_time)->format('H:i'));
                    $sheet->setCellValue('D' . $row, Carbon::parse($report->end_time)->format('H:i'));
                    $sheet->setCellValue('E' . $row, $report->location);
                    
                    // Detail Pekerjaan
                    $details = $report->details->map(function($detail) {
                        return "- " . $detail->description;
                    })->join("\n");
                    $sheet->setCellValue('F' . $row, $details);
                    
                    // Status
                    $statuses = $report->details->pluck('status')->unique();
                    $sheet->setCellValue('G' . $row, $statuses->join(', '));

                    // Set wrap text untuk detail pekerjaan
                    $sheet->getStyle('F' . $row)->getAlignment()
                        ->setWrapText(true)
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

                    // Cek dan merge cells untuk project code
                    if ($lastProject !== null && $lastProject !== $report->project_code) {
                        if ($projectStartRow < $row - 1) {
                            $sheet->mergeCells("B{$projectStartRow}:B" . ($row - 1));
                            $sheet->getStyle("B{$projectStartRow}")
                                ->getAlignment()
                                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                        }
                        $projectStartRow = $row;
                    }
                    $lastProject = $report->project_code;

                    // Cek dan merge cells untuk jam mulai
                    $currentStartTime = Carbon::parse($report->start_time)->format('H:i');
                    if ($lastStartTime !== null && $lastStartTime !== $currentStartTime) {
                        if ($startTimeStartRow < $row - 1) {
                            $sheet->mergeCells("C{$startTimeStartRow}:C" . ($row - 1));
                            $sheet->getStyle("C{$startTimeStartRow}")
                                ->getAlignment()
                                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                        }
                        $startTimeStartRow = $row;
                    }
                    $lastStartTime = $currentStartTime;

                    // Cek dan merge cells untuk jam selesai
                    $currentEndTime = Carbon::parse($report->end_time)->format('H:i');
                    if ($lastEndTime !== null && $lastEndTime !== $currentEndTime) {
                        if ($endTimeStartRow < $row - 1) {
                            $sheet->mergeCells("D{$endTimeStartRow}:D" . ($row - 1));
                            $sheet->getStyle("D{$endTimeStartRow}")
                                ->getAlignment()
                                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                        }
                        $endTimeStartRow = $row;
                    }
                    $lastEndTime = $currentEndTime;

                    // Tambahkan pengecekan untuk lokasi
                    if ($lastLocation !== null && $lastLocation !== $report->location) {
                        if ($locationStartRow < $row - 1) {
                            $sheet->mergeCells("E{$locationStartRow}:E" . ($row - 1));
                            $sheet->getStyle("E{$locationStartRow}")
                                ->getAlignment()
                                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                        }
                        $locationStartRow = $row;
                    }
                    $lastLocation = $report->location;

                    $row++;
                }

                // Merge cells terakhir untuk setiap kolom jika diperlukan
                if ($startRow < ($row - 1)) {
                    // Merge tanggal
                    $sheet->mergeCells("A{$startRow}:A" . ($row - 1));
                    $sheet->getStyle("A{$startRow}")
                        ->getAlignment()
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                    // Merge project code terakhir
                    if ($projectStartRow < $row) {
                        $sheet->mergeCells("B{$projectStartRow}:B" . ($row - 1));
                        $sheet->getStyle("B{$projectStartRow}")
                            ->getAlignment()
                            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    }

                    // Merge jam mulai terakhir
                    if ($startTimeStartRow < $row) {
                        $sheet->mergeCells("C{$startTimeStartRow}:C" . ($row - 1));
                        $sheet->getStyle("C{$startTimeStartRow}")
                            ->getAlignment()
                            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    }

                    // Merge jam selesai terakhir
                    if ($endTimeStartRow < $row) {
                        $sheet->mergeCells("D{$endTimeStartRow}:D" . ($row - 1));
                        $sheet->getStyle("D{$endTimeStartRow}")
                            ->getAlignment()
                            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    }

                    // Merge lokasi terakhir
                    if ($locationStartRow < $row) {
                        $sheet->mergeCells("E{$locationStartRow}:E" . ($row - 1));
                        $sheet->getStyle("E{$locationStartRow}")
                            ->getAlignment()
                            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    }
                }
            }

            // Auto-size columns
            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Set border untuk semua cells yang digunakan
            $lastRow = $row - 1;
            $sheet->getStyle("A5:G{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            );

            // Set header untuk download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Laporan_' . $user->name . '_' . $year . '_' . $month . '.xlsx"');
            header('Cache-Control: max-age=0');

            // Create Excel writer
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            
            // Save to php output
            ob_end_clean();
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            \Log::error('Excel export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengexport file: ' . $e->getMessage());
        }
    }
} 