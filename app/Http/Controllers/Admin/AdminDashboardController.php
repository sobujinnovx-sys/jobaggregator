<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Admin dashboard overview.
     */
    public function index()
    {
        $stats = [
            'total_jobs' => JobListing::count(),
            'pending_jobs' => JobListing::where('status', 'pending')->count(),
            'approved_jobs' => JobListing::where('status', 'approved')->count(),
            'rejected_jobs' => JobListing::where('status', 'rejected')->count(),
            'companies' => Company::count(),
            'users' => User::count(),
        ];

        $pendingJobs = JobListing::where('status', 'pending')
            ->with(['company', 'category'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingJobs'));
    }
}
