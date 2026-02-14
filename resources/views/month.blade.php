<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center py-2">
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                {{ $startOfMonth->locale('de')->isoFormat('MMMM YYYY') }}
            </h2>
            <div class="flex items-center space-x-4">
                <a href="{{ route('month.show', ['year' => $startOfMonth->copy()->subMonth()->year, 'month' => $startOfMonth->copy()->subMonth()->month]) }}" class="px-3 py-1 bg-gray-700 text-gray-300 rounded hover:bg-gray-600 transition">&larr;</a>
                <a href="{{ route('month.show', ['year' => $startOfMonth->copy()->addMonth()->year, 'month' => $startOfMonth->copy()->addMonth()->month]) }}" class="px-3 py-1 bg-gray-700 text-gray-300 rounded hover:bg-gray-600 transition">&rarr;</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-4 flex justify-between items-center px-2">
                <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-orange-400 transition flex items-center gap-1">
                    <span>&larr;</span> Zurück zur Übersicht
                </a>
                <a href="{{ route('report.download', ['year' => $startOfMonth->year, 'month' => $startOfMonth->month]) }}" class="bg-gray-700 text-gray-200 px-3 py-1.5 rounded text-sm hover:bg-gray-600 transition shadow-sm border border-gray-600">
                    PDF Export
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-900/50 border border-green-700 text-green-200 px-4 py-3 rounded relative mb-6 mx-2" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 px-2">
                @for ($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $currentDate = $startOfMonth->copy()->addDays($day - 1);
                        $dateString = $currentDate->format('Y-m-d');
                        $hasEntry = isset($workDays[$dateString]);
                        $workDay = $hasEntry ? $workDays[$dateString] : null;
                        $isWeekend = $currentDate->isWeekend();
                        $isToday = $currentDate->isToday();
                    @endphp

                    <div class="relative flex flex-col justify-between rounded-lg border p-4 shadow-sm transition hover:shadow-md
                        {{ $isToday ? 'bg-gray-800 border-orange-500 ring-1 ring-orange-500' : 'bg-gray-800 border-gray-700' }}
                        {{ $isWeekend ? 'opacity-70' : '' }}
                    ">
                        {{-- Header: Date & Status --}}
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <div class="text-lg font-bold {{ $isWeekend ? 'text-red-400' : 'text-gray-100' }}">
                                    {{ $currentDate->format('d.') }} {{ $currentDate->locale('de')->isoFormat('dd') }}
                                </div>
                                <div class="text-xs text-gray-500 uppercase tracking-wider">
                                    {{ $currentDate->locale('de')->isoFormat('dddd') }}
                                </div>
                            </div>
                            @if($hasEntry)
                                <div class="bg-orange-600 text-white text-xs font-bold px-2 py-1 rounded shadow-sm">
                                    {{ number_format($workDay->total_hours, 1) }} h
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
                        <a href="{{ route('workday.edit', $dateString) }}" 
                           class="block w-full text-center py-2 rounded text-sm font-semibold transition
                           {{ $hasEntry ? 'bg-gray-700 text-gray-100 hover:bg-gray-600' : 'bg-gray-900 text-orange-500 hover:text-orange-300 hover:bg-gray-950 border border-gray-700' }}
                        ">
                            {{ $hasEntry ? 'Bearbeiten' : 'Erfassen' }}
                        </a>
                    </div>
                @endfor
            </div>

        </div>
    </div>
</x-app-layout>
