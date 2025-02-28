<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

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

        $reports = $query->paginate(10)->withQueryString();

        return view('reports.index', compact('reports'));
    }

    public function create()
    {
        return view('reports.create');
    }

    private function isOvertime($start_time, $end_time)
    {
        $normal_start = '08:45';
        $normal_end = '17:00';
        
        return $start_time < $normal_start || $end_time > $normal_end;
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

        $is_overtime = $this->isOvertime($request->start_time, $request->end_time);

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

        return redirect()->route('reports.index')
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

        $is_overtime = $this->isOvertime($request->start_time, $request->end_time);

        $report->update([
            'report_date' => $validated['report_date'],
            'project_code' => $validated['project_code'],
            'location' => $validated['location'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'is_overnight' => $request->boolean('is_overnight'),
            'is_overtime' => $is_overtime,
            'work_day_type' => $request->work_day_type,
        ]);

        // Update work details
        $report->details()->delete(); // Delete existing details
        foreach ($validated['work_details'] as $detail) {
            $report->details()->create([
                'description' => $detail['description'],
                'status' => $detail['status'],
            ]);
        }

        return redirect()->route('reports.show', $report)
            ->with('success', 'Laporan berhasil diperbarui.');
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
            $normalStart = '08:45';
            $normalEnd = '17:00';
            
            // Convert times untuk perbandingan
            $startTime = substr($report->start_time, 0, 5);
            $endTime = substr($report->end_time, 0, 5);
            
            // Handle overnight case
            if ($report->is_overnight) {
                // Jika overnight, anggap waktu selesai lebih besar dari 17:00
                $isAfterNormalEnd = true;
            } else {
                $isAfterNormalEnd = $endTime > $normalEnd;
            }

            // Fungsi untuk mengisi sheet
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
                    $sheet->setCellValue('C12', '☑'); // Homebase checked
                    $sheet->setCellValue('C13', '☐'); // Lokasi Dinas unchecked
                    $sheet->setCellValue('E13', ''); // Kosongkan lokasi dinas
                } else {
                    $sheet->setCellValue('C12', '☐'); // Homebase unchecked
                    $sheet->setCellValue('C13', '☑'); // Lokasi Dinas checked
                    $sheet->setCellValue('E13', $report->location); // Isi lokasi dinas
                }

                // Fill work details
                $details = $report->details->take(3)->values();
                foreach ($details as $index => $detail) {
                    $description = preg_replace('/^Task #\d+:\s*/', '', $detail->description);
                    $description = preg_replace('/ - (Selesai|Dalam Proses|Tertunda|Bermasalah)$/', '', $description);
                    $sheet->setCellValue('C' . (14 + $index), $description);
                }

                $sheet->setCellValue('C17', $report->project_code);
                $sheet->setCellValue('B25', $report->user->name);
                $sheet->setCellValue('B26', $exportDate);

                // Add signature if exists
                if ($report->user->signature_path && file_exists(storage_path('app/public/' . $report->user->signature_path))) {
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setName('Signature');
                    $drawing->setDescription('Signature');
                    $drawing->setPath(storage_path('app/public/' . $report->user->signature_path));
                    $drawing->setCoordinates('B23');      // Tetap di B23
                    $drawing->setWidth(200);              // Ukuran tetap 200
                    $drawing->setHeight(80);              // Ukuran tetap 80
                    $drawing->setOffsetX(35);             // Tambah offset ke kanan dari 25 ke 35
                    $drawing->setOffsetY(0);              // Ubah dari -10 ke 0 untuk turun
                    $drawing->setRotation(0);
                    $drawing->setWorksheet($sheet);
                }
            };

            // Sheet 1 - Dari Pagi
            if ($startTime < $normalStart) {
                $sheet1 = $spreadsheet->getSheet(0); // "Dari Pagi" sheet
                $fillSheet($sheet1, $startTime, $normalStart);
            }

            // Sheet 2 - Dari Sore
            if ($isAfterNormalEnd) {
                $sheet2 = $spreadsheet->getSheet(1); // "Dari Sore" sheet
                $fillSheet($sheet2, $normalEnd, $endTime);
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