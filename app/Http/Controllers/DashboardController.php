<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get recent reports
        $recentReportsQuery = Report::with(['user', 'details'])
            ->latest('report_date');

        if (!auth()->user()->hasRole('admin')) {
            $recentReportsQuery->where('user_id', auth()->id());
        }

        $recentReports = $recentReportsQuery->take(5)->get();

        // Get summaries for calendar
        $summaries = collect();
        if (auth()->user()->hasRole('admin')) {
            $summaries = User::role('employee')
                ->with(['reports' => function ($query) {
                    $query->whereMonth('report_date', now()->month)
                        ->whereYear('report_date', now()->year);
                }])
                ->get()
                ->map(function ($user) {
                    return (object)[
                        'user_name' => $user->name,
                        'reports' => $user->reports
                    ];
                });
        } else {
            $summaries->push((object)[
                'user_name' => auth()->user()->name,
                'reports' => auth()->user()->reports()
                    ->whereMonth('report_date', now()->month)
                    ->whereYear('report_date', now()->year)
                    ->get()
            ]);
        }

        // Get workers without report today
        $workersWithoutReport = collect();
        if (auth()->user()->hasRole('admin')) {
            $workersWithoutReport = User::role('employee')
                ->whereDoesntHave('reports', function ($query) {
                    $query->whereDate('report_date', today());
                })
                ->get();
        }

        return view('dashboard', compact('recentReports', 'summaries', 'workersWithoutReport'));
    }
} 