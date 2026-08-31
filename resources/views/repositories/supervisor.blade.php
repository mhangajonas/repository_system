<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Supervisor Review Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
                
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <h3 class="text-lg font-medium mb-4">Kazi Zinazosubiri Uhakiki Wako</h3>

                @if($pendingDocuments->isEmpty())
                    <p class="text-gray-500">Hakuna kazi kwa sasa zinazosubiri uhakiki wako.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mwanafunzi</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kichwa cha Habari</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Idara</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Faili</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uamuzi na Vitendo</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($pendingDocuments as $repository)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $repository->user->name ?? 'Haijulikani' }}</td>
                                        <td class="px-6 py-4">{{ $repository->title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $repository->department }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ asset('storage/' . $repository->file_path) }}" target="_blank" class="text-blue-600 hover:underline">Angalia PDF</a>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col space-y-3">
                                                <!-- Fomu ya Uamuzi (Approve/Reject/Revision) -->
                                                <form action="{{ route('supervisor.action', $repository->id) }}" method="POST" class="space-y-2">
                                                    @csrf
                                                    <select name="action" class="border-gray-300 rounded-md shadow-sm text-sm" required>
                                                        <option value="approve">Approve (Peleka Library)</option>
                                                        <option value="revision">Omba Marekebisho (Revision)</option>
                                                        <option value="reject">Kataa (Reject)</option>
                                                    </select>
                                                    <textarea name="comments" placeholder="Andika maoni hapa..." class="border-gray-300 rounded-md shadow-sm text-sm w-full" rows="2"></textarea>
                                                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">Tuma Uamuzi</button>
                                                </form>

                                                <!-- Fomu ya Kufuta Kazi (Soft Delete) -->
                                                <form action="{{ route('repositories.destroy', $repository->id) }}" method="POST" onsubmit="return confirm('Una uhakika unataka kufuta kazi hii?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700">
                                                        Futa Kazi
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>