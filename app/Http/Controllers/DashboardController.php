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

        if (!auth()->user()->hasRole('admin')) {
            $recentReportsQuery->where('user_id', auth()->id());
        }

        $recentReports = $recentReportsQuery->take(5)->get();

        // Get summaries for calendar
        $summaries = collect();
        if (auth()->user()->hasRole('admin')) {
            $summaries = User::with(['reports' => function ($query) {
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

        // Get users without report today (including admin)
        $usersWithoutReport = User::whereDoesntHave('reports', function ($query) use ($today) {
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

        // Hitung statistik
        $totalReports = Report::count();
        $monthlyReports = Report::whereMonth('report_date', now()->month)->count();
        $dailyReports = Report::whereDate('report_date', $today)->count();

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