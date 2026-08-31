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
        <div class="bg-white p-6 rounded-xl shadow-md mb-8 border border-gray-100">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">🔍 Search Institutional Repository</h2>
            <form action="{{ route('public.search') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Keywords, Title, Author, Accession No</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tafuta kwa kichwa, mwandishi au keyword..." class="w-full border-gray-300 rounded-lg p-2.5 border text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Document Type</label>
                    <select name="type" class="w-full border-gray-300 rounded-lg p-2.5 border text-sm">
                        <option value="">All Document Types</option>
                        <option value="Thesis" {{ request('type') == 'Thesis' ? 'selected' : '' }}>Thesis</option>
                        <option value="Dissertation" {{ request('type') == 'Dissertation' ? 'selected' : '' }}>Dissertation</option>
                        <option value="Research Paper" {{ request('type') == 'Research Paper' ? 'selected' : '' }}>Research Paper</option>
                        <option value="Past Exam" {{ request('type') == 'Past Exam' ? 'selected' : '' }}>Past Examination</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Access Level</label>
                    <select name="access_level" class="w-full border-gray-300 rounded-lg p-2.5 border text-sm">
                        <option value="">All Access Levels</option>
                        <option value="Open-Access" {{ request('access_level') == 'Open-Access' ? 'selected' : '' }}>🟢 Open Access</option>
                        <option value="Institution-Only" {{ request('access_level') == 'Institution-Only' ? 'selected' : '' }}>🟡 Institution-Only</option>
                        <option value="Restricted" {{ request('access_level') == 'Restricted' ? 'selected' : '' }}>🔴 Restricted</option>
                    </select>
                </div>
                <div class="sm:col-span-2 md:col-span-4 flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg text-sm shadow transition">
                        🔎 Search Repository
                    </button>
                </div>
            </form>
        </div>

        <!-- Search Results -->
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Published Academic Works</h3>
                <span class="text-xs text-gray-500 font-semibold">{{ $repositories->total() }} kazi zimepatikana</span>
            </div>
            
            @if($repositories->count() > 0)
                @foreach($repositories as $doc)
                    @php
                        $accessLevel = $doc->access_level ?? 'Open-Access';
                    @endphp
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <div class="space-y-1.5 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    @if($doc->accession_number)
                                        <span class="bg-blue-100 text-blue-800 text-xs font-mono font-bold px-2.5 py-0.5 rounded-full">
                                            {{ $doc->accession_number }}
                                        </span>
                                    @endif
                                    
                                    <span class="bg-gray-100 text-gray-700 text-xs font-semibold px-2 py-0.5 rounded">
                                        {{ $doc->document_type }}
                                    </span>

                                    @if($accessLevel === 'Open-Access')
                                        <span class="bg-green-100 text-green-800 text-xs font-bold px-2.5 py-0.5 rounded-full border border-green-200">
                                            🟢 Open Access
                                        </span>
                                    @elseif($accessLevel === 'Institution-Only')
                                        <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2.5 py-0.5 rounded-full border border-yellow-200">
                                            🟡 Institution-Only
                                        </span>
                                    @else
                                        <span class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded-full border border-red-200">
                                            🔴 Restricted Access
                                        </span>
                                    @endif
                                </div>

                                <h4 class="text-xl font-bold text-gray-900 leading-snug">
                                    <a href="{{ route('repositories.show', $doc->id) }}" class="hover:text-blue-600 transition">
                                        {{ $doc->title }}
                                    </a>
                                </h4>
                                <p class="text-xs text-gray-600">
                                    <strong>Authors:</strong> {{ $doc->authors }} | <strong>Year:</strong> {{ $doc->year }} | <strong>Dept:</strong> {{ $doc->department }}
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('repositories.show', $doc->id) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-3.5 py-2 rounded-lg text-xs font-bold transition">
                                    👁️ Details
                                </a>
                                <a href="{{ route('repositories.download', $doc->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-bold shadow transition flex items-center gap-1">
                                    ⬇️ Download PDF
                                </a>
                            </div>
                        </div>

                        @if($doc->abstract)
                            <div class="mt-3 text-xs text-gray-600 bg-gray-50 p-3 rounded-lg border border-gray-100 leading-relaxed line-clamp-2">
                                <strong>Abstract:</strong> {{ $doc->abstract }}
                            </div>
                        @endif
                    </div>
                @endforeach

                <div class="mt-6">
                    {{ $repositories->links() }}
                </div>
            @else
                <div class="bg-white p-8 rounded-xl shadow text-center text-gray-500">
                    <p class="text-base font-medium">Hakuna kazi yoyote iliyochapishwa inayolingana na utafutaji wako kwa sasa.</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>