<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $query = Report::with(['user', 'details'])
            ->when(!auth()->user()->hasRole('admin'), function ($query) {
                return $query->where('user_id', auth()->id());
            })
            ->when($request->filled('employee_search'), function ($query) use ($request) {
                return $query->whereHas('user', function($q) use ($request) {
                    $q->where('name', $request->employee_search);
                });
            })
            ->when($request->filled('report_date'), function ($query) use ($request) {
                return $query->whereDate('report_date', $request->report_date);
            })
            ->when($request->filled('location'), function ($query) use ($request) {
                return $query->where('location', $request->location);
            })
            ->when($request->filled('project_code'), function ($query) use ($request) {
                return $query->where('project_code', $request->project_code);
            })
            ->latest('report_date');

        // Debug query
        \Log::info('Report Query', [
            'user_id' => $request->user_id,
            'employee_search' => $request->employee_search,
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);

        // Get unique values for dropdowns
        $locations = Report::distinct()->pluck('location');
        $projectCodes = Report::distinct()->pluck('project_code');
        $employees = User::pluck('name');
        $workDayTypes = ['Hari Kerja', 'Hari Libur'];

        $reports = $query->paginate(10)->withQueryString();

        return view('reports.index', compact('reports', 'locations', 'projectCodes', 'workDayTypes', 'employees'));
    }

    public function create()
    {
        return view('reports.create');
    }

    private function isOvertime($start_time, $end_time, $is_overnight = false, $work_day_type = 'Hari Kerja', $report_date = null)
    {
        // Gunakan report_date dari parameter atau hari ini jika null
        $date = $report_date ? Carbon::parse($report_date) : Carbon::today();
        $dayOfWeek = $date->dayOfWeek;

        // Hari Minggu (0) otomatis dianggap lembur
        if ($dayOfWeek == 0) {
            return true;
        }

        // Cek status Hari Libur (hanya berlaku untuk Senin-Sabtu)
        if ($work_day_type === 'Hari Libur' && $dayOfWeek != 0) {
            return true;
        }

        $normal_start = '08:45';
        // Jam pulang berbeda untuk hari Sabtu
        $normal_end = ($dayOfWeek == 6) ? '13:00' : '17:00';
        
        // Convert times untuk perbandingan
        $start = substr($start_time, 0, 5);
        $end = substr($end_time, 0, 5);

        // Jika overnight, otomatis overtime
        if ($is_overnight) {
            return true;
        }

        // Cek overtime berdasarkan start time atau end time
        return $start < $normal_start || $end > $normal_end;
    }

    public function store(Request $request)
    {
        $request->validate([
            'report_date' => 'required|date',
            'project_code' => 'required|string',
            'location' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
            'work_day_type' => 'required|in:Hari Kerja,Hari Libur',
            'work_details' => 'required|array|min:1',
            'work_details.*.description' => 'required|string',
            'work_details.*.status' => 'required|in:Selesai,Dalam Proses,Tertunda,Bermasalah',
        ]);

        $is_overtime = $this->isOvertime(
            $request->start_time, 
            $request->end_time,
            $request->boolean('is_overnight'),
            $request->work_day_type,
            $request->report_date
        );

        $report = Report::create([
            'user_id' => auth()->id(),
            'report_date' => $request->report_date,
            'project_code' => $request->project_code,
            'location' => $request->location,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_overnight' => $request->boolean('is_overnight'),
            'is_overtime' => $is_overtime,
            'work_day_type' => $request->work_day_type,
        ]);

        foreach ($request->work_details as $detail) {
            $report->details()->create([
                'description' => $detail['description'],
                'status' => $detail['status'],
            ]);
        }

        return redirect()->route('reports.show', $report)
            ->with('success', 'Laporan berhasil dibuat.');
    }

    public function show(Report $report)
    {
        $this->authorize('view', $report);
        return view('reports.show', compact('report'));
    }

    public function edit(Report $report)
    {
        $this->authorize('update', $report);
        return view('reports.edit', compact('report'));
    }

    public function update(Request $request, Report $report)
    {
        $this->authorize('update', $report);

        $validated = $request->validate([
            'report_date' => 'required|date',
            'project_code' => 'required|string',
            'location' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
            'work_details' => 'required|array|min:1',
            'work_details.*.description' => 'required|string',
            'work_details.*.status' => 'required|in:Selesai,Dalam Proses,Tertunda,Bermasalah',
        ]);

        $is_overtime = $this->isOvertime(
            $request->start_time, 
            $request->end_time,
            $request->boolean('is_overnight'),
            $request->work_day_type,
            $request->report_date
        );

        DB::beginTransaction();
        try {
            // Update report dengan waktu Asia/Jakarta
            $report->update([
                'report_date' => $validated['report_date'],
                'project_code' => $validated['project_code'],
                'location' => $validated['location'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'is_overnight' => $request->boolean('is_overnight'),
                'is_overtime' => $is_overtime,
                'work_day_type' => $request->work_day_type,
                'updated_by' => auth()->id(),
            ]);

            // Set timezone ke Asia/Jakarta sebelum touch
            date_default_timezone_set('Asia/Jakarta');
            $report->touch();

            // Update work details
            $report->details()->delete();
            foreach ($validated['work_details'] as $detail) {
                $report->details()->create([
                    'description' => $detail['description'],
                    'status' => $detail['status'],
                ]);
            }

            DB::commit();
            return redirect()->route('reports.show', $report)
                ->with('success', 'Laporan berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal memperbarui laporan: ' . $e->getMessage());
        }
    }

    public function destroy(Report $report)
    {
        $this->authorize('delete', $report);
        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }

    public function export(Report $report)
    {
        $this->authorize('view', $report);

        try {
            // Load template
            $templatePath = storage_path('app/templates/exportlembur.xlsx');
            if (!file_exists($templatePath)) {
                throw new \Exception('Template file not found');
            }

            $spreadsheet = IOFactory::load($templatePath);
            
            // Format tanggal dan data umum
            Carbon::setLocale('id');
            $reportDate = Carbon::parse($report->report_date);
            $dayDate = $reportDate->isoFormat('dddd, D MMMM Y');
            $exportDate = 'Tgl. ' . $reportDate->isoFormat('D MMMM Y');

            // Waktu normal
            $dayOfWeek = $reportDate->dayOfWeek;
            $normalStart = '08:45';
            $normalEnd = ($dayOfWeek == 6) ? '13:00' : '17:00';
            
            // Convert times untuk perbandingan
            $startTime = substr($report->start_time, 0, 5);
            $endTime = substr($report->end_time, 0, 5);
            
            // Function untuk mengisi sheet
            $fillSheet = function($sheet, $start, $end) use ($report, $dayDate, $exportDate) {
                // Set checkbox berdasarkan work_day_type
                if ($report->work_day_type === 'Hari Kerja') {
                    $sheet->setCellValue('H7', 'Hari Kerja            ☑');
                    $sheet->setCellValue('H8', 'Hari Libur            ☐');
                } else {
                    $sheet->setCellValue('H7', 'Hari Kerja            ☐');
                    $sheet->setCellValue('H8', 'Hari Libur            ☑');
                }

                // Fill data
                $sheet->setCellValue('C7', $report->user->name);
                $sheet->setCellValue('C8', 'Project Engineering');
                $sheet->setCellValue('C11', $dayDate);
                $sheet->setCellValue('H11', $start);
                $sheet->setCellValue('H12', $end);

                // Set checkbox dan lokasi berdasarkan perbandingan lokasi
                if ($report->location === $report->user->homebase) {
                    $sheet->setCellValue('C12', '☑');
                    $sheet->setCellValue('C13', '☐');
                    $sheet->setCellValue('E13', '');
                } else {
                    $sheet->setCellValue('C12', '☐');
                    $sheet->setCellValue('C13', '☑');
                    $sheet->setCellValue('E13', $report->location);
                }

                // Fill work details (maksimal 3)
                $details = $report->details->take(3)->values();
                foreach ($details as $index => $detail) {
                    $description = preg_replace('/^Task #\d+:\s*/', '', $detail->description);
                    $description = preg_replace('/ - (Selesai|Dalam Proses|Tertunda|Bermasalah)$/', '', $description);
                    $currentRow = 14 + $index;
                    $sheet->setCellValue('C' . $currentRow, $description);
                }

                // Project ID dan tanda tangan tetap di posisi yang sama
                $sheet->setCellValue('C17', $report->project_code);
                $sheet->setCellValue('B25', $report->user->name);
                $sheet->setCellValue('B26', $exportDate);

                // Tambahkan border bottom untuk cell tanda tangan
                $sheet->getStyle('B25')->getBorders()->getBottom()->setBorderStyle(
                    \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                );

                // Update posisi tanda tangan
                if ($report->user->signature_path && file_exists(storage_path('app/public/' . $report->user->signature_path))) {
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setName('Signature');
                    $drawing->setDescription('Signature');
                    $drawing->setPath(storage_path('app/public/' . $report->user->signature_path));
                    $drawing->setCoordinates('B23');
                    $drawing->setWidth(200);
                    $drawing->setHeight(80);
                    $drawing->setOffsetX(35);
                    $drawing->setOffsetY(0);
                    $drawing->setRotation(0);
                    $drawing->setWorksheet($sheet);
                }
            };

            // Logika export berdasarkan tipe hari dan status
            if ($report->work_day_type === 'Hari Libur' || $dayOfWeek == 0) {
                // Jika hari libur atau Minggu, semua jam dihitung lembur
                $sheet1 = $spreadsheet->getSheet(0);
                $fillSheet($sheet1, $startTime, $endTime);
            } else {
                // Untuk hari kerja normal
                if ($startTime < $normalStart) {
                    // Lembur pagi
                    $sheet1 = $spreadsheet->getSheet(0);
                    $fillSheet($sheet1, $startTime, $normalStart);
                }

                if ($endTime > $normalEnd || $report->is_overnight) {
                    // Lembur sore/malam
                    $sheet2 = $spreadsheet->getSheet(1);
                    $fillSheet($sheet2, $normalEnd, $endTime);
                }
            }

            // Set header untuk download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Lembur-' . $report->user->name . '-' . $report->report_date->format('Y-m-d') . '.xlsx"');
            header('Cache-Control: max-age=0');

            // Create Excel writer
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            
            // Save to php output
            ob_end_clean();
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengexport file: ' . $e->getMessage());
        }
    }
} 