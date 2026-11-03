<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $totalReports = Report::count();
            $acceptCount = Report::where('has_rejects', false)->count();
            $rejectCount = Report::where('has_rejects', true)->count();
            $recentReports = Report::orderBy('created_at', 'desc')->take(6)->get();
        } catch (\Exception $e) {
            $totalReports = 0;
            $acceptCount = 0;
            $rejectCount = 0;
            $recentReports = collect();
        }

        $yieldRate = $totalReports > 0 ? round(($acceptCount / $totalReports) * 100, 1) : 100;

        return view('dashboard', compact(
            'totalReports',
            'acceptCount',
            'rejectCount',
            'yieldRate',
            'recentReports'
        ));
    }
}

