<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Arbeitszeit erfassen: ') }} {{ \Carbon\Carbon::parse($workDay->date)->locale('de')->isoFormat('dddd, D. MMMM YYYY') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="workDayForm({{ $workDay->timeEntries->load('constructionSite')->toJson() }})">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('workday.update', $workDay) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                        <div>
                            <x-input-label for="date" :value="__('Datum')" />
                            <x-text-input id="date" class="block mt-1 w-full bg-gray-100 text-gray-500 cursor-not-allowed" type="date" name="date" :value="$workDay->date" disabled readonly />
                        </div>
                        <div>
                            <x-input-label for="start_time" :value="__('Startzeit')" />
                            <x-text-input id="start_time" class="block mt-1 w-full" type="time" name="start_time" :value="old('start_time', $workDay->start_time)" required />
                        </div>
                        <div>
                            <x-input-label for="end_time" :value="__('Endzeit')" />
                            <x-text-input id="end_time" class="block mt-1 w-full" type="time" name="end_time" :value="old('end_time', $workDay->end_time)" required />
                        </div>
                        <div>
                            <x-input-label for="break_duration" :value="__('Pause (Minuten)')" />
                            <x-text-input id="break_duration" class="block mt-1 w-full" type="number" name="break_duration" :value="old('break_duration', $workDay->break_duration)" required />
                        </div>
                    </div>

                    <hr class="my-6">
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Baustellen & Zeiten</h3>
                    
                    <div class="space-y-4">
                        <template x-for="(entry, index) in entries" :key="index">
                            <div class="flex items-center gap-4 bg-gray-50 p-4 rounded border border-gray-200">
                                <div class="flex-grow">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Baustelle</label>
                                    <input type="text" list="sites-list" :name="'entries['+index+'][construction_site_name]'" x-model="entry.construction_site_name" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full" placeholder="Baustelle eingeben oder wählen..." required>
                                    <datalist id="sites-list">
                                        @foreach($sites as $site)
                                            <option value="{{ $site->name }}">
                                        @endforeach
                                    </datalist>
                                </div>
                                <div class="w-32">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Stunden</label>
                                    <select :name="'entries['+index+'][hours]'" x-model="entry.hours" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full">
                                        @for ($i = 0.5; $i <= 12; $i += 0.5)
                                            <option value="{{ number_format($i, 1) }}">{{ number_format($i, 1) }} h</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="pt-6 flex items-center gap-2">
                                    {{-- Requested Check/Done Button per row --}}
                                    <button type="button" class="text-green-500 hover:text-green-700" title="Fertig">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>

                                    <button type="button" @click="removeEntry(index)" class="text-red-500 hover:text-red-700" title="Löschen">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-4">
                        <button type="button" @click="addEntry()" class="flex items-center text-blue-600 hover:text-blue-800 font-semibold">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                              <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Baustelle hinzufügen
                        </button>
                    </div>

                    <div class="mt-8 flex justify-end gap-4 p-4 bg-gray-50 rounded-lg">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Abbrechen
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Fertig
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('workDayForm', (initialEntries) => ({
                entries: initialEntries.length > 0 ? initialEntries.map(e => ({
                    ...e,
                    construction_site_name: e.construction_site ? e.construction_site.name : ''
                })) : [{ construction_site_name: '', hours: '0.5' }],
                
                addEntry() {
                    this.entries.push({ construction_site_name: '', hours: '0.5' });
                },
                
                removeEntry(index) {
                    this.entries.splice(index, 1);
                }
            }));
        });
    </script>
</x-app-layout>
