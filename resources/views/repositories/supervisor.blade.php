<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    👨‍🏫 {{ __('Supervisor Review & Approval Dashboard') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Hakiki kazi za wanafunzi, pakua faili, na utoe maamuzi kabla ya kuwasilisha kwa Librarian</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('repositories.published') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3.5 py-2 rounded-md text-xs font-semibold shadow transition flex items-center gap-1">
                    🏛️ Institutional Repository
                </a>
                <a href="{{ route('supervisor.history') }}" class="bg-gray-700 hover:bg-gray-800 text-white px-3.5 py-2 rounded-md text-xs font-semibold shadow transition">
                    📜 Tazama Kumbukumbu (History)
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-6 border border-gray-100">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        ⏳ Kazi Zinazosubiri Uhakiki Wako (Pending Reviews)
                    </h3>
                    <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2.5 py-1 rounded-full">
                        {{ $pendingDocuments->count() }} Zinasubiri
                    </span>
                </div>

                @if($pendingDocuments->isEmpty())
                    <div class="text-center py-12 text-gray-500">
                        <div class="text-4xl mb-2">🎉</div>
                        <p class="font-medium">Hakuna kazi mpya kwa sasa zinazosubiri uhakiki wako.</p>
                        <p class="text-xs text-gray-400 mt-1">Kazi mpya zikitumwa na wanafunzi zitajitokeza hapa moja kwa moja.</p>
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach($pendingDocuments as $repository)
                            <div class="border border-gray-200 rounded-xl p-5 bg-gray-50/70 hover:bg-gray-50 transition shadow-sm space-y-4">
                                
                                <!-- Sehemu ya Maelezo ya Mwanafunzi & Kichwa cha Kazi -->
                                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 border-b border-gray-200 pb-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-blue-100 text-blue-800">
                                                {{ $repository->document_type }}
                                            </span>
                                            <span class="text-xs text-gray-400">Mwaka: {{ $repository->year }}</span>
                                        </div>
                                        <h4 class="text-lg font-bold text-gray-900">{{ $repository->title }}</h4>
                                        <p class="text-xs text-gray-600">
                                            Mwanafunzi: <strong class="text-gray-800">{{ $repository->user->name ?? 'Mwanafunzi' }}</strong> 
                                            ({{ $repository->user->email ?? 'N/A' }})
                                            | Idara: <strong class="text-gray-800">{{ $repository->department }}</strong>
                                            | Programu: <strong class="text-gray-800">{{ $repository->degree_programme }}</strong>
                                        </p>
                                    </div>

                                    <!-- Vitufe vya Kupakua na Kuangalia Faili (Download & Preview Actions) -->
                                    <div class="flex flex-wrap items-center gap-2">
                                        {{-- 1. Kitufe Kikubwa cha Kupakua (Download PDF) --}}
                                        <a href="{{ route('repositories.download', $repository->id) }}" class="inline-flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white font-bold px-3.5 py-2 rounded-lg text-xs shadow transition">
                                            ⬇️ Pakua Faili (PDF)
                                        </a>

                                        {{-- 2. Kitufe cha Kufungua PDF kwenye Tab Mpya --}}
                                        <a href="{{ route('repositories.download', $repository->id) }}" class="inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-3 py-2 rounded-lg text-xs shadow transition">
                                            👁️ Angalia PDF
                                        </a>

                                        {{-- 3. Kitufe cha Metadata --}}
                                        <a href="{{ route('repositories.show', $repository->id) }}" target="_blank" class="inline-flex items-center gap-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold px-3 py-2 rounded-lg text-xs border border-indigo-200 shadow-sm transition">
                                            📄 Metadata
                                        </a>
                                    </div>
                                </div>

                                <!-- Muhtasari wa Abstract ya Kazi -->
                                @if($repository->abstract)
                                    <div class="bg-white p-3.5 rounded-lg border border-gray-200 text-xs text-gray-700">
                                        <strong class="text-gray-900 block mb-1">Muhtasari (Abstract):</strong>
                                        <p class="leading-relaxed line-clamp-3 hover:line-clamp-none transition">{{ $repository->abstract }}</p>
                                    </div>
                                @endif

                                <!-- Sehemu ya Uamuzi & Maoni (Decision & Submission Form) -->
                                <div class="bg-white p-4 rounded-lg border border-gray-200 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide">
                                            ✍️ Uamuzi na Maoni Yako Kabla ya Kutuma kwa Mkutubi (Librarian)
                                        </label>
                                        <span class="text-xs text-gray-400">Hakikisha umepakua na kusoma faili kabla ya kuthibitisha</span>
                                    </div>

                                    <form action="{{ route('supervisor.action', $repository->id) }}" method="POST" class="space-y-3">
                                        @csrf
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                            <!-- Chagua Uamuzi -->
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-600 mb-1">Hatua ya Uamuzi *</label>
                                                <select name="action" class="w-full border-gray-300 rounded-md shadow-sm text-xs p-2 focus:ring-blue-500 focus:border-blue-500" required>
                                                    <option value="approve">✅ Approve (Idhinisha & Peleka Library)</option>
                                                    <option value="revision">🔄 Omba Marekebisho (Request Revision)</option>
                                                    <option value="reject">❌ Kataa Kazi Hii (Reject)</option>
                                                </select>
                                            </div>

                                            <!-- Maoni kwa Mwanafunzi / Librarian -->
                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-semibold text-gray-600 mb-1">Maoni / Mapendekezo (Comments)</label>
                                                <textarea name="comments" placeholder="Andika maoni au marekebisho yanayohitajika kwa mwanafunzi..." class="w-full border-gray-300 rounded-md shadow-sm text-xs p-2 focus:ring-blue-500 focus:border-blue-500" rows="2"></textarea>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap justify-between items-center pt-3 border-t border-gray-100 gap-2">
                                            <!-- Pakua reminder link -->
                                            <a href="{{ route('repositories.download', $repository->id) }}" class="text-xs font-bold text-green-700 hover:text-green-900 flex items-center gap-1">
                                                ⬇️ Bonyeza hapa kupakua tena faili la kazi hii kabla ya kutuma uamuzi
                                            </a>

                                            <div class="flex items-center space-x-2">
                                                <!-- Kitufe cha Futa Kazi (Soft Delete) -->
                                                <button type="button" onclick="if(confirm('Una uhakika unataka kufuta kazi hii?')) { document.getElementById('delete-form-{{ $repository->id }}').submit(); }" class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-3 py-1.5 rounded-md text-xs font-semibold transition">
                                                    🗑️ Futa Kazi
                                                </button>

                                                <!-- Kitufe cha Kutuma Uamuzi -->
                                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-1.5 rounded-md text-xs shadow transition">
                                                    🚀 Tuma Uamuzi
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                    <!-- Fomu ya Kufuta Kazi (Hidden form for Soft Delete) -->
                                    <form id="delete-form-{{ $repository->id }}" action="{{ route('repositories.destroy', $repository->id) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>