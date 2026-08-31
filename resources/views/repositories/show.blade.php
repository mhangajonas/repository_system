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
            <a href="{{ route('home') }}" class="text-sm sm:text-base font-bold text-indigo-700 flex items-center gap-1">
                ← Rudi Kwenye Catalogue (Search)
            </a>
            <div class="flex items-center space-x-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-gray-700 hover:text-indigo-600 bg-gray-100 px-3 py-1.5 rounded-md">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-700 hover:text-indigo-600">
                        Log in
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1.5 rounded-md">
                            Register
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 text-sm shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm shadow-sm flex items-center gap-2">
                <span>⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-md p-6 sm:p-8 border border-gray-100">
            
            <!-- Header Badges & Access Level -->
            <div class="border-b border-gray-100 pb-5 mb-6">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <!-- Document Type -->
                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800">
                        📑 {{ $doc->document_type }}
                    </span>

                    <!-- Access Level Badge -->
                    @php
                        $accessLevel = $doc->access_level ?? 'Open-Access';
                    @endphp
                    @if($accessLevel === 'Open-Access')
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800 border border-green-200">
                            🟢 Open Access (Public)
                        </span>
                    @elseif($accessLevel === 'Institution-Only')
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                            🟡 Institution-Only (Login Required)
                        </span>
                    @else
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-800 border border-red-200">
                            🔴 Restricted Access (Idhini Maalum)
                        </span>
                    @endif

                    <!-- Accession Number -->
                    @if($doc->accession_number)
                        <span class="px-3 py-1 text-xs font-mono font-bold rounded-full bg-gray-100 text-gray-700 border">
                            Accession: {{ $doc->accession_number }}
                        </span>
                    @endif

                    <!-- Librarian Edit Button -->
                    @auth
                        @if(Auth::user()->role === 'librarian' || Auth::user()->role === 'admin')
                            <a href="{{ route('library.repositories.edit', $doc->id) }}" class="ml-auto bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 px-3 py-1 rounded-md text-xs font-bold transition">
                                ✏️ Edit Metadata & Access Level
                            </a>
                        @endif
                    @endauth
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 leading-snug">
                    {{ $doc->title }}
                </h1>
            </div>

            <!-- FR-3.3 Full Metadata View Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6 text-sm bg-gray-50/70 p-4 rounded-xl border border-gray-100">
                <div><span class="font-bold text-gray-700">👤 Authors:</span> <span class="text-gray-900">{{ $doc->authors }}</span></div>
                <div><span class="font-bold text-gray-700">👨‍🏫 Supervisor:</span> <span class="text-gray-900">{{ $doc->supervisor }}</span></div>
                <div><span class="font-bold text-gray-700">🏛️ Department:</span> <span class="text-gray-900">{{ $doc->department }}</span></div>
                <div><span class="font-bold text-gray-700">🎓 Programme:</span> <span class="text-gray-900">{{ $doc->degree_programme }}</span></div>
                <div><span class="font-bold text-gray-700">📅 Year of Publication:</span> <span class="text-gray-900">{{ $doc->year }}</span></div>
                <div><span class="font-bold text-gray-700">🏷️ Keywords:</span> <span class="text-gray-900">{{ $doc->keywords }}</span></div>
            </div>

            <!-- Abstract -->
            <div class="mb-6">
                <h3 class="text-base font-bold text-gray-800 mb-2">📄 Abstract / Muhtasari</h3>
                <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line bg-gray-50 p-4 rounded-xl border border-gray-200">
                    {{ $doc->abstract }}
                </p>
            </div>

            <!-- Access Level Notice & Download Action -->
            <div class="pt-5 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                
                <!-- Access Level Explanatory Message -->
                <div class="text-xs text-gray-600">
                    @if($accessLevel === 'Open-Access')
                        <span class="text-green-700 font-semibold">✅ Nyaraka hii ni ya Open Access. Mtu yeyote anaruhusiwa kupakua.</span>
                    @elseif($accessLevel === 'Institution-Only')
                        @auth
                            <span class="text-blue-700 font-semibold">🎓 Umeingia kama mwanachuo. Unaruhusiwa kupakua faili hili.</span>
                        @else
                            <span class="text-yellow-700 font-semibold">🔒 Unatakiwa kuingia kwenye akaunti yako ili kupakua (Institution-Only).</span>
                        @endauth
                    @else
                        @auth
                            @if($doc->user_id === Auth::id() || in_array(Auth::user()->role, ['admin', 'librarian', 'supervisor']))
                                <span class="text-purple-700 font-semibold">🔑 Una idhini ya Restricted Access (Mwandishi / Staff).</span>
                            @else
                                <span class="text-red-700 font-semibold">⚠️ Nyaraka hii imezuiwa (Restricted). Inahitaji idhini maalum.</span>
                            @endif
                        @else
                            <span class="text-red-700 font-semibold">🔒 Nyaraka hii imezuiwa (Restricted). Ingia kuthibitisha idhini yako.</span>
                        @endauth
                    @endif
                </div>

                <!-- Download Button -->
                <div>
                    @if($accessLevel === 'Institution-Only' && !Auth::check())
                        <a href="{{ route('login') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2.5 px-5 rounded-lg shadow text-xs flex items-center gap-2 transition">
                            <span>🔒 Ingia (Log in) Ili Kupakua</span>
                        </a>
                    @elseif($accessLevel === 'Restricted' && !Auth::check())
                        <a href="{{ route('login') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-5 rounded-lg shadow text-xs flex items-center gap-2 transition">
                            <span>🔒 Ingia Kuthibitisha Idhini</span>
                        </a>
                    @else
                        <a href="{{ route('repositories.download', $doc->id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-lg shadow transition text-xs flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>Download Full Document (PDF)</span>
                        </a>
                    @endif
                </div>
            </div>

        </div>
    </div>
</body>
</html>