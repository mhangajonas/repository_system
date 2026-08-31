<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    🏛️ {{ __('Institutional Repository (Machapisho ya Ndani ya Chuo)') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Eneo maalum la kupakua na kusoma machapisho yote yaliyoidhinishwa ya <strong>Institution-Only</strong> na <strong>Open-Access</strong>
                </p>
            </div>
            @if(Auth::user()->role === 'student')
                <a href="{{ route('repositories.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-bold shadow transition">
                    + Pakia Kazi Mpya
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative text-sm shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative text-sm shadow-sm flex items-center gap-2">
                    <span>⚠️</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Summary KPI Metric Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-yellow-500 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Institution-Only Works</p>
                        <p class="text-2xl font-extrabold text-yellow-600 mt-1">{{ $totalInstitutionOnly }}</p>
                        <span class="text-[11px] text-gray-500">Inapatikana kwa wanachuo</span>
                    </div>
                    <div class="p-3 bg-yellow-50 rounded-xl text-2xl">
                        🔒
                    </div>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-green-500 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Open Access Works</p>
                        <p class="text-2xl font-extrabold text-green-600 mt-1">{{ $totalOpenAccess }}</p>
                        <span class="text-[11px] text-gray-500">Inapatikana kwa wote</span>
                    </div>
                    <div class="p-3 bg-green-50 rounded-xl text-2xl">
                        🌐
                    </div>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-blue-500 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Published Research</p>
                        <p class="text-2xl font-extrabold text-blue-600 mt-1">{{ $totalApproved }}</p>
                        <span class="text-[11px] text-gray-500">Tafiti zote zilizothibitishwa</span>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-xl text-2xl">
                        📚
                    </div>
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-4">
                <form action="{{ route('repositories.published') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    
                    <!-- Search Keyword -->
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tafuta Kazi (Search)</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Kichwa, mwandishi au keyword..." class="w-full border-gray-300 rounded-lg p-2 text-xs focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Department Filter -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Idara (Department)</label>
                        <select name="department" class="w-full border-gray-300 rounded-lg p-2 text-xs focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Idara Zote</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Document Type Filter -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Aina ya Nyaraka</label>
                        <select name="type" class="w-full border-gray-300 rounded-lg p-2 text-xs focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Aina Zote</option>
                            @foreach($documentTypes as $t)
                                <option value="{{ $t }}" {{ request('type') == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Access Level Filter -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Access Level</label>
                        <select name="access_level" class="w-full border-gray-300 rounded-lg p-2 text-xs focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Viwango Vyote</option>
                            <option value="Institution-Only" {{ request('access_level') == 'Institution-Only' ? 'selected' : '' }}>🟡 Institution-Only</option>
                            <option value="Open-Access" {{ request('access_level') == 'Open-Access' ? 'selected' : '' }}>🟢 Open-Access</option>
                            <option value="Restricted" {{ request('access_level') == 'Restricted' ? 'selected' : '' }}>🔴 Restricted</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="sm:col-span-2 md:col-span-4 lg:col-span-5 flex justify-end gap-2 pt-2 border-t border-gray-100">
                        @if(request()->hasAny(['search', 'department', 'type', 'access_level']))
                            <a href="{{ route('repositories.published') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-xs font-semibold transition">
                                Safisha Filters
                            </a>
                        @endif
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-xs font-bold shadow transition flex items-center gap-1.5">
                            🔍 Tafuta Sasa
                        </button>
                    </div>
                </form>
            </div>

            <!-- Published Documents Grid -->
            <div class="space-y-4">
                <div class="flex justify-between items-center px-1">
                    <h3 class="text-base font-bold text-gray-800">
                        Machapisho Yaliyopatikana ({{ $documents->total() }})
                    </h3>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('repositories.published', ['access_level' => 'Institution-Only']) }}" class="px-3 py-1 rounded-full text-xs font-bold {{ request('access_level') == 'Institution-Only' ? 'bg-yellow-500 text-white' : 'bg-yellow-50 text-yellow-800 hover:bg-yellow-100' }} border border-yellow-200 transition">
                            🟡 Institution-Only ({{ $totalInstitutionOnly }})
                        </a>
                        <a href="{{ route('repositories.published', ['access_level' => 'Open-Access']) }}" class="px-3 py-1 rounded-full text-xs font-bold {{ request('access_level') == 'Open-Access' ? 'bg-green-600 text-white' : 'bg-green-50 text-green-800 hover:bg-green-100' }} border border-green-200 transition">
                            🟢 Open Access ({{ $totalOpenAccess }})
                        </a>
                    </div>
                </div>

                @if($documents->isEmpty())
                    <div class="bg-white p-12 rounded-xl border border-gray-100 text-center text-gray-500">
                        <div class="text-4xl mb-3">📭</div>
                        <p class="font-bold text-base text-gray-700">Hakuna kazi zilizopatikana kulingana na utafutaji wako.</p>
                        <p class="text-xs text-gray-400 mt-1">Jaribu kusafisha vichungi vya utafutaji au utafute kwa maneno mengine.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($documents as $doc)
                            @php
                                $accessLevel = $doc->access_level ?? 'Open-Access';
                            @endphp
                            <div class="bg-white rounded-xl p-5 border border-gray-200/80 hover:border-blue-400 shadow-sm hover:shadow-md transition flex flex-col justify-between space-y-4">
                                
                                <div class="space-y-2">
                                    <!-- Badges Header -->
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        @if($doc->accession_number)
                                            <span class="bg-blue-100 text-blue-800 font-mono text-[10px] font-bold px-2 py-0.5 rounded">
                                                {{ $doc->accession_number }}
                                            </span>
                                        @endif

                                        <span class="bg-gray-100 text-gray-700 text-[10px] font-semibold px-2 py-0.5 rounded">
                                            {{ $doc->document_type }}
                                        </span>

                                        @if($accessLevel === 'Institution-Only')
                                            <span class="bg-yellow-100 text-yellow-800 text-[10px] font-bold px-2 py-0.5 rounded border border-yellow-200">
                                                🟡 Institution-Only
                                            </span>
                                        @elseif($accessLevel === 'Open-Access')
                                            <span class="bg-green-100 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded border border-green-200">
                                                🟢 Open Access
                                            </span>
                                        @else
                                            <span class="bg-red-100 text-red-800 text-[10px] font-bold px-2 py-0.5 rounded border border-red-200">
                                                🔴 Restricted
                                            </span>
                                        @endif

                                        <span class="text-gray-400 text-[11px] ml-auto">Mwaka: {{ $doc->year }}</span>
                                    </div>

                                    <!-- Title -->
                                    <h4 class="text-base font-bold text-gray-900 leading-snug hover:text-blue-600 transition">
                                        <a href="{{ route('repositories.show', $doc->id) }}">
                                            {{ $doc->title }}
                                        </a>
                                    </h4>

                                    <!-- Meta Info -->
                                    <div class="text-xs text-gray-600 space-y-0.5">
                                        <p>👤 <strong>Authors:</strong> {{ $doc->authors }}</p>
                                        <p>👨‍🏫 <strong>Supervisor:</strong> {{ $doc->supervisor }}</p>
                                        <p>🏛️ <strong>Idara:</strong> {{ $doc->department }} | <strong>Programu:</strong> {{ $doc->degree_programme }}</p>
                                    </div>

                                    <!-- Abstract Snippet -->
                                    @if($doc->abstract)
                                        <p class="text-xs text-gray-500 bg-gray-50 p-2.5 rounded-lg border border-gray-100 line-clamp-2">
                                            {{ $doc->abstract }}
                                        </p>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100 gap-2">
                                    <a href="{{ route('repositories.show', $doc->id) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                                        📄 Angalia Maelezo →
                                    </a>

                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('repositories.download', $doc->id) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                            👁️ Preview
                                        </a>

                                        {{-- Kitufe kikuu cha Kupakua (Download PDF) --}}
                                        <a href="{{ route('repositories.download', $doc->id) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold px-3.5 py-1.5 rounded-lg text-xs shadow transition flex items-center gap-1">
                                            ⬇️ Pakua PDF
                                        </a>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $documents->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

