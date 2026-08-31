<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ⚙️ System Settings & Configuration
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">URMS System Configurations</h3>

                <form action="{{ route('admin.save_settings') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- System Name -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">System Name</label>
                            <input type="text" name="system_name" value="{{ $settings['system_name'] ?? 'URMS System' }}" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Max Upload Size -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Max Upload File Size (MB)</label>
                            <input type="number" name="max_file_size" value="{{ $settings['max_file_size'] ?? '50' }}" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Institutional Email Domain -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Allowed Email Domain</label>
                            <input type="text" name="email_domain" value="{{ $settings['email_domain'] ?? 'mzumbe.ac.tz' }}" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Default Access Level -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Default Access Level</label>
                            <select name="default_access_level" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="Open-Access" {{ ($settings['default_access_level'] ?? '') === 'Open-Access' ? 'selected' : '' }}>Open Access</option>
                                <option value="Institution-Only" {{ ($settings['default_access_level'] ?? '') === 'Institution-Only' ? 'selected' : '' }}>Institution Only</option>
                                <option value="Restricted" {{ ($settings['default_access_level'] ?? '') === 'Restricted' ? 'selected' : '' }}>Restricted</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-4 border-t flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2 rounded text-sm transition duration-150">
                            💾 Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>