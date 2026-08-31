<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-red-600 leading-tight">
                System Administrator Dashboard (ICT Control Center)
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Cards za Takwimu za Admin -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
                    <p class="text-xs text-gray-500 font-bold uppercase">Total Users</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalUsers }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
                    <p class="text-xs text-gray-500 font-bold uppercase">Students</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalStudents }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow border-l-4 border-yellow-500">
                    <p class="text-xs text-gray-500 font-bold uppercase">Supervisors</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalSupervisors }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow border-l-4 border-purple-500">
                    <p class="text-xs text-gray-500 font-bold uppercase">Librarians & Admins</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalLibrarians + $totalAdmins }}</p>
                </div>
            </div>

            <!-- Quick Link kwenda Manage Users -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">User Management Eneo Maalum</h3>
                <p class="text-sm text-gray-600 mb-4">Unaweza kuangalia na kubadilisha majukumu ya watumiaji wote kwa kubofya kitufe hapa chini.</p>
                <a href="{{ route('admin.users.index') }}" class="inline-block bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-2 rounded text-sm transition">
                    👉 Nenda kwenye Manage Users
                </a>
            </div>
        </div>
    </div>
</x-app-layout>