@extends('layouts.app')

@section('title', 'Saved Jobs - Job Aggregator')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">
        <i class="fas fa-bookmark text-indigo-600 mr-2"></i>Saved Jobs
    </h1>

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
                                    {{ $job->location_type === 'remote' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($job->location_type) }}
                                </span>
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst($job->experience_level) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2 flex-shrink-0">
                        <form method="POST" action="{{ route('bookmarks.toggle', $job) }}">
                            @csrf
                            <button type="submit" class="border border-red-300 text-red-600 px-4 py-2 rounded-lg text-sm hover:bg-red-50">
                                <i class="fas fa-trash mr-1"></i>Remove
                            </button>
                        </form>
                        <a href="{{ route('jobs.show', $job) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16 bg-white rounded-xl border border-gray-200">
                <i class="fas fa-bookmark text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500 text-lg">You haven't saved any jobs yet.</p>
                <a href="{{ route('jobs.index') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800">
                    Browse Jobs
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $jobs->links() }}
    </div>
</div>
@endsection
