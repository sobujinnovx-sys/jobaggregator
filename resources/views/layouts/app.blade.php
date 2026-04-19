<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Job Aggregator Dashboard - Find the best tech jobs from top companies">
    <meta name="keywords" content="jobs, tech jobs, remote jobs, developer jobs, career">
    <title>@yield('title', 'Job Aggregator Dashboard')</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-indigo-600">
                        <i class="fas fa-briefcase mr-2"></i>JobAggregator
                    </a>
                    <div class="hidden md:flex space-x-4">
                        <a href="{{ route('dashboard') }}"
                           class="px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-gray-900' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('jobs.index') }}"
                           class="px-3 py-2 text-sm font-medium {{ request()->routeIs('jobs.*') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-gray-900' }}">
                            Jobs
                        </a>
                        @auth
                            <a href="{{ route('bookmarks.index') }}"
                               class="px-3 py-2 text-sm font-medium {{ request()->routeIs('bookmarks.*') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-gray-900' }}">
                                <i class="fas fa-bookmark mr-1"></i>Saved Jobs
                            </a>
                        @endauth
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        @if(auth()->user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="text-sm text-red-600 hover:text-red-800 font-medium">
                                <i class="fas fa-shield-alt mr-1"></i>Admin
                            </a>
                        @endif
                        <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">Login</a>
                        <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Sign Up</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">JobAggregator</h3>
                    <p class="mt-2 text-sm text-gray-600">Find the best tech jobs from top companies worldwide.</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 uppercase">Quick Links</h4>
                    <ul class="mt-2 space-y-1">
                        <li><a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-indigo-600">Dashboard</a></li>
                        <li><a href="{{ route('jobs.index') }}" class="text-sm text-gray-600 hover:text-indigo-600">Browse Jobs</a></li>
                        <li><a href="{{ route('jobs.index', ['location_type' => 'remote']) }}" class="text-sm text-gray-600 hover:text-indigo-600">Remote Jobs</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 uppercase">Job Alerts</h4>
                    <p class="mt-2 text-sm text-gray-600 mb-3">Get notified about new jobs matching your interests.</p>
                    <form method="POST" action="{{ route('job-alerts.store') }}" class="flex gap-2">
                        @csrf
                        <input type="email" name="email" placeholder="your@email.com" required
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>
            <div class="mt-8 pt-4 border-t border-gray-200 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} JobAggregator. All rights reserved.
            </div>
        </div>
    </footer>
</body>
</html>
