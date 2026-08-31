<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Backups & Activity Logs') }}
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

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mtumiaji</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kitendo (Action)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Maelezo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarehe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vitendo (Actions)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            @forelse($backups as $log)
                                <tr>
                                    <td class="px-6 py-4 font-semibold text-gray-900">
                                        {{ $log->user->name ?? 'System / Mgeni' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800 font-bold">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ $log->description }}</td>
                                    <td class="px-6 py-4 text-xs text-gray-500">{{ $log->created_at->format('d M Y, H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            // Chomoa ID ya document kutoka kwenye maelezo ya logi
                                            preg_match('/\(ID: (\d+)\)/', $log->description, $matches);
                                            $docId = $matches[1] ?? null;
                                        @endphp

                                        <div class="flex items-center space-x-2">
                                            {{-- 1. Kitufe cha Kurudisha (Restore) - Admin pekee --}}
                                            @if($log->action === 'DELETE_DOCUMENT' && auth()->user()->role === 'admin')
                                                <form action="{{ route('admin.backups.restore', $log->id) }}" method="POST" onsubmit="return confirm('Una uhakika unataka kurudisha kazi hii kwenye mfumo?');">
                                                    @csrf
                                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs font-semibold shadow">
                                                        🔄 Rudisha
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- 2. Kitufe cha Kudownload Kazi Iliyofutwa (Functional) --}}
                                            @if($log->action === 'DELETE_DOCUMENT' && $docId)
                                                <a href="{{ route('repositories.download', $docId) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-semibold shadow">
                                                    ⬇️ Pakua
                                                </a>
                                            @endif

                                            {{-- 3. Kitufe cha Onyesha Metadata --}}
                                            @if($log->action === 'DELETE_DOCUMENT' && $docId)
                                                <a href="{{ route('repositories.show', $docId) }}" target="_blank" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-xs font-semibold shadow">
                                                    👁️ Onyesha
                                                </a>
                                            @endif

                                            {{-- 4. Kitufe cha Futa Kabisa (Force Delete) - Kinatambua Admin au Supervisor --}}
                                            <form action="{{ auth()->user()->role === 'admin' ? route('admin.backups.destroy', $log->id) : route('supervisor.backups.destroy', $log->id) }}" method="POST" onsubmit="return confirm('Una uhakika unataka kufuta kabisa kumbukumbu hii na faili lake?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-semibold shadow">
                                                    🗑️ Futa
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        Hakuna kumbukumbu za backups kwa sasa.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $backups->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>