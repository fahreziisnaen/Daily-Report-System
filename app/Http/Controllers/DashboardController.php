<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        
        // Get selected month and year from request, default to current month/year
        $selectedMonth = $request->get('month', now()->month);
        $selectedYear = $request->get('year', now()->year);
        
        // Create Carbon instance for selected date
        $selectedDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1);
        
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
            // Untuk Super Admin, ambil semua user termasuk Super Admin
            $summaries = User::with(['reports' => function ($query) use ($selectedMonth, $selectedYear) {
                    $query->whereMonth('report_date', $selectedMonth)
                        ->whereYear('report_date', $selectedYear);
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
                    ->whereMonth('report_date', $selectedMonth)
                    ->whereYear('report_date', $selectedYear)
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
            $monthlyReports = Report::whereMonth('report_date', $selectedMonth)
                ->whereYear('report_date', $selectedYear)
                ->count();
            $dailyReports = Report::whereDate('report_date', $today)->count();
        } else {
            $totalReports = Report::where('user_id', auth()->id())->count();
            $monthlyReports = Report::where('user_id', auth()->id())
                ->whereMonth('report_date', $selectedMonth)
                ->whereYear('report_date', $selectedYear)
                ->count();
            $dailyReports = Report::where('user_id', auth()->id())
                ->whereDate('report_date', $today)
                ->count();
        }

        // Generate months and years for dropdown
        $months = collect(range(1, 12))->mapWithKeys(function ($month) {
            return [$month => Carbon::create()->month($month)->format('F')];
        });

        $years = collect(range(now()->year - 2, now()->year + 1));

        return view('dashboard', compact(
            'recentReports',
            'summaries',
            'usersWithoutReport',
            'totalReports',
            'monthlyReports',
            'dailyReports',
            'months',
            'years',
            'selectedMonth',
            'selectedYear',
            'selectedDate'
        ));
    }
} 