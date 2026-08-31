<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    📥 Download Logs — Historia ya Upakuaji
                </h2>
                <p class="text-sm text-gray-500 mt-1">Orodha kamili ya kila mtu aliyepakua kazi kutoka kwenye mfumo huu</p>
            </div>
            <div class="flex gap-2">
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="bg-gray-700 hover:bg-gray-800 text-white px-3.5 py-2 rounded-md text-xs font-semibold shadow transition">
                        ← Admin Dashboard
                    </a>
                @else
                    <a href="{{ route('library.index') }}" class="bg-gray-700 hover:bg-gray-800 text-white px-3.5 py-2 rounded-md text-xs font-semibold shadow transition">
                        ← Library Panel
                    </a>
                @endif
                <a href="{{ route('repositories.published') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3.5 py-2 rounded-md text-xs font-semibold shadow transition">
                    🏛️ Institutional Repository
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- KPI Summary Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-gray-400">
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Jumla Downloads</p>
                    <p class="text-2xl font-extrabold text-gray-700 mt-1">{{ number_format($totalDownloads) }}</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">wote pamoja</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-green-500">
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Na Wanafunzi</p>
                    <p class="text-2xl font-extrabold text-green-600 mt-1">{{ number_format($byStudents) }}</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">Students</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-blue-500">
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Na Supervisors</p>
                    <p class="text-2xl font-extrabold text-blue-600 mt-1">{{ number_format($bySupervisors) }}</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">Supervisors</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-purple-500">
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Na Librarians</p>
                    <p class="text-2xl font-extrabold text-purple-600 mt-1">{{ number_format($byLibrarians) }}</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">Librarians</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-orange-400">
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Wageni (Guest)</p>
                    <p class="text-2xl font-extrabold text-orange-500 mt-1">{{ number_format($byGuests) }}</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">Bila Login</p>
                </div>
            </div>

            <!-- Top 5 Most Downloaded -->
            @if($topDownloaded->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                <h3 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">🏆 Top 5 Kazi Zilizopakuliwa Zaidi</h3>
                <div class="flex flex-wrap gap-3">
                    @foreach($topDownloaded as $i => $top)
                        <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs">
                            <span class="font-bold text-lg {{ $i === 0 ? 'text-yellow-500' : ($i === 1 ? 'text-gray-400' : 'text-orange-400') }}">
                                #{{ $i + 1 }}
                            </span>
                            <div>
                                <p class="font-semibold text-gray-800 max-w-[180px] truncate">{{ $top->repository?->title ?? 'Imefutwa' }}</p>
                                <p class="text-gray-400">{{ $top->cnt }} {{ $top->cnt == 1 ? 'download' : 'downloads' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                <form method="GET" action="{{ route('download.logs') }}" class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Tafuta Jina la Kazi</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Kichwa cha kazi..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                    <div class="min-w-[140px]">
                        <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Chuja kwa Role</label>
                        <select name="role" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Wote --</option>
                            <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>🟢 Student</option>
                            <option value="supervisor" {{ request('role') === 'supervisor' ? 'selected' : '' }}>🔵 Supervisor</option>
                            <option value="librarian" {{ request('role') === 'librarian' ? 'selected' : '' }}>🟣 Librarian</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>🔴 Admin</option>
                            <option value="guest" {{ request('role') === 'guest' ? 'selected' : '' }}>🟠 Guest</option>
                        </select>
                    </div>
                    <div class="min-w-[140px]">
                        <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Tarehe: Kuanzia</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="min-w-[140px]">
                        <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Hadi</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="flex gap-2 pt-1">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-2 rounded-lg text-sm transition">
                            🔍 Chuja
                        </button>
                        <a href="{{ route('download.logs') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold px-4 py-2 rounded-lg text-sm transition">
                            ✕ Ondoa
                        </a>
                    </div>
                </form>
            </div>

            <!-- Main Logs Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-700">
                        📋 Historia ya Upakuaji
                        @if(request()->hasAny(['search', 'role', 'date_from', 'date_to']))
                            <span class="ml-2 text-xs font-normal text-indigo-500">(Imechujwa)</span>
                        @endif
                    </h3>
                    <span class="text-xs text-gray-400">{{ $logs->total() }} matokeo</span>
                </div>

                @if($logs->isEmpty())
                    <div class="p-10 text-center text-gray-400">
                        <p class="text-4xl mb-2">📭</p>
                        <p class="text-sm font-medium">Hakuna matukio ya upakuaji yaliyopatikana.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Kazi Iliyopakuliwa</th>
                                    <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Aliyepakua</th>
                                    <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">IP Address</th>
                                    <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Tarehe & Saa</th>
                                    <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Vitendo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($logs as $i => $log)
                                    @php
                                        $role = $log->downloaded_by_role ?? 'guest';
                                        $roleColors = [
                                            'admin'      => ['bg' => 'bg-red-100',    'text' => 'text-red-700',    'border' => 'border-red-200',    'dot' => '🔴'],
                                            'librarian'  => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'dot' => '🟣'],
                                            'supervisor' => ['bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'border' => 'border-blue-200',   'dot' => '🔵'],
                                            'student'    => ['bg' => 'bg-green-100',  'text' => 'text-green-700',  'border' => 'border-green-200',  'dot' => '🟢'],
                                            'guest'      => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'border' => 'border-orange-200', 'dot' => '🟠'],
                                        ];
                                        $rc = $roleColors[$role] ?? $roleColors['guest'];
                                        $downloaderName = $log->downloaded_by_name ?? ($log->user?->name ?? 'Mgeni');
                                    @endphp
                                    <tr class="hover:bg-indigo-50/30 transition">
                                        <td class="px-4 py-3 text-gray-400 font-mono">
                                            {{ ($logs->currentPage() - 1) * $logs->perPage() + $loop->iteration }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($log->repository)
                                                <div>
                                                    <a href="{{ route('repositories.show', $log->repository->id) }}"
                                                       class="font-semibold text-gray-900 hover:text-indigo-600 line-clamp-1">
                                                        {{ $log->repository->title }}
                                                    </a>
                                                    <p class="text-gray-400 mt-0.5">
                                                        {{ $log->repository->document_type }}
                                                        @if($log->repository->department)
                                                            · {{ $log->repository->department }}
                                                        @endif
                                                    </p>
                                                </div>
                                            @else
                                                <span class="text-gray-400 italic">Kazi imefutwa</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="w-7 h-7 rounded-full {{ $rc['bg'] }} {{ $rc['border'] }} border flex items-center justify-center text-xs font-bold {{ $rc['text'] }}">
                                                    {{ strtoupper(substr($downloaderName, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-800">{{ $downloaderName }}</p>
                                                    @if($log->user?->email)
                                                        <p class="text-gray-400 text-[10px]">{{ $log->user->email }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $rc['bg'] }} {{ $rc['text'] }} {{ $rc['border'] }}">
                                                {{ $rc['dot'] }} {{ ucfirst($role) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-gray-500 font-mono text-[11px]">
                                            {{ $log->ip_address ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                            <p class="font-medium">{{ $log->created_at->format('d M Y') }}</p>
                                            <p class="text-gray-400 text-[10px]">{{ $log->created_at->format('H:i:s') }}</p>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($log->repository)
                                                <div class="flex gap-1">
                                                    <a href="{{ route('repositories.show', $log->repository->id) }}"
                                                       class="bg-indigo-50 hover:bg-indigo-100 text-indigo-600 px-2.5 py-1 rounded text-[10px] font-semibold transition">
                                                        👁️ Tazama
                                                    </a>
                                                    <a href="{{ route('repositories.download', $log->repository->id) }}"
                                                       class="bg-green-50 hover:bg-green-100 text-green-600 px-2.5 py-1 rounded text-[10px] font-semibold transition">
                                                        ⬇️
                                                    </a>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($logs->hasPages())
                        <div class="px-5 py-4 border-t border-gray-100">
                            {{ $logs->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
