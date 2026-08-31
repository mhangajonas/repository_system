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
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 relative">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-700">Audit Trails & Activity Logs</h3>
                    
                    <!-- Kitufe cha kutengeneza Backup mpya -->
                    <form action="{{ route('admin.create_backup') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-semibold shadow">
                            + Tengeneza Backup Mpya
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mtumiaji</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kitendo (Action)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Maelezo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarehe</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kitendo (Restore)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            @forelse($backups as $log)
                                <tr>
                                    <td class="px-6 py-4 font-semibold text-gray-900">
                                        {{ $log->user->name ?? 'System / Mgeni' }}<br>
                                        <span class="text-xs text-gray-400">{{ $log->user->email ?? '' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 text-xs rounded-full font-bold 
                                            @if($log->action === 'DELETE_DOCUMENT' || $log->action === 'DELETE_USER') bg-red-100 text-red-800 
                                            @elseif($log->action === 'SUPERVISOR_ACTION') bg-blue-100 text-blue-800 
                                            @else bg-gray-100 text-gray-850 @endif">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 max-w-xs">{{ $log->description }}</td>
                                    <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">{{ $log->created_at->format('d M Y, H:i') }}</td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <!-- Kitufe cha Kurudisha (Restore Button) -->
                                        <form action="{{ route('admin.backups.restore', $log->id) }}" method="POST" onsubmit="return confirm('Una uhakika unataka kurudisha hali hii kutoka kwenye logi hii?');" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-xs font-semibold shadow transition">
                                                🔄 Rudisha
                                            </button>
                                        </form>
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