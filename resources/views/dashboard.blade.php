<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    🎓 {{ __('Student Dashboard (URMS Portal)') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Simamia machapisho yako, fuatilia uhakiki, na pakua tafiti za chuo (Institution-Only)</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('repositories.published') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-3.5 rounded-lg text-xs shadow transition flex items-center gap-1.5">
                    📚 Institutional Repository
                </a>
                <a href="{{ route('repositories.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-3.5 rounded-lg text-xs shadow transition flex items-center gap-1">
                    + Pakia Kazi Mpya
                </a>
            </div>
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

            <!-- SEHEMU YA 1: PENDING SUBMISSIONS UNDER REVIEW -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-6 border border-gray-100">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        ⏳ Kazi Zako Zinazohakikiwa (Pending Submissions)
                    </h3>
                    <a href="{{ route('student.history') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">
                        Tazama Zote (My Submissions) →
                    </a>
                </div>
                
                @if($myDocuments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-xs">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Kichwa cha Kazi</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Supervisor</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Aina</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Mwaka</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Hali (Status)</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Tarehe</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($myDocuments as $doc)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-semibold text-gray-900">
                                            {{ $doc->title }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-600">
                                            {{ $doc->supervisor }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-500">{{ $doc->document_type }}</td>
                                        <td class="px-4 py-3 text-gray-500">{{ $doc->year }}</td>
                                        <td class="px-4 py-3">
                                            @if($doc->status === 'pending_supervisor')
                                                <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                    ⏳ Pending Supervisor
                                                </span>
                                            @elseif($doc->status === 'pending_library')
                                                <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                                                    ⏳ Pending Library
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right text-gray-400 whitespace-nowrap">{{ $doc->created_at->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="bg-gray-50 p-6 rounded-xl text-center text-gray-500 text-xs">
                        <p>Hauna kazi inayohakikiwa (Pending) kwa sasa. Unaweza kupakia kazi mpya au kuangalia kazi zako zilizoidhinishwa kwenye <a href="{{ route('student.history') }}" class="font-bold text-blue-600 hover:underline">My Submissions</a>.</p>
                    </div>
                @endif
            </div>

            <!-- SEHEMU YA 2: PUBLISHED INSTITUTIONAL WORKS (INSTITUTION-ONLY REPOSITORY) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-6 border border-gray-100 space-y-4">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-gray-100 pb-3">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            🏛️ Machapisho ya Ndani ya Chuo (Institution-Only & Published Works)
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Kama mwanachuo uliyeingia, unayo fursa ya kusoma na kupakua kazi hizi zote zilizochapishwa.
                        </p>
                    </div>
                    <a href="{{ route('repositories.published') }}" class="bg-yellow-50 hover:bg-yellow-100 text-yellow-800 border border-yellow-300 font-bold px-3 py-1.5 rounded-lg text-xs transition flex items-center gap-1">
                        Fungua Katalogi Kamili ({{ $totalInstitutionalCount }} Kazi) →
                    </a>
                </div>

                @if(isset($publishedInstitutionalDocs) && $publishedInstitutionalDocs->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($publishedInstitutionalDocs as $pDoc)
                            <div class="p-4 rounded-xl border border-gray-200 bg-gray-50/60 hover:bg-white hover:border-blue-300 hover:shadow-sm transition flex flex-col justify-between space-y-3">
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $pDoc->access_level === 'Institution-Only' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 'bg-green-100 text-green-800 border border-green-200' }}">
                                            {{ $pDoc->access_level === 'Institution-Only' ? '🟡 Institution-Only' : '🟢 Open-Access' }}
                                        </span>
                                        <span class="text-[11px] text-gray-400 font-mono">{{ $pDoc->document_type }}</span>
                                    </div>
                                    <h4 class="text-sm font-bold text-gray-900 leading-snug line-clamp-2">
                                        {{ $pDoc->title }}
                                    </h4>
                                    <p class="text-xs text-gray-600">
                                        👤 <strong>{{ $pDoc->authors }}</strong> ({{ $pDoc->year }})<br>
                                        🏛️ {{ $pDoc->department }}
                                    </p>
                                </div>

                                <div class="flex items-center justify-between pt-2 border-t border-gray-200/70">
                                    <a href="{{ route('repositories.show', $pDoc->id) }}" class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-800">
                                        Maelezo →
                                    </a>
                                    {{-- Direct Download Button for Logged-In Student --}}
                                    <a href="{{ route('repositories.download', $pDoc->id) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold px-3 py-1 rounded text-xs shadow transition flex items-center gap-1">
                                        ⬇️ Pakua PDF
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-gray-400 py-4 text-center">Hakuna machapisho ya chuo yaliyoidhinishwa kwa sasa.</p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>