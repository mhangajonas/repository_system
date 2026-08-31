<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    ✏️ {{ __('Edit Document Metadata & Access Level') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Rekebisha maelezo ya kazi na kiwango cha ufikiaji (Access Level) kwa mkutubi</p>
            </div>
            <a href="{{ route('library.index') }}" class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-md text-xs font-semibold shadow transition">
                ← Rudi Kwenye Review Queue
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-8 border border-gray-100">
                
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 text-sm">
                        <ul class="list-disc pl-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('library.repositories.update', $repository->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Accession Number Info -->
                    @if($repository->accession_number)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center justify-between text-sm">
                            <div>
                                <span class="font-bold text-blue-900">Accession Number:</span>
                                <span class="font-mono text-blue-700 font-bold ml-2">{{ $repository->accession_number }}</span>
                            </div>
                            <span class="text-xs bg-blue-200 text-blue-800 font-semibold px-2.5 py-1 rounded-full">{{ $repository->document_type }}</span>
                        </div>
                    @endif

                    <!-- Document Title -->
                    <div>
                        <label class="block font-bold text-sm text-gray-700 mb-1">Kichwa cha Habari (Document Title) *</label>
                        <input type="text" name="title" value="{{ old('title', $repository->title) }}" required class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Authors -->
                        <div>
                            <label class="block font-bold text-sm text-gray-700 mb-1">Waandishi (Authors) *</label>
                            <input type="text" name="authors" value="{{ old('authors', $repository->authors) }}" required class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                            <span class="text-xs text-gray-400">Tenganisha kwa koma kama wapo wengi</span>
                        </div>

                        <!-- Department -->
                        <div>
                            <label class="block font-bold text-sm text-gray-700 mb-1">Idara (Department) *</label>
                            <input type="text" name="department" value="{{ old('department', $repository->department) }}" required class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Keywords -->
                    <div>
                        <label class="block font-bold text-sm text-gray-700 mb-1">Maneno Muhimu (Keywords / Tags) *</label>
                        <input type="text" name="keywords" value="{{ old('keywords', $repository->keywords) }}" required placeholder="Mfano: Artificial Intelligence, Cloud Computing, Agriculture" class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                        <span class="text-xs text-gray-400">Husaidia watumiaji kutafuta na kugundua kazi hii kwa urahisi</span>
                    </div>

                    <!-- Access Level Selection -->
                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 space-y-4">
                        <div>
                            <label class="block font-bold text-base text-gray-800 mb-1">
                                🔐 Kiwango cha Ufikiaji (Access Level) *
                            </label>
                            <p class="text-xs text-gray-500">Chagua jinsi faili la kazi hii litakavyofikiwa na kupakuliwa na wasomaji</p>
                        </div>

                        <div class="space-y-3">
                            <label class="flex items-start p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-green-500 transition">
                                <input type="radio" name="access_level" value="Open-Access" {{ old('access_level', $repository->access_level) === 'Open-Access' ? 'checked' : '' }} class="mt-1 text-green-600 focus:ring-green-500">
                                <div class="ml-3">
                                    <span class="font-bold text-sm text-green-700">🟢 Open Access (Public / Huria)</span>
                                    <p class="text-xs text-gray-500 mt-0.5">Inapatikana kwa umma wote. Mtu yeyote (hata bila kuingia kwenye mfumo) anaweza kuangalia na kupakua faili.</p>
                                </div>
                            </label>

                            <label class="flex items-start p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-blue-500 transition">
                                <input type="radio" name="access_level" value="Institution-Only" {{ old('access_level', $repository->access_level) === 'Institution-Only' ? 'checked' : '' }} class="mt-1 text-blue-600 focus:ring-blue-500">
                                <div class="ml-3">
                                    <span class="font-bold text-sm text-blue-700">🟡 Institution-Only (Wanachuo Pekee)</span>
                                    <p class="text-xs text-gray-500 mt-0.5">Inahitaji mtumiaji kuwa ameingia (Log in) kwenye mfumo wa chuo ili aweze kupakua faili la kazi hii.</p>
                                </div>
                            </label>

                            <label class="flex items-start p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-red-500 transition">
                                <input type="radio" name="access_level" value="Restricted" {{ old('access_level', $repository->access_level) === 'Restricted' ? 'checked' : '' }} class="mt-1 text-red-600 focus:ring-red-500">
                                <div class="ml-3">
                                    <span class="font-bold text-sm text-red-700">🔴 Restricted Access (Idhini Maalum Pekee)</span>
                                    <p class="text-xs text-gray-500 mt-0.5">Faili linafungwa. Mwandishi wake, supervisor, wakutubi na wasimamizi wa mfumo pekee wanaoruhusiwa kupakua.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('library.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-5 py-2.5 rounded-lg text-sm transition">
                            Ghairi
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-lg text-sm shadow transition">
                            💾 Hifadhi Mabadiliko
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>

