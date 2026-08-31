<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URMS - Institutional Repository</title>
    
    <!-- CSS na JS za lokali kupitia Vite (Inafanya kazi Ukiwa Online na Offline) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation Bar -->
    <nav class="bg-blue-900 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-4 relative flex justify-between items-center">
            
            <!-- Logo upande wa kushoto -->
            <div class="flex items-center z-10">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="h-10 w-auto">
            </div>

            <!-- Jina katikati kabisa kwa herufi kubwa -->
            <div class="absolute left-1/2 transform -translate-x-1/2 text-center pointer-events-none">
                <h1 class="text-xl font-bold tracking-wide uppercase">University Repository Management System</h1>
            </div>

            <!-- Vifungo vya Login/Register upande wa kulia -->
            <div class="z-10">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="bg-blue-700 hover:bg-blue-800 px-4 py-2 rounded text-sm font-semibold">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold mr-4 hover:underline">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded text-sm font-semibold">Register</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Search Section -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Search Institutional Repository</h2>
            <form action="{{ route('public.search') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title, author, keyword or accession no..." class="w-full border-gray-300 rounded-lg p-2.5 border focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <select name="type" class="w-full border-gray-300 rounded-lg p-2.5 border">
                        <option value="">All Document Types</option>
                        <option value="Thesis" {{ request('type') == 'Thesis' ? 'selected' : '' }}>Thesis</option>
                        <option value="Dissertation" {{ request('type') == 'Dissertation' ? 'selected' : '' }}>Dissertation</option>
                        <option value="Research Paper" {{ request('type') == 'Research Paper' ? 'selected' : '' }}>Research Paper</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2.5 rounded-lg hover:bg-blue-700">Search</button>
                </div>
            </form>
        </div>

        <!-- Search Results -->
        <div class="space-y-4">
            <h3 class="text-xl font-bold text-gray-800">Published Works</h3>
            
            @if($repositories->count() > 0)
                @foreach($repositories as $doc)
                    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-600">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                    {{ $doc->accession_number }}
                                </span>
                                <h4 class="text-xl font-bold text-gray-900 mt-2">{{ $doc->title }}</h4>
                                <p class="text-sm text-gray-600"><strong>Authors:</strong> {{ $doc->authors }} | <strong>Year:</strong> {{ $doc->year }}</p>
                                <p class="text-sm text-gray-600"><strong>Department:</strong> {{ $doc->department }}</p>
                            </div>
                            <div>
                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-semibold inline-block">
                                    📄 Download PDF
                                </a>
                            </div>
                        </div>
                        <div class="mt-4 text-sm text-gray-700 bg-gray-50 p-3 rounded">
                            <strong>Abstract:</strong> {{ Str::limit($doc->abstract, 250) }}
                        </div>
                    </div>
                @endforeach

                <div class="mt-4">
                    {{ $repositories->links() }}
                </div>
            @else
                <div class="bg-white p-6 rounded-lg shadow text-center text-gray-500">
                    Hakuna kazi yoyote iliyochapishwa inayolingana na utafutaji wako kwa sasa.
                </div>
            @endif
        </div>
    </div>
</body>
</html>