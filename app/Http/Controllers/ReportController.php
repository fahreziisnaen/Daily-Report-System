<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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

    public function store(Request $request)
    {
        $request->validate([
            'report_date' => 'required|date',
            'project_code' => 'required|string',
            'location' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
            'work_details' => 'required|array|min:1',
            'work_details.*.description' => 'required|string',
            'work_details.*.status' => 'required|in:Selesai,Dalam Proses,Tertunda,Bermasalah',
        ]);

        $report = Report::create([
            'user_id' => auth()->id(),
            'report_date' => $request->report_date,
            'project_code' => $request->project_code,
            'location' => $request->location,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_overnight' => $request->boolean('is_overnight'),
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

        $report->update([
            'report_date' => $validated['report_date'],
            'project_code' => $validated['project_code'],
            'location' => $validated['location'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'is_overnight' => $request->boolean('is_overnight'),
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
} 