@extends('layouts.app')

@section('title', 'Dashboard - Job Aggregator')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Hero Section -->
    <div class="text-center mb-10">
        <h1 class="text-4xl font-bold text-gray-900">Find Your Dream Tech Job</h1>
        <p class="mt-3 text-lg text-gray-600">Aggregated from top companies worldwide</p>
        <div class="mt-6 max-w-xl mx-auto">
            <form action="{{ route('jobs.index') }}" method="GET" class="flex gap-2">
                <input type="text" name="keyword" placeholder="Search jobs... (e.g. Laravel Developer)"
                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Jobs</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($totalJobs) }}</p>
                </div>
                <div class="bg-indigo-100 p-3 rounded-full">
                    <i class="fas fa-briefcase text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Remote Jobs</p>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($remoteJobs) }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-globe text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Companies</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $topCompanies->count() }}+</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-building text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Job Roles</p>
                    <p class="text-3xl font-bold text-orange-600">{{ $popularRoles->count() }}+</p>
                </div>
                <div class="bg-orange-100 p-3 rounded-full">
                    <i class="fas fa-tags text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Local & Remote Buttons -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-10">
        <a href="{{ route('jobs.index', ['region' => 'bd']) }}"
           class="group flex items-center justify-between bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl p-6 shadow-lg hover:shadow-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200">
            <div class="flex items-center">
                <div class="bg-white/20 p-4 rounded-xl mr-5">
                    <i class="fas fa-flag text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold">Bangladesh Jobs</h3>
                    <p class="text-blue-100 text-sm mt-1">Local companies in Bangladesh</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <span class="text-4xl font-extrabold">{{ number_format($localJobs) }}</span>
                    <p class="text-blue-200 text-xs mt-1">available now</p>
                </div>
                <i class="fas fa-arrow-right opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-200 text-xl"></i>
            </div>
        </a>

        <a href="{{ route('jobs.index', ['region' => 'global', 'location_type' => 'remote']) }}"
           class="group flex items-center justify-between bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl p-6 shadow-lg hover:shadow-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-200">
            <div class="flex items-center">
                <div class="bg-white/20 p-4 rounded-xl mr-5">
                    <i class="fas fa-globe text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold">Remote Jobs</h3>
                    <p class="text-green-100 text-sm mt-1">Work from anywhere worldwide</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <span class="text-4xl font-extrabold">{{ number_format($remoteJobs) }}</span>
                    <p class="text-green-200 text-xs mt-1">available now</p>
                </div>
                <i class="fas fa-arrow-right opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-200 text-xl"></i>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
        <!-- Top Hiring Companies -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-trophy text-yellow-500 mr-2"></i>Top Hiring Companies
            </h2>
            <div class="space-y-3">
                @forelse($topCompanies as $company)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-sm font-bold text-indigo-600">{{ substr($company->name, 0, 1) }}</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $company->name }}</span>
                        </div>
                        <span class="bg-indigo-50 text-indigo-700 text-xs font-medium px-2 py-1 rounded-full">
                            {{ $company->job_listings_count }} jobs
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No companies yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Popular Job Roles -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-fire text-red-500 mr-2"></i>Popular Job Roles
            </h2>
            <div class="space-y-3">
                @forelse($popularRoles as $role)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <a href="{{ route('jobs.index', ['keyword' => $role->title]) }}"
                           class="text-sm font-medium text-gray-900 hover:text-indigo-600">
                            {{ $role->title }}
                        </a>
                        <span class="bg-red-50 text-red-700 text-xs font-medium px-2 py-1 rounded-full">
                            {{ $role->count }} openings
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No roles yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Quick Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-filter text-blue-500 mr-2"></i>Quick Filters
            </h2>
            <div class="space-y-2">
                <a href="{{ route('jobs.index', ['location_type' => 'remote']) }}"
                   class="block px-4 py-3 bg-green-50 rounded-lg hover:bg-green-100 transition">
                    <span class="text-sm font-medium text-green-800"><i class="fas fa-globe mr-2"></i>Remote Jobs</span>
                </a>
                <a href="{{ route('jobs.index', ['location_type' => 'onsite']) }}"
                   class="block px-4 py-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                    <span class="text-sm font-medium text-blue-800"><i class="fas fa-building mr-2"></i>Onsite Jobs</span>
                </a>
                <a href="{{ route('jobs.index', ['experience' => 'junior']) }}"
                   class="block px-4 py-3 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition">
                    <span class="text-sm font-medium text-yellow-800"><i class="fas fa-seedling mr-2"></i>Junior Level</span>
                </a>
                <a href="{{ route('jobs.index', ['experience' => 'senior']) }}"
                   class="block px-4 py-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                    <span class="text-sm font-medium text-purple-800"><i class="fas fa-star mr-2"></i>Senior Level</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Latest Jobs -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Latest Jobs</h2>
            <a href="{{ route('jobs.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                View all jobs <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($latestJobs as $job)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                <span class="font-bold text-gray-600">{{ substr($job->company->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">
                                    <a href="{{ route('jobs.show', $job) }}" class="hover:text-indigo-600">{{ $job->title }}</a>
                                </h3>
                                <p class="text-sm text-gray-500">{{ $job->company->name }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium
                            {{ $job->location_type === 'remote' ? 'bg-green-100 text-green-800' : ($job->location_type === 'hybrid' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                            {{ ucfirst($job->location_type) }}
                        </span>
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                            {{ ucfirst($job->experience_level) }}
                        </span>
                        @if($job->category)
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-indigo-50 text-indigo-700">
                                {{ $job->category->name }}
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">{{ $job->posted_at?->diffForHumans() ?? 'Recently' }}</span>
                        <a href="{{ route('jobs.show', $job) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                            View <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12 text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-4"></i>
                    <p>No jobs available yet. Check back soon!</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
