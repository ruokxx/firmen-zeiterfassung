<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Meine Dokumente') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Hier findest du Dokumente, die dir zugewiesen wurden. Du kannst sie herunterladen, bearbeiten und wieder hochladen.') }}
        </p>
    </header>

    <div class="mt-6 space-y-6">
        @forelse($user->documents as $document)
            <div x-data="{ open: false }" class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                {{-- Accordion Header --}}
                <div @click="open = !open" class="p-4 flex justify-between items-center cursor-pointer hover:bg-gray-100 transition">
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
                        @if($document->is_completed)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Erledigt</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Offen</span>
                        @endif
                    </div>
                </div>

                {{-- Accordion Body --}}
                <div x-show="open" x-collapse class="border-t border-gray-200 p-4 bg-white">
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Von: {{ $document->creator->name }}</p>
                        @if($document->description)
                            <p class="text-sm text-gray-600 mt-2 bg-gray-50 p-2 rounded">{{ $document->description }}</p>
                        @endif
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center mb-4">
                        <a href="{{ route('user.documents.download', $document) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download Original
                        </a>

                        @if($document->response_file_path)
                            <a href="{{ route('user.documents.download-response', $document) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium flex items-center">
                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                  </svg>
                                Deine Antwort
                            </a>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('user.documents.update', $document) }}" enctype="multipart/form-data" class="border-t pt-4">
                        @csrf
                        @method('PATCH')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Neue Version hochladen</label>
                                <input type="file" name="response_file" class="block w-full text-sm text-slate-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-indigo-50 file:text-indigo-700
                                  hover:file:bg-indigo-100
                                "/>
                            </div>
                            
                            <div class="flex items-center justify-end">
                                <label class="inline-flex items-center mr-4">
                                    <input type="checkbox" name="is_completed" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ $document->is_completed ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Als erledigt markieren</span>
                                </label>
                                
                                <x-primary-button>
                                    {{ __('Speichern') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-sm italic">Du hast aktuell keine Dokumente.</p>
        @endforelse
    </div>
</section>
