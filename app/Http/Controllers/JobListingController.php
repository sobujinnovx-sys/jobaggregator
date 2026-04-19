<?php

namespace App\Http\Controllers;

use App\Models\JobListing;
use Illuminate\Http\Request;

class JobListingController extends Controller
{
    /**
     * Display paginated, filterable job listings.
     */
    public function index(Request $request)
    {
        $jobs = JobListing::approved()
            ->with(['company', 'category'])
            ->byLocationType($request->input('location_type'))
            ->byExperience($request->input('experience'))
            ->byKeyword($request->input('keyword'))
            ->byRegion($request->input('region'))
            ->when($request->input('category'), function ($q, $categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->latest('posted_at')
            ->paginate(15)
            ->withQueryString();

        $categories = \App\Models\Category::orderBy('name')->get();

        return view('jobs.index', compact('jobs', 'categories'));
    }

    /**
     * Show a single job listing.
     */
    public function show(JobListing $jobListing)
    {
        // Only show approved jobs to public
        abort_unless($jobListing->status === 'approved', 404);

        $jobListing->load(['company', 'category']);

        $relatedJobs = JobListing::approved()
            ->where('id', '!=', $jobListing->id)
            ->where(function ($q) use ($jobListing) {
                $q->where('company_id', $jobListing->company_id)
                  ->orWhere('category_id', $jobListing->category_id);
            })
            ->with('company')
            ->take(4)
            ->get();

        return view('jobs.show', compact('jobListing', 'relatedJobs'));
    }
}
