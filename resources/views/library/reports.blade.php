<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    📊 {{ __('URMS Analytical Dashboard & Graphical Reports') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Uchambuzi wa takwimu za nyaraka, idara, viwango vya ufikiaji na mienendo ya mfumo</p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('library.index') }}" class="bg-gray-700 hover:bg-gray-800 text-white px-3.5 py-2 rounded-md text-xs font-semibold shadow transition">
                    ← Review Queue
                </a>
                <a href="{{ route('library.catalogues') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3.5 py-2 rounded-md text-xs font-semibold shadow transition">
                    Manage Catalogues
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Load Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Cards za Takwimu Kuu (Stat Metric Cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-blue-500 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Uploads</p>
                            <p class="text-2xl font-extrabold text-gray-800 mt-1">{{ $totalDocuments }}</p>
                        </div>
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-lg text-xl">
                            📚
                        </div>
                    </div>
                    <span class="text-xs text-gray-500 mt-2 block">Nyaraka zote zilizopakiwa</span>
                </div>
                
                <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-green-500 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Approved</p>
                            <p class="text-2xl font-extrabold text-green-600 mt-1">{{ $approvedCount }}</p>
                        </div>
                        <div class="p-3 bg-green-50 text-green-600 rounded-lg text-xl">
                            ✅
                        </div>
                    </div>
                    <span class="text-xs text-gray-500 mt-2 block">Zilizothibitishwa & Cataloged</span>
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

                <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-red-500 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Revisions/Reject</p>
                            <p class="text-2xl font-extrabold text-red-600 mt-1">{{ $revisionCount + $rejectedCount }}</p>
                        </div>
                        <div class="p-3 bg-red-50 text-red-600 rounded-lg text-xl">
                            🔄
                        </div>
                    </div>
                    <span class="text-xs text-gray-500 mt-2 block">Rev: {{ $revisionCount }} | Rej: {{ $rejectedCount }}</span>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-purple-500 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Downloads</p>
                            <p class="text-2xl font-extrabold text-purple-600 mt-1">{{ $totalDownloads }}</p>
                        </div>
                        <div class="p-3 bg-purple-50 text-purple-600 rounded-lg text-xl">
                            ⬇️
                        </div>
                    </div>
                    <span class="text-xs text-gray-500 mt-2 block">Kazi zilizopakuliwa na wasomaji</span>
                </div>
            </div>

            <!-- SEHEMU YA CHARTS (GRAPHIC ANALYTICS GRID) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Chart 1: Document Status Breakdown (Doughnut) -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                                🍩 Hali ya Nyaraka (Document Status)
                            </h3>
                        </div>
                        <p class="text-xs text-gray-500 mb-4">Mgawanyo wa asilimia ya hali za nyaraka zote</p>
                    </div>
                    <div class="relative h-64 flex items-center justify-center">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>

                <!-- Chart 2: Document Type Breakdown (Pie) -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                                📑 Aina za Nyaraka (Document Types)
                            </h3>
                        </div>
                        <p class="text-xs text-gray-500 mb-4">Mgawanyo wa Thesis, Dissertation, Research n.k.</p>
                    </div>
                    <div class="relative h-64 flex items-center justify-center">
                        <canvas id="typeChart"></canvas>
                    </div>
                </div>

                <!-- Chart 3: Access Level Distribution (Doughnut) -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                                🔐 Ngazi za Ufikiaji (Access Levels)
                            </h3>
                        </div>
                        <p class="text-xs text-gray-500 mb-4">Open-Access, Institution-Only na Restricted</p>
                    </div>
                    <div class="relative h-64 flex items-center justify-center">
                        <canvas id="accessChart"></canvas>
                    </div>
                </div>

            </div>

            <!-- ROW YA PILI YA CHARTS: Department Distribution & Monthly Trends -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Chart 4: Submissions by Department (Bar Chart) -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                            🏛️ Nyaraka kwa Idara (Department Breakdown)
                        </h3>
                        <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded">Top Departments</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-4">Idadi ya machapisho yaliyopakiwa kwa kila idara ya chuo</p>
                    <div class="relative h-72">
                        <canvas id="departmentChart"></canvas>
                    </div>
                </div>

                <!-- Chart 5: Monthly Submission Trends (Line Chart) -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                            📈 Mwenendo wa Upakiaji (Monthly Trends)
                        </h3>
                        <span class="text-xs font-semibold text-green-600 bg-green-50 px-2.5 py-1 rounded">Mwezi hadi Mwezi</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-4">Mwelekeo wa idadi ya tafiti zilizopakiwa kwenye mfumo</p>
                    <div class="relative h-72">
                        <canvas id="trendsChart"></canvas>
                    </div>
                </div>

            </div>

            <!-- SEHEMU YA JEDWALI (TOP DOWNLOADED & RECENT WORKS) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Top Downloaded Works -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-base font-bold text-gray-800 mb-2 flex items-center gap-2">
                        🔥 Tafiti Zinazoongoza kwa Kupakuliwa (Top Downloaded)
                    </h3>
                    <p class="text-xs text-gray-500 mb-4">Kazi zenye idadi kubwa zaidi ya downloads</p>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-xs">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600">Accession No</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600">Kichwa cha Kazi</th>
                                    <th class="px-3 py-2 text-right font-semibold text-gray-600">Downloads</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($topDownloaded as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2.5 font-bold text-blue-600">{{ $item->repository->accession_number ?? 'URMS-'.$item->repository_id }}</td>
                                        <td class="px-3 py-2.5 font-medium text-gray-800 truncate max-w-xs">{{ $item->repository->title ?? 'Nyaraka haipo' }}</td>
                                        <td class="px-3 py-2.5 text-right font-bold text-purple-600">{{ $item->download_count }} ⬇️</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-3 py-4 text-center text-gray-400">Hakuna kumbukumbu za downloads kwa sasa.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recently Approved Research Works -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-base font-bold text-gray-800 mb-2 flex items-center gap-2">
                        ⭐ Tafiti Zilizoidhinishwa Hivi Karibuni (Recent Cataloged)
                    </h3>
                    <p class="text-xs text-gray-500 mb-4">Nyaraka 5 za mwisho zilizokamilika na kupewa Accession Number</p>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-xs">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600">Accession No</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600">Title</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600">Author</th>
                                    <th class="px-3 py-2 text-right font-semibold text-gray-600">Mwaka</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($recentApproved as $doc)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2.5 font-bold text-green-600">{{ $doc->accession_number }}</td>
                                        <td class="px-3 py-2.5 font-medium text-gray-800 truncate max-w-xs">{{ $doc->title }}</td>
                                        <td class="px-3 py-2.5 text-gray-600">{{ $doc->authors }}</td>
                                        <td class="px-3 py-2.5 text-right text-gray-500">{{ $doc->year }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-3 py-4 text-center text-gray-400">Hakuna tafiti zilizoidhinishwa bado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- INITIALIZE CHART.JS SCRIPTS -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // 1. Status Chart (Doughnut)
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            new Chart(statusCtx, {
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

            // 2. Type Chart (Pie)
            const typeCtx = document.getElementById('typeChart').getContext('2d');
            const typeLabels = {!! json_encode($typeStats->pluck('document_type')) !!};
            const typeValues = {!! json_encode($typeStats->pluck('total')) !!};
            new Chart(typeCtx, {
                type: 'pie',
                data: {
                    labels: typeLabels.length ? typeLabels : ['No Data'],
                    datasets: [{
                        data: typeValues.length ? typeValues : [1],
                        backgroundColor: ['#8B5CF6', '#EC4899', '#06B6D4', '#F59E0B', '#64748B'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
                    }
                }
            });

            // 3. Access Level Chart (Doughnut)
            const accessCtx = document.getElementById('accessChart').getContext('2d');
            const accessLabels = {!! json_encode($accessLevelStats->pluck('access_level')) !!};
            const accessValues = {!! json_encode($accessLevelStats->pluck('total')) !!};
            new Chart(accessCtx, {
                type: 'doughnut',
                data: {
                    labels: accessLabels.length ? accessLabels : ['Open-Access'],
                    datasets: [{
                        data: accessValues.length ? accessValues : [{{ $approvedCount > 0 ? $approvedCount : 1 }}],
                        backgroundColor: ['#10B981', '#3B82F6', '#EF4444'],
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
                    cutout: '60%'
                }
            });

            // 4. Department Bar Chart
            const deptCtx = document.getElementById('departmentChart').getContext('2d');
            const deptLabels = {!! json_encode($departmentStats->pluck('department')) !!};
            const deptValues = {!! json_encode($departmentStats->pluck('total')) !!};
            new Chart(deptCtx, {
                type: 'bar',
                data: {
                    labels: deptLabels.length ? deptLabels : ['General'],
                    datasets: [{
                        label: 'Nyaraka zilizopakiwa',
                        data: deptValues.length ? deptValues : [{{ $totalDocuments }}],
                        backgroundColor: 'rgba(59, 130, 246, 0.85)',
                        borderColor: '#2563EB',
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
            const trendsCtx = document.getElementById('trendsChart').getContext('2d');
            const monthLabels = {!! json_encode($monthlyUploads->pluck('month_label')) !!};
            const monthValues = {!! json_encode($monthlyUploads->pluck('total')) !!};
            new Chart(trendsCtx, {
                type: 'line',
                data: {
                    labels: monthLabels.length ? monthLabels : ['Recent'],
                    datasets: [{
                        label: 'Upakiaji wa Kila Mwezi',
                        data: monthValues.length ? monthValues : [{{ $totalDocuments }}],
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.15)',
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#10B981',
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