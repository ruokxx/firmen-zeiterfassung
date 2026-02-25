<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 sm:gap-0">
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                {{ __('Materialverwaltung') }}
            </h2>
            <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                <a href="{{ route('materials.index') }}" class="bg-gray-700 hover:bg-gray-600 text-gray-200 font-bold py-2 px-4 rounded transition border border-gray-600 text-sm flex-1 sm:flex-none text-center">
                    Zum Lager
                </a>
                <a href="{{ route('materials.stats') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition shadow text-sm flex-1 sm:flex-none text-center">
                    Statistiken
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-900 border border-green-700 text-green-100 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-900 border border-red-700 text-red-100 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-900 border border-red-700 text-red-100 px-4 py-3 rounded relative">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Neues Material anlegen -->
                <div class="lg:col-span-1">
                    <div class="bg-gray-800 border border-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-100">
                            <h3 class="text-lg font-bold text-orange-500 mb-4 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Neues Material
                            </h3>
                            <form action="{{ route('materials.store') }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="name" class="block text-sm font-medium text-gray-300">Name</label>
                                    <input type="text" name="name" id="name" required class="mt-1 block w-full bg-gray-900 border border-gray-600 text-gray-200 rounded-md shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-50">
                                </div>
                                <div class="mb-4">
                                    <label for="stock_count" class="block text-sm font-medium text-gray-300">Startbestand</label>
                                    <input type="number" name="stock_count" id="stock_count" min="0" value="0" required class="mt-1 block w-full bg-gray-900 border border-gray-600 text-gray-200 rounded-md shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-50">
                                </div>
                                <div class="mb-4">
                                    <label for="low_stock_threshold" class="block text-sm font-medium text-gray-300">Warnschwelle (E-Mail)</label>
                                    <input type="number" name="low_stock_threshold" id="low_stock_threshold" min="0" value="2" required class="mt-1 block w-full bg-gray-900 border border-gray-600 text-gray-200 rounded-md shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-50">
                                </div>
                                <div class="mt-6 flex justify-end">
                                    <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded shadow transition">
                                        Speichern
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

                <!-- Material List -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-800 border border-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-100">
                            <h3 class="text-lg font-bold text-orange-500 mb-4 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                Materialien bearbeiten
                            </h3>

                            <div class="space-y-4">
                                <!-- Desktop Header (Hidden on Mobile) -->
                                <div class="hidden sm:grid sm:grid-cols-12 gap-4 px-4 py-3 bg-gray-900 border border-gray-700 text-xs font-bold text-gray-400 uppercase tracking-wider rounded-lg mb-2">
                                    <div class="col-span-5">Name</div>
                                    <div class="col-span-2">Bestand</div>
                                    <div class="col-span-2">Warnschwelle</div>
                                    <div class="col-span-3 text-right">Aktionen</div>
                                </div>

                                @forelse($materials as $material)
                                    <div class="bg-gray-900 sm:bg-transparent border border-gray-700 sm:border-b sm:border-t-0 sm:border-l-0 sm:border-r-0 rounded-lg sm:rounded-none p-4 sm:p-0 sm:pb-3 mb-4 sm:mb-0 relative shadow-sm sm:shadow-none">
                                        <form action="{{ route('materials.update', $material) }}" method="POST" class="flex flex-col sm:grid sm:grid-cols-12 gap-4 items-start sm:items-center">
                                            @csrf
                                            @method('PUT')
                                            
                                            <!-- Name -->
                                            <div class="w-full sm:col-span-5">
                                                <label class="block text-xs font-bold text-gray-500 mb-1 sm:hidden uppercase">Name</label>
                                                <input type="text" name="name" value="{{ $material->name }}" required class="w-full bg-gray-800 sm:bg-gray-900 border border-gray-600 text-gray-200 rounded-md focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-50 text-sm">
                                            </div>

                                            <div class="w-full grid grid-cols-2 gap-4 sm:col-span-4 sm:flex sm:gap-x-8 sm:w-auto">
                                                <!-- Bestand -->
                                                <div class="w-full sm:w-24">
                                                    <label class="block text-xs font-bold text-gray-500 mb-1 sm:hidden uppercase">Bestand</label>
                                                    <input type="number" name="stock_count" value="{{ $material->stock_count }}" min="0" required class="w-full bg-gray-800 sm:bg-gray-900 border border-gray-600 text-gray-200 rounded-md focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-50 text-sm">
                                                </div>

                                                <!-- Warnschwelle -->
                                                <div class="w-full sm:w-24">
                                                    <label class="block text-xs font-bold text-gray-500 mb-1 sm:hidden uppercase">Warnschwelle</label>
                                                    <input type="number" name="low_stock_threshold" value="{{ $material->low_stock_threshold }}" min="0" required class="w-full bg-gray-800 sm:bg-gray-900 border border-gray-600 text-gray-200 rounded-md focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-50 text-sm">
                                                </div>
                                            </div>

                                            <!-- Actions -->
                                            <div class="w-full sm:col-span-3 flex flex-row justify-end items-center gap-2 mt-2 sm:mt-0 pt-3 sm:pt-0 border-t border-gray-700 sm:border-0">
                                                <button type="submit" class="text-blue-100 bg-blue-700 hover:bg-blue-600 border border-blue-600 sm:border-0 sm:bg-gray-700 sm:text-blue-400 sm:hover:text-blue-300 px-4 py-2 sm:px-3 sm:py-1 rounded text-sm font-medium transition flex-1 sm:flex-none text-center shadow sm:shadow-none">Speichern</button>
                                                <button type="button" onclick="if(confirm('Möchtest du dieses Material wirklich löschen? Historie bleibt bei den Transaktionen ggf. erhalten (ohne Material Name), besser ist es Bestand auf 0 zu setzen.')) { document.getElementById('delete-form-{{ $material->id }}').submit(); }" class="text-red-100 bg-red-900 hover:bg-red-800 border border-red-700 sm:border-0 sm:bg-gray-700 sm:text-red-400 sm:hover:text-red-300 px-4 py-2 sm:px-3 sm:py-1 rounded text-sm font-medium transition flex-1 sm:flex-none text-center shadow sm:shadow-none">Löschen</button>
                                            </div>
                                        </form>
                                        
                                        <!-- Separate Delete Form outside the visual layout grid -->
                                        <form id="delete-form-{{ $material->id }}" action="{{ route('materials.destroy', $material) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                @empty
                                    <div class="text-sm text-gray-400 text-center py-8">Keine Materialien gefunden.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
