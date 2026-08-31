<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kumbukumbu ya Kazi Zangu Nilizowahi Kutuma') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if($myHistory->isEmpty())
                    <p class="text-gray-500 text-center py-4">Hujawahi kutuma kazi yoyote bado.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kichwa cha Kazi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supervisor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarehe</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hali (Status)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Maoni (Comments)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kitendo</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($myHistory as $doc)
                                    <tr>
                                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $doc->title }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $doc->supervisor }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $doc->created_at->format('d M Y') }}</td>
                                        <td class="px-6 py-4">
                                            @if($doc->status == 'pending_supervisor')
                                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending Supervisor</span>
                                            @elseif($doc->status == 'pending_library')
                                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Pending Library</span>
                                            @elseif($doc->status == 'approved')
                                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Approved</span>
                                            @elseif($doc->status == 'revision_requested')
                                                <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">Inahitaji Marudio</span>
                                            @else
                                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Rejected</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ $doc->comments ?? 'Bila maoni' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center space-x-2">
                                            <!-- Kitufe cha Kuangalia Maelezo (Show) -->
                                            <a href="{{ route('repositories.show', $doc->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold bg-indigo-50 px-3 py-1 rounded-md border border-indigo-200">Onyesha</a>

                                            <!-- Kitufe cha Futa (Delete) -->
                                            <form action="{{ route('repositories.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Una uhakika unataka kufuta kazi hii?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 font-semibold bg-red-50 px-3 py-1 rounded-md border border-red-200">Futa</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $myHistory->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>