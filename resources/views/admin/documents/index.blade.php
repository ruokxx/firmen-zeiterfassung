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

                    <!-- Mobile View (Accordion Grouped by Document) -->
                    <div class="md:hidden space-y-3">
                        @forelse ($documents as $document)
                            <div x-data="{ open: false }" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                                {{-- Accordion Header --}}
                                <div @click="open = !open" class="p-4 flex justify-between items-center cursor-pointer hover:bg-gray-50 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="text-gray-400">
                                            <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                            <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                                        </div>
                                        <div>
                                            <span class="font-bold text-gray-800">{{ $document->created_at->format('d.m.Y') }}</span>
                                            <span class="mx-1 text-gray-400">|</span>
                                            <span class="text-gray-700">{{ $document->title }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $assignments[$document->file_path]->count() }} Empfänger
                                        </span>
                                    </div>
                                </div>

                                {{-- Accordion Body (List of Users) --}}
                                <div x-show="open" x-collapse class="border-t border-gray-200 bg-gray-50 divide-y divide-gray-200">
                                    @foreach($assignments[$document->file_path] as $assignment)
                                        <div class="p-4 hover:bg-gray-100 transition">
                                            <div class="flex justify-between items-start mb-2">
                                                <span class="font-semibold text-gray-700">{{ $assignment->user->name }}</span>
                                                @if($assignment->is_completed)
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Erledigt</span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Offen</span>
                                                @endif
                                            </div>
                                            
                                            <div class="flex justify-between items-center mt-2">
                                                @if($assignment->response_file_path)
                                                    <a href="{{ route('user.documents.download-response', $assignment) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                                        Antwort
                                                    </a>
                                                @else
                                                     <span class="text-gray-400 text-xs italic">Keine Antwort</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    @if($document->description)
                                        <div class="p-4 bg-gray-100 text-sm text-gray-600 border-t border-gray-200">
                                            <strong>Beschreibung:</strong> {{ $document->description }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center italic">Keine Dokumente gefunden.</p>
                        @endforelse
                    </div>

                    <!-- Desktop View (Table Grouped) -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titel</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Erstellt am</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empfänger (Status)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktionen</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($documents as $document)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $document->title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $document->created_at->format('d.m.Y H:i') }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col space-y-1">
                                                @foreach($assignments[$document->file_path] as $assignment)
                                                    <div class="flex items-center justify-between text-sm">
                                                        <span class="text-gray-600 mr-2">{{ $assignment->user->name }}</span>
                                                        <div class="flex items-center space-x-2">
                                                            @if($assignment->is_completed)
                                                                <span class="text-xs text-green-600 font-bold">✓</span>
                                                            @else
                                                                <span class="text-xs text-yellow-600">○</span>
                                                            @endif
                                                            
                                                            @if($assignment->response_file_path)
                                                                <a href="{{ route('user.documents.download-response', $assignment) }}" class="text-indigo-600 hover:text-indigo-900" title="Antwort herunterladen">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                             {{-- Actions if needed --}}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">Keine Dokumente gefunden.</td>
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
