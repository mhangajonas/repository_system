<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Librarian Final Review Panel (URMS)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Documents Approved by Supervisor (Pending Cataloging)</h3>

                @if($pendingDocuments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title & Details</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PDF</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action & Comments</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($pendingDocuments as $doc)
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ $doc->user->name ?? 'N/A' }}<br>
                                            <span class="text-xs text-gray-500">{{ $doc->user->email ?? '' }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            <strong>{{ $doc->title }}</strong><br>
                                            <span class="text-xs text-green-600 font-semibold">Supervisor Approved ✓</span><br>
                                            <span class="text-xs text-gray-500">Dept: {{ $doc->department }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-blue-600 hover:underline">📄 View PDF</a>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <form action="{{ route('library.action', $doc->id) }}" method="POST" class="space-y-2">
                                                @csrf
                                                <div>
                                                    <textarea name="comments" rows="2" class="w-full text-xs border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Andika sababu ya kukataa au Maelekezo ya Mkutubi..."></textarea>
                                                </div>
                                                <div class="flex justify-center space-x-2">
                                                    <button type="submit" name="action" value="approve" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-xs font-bold transition">Publish & Assign Accession No.</button>
                                                    <button type="submit" name="action" value="reject" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-xs font-bold transition">Reject</button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500">Hakuna kazi iliyothibitishwa na supervisor inayowasili maktaba kwa sasa.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>