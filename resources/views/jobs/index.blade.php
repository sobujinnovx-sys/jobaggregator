@extends('layouts.app')

@section('title', 'Browse Jobs - Job Aggregator')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Filters Sidebar -->
        <div class="lg:w-72 flex-shrink-0">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-filter mr-2 text-indigo-600"></i>Filters
                </h2>
                <form method="GET" action="{{ route('jobs.index') }}">
                    <!-- Keyword Search -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keyword</label>
                        <input type="text" name="keyword" value="{{ request('keyword') }}"
                               placeholder="e.g. Laravel, React..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>

                    <!-- Location Type -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                        <select name="location_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Locations</option>
                            <option value="remote" {{ request('location_type') === 'remote' ? 'selected' : '' }}>Remote</option>
                            <option value="onsite" {{ request('location_type') === 'onsite' ? 'selected' : '' }}>Onsite</option>
                            <option value="hybrid" {{ request('location_type') === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                    </div>

                    <!-- Experience Level -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Experience Level</label>
                        <select name="experience" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Levels</option>
                            <option value="junior" {{ request('experience') === 'junior' ? 'selected' : '' }}>Junior</option>
                            <option value="mid" {{ request('experience') === 'mid' ? 'selected' : '' }}>Mid-Level</option>
                            <option value="senior" {{ request('experience') === 'senior' ? 'selected' : '' }}>Senior</option>
                            <option value="lead" {{ request('experience') === 'lead' ? 'selected' : '' }}>Lead</option>
                        </select>
                    </div>

                    <!-- Category -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
                        <i class="fas fa-search mr-1"></i>Apply Filters
                    </button>

                    @if(request()->hasAny(['keyword', 'location_type', 'experience', 'category']))
                        <a href="{{ route('jobs.index') }}" class="block text-center mt-2 text-sm text-gray-500 hover:text-gray-700">
                            Clear all filters
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Job Listings -->
        <div class="flex-1">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">
                    Job Listings
                    <span class="text-lg font-normal text-gray-500">({{ $jobs->total() }} results)</span>
                </h1>
            </div>

            <!-- Job Cards Table -->
            <div class="space-y-4">
                @forelse($jobs as $job)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <span class="text-lg font-bold text-gray-600">{{ substr($job->company->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        <a href="{{ route('jobs.show', $job) }}" class="hover:text-indigo-600">{{ $job->title }}</a>
                                    </h3>
                                    <p class="text-sm text-gray-600">{{ $job->company->name }}</p>
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium
                                            {{ $job->location_type === 'remote' ? 'bg-green-100 text-green-800' : ($job->location_type === 'hybrid' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                            <i class="fas {{ $job->location_type === 'remote' ? 'fa-globe' : 'fa-building' }} mr-1"></i>
                                            {{ ucfirst($job->location_type) }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ ucfirst($job->experience_level) }}
                                        </span>
                                        @if($job->location)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-50 text-gray-600">
                                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $job->location }}
                                            </span>
                                        @endif
                                        @if($job->salary_range)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-emerald-50 text-emerald-700">
                                                <i class="fas fa-dollar-sign mr-1"></i>{{ $job->salary_range }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-2 flex-shrink-0">
                                <span class="text-xs text-gray-500">{{ $job->posted_at?->diffForHumans() ?? 'Recently' }}</span>
                                <div class="flex gap-2">
                                    @auth
                                        <form method="POST" action="{{ route('bookmarks.toggle', $job) }}">
                                            @csrf
                                            <button type="submit" class="text-gray-400 hover:text-yellow-500" title="Bookmark">
                                                <i class="fas fa-bookmark"></i>
                                            </button>
                                        </form>
                                    @endauth
                                    <a href="{{ route('jobs.show', $job) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 bg-white rounded-xl border border-gray-200">
                        <i class="fas fa-search text-gray-300 text-5xl mb-4"></i>
                        <p class="text-gray-500 text-lg">No jobs found matching your filters.</p>
                        <a href="{{ route('jobs.index') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800">
                            Clear filters and try again
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $jobs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
