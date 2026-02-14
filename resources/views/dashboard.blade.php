<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center py-2">
            <h2 class="font-semibold text-lg text-gray-100 leading-tight">
                {{ __('Jahresübersicht') }}
            </h2>
            <div class="flex items-center space-x-4">
                <a href="{{ route('dashboard', ['year' => $year - 1]) }}" class="px-2 py-1 bg-gray-700 text-gray-200 rounded hover:bg-gray-600 transition">&larr;</a>
                <span class="text-md font-bold text-orange-500">{{ $year }}</span>
                <a href="{{ route('dashboard', ['year' => $year + 1]) }}" class="px-2 py-1 bg-gray-700 text-gray-200 rounded hover:bg-gray-600 transition">&rarr;</a>
            </div>
        </div>
    </x-slot>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"> <!-- Standard width for margins -->
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($months as $monthNum => $data)
                    <a href="{{ route('month.show', ['year' => $year, 'month' => $monthNum]) }}" 
                       class="block bg-gray-800 border border-gray-700 rounded-lg shadow-md hover:shadow-xl transition p-4 transform hover:scale-105 duration-200 group"> <!-- Expanded Industrial Card -->
                        
                        <div class="flex justify-between items-center mb-3 pb-2 border-b border-gray-700">
                            <h3 class="font-bold text-lg text-gray-100 group-hover:text-orange-400 transition">{{ $data['date']->locale('de')->isoFormat('MMMM') }}</h3>
                            <div class="text-xs font-bold px-2 py-1 rounded-full {{ $data['total_hours'] > 0 ? 'bg-orange-600 text-white shadow-sm' : 'bg-gray-700 text-gray-400' }}">
                                {{ number_format($data['total_hours'], 1) }} h
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

            <div class="mt-3 bg-gray-800 border border-gray-700 rounded-lg p-3 text-center shadow-sm max-w-sm mx-auto">
                <span class="text-gray-400 font-medium text-sm">Gesamt {{ $year }}:</span>
                <span class="text-xl font-bold text-orange-500 ml-2">{{ number_format($yearlyTotal, 1) }} h</span>
            </div>

        </div>
    </div>
</x-app-layout>
