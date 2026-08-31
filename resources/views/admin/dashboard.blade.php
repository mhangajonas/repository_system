<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-red-600 leading-tight flex items-center gap-2">
                    🛡️ {{ __('System Administrator Dashboard (ICT Control Center)') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Usimamizi wa mfumo mzima, watumiaji, backups, na uchambuzi wa takwimu za kiutendaji</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.users.index') }}" class="bg-red-600 hover:bg-red-700 text-white px-3.5 py-2 rounded-md text-xs font-semibold shadow transition">
                    👥 Manage Users
                </a>
                <a href="{{ route('admin.backups') }}" class="bg-gray-700 hover:bg-gray-800 text-white px-3.5 py-2 rounded-md text-xs font-semibold shadow transition">
                    💾 Manage Backups
                </a>
                <a href="{{ route('admin.settings') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3.5 py-2 rounded-md text-xs font-semibold shadow transition">
                    ⚙️ Settings
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Load Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Cards za Takwimu Kuu (Admin KPI Metrics) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-blue-500 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Users</p>
                            <p class="text-2xl font-extrabold text-gray-800 mt-1">{{ $totalUsers }}</p>
                        </div>
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-lg text-xl">
                            👥
                        </div>
                    </div>
                    <span class="text-xs text-gray-500 mt-2 block">Students: {{ $totalStudents }} | Staff: {{ $totalUsers - $totalStudents }}</span>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-green-500 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Documents</p>
                            <p class="text-2xl font-extrabold text-green-600 mt-1">{{ $totalDocuments }}</p>
                        </div>
                        <div class="p-3 bg-green-50 text-green-600 rounded-lg text-xl">
                            📚
                        </div>
                    </div>
                    <span class="text-xs text-gray-500 mt-2 block">Approved: {{ $approvedCount }}</span>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-yellow-500 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pending Review</p>
                            <p class="text-2xl font-extrabold text-yellow-600 mt-1">{{ $pendingSupervisorCount + $pendingLibraryCount }}</p>
                        </div>
                        <div class="p-3 bg-yellow-50 text-yellow-600 rounded-lg text-xl">
                            ⏳
                        </div>
                    </div>
                    <span class="text-xs text-gray-500 mt-2 block">Sup: {{ $pendingSupervisorCount }} | Lib: {{ $pendingLibraryCount }}</span>
                </div>

                <a href="{{ route('download.logs') }}" class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-purple-500 hover:shadow-md transition hover:border-purple-700 block">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Downloads</p>
                            <p class="text-2xl font-extrabold text-purple-600 mt-1">{{ $totalDownloads }}</p>
                        </div>
                        <div class="p-3 bg-purple-50 text-purple-600 rounded-lg text-xl">
                            ⬇️
                        </div>
                    </div>
                    <span class="text-xs text-purple-500 mt-2 block font-semibold">📥 Angalia Download Logs →</span>
                </a>

                <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-red-500 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Audit & Backups</p>
                            <p class="text-2xl font-extrabold text-red-600 mt-1">{{ $totalBackups }}</p>
                        </div>
                        <div class="p-3 bg-red-50 text-red-600 rounded-lg text-xl">
                            💾
                        </div>
                    </div>
                    <span class="text-xs text-gray-500 mt-2 block">Activity Logs Zilizorekodiwa</span>
                </div>
            </div>

            <!-- GRAPHIC ANALYTICS ROW 1 (User Roles & Document Status) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Chart 1: User Roles Distribution (Doughnut) -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                                👥 Mgawanyo wa Watumiaji (User Roles)
                            </h3>
                        </div>
                        <p class="text-xs text-gray-500 mb-4">Mgawanyo wa majukumu ya watumiaji kwenye mfumo</p>
                    </div>
                    <div class="relative h-64 flex items-center justify-center">
                        <canvas id="adminUserRolesChart"></canvas>
                    </div>
                </div>

                <!-- Chart 2: Document Status Lifecycle (Doughnut) -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                                📑 Hali ya Nyaraka (Document Status)
                            </h3>
                        </div>
                        <p class="text-xs text-gray-500 mb-4">Takwimu za nyaraka zilizoidhinishwa na zinazosubiri</p>
                    </div>
                    <div class="relative h-64 flex items-center justify-center">
                        <canvas id="adminDocStatusChart"></canvas>
                    </div>
                </div>

                <!-- Chart 3: System Audit Trail Activity (Doughnut) -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                                🛡️ Vitendo vya Mfumo (Audit Activity)
                            </h3>
                        </div>
                        <p class="text-xs text-gray-500 mb-4">Aina za matukio (Kufuta, Kurejesha, Idhini n.k.)</p>
                    </div>
                    <div class="relative h-64 flex items-center justify-center">
                        <canvas id="adminActivityChart"></canvas>
                    </div>
                </div>

            </div>

            <!-- GRAPHIC ANALYTICS ROW 2 (Departments & Monthly Trends) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Chart 4: Department Distribution (Bar Chart) -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                            🏛️ Nyaraka kwa Idara (Department Submissions)
                        </h3>
                        <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded">Top Idara</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-4">Idadi ya machapisho yaliyopakiwa kulingana na idara</p>
                    <div class="relative h-72">
                        <canvas id="adminDepartmentChart"></canvas>
                    </div>
                </div>

                <!-- Chart 5: Monthly Submission Trends (Line Chart) -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                            📈 Mwelekeo wa Upakiaji (Monthly Trends)
                        </h3>
                        <span class="text-xs font-semibold text-green-600 bg-green-50 px-2.5 py-1 rounded">Kila Mwezi</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-4">Mwenendo wa kazi zinazopakiwa kwa miezi 6 ya hivi karibuni</p>
                    <div class="relative h-72">
                        <canvas id="adminTrendsChart"></canvas>
                    </div>
                </div>

            </div>

            <!-- LIVE ACTIVITY FEED & RECENT USERS -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Recent Activity Logs / Audit Trail Feed -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                                💾 Kumbukumbu za Hivi Karibuni (Recent Backups & Logs)
                            </h3>
                            <p class="text-xs text-gray-500">Matukio 5 ya mwisho yaliyorekodiwa kwenye Audit Trail</p>
                        </div>
                        <a href="{{ route('admin.backups') }}" class="text-xs font-bold text-red-600 hover:text-red-800">
                            Tazama Zote →
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-xs">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600">Mtumiaji</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600">Kitendo</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600">Maelezo</th>
                                    <th class="px-3 py-2 text-right font-semibold text-gray-600">Tarehe</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($recentActivityLogs as $log)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2.5 font-semibold text-gray-900">{{ $log->user->name ?? 'System' }}</td>
                                        <td class="px-3 py-2.5">
                                            <span class="px-2 py-0.5 text-[10px] rounded-full font-bold
                                                @if($log->action === 'DELETE_DOCUMENT' || $log->action === 'DELETE_USER') bg-red-100 text-red-800
                                                @elseif($log->action === 'RESTORE_ACTION') bg-green-100 text-green-800
                                                @elseif($log->action === 'SUPERVISOR_ACTION') bg-blue-100 text-blue-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $log->action }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2.5 text-gray-600 truncate max-w-xs">{{ $log->description }}</td>
                                        <td class="px-3 py-2.5 text-right text-gray-400 whitespace-nowrap">{{ $log->created_at->format('d M, H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-3 py-4 text-center text-gray-400">Hakuna kumbukumbu za vitendo kwa sasa.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Registered Users -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                                👤 Watumiaji Wapya Waliojiunga (Recent Users)
                            </h3>
                            <p class="text-xs text-gray-500">Watumiaji 5 wa mwisho waliosajiliwa kwenye mfumo</p>
                        </div>
                        <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-red-600 hover:text-red-800">
                            Manage Users →
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-xs">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600">Jina</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600">Barua Pepe</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600">Jukumu (Role)</th>
                                    <th class="px-3 py-2 text-right font-semibold text-gray-600">Tarehe</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($recentUsers as $u)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2.5 font-semibold text-gray-900">{{ $u->name }}</td>
                                        <td class="px-3 py-2.5 text-gray-500">{{ $u->email }}</td>
                                        <td class="px-3 py-2.5">
                                            <span class="px-2 py-0.5 text-[10px] rounded-full font-bold
                                                @if($u->role === 'admin') bg-red-100 text-red-800
                                                @elseif($u->role === 'librarian') bg-purple-100 text-purple-800
                                                @elseif($u->role === 'supervisor') bg-yellow-100 text-yellow-800
                                                @else bg-green-100 text-green-800 @endif">
                                                {{ ucfirst($u->role) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2.5 text-right text-gray-400 whitespace-nowrap">{{ $u->created_at->format('d M Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-3 py-4 text-center text-gray-400">Hakuna watumiaji kwa sasa.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- INITIALIZE CHART.JS SCRIPTS FOR ADMIN -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // 1. User Roles Chart (Doughnut)
            const userCtx = document.getElementById('adminUserRolesChart').getContext('2d');
            new Chart(userCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Students', 'Supervisors', 'Librarians', 'Admins'],
                    datasets: [{
                        data: [
                            {{ $totalStudents }},
                            {{ $totalSupervisors }},
                            {{ $totalLibrarians }},
                            {{ $totalAdmins }}
                        ],
                        backgroundColor: ['#10B981', '#F59E0B', '#8B5CF6', '#EF4444'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
                    },
                    cutout: '65%'
                }
            });

            // 2. Document Status Chart (Doughnut)
            const docCtx = document.getElementById('adminDocStatusChart').getContext('2d');
            new Chart(docCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Approved', 'Pending Sup', 'Pending Lib', 'Revision', 'Rejected'],
                    datasets: [{
                        data: [
                            {{ $approvedCount }},
                            {{ $pendingSupervisorCount }},
                            {{ $pendingLibraryCount }},
                            {{ $revisionCount }},
                            {{ $rejectedCount }}
                        ],
                        backgroundColor: ['#10B981', '#FBBF24', '#3B82F6', '#F97316', '#EF4444'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
                    },
                    cutout: '65%'
                }
            });

            // 3. System Activity Chart (Doughnut / Pie)
            const actCtx = document.getElementById('adminActivityChart').getContext('2d');
            const actLabels = {!! json_encode($activityStats->pluck('action')) !!};
            const actValues = {!! json_encode($activityStats->pluck('total')) !!};
            new Chart(actCtx, {
                type: 'doughnut',
                data: {
                    labels: actLabels.length ? actLabels : ['No Activity Logs'],
                    datasets: [{
                        data: actValues.length ? actValues : [1],
                        backgroundColor: ['#EF4444', '#10B981', '#3B82F6', '#F59E0B', '#64748B'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } }
                    },
                    cutout: '60%'
                }
            });

            // 4. Department Bar Chart
            const deptCtx = document.getElementById('adminDepartmentChart').getContext('2d');
            const deptLabels = {!! json_encode($departmentStats->pluck('department')) !!};
            const deptValues = {!! json_encode($departmentStats->pluck('total')) !!};
            new Chart(deptCtx, {
                type: 'bar',
                data: {
                    labels: deptLabels.length ? deptLabels : ['General'],
                    datasets: [{
                        label: 'Nyaraka zilizopakiwa',
                        data: deptValues.length ? deptValues : [{{ $totalDocuments }}],
                        backgroundColor: 'rgba(239, 68, 68, 0.85)',
                        borderColor: '#DC2626',
                        borderWidth: 1,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } },
                        x: { ticks: { maxRotation: 45, minRotation: 0, font: { size: 10 } } }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });

            // 5. Monthly Trends Line Chart
            const trendsCtx = document.getElementById('adminTrendsChart').getContext('2d');
            const monthLabels = {!! json_encode($monthlyUploads->pluck('month_label')) !!};
            const monthValues = {!! json_encode($monthlyUploads->pluck('total')) !!};
            new Chart(trendsCtx, {
                type: 'line',
                data: {
                    labels: monthLabels.length ? monthLabels : ['Recent'],
                    datasets: [{
                        label: 'Upakiaji wa Kila Mwezi',
                        data: monthValues.length ? monthValues : [{{ $totalDocuments }}],
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.15)',
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#3B82F6',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        });
    </script>
</x-app-layout>