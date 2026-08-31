<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kumbukumbu ya Kazi Zilizohakikiwa (Supervisor History)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-700">Kazi Zote Zilizopitia Kwako</h3>
                    <a href="{{ route('supervisor.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-semibold shadow">
                        ← Rudi Kwenye Uhakiki (Review Dashboard)
                    </a>
                </div>

                @if($reviewedDocuments->isEmpty())
                    <p class="text-gray-500 text-center py-6">Hakuna kumbukumbu ya kazi zilizohakikiwa kwa sasa.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mwanafunzi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kichwa cha Kazi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Idara / Mwaka</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hali (Status)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Maoni</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarehe</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Vitendo</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 text-sm">
                                @foreach($reviewedDocuments as $doc)
                                    <tr class="{{ $doc->trashed() ? 'bg-red-50/50' : '' }}">
                                        <td class="px-6 py-4 font-semibold text-gray-900">
                                            {{ $doc->user->name ?? 'Mwanafunzi' }}<br>
                                            <span class="text-xs text-gray-400">{{ $doc->user->reg_number ?? ($doc->user->email ?? '') }}</span>
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-800">
                                            {{ $doc->title }}
                                            <div class="text-xs text-gray-500">{{ $doc->document_type }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600">
                                            {{ $doc->department }} ({{ $doc->year }})
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($doc->trashed())
                                                <span class="px-2.5 py-1 text-xs rounded-full bg-red-100 text-red-800 font-bold">
                                                    🗑️ Imefutwa (Kwenye Backups)
                                                </span>
                                            @elseif($doc->status == 'approved')
                                                <span class="px-2.5 py-1 text-xs rounded-full bg-green-100 text-green-800 font-bold">
                                                    ✅ Approved
                                                </span>
                                            @elseif($doc->status == 'pending_library')
                                                <span class="px-2.5 py-1 text-xs rounded-full bg-blue-100 text-blue-800 font-bold">
                                                    📚 Pending Library
                                                </span>
                                            @elseif($doc->status == 'revision_requested')
                                                <span class="px-2.5 py-1 text-xs rounded-full bg-orange-100 text-orange-800 font-bold">
                                                    🔄 Inahitaji Marekebisho
                                                </span>
                                            @elseif($doc->status == 'rejected')
                                                <span class="px-2.5 py-1 text-xs rounded-full bg-red-100 text-red-800 font-bold">
                                                    ❌ Rejected
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 font-bold">
                                                    ⏳ Pending Review
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 max-w-xs text-xs">
                                            {{ $doc->comments ?? 'Bila maoni' }}
                                        </td>
                                        <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">
                                            {{ $doc->updated_at->format('d M Y, H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <!-- Onyesha -->
                                                <a href="{{ route('repositories.show', $doc->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold bg-indigo-50 px-2.5 py-1 rounded text-xs border border-indigo-200 shadow-sm">
                                                    👁️ Onyesha
                                                </a>

                                                <!-- Pakua -->
                                                <a href="{{ route('repositories.download', $doc->id) }}" class="text-green-700 hover:text-green-900 font-semibold bg-green-50 px-2.5 py-1 rounded text-xs border border-green-200 shadow-sm">
                                                    ⬇️ Pakua
                                                </a>

                                                <!-- Futa (Inafanya Soft Delete na Kuiweka kwenye Backups za Admin) -->
                                                @if(!$doc->trashed())
                                                    <form action="{{ route('repositories.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Una uhakika unataka kufuta kazi hii na kuiweka kwenye backups za mfumo?');" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 font-semibold bg-red-50 px-2.5 py-1 rounded text-xs border border-red-200 shadow-sm">
                                                            🗑️ Futa
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $reviewedDocuments->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>