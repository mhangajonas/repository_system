<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $doc->title }} - URMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased min-h-screen">
    <nav class="bg-white border-b border-gray-200 py-4 px-6 mb-8 shadow-sm">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-xl font-bold text-indigo-700">← Back to Search Catalogue</a>
            @auth
                <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-gray-700 hover:text-indigo-600">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-700 hover:text-indigo-600">Log in</a>
            @endauth
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="border-b pb-4 mb-6">
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                    {{ $doc->document_type }}
                </span>
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 ml-2">
                    Access: {{ $doc->access_level ?? 'Open Access' }}
                </span>
                <h1 class="text-2xl font-bold text-gray-900 mt-3">{{ $doc->title }}</h1>
                <p class="text-sm text-gray-500 mt-1">Accession No: <strong>{{ $doc->accession_number }}</strong></p>
            </div>

            <!-- FR-3.3 Full Metadata View -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 text-sm">
                <div><strong>Authors:</strong> {{ $doc->authors }}</div>
                <div><strong>Supervisor:</strong> {{ $doc->supervisor }}</div>
                <div><strong>Department:</strong> {{ $doc->department }}</div>
                <div><strong>Degree / Programme:</strong> {{ $doc->degree_programme }}</div>
                <div><strong>Year:</strong> {{ $doc->year }}</div>
                <div><strong>Keywords:</strong> {{ $doc->keywords }}</div>
            </div>

            <div class="mb-6">
                <h3 class="text-md font-bold text-gray-800 mb-2">Abstract</h3>
                <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line bg-gray-50 p-4 rounded border">
                    {{ $doc->abstract }}
                </p>
            </div>

            <!-- Action Button -->
            <div class="pt-4 border-t flex justify-end">
                <a href="{{ route('repositories.download', $doc->id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded shadow transition text-sm flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>Download Full Document PDF</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>