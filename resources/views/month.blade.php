<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center py-2">
            <h2 class="font-semibold text-xl text-gray-100 leading-tight flex items-center gap-4">
                {{ $startOfMonth->locale('de')->isoFormat('MMMM YYYY') }}
                <span class="text-base font-normal text-gray-400">
                    Gesamt: <span class="text-orange-500 font-bold">{{ number_format($totalHours, 1, ',', '.') }} h</span>
                    <span class="text-gray-500 mx-1">/</span>
                    <span class="text-gray-400">{{ number_format($targetHours, 0, ',', '.') }} h (Soll)</span>
                </span>
            </h2>
            <div class="flex items-center space-x-4">
                <a href="{{ route('month.show', ['year' => $startOfMonth->copy()->subMonth()->year, 'month' => $startOfMonth->copy()->subMonth()->month]) }}" class="px-3 py-1 bg-gray-700 text-gray-300 rounded hover:bg-gray-600 transition">&larr;</a>
                <a href="{{ route('month.show', ['year' => $startOfMonth->copy()->addMonth()->year, 'month' => $startOfMonth->copy()->addMonth()->month]) }}" class="px-3 py-1 bg-gray-700 text-gray-300 rounded hover:bg-gray-600 transition">&rarr;</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center px-2 gap-4" x-data="{ includeCarryover: false }">
                <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-orange-400 transition flex items-center gap-1">
                    <span>&larr;</span> Zurück zur Übersicht
                </a>
                
                <div class="flex items-center gap-4 bg-gray-800 p-2 rounded-lg border border-gray-700">
                    {{-- Holiday Import --}}
                    <form action="{{ route('month.import-holidays') }}" method="POST" class="flex items-center" onsubmit="return confirm('Möchten Sie wirklich Feiertage (NI) für {{ $startOfMonth->year }} importieren? Bereits existierende Einträge werden nicht überschrieben.');">
                        @csrf
                        <input type="hidden" name="year" value="{{ $startOfMonth->year }}">
                        <input type="hidden" name="month" value="{{ $startOfMonth->month }}">
                        <button type="submit" class="text-xs bg-indigo-700 text-indigo-100 px-2 py-1.5 rounded hover:bg-indigo-600 transition border border-indigo-600 mr-4" title="Feiertage für Niedersachsen importieren">
                            Feiertage (NI)
                        </button>
                    </form>

                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="includeCarryover" class="rounded border-gray-600 bg-gray-700 text-orange-500 shadow-sm focus:ring-orange-500 focus:ring-offset-gray-800">
                        <span class="ml-2 text-sm text-gray-300">Übertrag Vormonat</span>
                    </label>
                    
                    <a :href="'{{ route('report.download', ['year' => $startOfMonth->year, 'month' => $startOfMonth->month]) }}' + '&include_carryover=' + (includeCarryover ? '1' : '0')" 
                       class="bg-gray-700 text-gray-200 px-3 py-1.5 rounded text-sm hover:bg-gray-600 transition shadow-sm border border-gray-600 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        PDF Export
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-900/50 border border-green-700 text-green-200 px-4 py-3 rounded relative mb-6 mx-2" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Legend --}}
            <div class="mb-4 px-2 flex flex-wrap gap-4 text-sm text-gray-400">
                <div class="flex items-center gap-2">
                    <span class="bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">K</span>
                    <span>= Krank</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded">U</span>
                    <span>= Urlaub</span>
                </div>
                @php
                    $defaultStart = \App\Models\Setting::where('key', 'default_start_time')->value('value') ?: '08:00';
                    $defaultEnd = \App\Models\Setting::where('key', 'default_end_time')->value('value') ?: '16:00';
                    $defaultBreak = \App\Models\Setting::where('key', 'default_break_duration')->value('value') !== null 
                        ? (int)\App\Models\Setting::where('key', 'default_break_duration')->value('value') 
                        : 0;
            
                    $start = \Carbon\Carbon::parse($defaultStart);
                    $end = \Carbon\Carbon::parse($defaultEnd);
                    $diffMinutes = $start->diffInMinutes($end);
                    $workMinutesFull = $diffMinutes; // No physical break is deducted for Krank/Urlaub/Schule etc.
                    $defaultDailyHoursNoBreak = round($workMinutesFull / 60, 2);
                @endphp
                <div class="flex items-center gap-2">
                    <span class="bg-gray-600 text-white text-xs font-bold px-2 py-1 rounded">{{ rtrim(rtrim((string)$defaultDailyHoursNoBreak, '0'), '.') }}</span>
                    <span>= Folgt nächsten Monat ({{ rtrim(rtrim((string)$defaultDailyHoursNoBreak, '0'), '.') }} Std)</span>
                </div>
                @if(auth()->user()->role === 'azubi')
                <div class="flex items-center gap-2">
                    <span class="bg-green-600 text-white text-xs font-bold px-2 py-1 rounded">S</span>
                    <span>= Schule</span>
                </div>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 px-2">
                @foreach ($calendarDays as $currentDate)
                    @php
                        // $currentDate is already a Carbon instance from the controller
                        $dateString = $currentDate->format('Y-m-d');
                        $hasEntry = isset($workDays[$dateString]);
                        $workDay = $hasEntry ? $workDays[$dateString] : null;
                        $isWeekend = $currentDate->isWeekend();
                        $isToday = $currentDate->isToday();
                    @endphp

                    <div class="relative flex flex-col justify-between rounded-lg border p-4 shadow-sm transition hover:shadow-md group cursor-pointer
                        {{ $isToday ? 'bg-gray-800 border-orange-500 ring-1 ring-orange-500' : 'bg-gray-800 border-gray-700' }}
                        {{ $isWeekend ? 'opacity-70' : '' }}
                    ">
                        {{-- Header: Date & Status --}}
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <div class="text-lg font-bold {{ $isWeekend ? 'text-red-400' : 'text-gray-100' }}">
                                    {{ $currentDate->format('d.m.Y') }}
                                </div>
                                <div class="text-xs text-gray-500 uppercase tracking-wider">
                                    {{ $currentDate->locale('de')->isoFormat('dddd') }}
                                </div>
                            </div>
                            @if($hasEntry)
                                <div class="bg-orange-600 text-white text-xs font-bold px-2 py-1 rounded shadow-sm">
                                    {{ number_format($workDay->total_hours, 1) }} h
                                </div>
                            @else
                                <div class="flex gap-1 relative z-10">
                                    <form method="POST" action="{{ route('workday.set-status', $currentDate->format('Y-m-d')) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="Krank">
                                        <button type="submit" class="bg-red-600 text-white text-xs font-bold px-2 py-1 rounded hover:bg-red-500" title="Krank">K</button>
                                    </form>
                                    <form method="POST" action="{{ route('workday.set-status', $currentDate->format('Y-m-d')) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="Urlaub">
                                        <button type="submit" class="bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded hover:bg-blue-500" title="Urlaub">U</button>
                                    </form>
                                    <form method="POST" action="{{ route('workday.set-status', $currentDate->format('Y-m-d')) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="Folgt nächsten Monat">
                                        <button type="submit" class="bg-gray-600 text-white text-xs font-bold px-2 py-1 rounded hover:bg-gray-500" title="Folgt nächsten Monat">{{ rtrim(rtrim((string)$defaultDailyHoursNoBreak, '0'), '.') }}</button>
                                    </form>
                                    @if(auth()->user()->role === 'azubi')
                                    <form method="POST" action="{{ route('workday.set-status', $currentDate->format('Y-m-d')) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="Schule">
                                        <button type="submit" class="bg-green-600 text-white text-xs font-bold px-2 py-1 rounded hover:bg-green-500" title="Schule">S</button>
                                    </form>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Body: Entries --}}
                        <div class="flex-grow space-y-1 mb-3">
                            @if($hasEntry)
                                @foreach($workDay->timeEntries as $entry)
                                    <div class="text-xs text-gray-300 bg-gray-900/50 rounded px-2 py-1 truncate border border-gray-700">
                                        {{ $entry->constructionSite->name }}
                                        <span class="text-orange-400 ml-1">({{ $entry->hours }}h)</span>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-sm text-gray-600 italic py-2">Keine Einträge</div>
                            @endif
                        </div>

                        {{-- Footer: Action --}}
                            <div class="flex gap-1">
                                <a href="{{ route('workday.edit', $dateString) }}" 
                                   class="block flex-grow text-center py-2 rounded text-sm font-semibold transition
                                   {{ $hasEntry ? 'bg-gray-700 text-gray-100 hover:bg-gray-600' : 'bg-gray-900 text-orange-500 hover:text-orange-300 hover:bg-gray-950 border border-gray-700' }}
                                ">
                                    {{ $hasEntry ? 'Bearbeiten' : 'Erfassen' }}
                                </a>
                                @if($hasEntry)
                                    <form method="POST" action="{{ route('workday.reset', $dateString) }}" onsubmit="return confirm('Möchten Sie alle Einträge für diesen Tag wirklich löschen?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="h-full px-3 bg-red-900/50 text-red-400 border border-red-800 rounded hover:bg-red-800 hover:text-white transition flex items-center justify-center" title="Tag zurücksetzen">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 bg-gray-800 rounded-lg border border-gray-700 p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-100 mb-4 border-b border-gray-700 pb-2">Sonstiges für {{ $startOfMonth->locale('de')->isoFormat('MMMM YYYY') }}</h3>
                <form action="{{ route('month.remark') }}" method="POST">
                    @csrf
                    <input type="hidden" name="year" value="{{ $year }}">
                    <input type="hidden" name="month" value="{{ $month }}">
                    
                    <div class="mb-4">
                        <textarea name="remark" class="w-full bg-gray-900 border-gray-600 text-gray-200 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm min-h-[100px]" placeholder="Hier können Sie zusätzliche Anmerkungen für den Monat eintragen. Dieser Text erscheint auch auf dem PDF-Bericht.">{{ old('remark', $remark?->remark) }}</textarea>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="bg-orange-600 hover:bg-orange-500 text-white font-bold py-2 px-4 rounded transition text-sm">
                            Sonstiges speichern
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
