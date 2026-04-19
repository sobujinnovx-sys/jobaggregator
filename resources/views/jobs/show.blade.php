@extends('layouts.app')

@section('title', $jobListing->title . ' - Job Aggregator')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Home</a>
        <span class="mx-2">/</span>
        <a href="{{ route('jobs.index') }}" class="hover:text-indigo-600">Jobs</a>
        <span class="mx-2">/</span>
        <span class="text-gray-900">{{ $jobListing->title }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="flex items-start gap-4 mb-6">
                    <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <span class="text-xl font-bold text-gray-600">{{ substr($jobListing->company->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $jobListing->title }}</h1>
                        <p class="text-lg text-gray-600">{{ $jobListing->company->name }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 mb-6">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $jobListing->location_type === 'remote' ? 'bg-green-100 text-green-800' : ($jobListing->location_type === 'hybrid' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                        <i class="fas {{ $jobListing->location_type === 'remote' ? 'fa-globe' : 'fa-building' }} mr-1"></i>
                        {{ ucfirst($jobListing->location_type) }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        {{ ucfirst($jobListing->experience_level) }} Level
                    </span>
                    @if($jobListing->location)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-50 text-gray-600">
                            <i class="fas fa-map-marker-alt mr-1"></i>{{ $jobListing->location }}
                        </span>
                    @endif
                    @if($jobListing->salary_range)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-50 text-emerald-700">
                            <i class="fas fa-dollar-sign mr-1"></i>{{ $jobListing->salary_range }}
                        </span>
                    @endif
                    @if($jobListing->category)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-50 text-indigo-700">
                            {{ $jobListing->category->name }}
                        </span>
                    @endif
                </div>

                <div class="prose prose-gray max-w-none">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Job Description</h3>
                    <div class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $jobListing->description ?? 'No detailed description provided.' }}</div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Apply Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <a href="{{ $jobListing->apply_link }}" target="_blank" rel="noopener noreferrer"
                   class="block w-full bg-indigo-600 text-white text-center px-6 py-3 rounded-lg hover:bg-indigo-700 font-medium">
                    <i class="fas fa-external-link-alt mr-2"></i>Apply Now
                </a>

                @auth
                    <form method="POST" action="{{ route('bookmarks.toggle', $jobListing) }}" class="mt-3">
                        @csrf
                        <button type="submit" class="w-full border border-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-50 font-medium">
                            <i class="fas fa-bookmark mr-2"></i>Save Job
                        </button>
                    </form>
                @endauth

                <div class="mt-4 pt-4 border-t border-gray-200 space-y-3 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-calendar text-gray-400 w-5"></i>
                        Posted: {{ $jobListing->posted_at?->format('M d, Y') ?? 'Recently' }}
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-building text-gray-400 w-5"></i>
                        {{ $jobListing->company->name }}
                    </div>
                    @if($jobListing->company->website)
                        <div class="flex items-center gap-2">
                            <i class="fas fa-globe text-gray-400 w-5"></i>
                            <a href="{{ $jobListing->company->website }}" target="_blank" rel="noopener noreferrer"
                               class="text-indigo-600 hover:text-indigo-800">Company Website</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Related Jobs -->
            @if($relatedJobs->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Related Jobs</h3>
                    <div class="space-y-3">
                        @foreach($relatedJobs as $related)
                            <a href="{{ route('jobs.show', $related) }}" class="block p-3 rounded-lg hover:bg-gray-50 transition">
                                <p class="font-medium text-gray-900 text-sm">{{ $related->title }}</p>
                                <p class="text-xs text-gray-500">{{ $related->company->name }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
