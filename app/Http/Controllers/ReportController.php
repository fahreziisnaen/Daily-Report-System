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
        $query = Report::with(['user', 'details']);
        
        // Debug user role
        \Log::info('User Role Check', [
            'user_id' => auth()->id(),
            'is_admin' => auth()->user()->hasRole('admin'),
            'roles' => auth()->user()->getRoleNames()
        ]);

        // Filter berdasarkan user_id kecuali untuk admin
        if (!auth()->user()->hasRole('admin')) {
            $query->where('user_id', auth()->id());
        }

        // Filter lainnya
        $query->when($request->filled('employee_search'), function ($query) use ($request) {
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
            'user_id' => auth()->id(),
            'is_filtered' => !auth()->user()->hasRole('admin'),
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);

        // Get data for dropdowns
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
        // Log input parameters
        \Log::info('Overtime Calculation Input', [
            'start_time' => $start_time,
            'end_time' => $end_time,
            'is_overnight' => $is_overnight,
            'work_day_type' => $work_day_type,
            'report_date' => $report_date
        ]);

        $date = $report_date ? Carbon::parse($report_date) : Carbon::today();
        $dayOfWeek = $date->dayOfWeek;

        $start = Carbon::parse($report_date . ' ' . $start_time);
        $end = Carbon::parse($report_date . ' ' . $end_time);
        
        if ($is_overnight) {
            $end->addDay();
        }

        // Gunakan diffInMinutes(true) untuk mendapatkan nilai absolut
        $totalMinutes = $end->diffInMinutes($start, true);
        $totalHours = $totalMinutes / 60;

        // Log calculation results
        \Log::info('Overtime Calculation Result', [
            'start_datetime' => $start->format('Y-m-d H:i'),
            'end_datetime' => $end->format('Y-m-d H:i'),
            'total_hours' => $totalHours,
            'day_of_week' => $dayOfWeek,
            'is_overtime' => ($dayOfWeek >= 1 && $dayOfWeek <= 5) ? ($totalHours >= 8.25) : ($dayOfWeek == 6 ? ($totalHours >= 4.25) : true)
        ]);

        // Jika hari Minggu (0) atau hari libur, otomatis overtime
        if ($dayOfWeek == 0 || $work_day_type === 'Hari Libur') {
            return true;
        }

        // Untuk hari kerja (Senin-Jumat)
        if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
            return $totalHours >= 8.25; // 8 jam 15 menit
        }
        // Untuk hari Sabtu
        else if ($dayOfWeek == 6) {
            return $totalHours >= 4.25; // 4 jam 15 menit
        }

        return false;
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
            'is_shift' => $request->boolean('is_shift'),
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

        // Hitung ulang overtime berdasarkan data terbaru
        $is_overtime = $this->isOvertime(
            $request->start_time,
            $request->end_time,
            $request->boolean('is_overnight'),
            $request->work_day_type,
            $request->report_date
        );

        // Update report dengan data baru termasuk is_overtime
        $report->update([
            'report_date' => $request->report_date,
            'project_code' => $request->project_code,
            'location' => $request->location,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_overnight' => $request->boolean('is_overnight'),
            'is_shift' => $request->boolean('is_shift'),
            'is_overtime' => $is_overtime,  // Set nilai overtime yang baru
            'work_day_type' => $request->work_day_type,
            'updated_by' => auth()->id()
        ]);

        // Update work details
        $report->details()->delete();
        foreach ($request->work_details as $detail) {
            $report->details()->create([
                'description' => $detail['description'],
                'status' => $detail['status'],
            ]);
        }

        return redirect()->route('reports.show', $report)
            ->with('success', 'Laporan berhasil diupdate.');
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
            $sheet = $spreadsheet->getActiveSheet();
            
            // Format tanggal dan data umum
            Carbon::setLocale('id');
            $reportDate = Carbon::parse($report->report_date);
            $dayDate = $reportDate->isoFormat('dddd, D MMMM Y');
            $exportDate = 'Tgl. ' . $reportDate->isoFormat('D MMMM Y');

            // Waktu mulai dan selesai (langsung ambil dari database)
            $startTime = Carbon::parse($report->start_time)->format('H:i');
            $endTime = Carbon::parse($report->end_time)->format('H:i');

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
            $sheet->setCellValue('H11', $startTime);
            $sheet->setCellValue('H12', $endTime);

            // Set checkbox dan lokasi
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

            // Project ID dan tanda tangan
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

            // Set header untuk download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Lembur-' . $report->user->name . '-' . $report->report_date->format('Y-m-d') . '.xlsx"');
            header('Cache-Control: max-age=0');

            // Create Excel writer dan export
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            ob_end_clean();
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            \Log::error('Export Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Gagal mengexport file: ' . $e->getMessage());
        }
    }
} 