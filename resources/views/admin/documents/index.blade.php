<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dokumente verwalten') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-4 flex justify-between">
                        <h3 class="text-lg font-medium">Verteilte Dokumente</h3>
                        <a href="{{ route('admin.documents.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Neues Dokument verteilen
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- Mobile View (Cards) -->
                    <div class="md:hidden space-y-4">
                        @forelse ($documents as $document)
                            <div class="bg-gray-50 p-4 rounded-lg shadow-sm border border-gray-200">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-bold text-gray-800">{{ $document->title }}</h3>
                                    @if($document->is_completed)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Erledigt</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Offen</span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-600 mb-2">
                                    <p><strong>Empfänger:</strong> {{ $document->user->name }}</p>
                                    <p><strong>Erstellt:</strong> {{ $document->created_at->format('d.m.Y H:i') }}</p>
                                </div>
                                <div class="mt-2 text-right">
                                    @if($document->response_file_path)
                                        <a href="{{ route('user.documents.download-response', $document) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                            Download Antwort
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-sm italic">Keine Antwort</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center italic">Keine Dokumente gefunden.</p>
                        @endforelse
                    </div>

                    <!-- Desktop View (Table) -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titel</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empfänger</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Erstellt am</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Antwort</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($documents as $document)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $document->title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $document->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $document->created_at->format('d.m.Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($document->is_completed)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Erledigt</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Offen</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($document->response_file_path)
                                                <a href="{{ route('user.documents.download-response', $document) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    Download
                                                </a>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">Keine Dokumente gefunden.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $documents->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
