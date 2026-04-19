<?php

namespace App\Http\Controllers;

use App\Models\JobAlert;
use Illuminate\Http\Request;

class JobAlertController extends Controller
{
    /**
     * Store a new job alert subscription.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'keyword' => 'nullable|string|max:255',
            'location_type' => 'nullable|in:remote,onsite,hybrid',
        ]);

        JobAlert::create($validated);

        return back()->with('success', 'Job alert created! You will receive emails for matching jobs.');
    }
}
