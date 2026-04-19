<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Models\JobListing;
use Illuminate\Http\Request;

class AdminJobController extends Controller
{
    /**
     * List all jobs with status filter for admin.
     */
    public function index(Request $request)
    {
        $jobs = JobListing::with(['company', 'category'])
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->input('keyword'), fn ($q, $k) => $q->where('title', 'like', "%{$k}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.jobs.index', compact('jobs'));
    }

    /**
     * Show form to create a new job manually.
     */
    public function create()
    {
        $companies = Company::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        return view('admin.jobs.create', compact('companies', 'categories'));
    }

    /**
     * Store a new manually-created job.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'category_id' => 'nullable|exists:categories,id',
            'location_type' => 'required|in:remote,onsite,hybrid',
            'location' => 'nullable|string|max:255',
            'experience_level' => 'required|in:junior,mid,senior,lead',
            'description' => 'nullable|string',
            'apply_link' => 'required|url|max:500',
            'salary_range' => 'nullable|string|max:100',
            'status' => 'required|in:pending,approved,rejected',
            'posted_at' => 'nullable|date',
        ]);

        $validated['source'] = 'manual';
        $validated['posted_at'] = $validated['posted_at'] ?? now();

        JobListing::create($validated);

        return redirect()->route('admin.jobs.index')->with('success', 'Job created successfully.');
    }

    /**
     * Show edit form for a job.
     */
    public function edit(JobListing $jobListing)
    {
        $companies = Company::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        return view('admin.jobs.edit', compact('jobListing', 'companies', 'categories'));
    }

    /**
     * Update a job listing.
     */
    public function update(Request $request, JobListing $jobListing)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'category_id' => 'nullable|exists:categories,id',
            'location_type' => 'required|in:remote,onsite,hybrid',
            'location' => 'nullable|string|max:255',
            'experience_level' => 'required|in:junior,mid,senior,lead',
            'description' => 'nullable|string',
            'apply_link' => 'required|url|max:500',
            'salary_range' => 'nullable|string|max:100',
            'status' => 'required|in:pending,approved,rejected',
            'posted_at' => 'nullable|date',
        ]);

        $jobListing->update($validated);

        return redirect()->route('admin.jobs.index')->with('success', 'Job updated successfully.');
    }

    /**
     * Delete a job listing.
     */
    public function destroy(JobListing $jobListing)
    {
        $jobListing->delete();

        return redirect()->route('admin.jobs.index')->with('success', 'Job deleted.');
    }

    /**
     * Approve a pending job.
     */
    public function approve(JobListing $jobListing)
    {
        $jobListing->update(['status' => 'approved']);
        return back()->with('success', 'Job approved.');
    }

    /**
     * Reject a pending job.
     */
    public function reject(JobListing $jobListing)
    {
        $jobListing->update(['status' => 'rejected']);
        return back()->with('success', 'Job rejected.');
    }
}
