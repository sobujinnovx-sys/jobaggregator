<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\JobListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    /**
     * Toggle bookmark for a job listing.
     */
    public function toggle(JobListing $jobListing)
    {
        $user = Auth::user();

        $existing = Bookmark::where('user_id', $user->id)
            ->where('job_listing_id', $jobListing->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', 'Bookmark removed.');
        }

        Bookmark::create([
            'user_id' => $user->id,
            'job_listing_id' => $jobListing->id,
        ]);

        return back()->with('success', 'Job bookmarked!');
    }

    /**
     * Show all bookmarked jobs for the current user.
     */
    public function index()
    {
        $jobs = Auth::user()
            ->bookmarkedJobs()
            ->approved()
            ->with(['company', 'category'])
            ->latest('bookmarks.created_at')
            ->paginate(15);

        return view('bookmarks.index', compact('jobs'));
    }
}
