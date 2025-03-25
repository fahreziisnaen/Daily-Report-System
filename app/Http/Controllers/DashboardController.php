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
        $today = Carbon::today();
        
        // Get recent reports
        $recentReportsQuery = Report::with(['user', 'details'])
            ->latest('report_date');

        // Jika bukan Super Admin, filter hanya laporan sendiri
        if (!auth()->user()->hasRole('Super Admin')) {
            $recentReportsQuery->where('user_id', auth()->id());
        }

        $recentReports = $recentReportsQuery->take(5)->get();

        // Get summaries for calendar
        $summaries = collect();
        if (auth()->user()->hasRole('Super Admin')) {
            // Untuk Super Admin, ambil semua user
            $summaries = User::role('employee')  // Hanya ambil user dengan role employee
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
            // Untuk user biasa, hanya ambil data sendiri
            $summaries->push((object)[
                'user_name' => auth()->user()->name,
                'reports' => auth()->user()->reports()
                    ->whereMonth('report_date', now()->month)
                    ->whereYear('report_date', now()->year)
                    ->get()
            ]);
        }

        // Get users without report today (untuk Super Admin)
        $usersWithoutReport = collect();
        if (auth()->user()->hasRole('Super Admin')) {
            $usersWithoutReport = User::role('employee')  // Hanya cek user dengan role employee
                ->whereDoesntHave('reports', function ($query) use ($today) {
                    $query->whereDate('report_date', $today);
                })
                ->get()
                ->map(function($user) {
                    return [
                        'name' => $user->name,
                        'email' => $user->email,
                        'avatar_url' => $user->avatar_url
                    ];
                });
        }

        // Hitung statistik
        if (auth()->user()->hasRole('Super Admin')) {
            $totalReports = Report::count();
            $monthlyReports = Report::whereMonth('report_date', now()->month)->count();
            $dailyReports = Report::whereDate('report_date', $today)->count();
        } else {
            $totalReports = Report::where('user_id', auth()->id())->count();
            $monthlyReports = Report::where('user_id', auth()->id())
                ->whereMonth('report_date', now()->month)
                ->count();
            $dailyReports = Report::where('user_id', auth()->id())
                ->whereDate('report_date', $today)
                ->count();
        }

        return view('dashboard', compact(
            'recentReports',
            'summaries',
            'usersWithoutReport',
            'totalReports',
            'monthlyReports',
            'dailyReports'
        ));
    }
} 