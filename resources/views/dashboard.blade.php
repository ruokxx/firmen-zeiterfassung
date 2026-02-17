<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center py-2 gap-4 md:gap-0">
            <h2 class="font-semibold text-lg text-gray-100 leading-tight">
                {{ __('Jahresübersicht') }}
            </h2>
            
            <div class="flex items-center space-x-4 order-3 md:order-2">
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
                    <span class="text-xs font-medium text-gray-400">{{ number_format($progressPercentage, 1) }}%</span>
                </div>
                <div class="w-full bg-gray-700 rounded-full h-2.5">
                    <div class="bg-orange-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $progressPercentage }}%"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($months as $monthNum => $data)
                    <a href="{{ route('month.show', ['year' => $year, 'month' => $monthNum]) }}" 
                       class="block bg-gray-800 border border-gray-700 rounded-lg shadow-md hover:shadow-xl transition p-4 transform hover:scale-105 duration-200 group"> <!-- Expanded Industrial Card -->
                        
                        <div class="flex justify-between items-center mb-3 pb-2 border-b border-gray-700">
                            <h3 class="font-bold text-lg text-gray-100 group-hover:text-orange-400 transition">{{ $data['date']->locale('de')->isoFormat('MMMM') }}</h3>
                            <div class="text-xs font-bold px-2 py-1 rounded-full {{ $data['total_hours'] > 0 ? 'bg-orange-600 text-white shadow-sm' : 'bg-gray-700 text-gray-400' }}">
                                {{ number_format($data['total_hours'], 1) }} / {{ number_format($data['target_hours'], 0) }} h
                            </div>
                        </div>

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
                                @endphp
                                
                                <div class="aspect-square flex items-center justify-center rounded-sm text-sm
                                    {{ $hasEntry ? 'bg-orange-600 text-white font-bold shadow-sm' : ($isWeekend ? 'text-red-400 font-bold bg-gray-900/50' : 'text-gray-300 bg-gray-900 hover:bg-gray-700 transition') }}
                                ">
                                    {{ $d }}
                                </div>
                            @endfor
                        </div>
                    </a>
                @endforeach
            </div>

            </div>



        </div>
    </div>
</x-app-layout>
