<x-app-layout>
    @push('styles')
    <style>
        @keyframes germany-blink {
            0%, 32% { background-color: #000000; color: white; border-color: #000000; }
            33%, 65% { background-color: #DD0000; color: white; border-color: #DD0000; }
            66%, 100% { background-color: #FFCE00; color: black; border-color: #FFCE00; }
        }
        .animate-germany-blink {
            animation: germany-blink 2s infinite step-end;
        }
    </style>
    @endpush
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center py-2 gap-4 md:gap-0">
            <h2 class="font-semibold text-lg text-gray-100 leading-tight">
                {{ __('Jahresübersicht') }}
            </h2>

            <a href="{{ route('workday.edit', \Carbon\Carbon::today()->format('Y-m-d')) }}" 
               class="order-1 md:order-2 px-4 py-2 bg-gray-800 border border-gray-600 rounded-md text-orange-500 font-bold hover:bg-gray-700 hover:text-orange-400 transition shadow-sm">
                {{ \Carbon\Carbon::today()->locale('de')->isoFormat('dddd, DD.MM.YYYY') }}
            </a>
            
            <div class="flex items-center space-x-4 order-3 md:order-3">
                <span class="text-gray-400 text-sm">Gesamt:</span>
                <span class="text-xl font-bold text-orange-500">{{ number_format($yearlyTotal, 1) }} h</span>
            </div>

            <div class="flex items-center space-x-4 order-2 md:order-3">
                <a href="{{ route('dashboard', ['year' => $year - 1]) }}" class="px-2 py-1 bg-gray-700 text-gray-200 rounded hover:bg-gray-600 transition">&larr;</a>
                <span class="text-md font-bold text-orange-500">{{ $year }}</span>
                <a href="{{ route('dashboard', ['year' => $year + 1]) }}" class="px-2 py-1 bg-gray-700 text-gray-200 rounded hover:bg-gray-600 transition">&rarr;</a>
            </div>
        </div>
    </x-slot>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"> <!-- Standard width for margins -->

            
            @if(auth()->user()->google_calendar_url)
                <div x-data="{ open: false }" class="mb-6 bg-gray-800 border border-gray-700 rounded-lg shadow-lg max-w-xl mx-auto overflow-hidden">
                    <button @click="open = !open" class="w-full flex justify-between items-center p-4 bg-gray-750 hover:bg-gray-700 transition focus:outline-none">
                        <span class="text-lg font-bold text-gray-100 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Mein Kalender
                        </span>
                        <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="border-t border-gray-700">
                        <div class="aspect-w-16 aspect-h-9">
                            <iframe src="{{ auth()->user()->google_calendar_url }}" style="border: 0" width="100%" height="400" frameborder="0" scrolling="no"></iframe>
                        </div>
                        <div class="p-2 text-center bg-gray-750 border-t border-gray-700">
                            <a href="{{ auth()->user()->google_calendar_url }}" target="_blank" class="text-xs text-blue-400 hover:text-blue-300 underline">
                                Kalender in neuem Fenster öffnen (falls Anzeige nicht funktionert)
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            @if(auth()->user()->trello_url)
                <div x-data="{ open: false }" class="mb-6 bg-gray-800 border border-gray-700 rounded-lg shadow-lg max-w-xl mx-auto overflow-hidden">
                    <button @click="open = !open" class="w-full flex justify-between items-center p-4 bg-gray-750 hover:bg-gray-700 transition focus:outline-none">
                        <span class="text-lg font-bold text-gray-100 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            Mein Trello
                        </span>
                        <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="border-t border-gray-700">
                        <div class="aspect-w-16 aspect-h-9">
                            <iframe src="{{ str_replace('trello.com/b/', 'trello.com/b/', auth()->user()->trello_url) . '.html' }}" style="border: 0" width="100%" height="400" frameborder="0" scrolling="no"></iframe>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Yearly Progress Bar --}}
            <div class="mb-6 bg-gray-800 border border-gray-700 rounded-lg shadow p-4">
                <div class="flex justify-between items-center mb-1">
                    <span class="text-sm font-medium text-gray-300">Arbeitstage: {{ $daysWorked }} / {{ $totalWorkingDays }}</span>
                    <span class="text-xs font-medium text-gray-400">{{ number_format($progressPercentage, 1, ',', '.') }}%</span>
                </div>
                <div class="w-full bg-gray-700 rounded-full h-2.5 overflow-hidden shadow-inner">
                    <div class="bg-blue-500 h-full rounded-full transition-all duration-500 shadow-[0_0_10px_#3b82f6]" style="width: {{ number_format($progressPercentage, 2, '.', '') }}%;"></div>
                </div>
            </div>

            <div class="mb-6 flex flex-wrap gap-4 justify-center sm:justify-start bg-gray-800 p-4 rounded-lg border border-gray-700 shadow-sm">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mr-2 self-center">Legende:</span>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-300">Arbeit</span>
                    <div class="w-3 h-3 rounded-sm bg-orange-600 shadow-sm flex-shrink-0" style="min-width: 0.75rem; min-height: 0.75rem;"></div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-300">Urlaub</span>
                    <div class="w-3 h-3 rounded-sm shadow-sm flex-shrink-0" style="background-color: #22c55e; min-width: 0.75rem; min-height: 0.75rem;"></div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-300">Krank</span>
                    <div class="w-3 h-3 rounded-sm shadow-sm flex-shrink-0" style="background-color: #ef4444; min-width: 0.75rem; min-height: 0.75rem;"></div>
                </div>
                @if(auth()->user()->role === 'azubi')
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-300">Schule</span>
                    <div class="w-3 h-3 rounded-sm shadow-sm flex-shrink-0" style="background-color: #3b82f6; min-width: 0.75rem; min-height: 0.75rem;"></div>
                </div>
                @endif
                <!-- Removed Schule / Sonstiges upon request -->
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-300">Feiertag</span>
                    <div class="w-3 h-3 rounded-sm shadow-sm animate-germany-blink flex-shrink-0" style="min-width: 0.75rem; min-height: 0.75rem;"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($months as $monthNum => $data)
                    <div class="block bg-gray-800 border border-gray-700 rounded-lg shadow-md hover:shadow-xl transition p-4 transform hover:scale-105 duration-200 group relative"> <!-- Expanded Industrial Card -->
                        
                        <a href="{{ route('month.show', ['year' => $year, 'month' => $monthNum]) }}" class="flex justify-between items-center mb-3 pb-2 border-b border-gray-700 hover:bg-gray-750 -mx-4 px-4 -mt-2 pt-2 rounded-t-lg transition">
                            <h3 class="font-bold text-lg text-gray-100 group-hover:text-orange-400 transition">{{ $data['date']->locale('de')->isoFormat('MMMM') }}</h3>
                            <div class="text-xs font-bold px-2 py-1 rounded-full {{ $data['total_hours'] > 0 ? 'bg-orange-600 text-white shadow-sm' : 'bg-gray-700 text-gray-400' }}">
                                {{ number_format($data['total_hours'], 1) }} / {{ number_format($data['target_hours'], 0) }} h
                            </div>
                        </a>

                        @if(isset($data['vacation_days']) && $data['vacation_days'] > 0)
                            <div class="text-xs font-bold text-center mb-2" style="color: #22c55e;">
                                {{ number_format($data['vacation_days'], 1) }} Urlaubstage
                            </div>
                        @endif

                        {{-- Micro Calendar Visual --}}
                        <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px;" class="text-center text-xs leading-tight">
                            {{-- Weekday Headers --}}
                            <div class="font-medium text-gray-500 mb-1">M</div>
                            <div class="font-medium text-gray-500 mb-1">D</div>
                            <div class="font-medium text-gray-500 mb-1">M</div>
                            <div class="font-medium text-gray-500 mb-1">D</div>
                            <div class="font-medium text-gray-500 mb-1">F</div>
                            <div class="font-medium text-red-500/80 mb-1">S</div>
                            <div class="font-medium text-red-500/80 mb-1">S</div>
                            
                            @php
                                $daysInMonth = $data['date']->daysInMonth;
                                $firstDayOfWeek = $data['date']->copy()->startOfMonth()->dayOfWeekIso;
                            @endphp

                            {{-- Empty slots before 1st of month --}}
                            @for($i = 1; $i < $firstDayOfWeek; $i++)
                                <div></div>
                            @endfor

                            {{-- Days --}}
                            @for($d = 1; $d <= $daysInMonth; $d++)
                                @php
                                    $currentDayOfWeek = ($d + $firstDayOfWeek - 2) % 7 + 1;
                                    $isWeekend = $currentDayOfWeek >= 6;
                                    $hasEntry = in_array($d, $data['worked_days']);
                                    $isVacation = in_array($d, $data['vacation_dates'] ?? []);
                                    $isSick = in_array($d, $data['sick_dates'] ?? []);
                                    $isSchool = in_array($d, $data['school_dates'] ?? []);
                                    $isFolgt = in_array($d, $data['folgt_dates'] ?? []);
                                    $isHoliday = in_array($d, $data['holiday_dates'] ?? []);
                                    $currentDateStr = $data['date']->copy()->day($d)->format('Y-m-d');
                                @endphp
                                
                                <a href="{{ route('workday.edit', $currentDateStr) }}" class="aspect-square flex items-center justify-center rounded-sm text-sm cursor-pointer
                                    {{ $isVacation ? 'text-white font-bold shadow-sm hover:opacity-80' : 
                                       ($isSick ? 'text-white font-bold shadow-sm hover:opacity-80' : 
                                       ($isSchool ? 'text-white font-bold shadow-sm hover:opacity-80' : 
                                       ($isFolgt ? 'text-white font-bold shadow-sm hover:opacity-80' :
                                       ($isHoliday ? 'text-white font-bold shadow-sm hover:opacity-80 animate-germany-blink' :
                                       ($hasEntry ? 'bg-orange-600 text-white font-bold shadow-sm hover:bg-orange-500' : 
                                       ($isWeekend ? 'text-red-400 font-bold bg-gray-900/50 hover:bg-gray-800' : 'text-gray-300 bg-gray-900 hover:bg-gray-700 transition')))))) }}"
                                   style="{{ $isVacation ? 'background-color: #22c55e;' : ($isSick ? 'background-color: #ef4444;' : ($isSchool ? 'background-color: #3b82f6;' : ($isFolgt ? 'background-color: #000000; border: 1px solid #374151;' : ''))) }}"
                                >
                                    {{ $d }}
                                </a>
                            @endfor
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Team List --}}
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700 p-6 mt-8">
                <h3 class="text-xl font-bold text-orange-500 mb-6 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Das Team
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($team as $member)
                        <div class="flex items-center p-4 bg-gray-750 rounded-lg border border-gray-700">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-600 flex items-center justify-center text-orange-500 font-bold text-lg">
                                {{ substr($member->name, 0, 1) }}
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-200">{{ $member->name }}</div>
                                <div class="text-xs text-gray-400">
                                    @if($member->role === 'admin')
                                        <span class="text-orange-500 font-bold">Admin</span>
                                    @elseif($member->role === 'chef')
                                        <span class="text-yellow-500 font-bold">Chef</span>
                                    @elseif($member->role === 'azubi')
                                        <span class="text-green-500 font-bold">Azubi</span>
                                    @elseif($member->role === 'geselle')
                                        <span class="text-green-500 font-bold">Geselle</span>
                                    @else
                                        <span class="text-yellow-500 font-bold">Mitarbeiter</span>
                                    @endif
                                    @if($member->is_materialwart)
                                        <span class="text-gray-400 font-bold ml-1 text-[10px] uppercase">(Materialwart)</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
