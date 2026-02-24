<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                {{ __('Materialverwaltung') }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('materials.index') }}" class="bg-gray-700 hover:bg-gray-600 text-gray-200 font-bold py-2 px-4 rounded transition border border-gray-600 text-sm">
                    Zum Lager
                </a>
                <a href="{{ route('materials.stats') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition shadow text-sm">
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

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-700">
                                    <thead class="bg-gray-900">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Name</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Bestand</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Warnschwelle</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Aktionen</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-gray-800 divide-y divide-gray-700">
                                        @forelse($materials as $material)
                                            <tr>
                                                <form action="{{ route('materials.update', $material) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <td class="px-4 py-4">
                                                        <input type="text" name="name" value="{{ $material->name }}" required class="min-w-[150px] w-full bg-gray-900 border border-gray-600 text-gray-200 rounded-md focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-50 text-sm">
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap">
                                                        <input type="number" name="stock_count" value="{{ $material->stock_count }}" min="0" required class="w-24 bg-gray-900 border border-gray-600 text-gray-200 rounded-md focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-50 text-sm">
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap">
                                                        <input type="number" name="low_stock_threshold" value="{{ $material->low_stock_threshold }}" min="0" required class="w-24 bg-gray-900 border border-gray-600 text-gray-200 rounded-md focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-50 text-sm">
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <div class="flex justify-end items-center gap-2">
                                                            <button type="submit" class="text-blue-400 hover:text-blue-300 bg-gray-700 px-3 py-1 rounded">Speichern</button>
                                                </form>
                                                            <form action="{{ route('materials.destroy', $material) }}" method="POST" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" onclick="return confirm('Möchtest du dieses Material wirklich löschen? Historie bleibt bei den Transaktionen ggf. erhalten (ohne Material Name), besser ist es Bestand auf 0 zu setzen.')" class="text-red-400 hover:text-red-300 bg-gray-700 px-3 py-1 rounded">Löschen</button>
                                                            </form>
                                                        </div>
                                                    </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 text-center">Keine Materialien gefunden.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
