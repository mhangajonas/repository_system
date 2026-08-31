<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Upload Academic Document (URMS)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('repositories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block font-medium text-sm text-gray-700">Document Title</label>
                        <input type="text" name="title" required class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Authors (Comma separated)</label>
                            <input type="text" name="authors" required class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Supervisor Name</label>
                            <input type="text" name="supervisor" required class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Department</label>
                            <input type="text" name="department" required class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Degree/Programme</label>
                            <input type="text" name="degree_programme" placeholder="e.g. BSc Computer Science" required class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Year</label>
                            <input type="number" name="year" value="{{ date('Y') }}" required class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Document Type *</label>
                            <select name="document_type" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="Thesis">Thesis</option>
                                <option value="Dissertation">Dissertation</option>
                                <option value="Research Paper">Research Paper</option>
                                <option value="Past Exam">Past Examination</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Access Level (Kiwango cha Ufikiaji) *</label>
                            <select name="access_level" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="Open-Access" {{ cache('default_access_level') === 'Open-Access' ? 'selected' : '' }}>🟢 Open Access (Public)</option>
                                <option value="Institution-Only" {{ cache('default_access_level') === 'Institution-Only' ? 'selected' : '' }}>🟡 Institution-Only (Wanachuo)</option>
                                <option value="Restricted" {{ cache('default_access_level') === 'Restricted' ? 'selected' : '' }}>🔴 Restricted (Idhini Maalum)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Keywords (Comma separated) *</label>
                            <input type="text" name="keywords" placeholder="e.g. AI, Laravel, Database" required class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700">Abstract</label>
                        <textarea name="abstract" rows="4" required class="w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700">Upload PDF Document (Max 50MB)</label>
                        <input type="file" name="file" accept=".pdf" required class="w-full border border-gray-300 p-2 rounded-md">
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            Submit Document
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>