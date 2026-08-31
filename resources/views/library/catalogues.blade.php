<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📚 Manage Library Catalogues & Categories
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Library Categories & Departments</h3>
                
                <p class="text-sm text-gray-600 mb-4">
                    Hapa unaweza kusimamia makundi (categories), idara, au mfumo wa katalogi za machapisho na tafiti zinazowasilishwa kwenye maktaba.
                </p>

                <!-- Eneo la kuongeza au kuorodhesha katalogi linaweza kuwekwa hapa -->
                <div class="border-t pt-4">
                    <span class="text-sm text-gray-500 italic">Hakuna katalogi zilizosajiliwa kwa sasa au mfumo unatumia makundi chaguomsingi.</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>