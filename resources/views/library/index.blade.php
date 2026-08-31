<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Librarian Final Review & Cataloging Panel (URMS)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Quick Analytics Overview Banner for Librarian -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-yellow-500 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase">Pending Cataloging</p>
                        <p class="text-2xl font-extrabold text-yellow-600 mt-1">{{ $pendingCount ?? $pendingDocuments->count() }}</p>
                    </div>
                    <span class="text-2xl">⏳</span>
                </div>
                <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-green-500 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase">Approved in Repository</p>
                        <p class="text-2xl font-extrabold text-green-600 mt-1">{{ $approvedCount ?? 0 }}</p>
                    </div>
                    <span class="text-2xl">✅</span>
                </div>
                <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-purple-500 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase">Institutional Repository</p>
                        <a href="{{ route('repositories.published') }}" class="inline-flex items-center gap-1 text-xs font-bold text-purple-600 hover:text-purple-800 mt-2">
                            🏛️ Kazi Zilizochapishwa →
                        </a>
                    </div>
                    <span class="text-2xl">📚</span>
                </div>
                <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-indigo-500 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase">Graphic Analytics</p>
                        <a href="{{ route('library.reports') }}" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:text-indigo-800 mt-2">
                            📊 Ripoti na Takwimu →
                        </a>
                    </div>
                    <span class="text-2xl">📈</span>
                </div>
                <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-teal-500 flex items-center justify-between lg:col-span-1 sm:col-span-2">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase">Download Logs</p>
                        <a href="{{ route('download.logs') }}" class="inline-flex items-center gap-1 text-xs font-bold text-teal-600 hover:text-teal-800 mt-2">
                            📥 Historia ya Upakuaji →
                        </a>
                    </div>
                    <span class="text-2xl">📋</span>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Documents Approved by Supervisor (Pending Cataloging)</h3>

                <div class="space-y-6">
                    @forelse($pendingDocuments as $doc)
                        <div class="border rounded-lg p-5 bg-gray-50 shadow-sm">
                            <div class="flex justify-between items-start border-b pb-3 mb-4">
                                <div>
                                    <h4 class="text-lg font-bold text-gray-800">{{ $doc->title }}</h4>
                                    <p class="text-xs text-gray-500">
                                        Submitted by: <span class="font-semibold text-gray-700">{{ $doc->user->name }}</span> ({{ $doc->user->email }}) 
                                        | Supervisor: <span class="font-semibold text-gray-700">{{ $doc->supervisor }}</span>
                                        | Dept: <span class="font-semibold text-gray-700">{{ $doc->department ?? 'N/A' }}</span>
                                    </p>
                                </div>
                                <a href="{{ route('repositories.show', $doc->id) }}" target="_blank" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-xs font-semibold flex items-center">
                                    📄 View Document / PDF
                                </a>
                            </div>

                            <!-- Form ya Review & Cataloging -->
                            <form action="{{ route('library.action', $doc->id) }}" method="POST" class="space-y-4">
                                @csrf

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- 1. Auto Accession Number Preview -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Accession Number (Auto)</label>
                                        <input type="text" value="{{ $doc->accession_number ?? 'URMS/'.date('Y').'/'.str_pad($doc->id, 4, '0', STR_PAD_LEFT) }}" readonly class="w-full bg-gray-100 border-gray-300 rounded text-xs p-2 text-gray-600 border">
                                    </div>

                                    <!-- 2. Set Access Level (Key Permission) -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Access Level *</label>
                                        <select name="access_level" class="w-full border-gray-300 rounded text-xs p-2 focus:ring-purple-500 border" required>
                                            <option value="Open-Access" {{ $doc->access_level == 'Open-Access' ? 'selected' : '' }}>Open Access (Public)</option>
                                            <option value="Institution-Only" {{ $doc->access_level == 'Institution-Only' ? 'selected' : '' }}>Institution Only (Login Required)</option>
                                            <option value="Restricted" {{ $doc->access_level == 'Restricted' ? 'selected' : '' }}>Restricted Access</option>
                                        </select>
                                    </div>

                                    <!-- 3. Edit Metadata (Keywords) -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Keywords / Tags</label>
                                        <input type="text" name="keywords" value="{{ $doc->keywords }}" placeholder="e.g. IT, Machine Learning" class="w-full border-gray-300 rounded text-xs p-2 focus:ring-purple-500 border">
                                    </div>
                                </div>

                                <!-- Reviewer Remarks -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Reviewer Remarks / Feedback</label>
                                    <textarea name="comments" rows="2" placeholder="Ingiza maoni au miongozo ya katalogi..." class="w-full border-gray-300 rounded text-xs p-2 focus:ring-purple-500 border"></textarea>
                                </div>

                                <!-- VITUFE VYA APPROVE NA REJECT -->
                                <div class="flex justify-end space-x-3 pt-3 border-t mt-4">
                                    <button type="submit" name="action" value="reject" class="bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2 rounded text-xs transition duration-150" onclick="return confirm('Una uhakika unataka kukataa kazi hii?')">
                                        ❌ Reject Submission
                                    </button>
                                    <button type="submit" name="action" value="approve" class="bg-green-600 hover:bg-green-700 text-white font-bold px-5 py-2 rounded text-xs transition duration-150">
                                        ✅ Approve & Catalog Document
                                    </button>
                                </div>
                            </form>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <p>Hakuna kazi zilizoidhinishwa na supervisors zinazosubiri cataloging kwa sasa.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>