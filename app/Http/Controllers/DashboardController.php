<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\JobListing;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the main dashboard with statistics and overview.
     */
    public function index()
    {
        $totalJobs = JobListing::approved()->count();
        $remoteJobs = JobListing::approved()->remote()->count();
        $localJobs = JobListing::approved()->byRegion('bd')->count();

        // Top hiring companies (by approved job count)
        $topCompanies = Company::withCount(['jobListings' => function ($q) {
            $q->where('status', 'approved');
        }])
            ->whereHas('jobListings', function ($q) {
                $q->where('status', 'approved');
            })
            ->orderByDesc('job_listings_count')
            ->take(5)
            ->get();

        // Most popular job roles
        $popularRoles = JobListing::approved()
            ->select('title')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('title')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        // Latest jobs for preview
        $latestJobs = JobListing::approved()
            ->with(['company', 'category'])
            ->latest('posted_at')
            ->take(6)
            ->get();

        return view('dashboard', compact(
            'totalJobs', 'remoteJobs', 'localJobs', 'topCompanies', 'popularRoles', 'latestJobs'
        ));
    }
}
