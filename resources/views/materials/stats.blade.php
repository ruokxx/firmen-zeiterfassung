<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 sm:gap-0">
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                {{ __('Material Statistiken') }}
            </h2>
            <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                <a href="{{ route('materials.index') }}" class="bg-gray-700 hover:bg-gray-600 text-gray-200 font-bold py-2 px-4 rounded transition border border-gray-600 text-sm flex-1 sm:flex-none text-center">
                    Zum Lager
                </a>
                <a href="{{ route('materials.manage') }}" class="bg-gray-700 hover:bg-gray-600 text-gray-200 font-bold py-2 px-4 rounded transition border border-gray-600 text-sm flex-1 sm:flex-none text-center">
                    Materialverwaltung
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Month Selection & Admin Controls -->
            <div class="bg-gray-800 border border-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center justify-between w-full sm:w-auto">
                        <h3 class="text-lg font-bold text-orange-500 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Zeitraum
                        </h3>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 w-full sm:w-auto">
                        <form action="{{ route('materials.stats') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                            <input type="month" name="month" value="{{ $month }}" class="bg-gray-900 border border-gray-600 text-gray-200 rounded-md shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-50">
                            <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded shadow transition text-center">
                                Anzeigen
                            </button>
                        </form>

                        @if(auth()->user()->is_super_admin)
                        <form action="{{ route('materials.stats.clear') }}" method="POST" class="w-full sm:w-auto mt-2 sm:mt-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-gray-700 sm:border-l sm:pl-4">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Möchten Sie wirklich ALLE Buchungen und Statistiken zurücksetzen? Diese Aktion ist endgültig.')" class="w-full sm:w-auto bg-red-900/40 hover:bg-red-900 border border-red-800 text-red-200 font-bold py-2 px-4 rounded shadow transition text-sm flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Historie löschen
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Most Taken Materials -->
                <div class="bg-gray-800 border border-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-100">
                        <h3 class="text-lg font-bold text-orange-500 mb-4 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Am meisten entnommen ({{ \Carbon\Carbon::parse($month)->locale('de')->translatedFormat('F Y') }})
                        </h3>
                        @if($stats->count() > 0)
                            <ul class="divide-y divide-gray-700">
                                @foreach($stats as $stat)
                                    <li class="py-3 flex justify-between items-center">
                                        <span class="font-medium text-gray-200">{{ $stat->material ? $stat->material->name : 'Gelöschtes Material' }}</span>
                                        <span class="bg-orange-600 text-white px-3 py-1 rounded-full text-sm font-bold shadow-sm">
                                            {{ $stat->total_taken }} {{ $stat->material ? $stat->material->unit : 'Stück' }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-400 italic">In diesem Monat wurden keine Materialien entnommen.</p>
                        @endif
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="bg-gray-800 border border-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-100">
                        <h3 class="text-lg font-bold text-orange-500 mb-4 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Letzte 50 Buchungen
                        </h3>
                        <div class="max-h-96 overflow-y-auto pr-2">
                            @if($recentTransactions->count() > 0)
                                <ul class="space-y-3">
                                    @foreach($recentTransactions as $transaction)
                                        <li class="bg-gray-900 border border-gray-700 p-3 rounded-md flex justify-between items-center">
                                            <div>
                                                <div class="text-sm font-semibold text-gray-200">
                                                    {{ $transaction->material ? $transaction->material->name : 'Unbekanntes Material' }}
                                                </div>
                                                <div class="text-xs text-gray-400 mt-1">
                                                    von {{ $transaction->user ? $transaction->user->name : 'System/Gelöscht' }}
                                                    am {{ $transaction->created_at->format('d.m.Y H:i') }}
                                                </div>
                                            </div>
                                            <div class="font-bold text-sm {{ $transaction->type === 'added' ? 'text-green-500' : 'text-red-500' }}">
                                                {{ $transaction->type === 'added' ? '+' : '-' }}{{ $transaction->quantity }} {{ $transaction->material ? $transaction->material->unit : 'Stück' }}
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-gray-400 italic">Noch keine Buchungen vorhanden.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
