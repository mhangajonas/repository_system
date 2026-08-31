<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                URMS Analytical Dashboard & Reports
            </h2>
            <div class="space-x-2">
                <a href="{{ route('library.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1.5 rounded text-sm font-semibold">
                    ← Review Queue
                </a>
                <a href="{{ route('library.users') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded text-sm font-semibold">
                    User Management
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Cards za Takwimu Kuu -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
                    <p class="text-xs font-bold text-gray-500 uppercase">Total Uploads</p>
                    <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ $totalDocuments }}</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
                    <p class="text-xs font-bold text-gray-500 uppercase">Approved & Published</p>
                    <p class="text-3xl font-extrabold text-green-600 mt-1">{{ $approvedCount }}</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow border-l-4 border-yellow-500">
                    <p class="text-xs font-bold text-gray-500 uppercase">Pending Review</p>
                    <p class="text-3xl font-extrabold text-yellow-600 mt-1">{{ $pendingSupervisorCount + $pendingLibraryCount }}</p>
                    <p class="text-xs text-gray-500 mt-1">Sup: {{ $pendingSupervisorCount }} | Lib: {{ $pendingLibraryCount }}</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow border-l-4 border-red-500">
                    <p class="text-xs font-bold text-gray-500 uppercase">Revisions / Rejected</p>
                    <p class="text-3xl font-extrabold text-red-600 mt-1">{{ $revisionCount + $rejectedCount }}</p>
                </div>
            </div>

            <!-- Uchambuzi kwa Idara na Aina ya Document -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Department Breakdown -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Submissions by Department</h3>
                    @if($departmentStats->isEmpty())
                        <p class="text-sm text-gray-500">Hakuna data ya idara kwa sasa.</p>
                    @else
                        <ul class="divide-y divide-gray-200">
                            @foreach($departmentStats as $stat)
                                <li class="py-2.5 flex justify-between items-center text-sm">
                                    <span class="font-medium text-gray-700">{{ $stat->department }}</span>
                                    <span class="bg-blue-100 text-blue-800 font-semibold px-2.5 py-0.5 rounded-full text-xs">
                                        {{ $stat->total }} Documents
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- Document Type Breakdown -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Submissions by Document Type</h3>
                    @if($typeStats->isEmpty())
                        <p class="text-sm text-gray-500">Hakuna data ya aina ya document.</p>
                    @else
                        <ul class="divide-y divide-gray-200">
                            @foreach($typeStats as $stat)
                                <li class="py-2.5 flex justify-between items-center text-sm">
                                    <span class="font-medium text-gray-700">{{ ucfirst($stat->document_type) }}</span>
                                    <span class="bg-purple-100 text-purple-800 font-semibold px-2.5 py-0.5 rounded-full text-xs">
                                        {{ $stat->total }} Documents
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Meza ya Hivi Karibuni Zilizoidhinishwa -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Recently Approved Research Works</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Accession No</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Title</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Author</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Year</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm">
                            @forelse($recentApproved as $doc)
                                <tr>
                                    <td class="px-4 py-3 font-bold text-blue-600">{{ $doc->accession_number }}</td>
                                    <td class="px-4 py-3 text-gray-800 font-medium">{{ $doc->title }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $doc->authors }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $doc->year }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-center text-gray-500">Hakuna tafiti zilizoidhinishwa bado.</td>
                                </tr>
                            @endempty
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>