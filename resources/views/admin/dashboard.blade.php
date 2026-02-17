<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if(isset($pendingUsers) && $pendingUsers->count() > 0)
                    <div class="mb-8 border-b pb-6">
                        <h3 class="text-lg font-bold mb-4 text-orange-600">Ausstehende Freischaltungen</h3>
                        <div class="space-y-4">
                            @foreach($pendingUsers as $pUser)
                                <div class="border border-orange-200 bg-orange-50 rounded-lg p-4 flex justify-between items-center">
                                    <div>
                                        <span class="font-bold block">{{ $pUser->first_name }} {{ $pUser->last_name }}</span>
                                        <span class="text-sm text-gray-600 block">{{ $pUser->email }}</span>
                                        <span class="text-xs text-gray-500">Registriert am: {{ $pUser->created_at->format('d.m.Y H:i') }}</span>
                                    </div>
                                    <div class="flex space-x-2">
                                        <form method="POST" action="{{ route('admin.users.approve', $pUser) }}">
                                            @csrf
                                            <button type="submit" class="bg-green-600 hover:bg-green-500 text-white font-bold py-2 px-4 rounded text-sm transition" style="background-color: #16a34a;">
                                                Freischalten
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.users.delete', $pUser) }}" onsubmit="return confirm('Benutzer wirklich ablehnen und löschen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 hover:bg-red-500 text-white font-bold py-2 px-4 rounded text-sm transition">
                                                Ablehnen
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mb-8 border-b pb-6">
                    <h3 class="text-lg font-bold mb-4">Datenbank Backup & Restore</h3>
                    <div class="flex flex-col md:flex-row gap-4 items-start">
                        <!-- Download -->
                        <div class="bg-gray-50 p-4 rounded border border-gray-200 w-full md:w-1/2">
                            <h4 class="font-semibold mb-2">Backup herunterladen</h4>
                            <p class="text-sm text-gray-600 mb-3">Laden Sie die aktuelle Datenbank-Datei herunter.</p>
                            <a href="{{ route('admin.backup.download') }}" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded text-sm inline-block transition">
                                Backup herunterladen
                            </a>
                        </div>
                        
                        <!-- Upload -->
                        <div class="bg-gray-50 p-4 rounded border border-gray-200 w-full md:w-1/2">
                            <h4 class="font-semibold mb-2">Backup wiederherstellen</h4>
                            <p class="text-sm text-gray-600 mb-3">Laden Sie eine Sicherungsdatei hoch, um die Datenbank wiederherzustellen. <strong class="text-red-600">Achtung: Dies überschreibt alle aktuellen Daten!</strong></p>
                            <form method="POST" action="{{ route('admin.backup.restore') }}" enctype="multipart/form-data" onsubmit="return confirm('SIND SIE SICHER? Dies wird die aktuelle Datenbank unwiderruflich überschreiben!');">
                                @csrf
                                <div class="flex gap-2 items-center">
                                    <input type="file" name="backup_file" required class="text-sm border border-gray-300 rounded p-1 w-full">
                                    <button type="submit" class="bg-red-600 hover:bg-red-500 text-white font-bold py-2 px-4 rounded text-sm transition whitespace-nowrap">
                                        Wiederherstellen
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <h3 class="text-lg font-bold mb-4">Mitarbeiter Übersicht</h3>
                
                <div class="space-y-6">
                    @foreach($users as $user)
                        <div x-data="{ open: false }" class="border rounded-lg p-4 bg-gray-50">
                            <div class="flex justify-between items-start cursor-pointer" @click="open = !open">
                                <div>
                                    <span class="font-bold text-lg block">{{ $user->name }}</span>
                                    <span class="text-sm text-gray-600 block">{{ $user->email }}</span>
                                    @if($user->mobile_number)
                                        <span class="text-sm text-gray-600 block">📞 {{ $user->mobile_number }}</span>
                                    @endif
                                    @if($user->address)
                                        <span class="text-sm text-gray-500 block mt-1 whitespace-pre-line">{{ $user->address }}</span>
                                    @endif

                                    <div class="mt-2">
                                         <a href="{{ route('admin.documents.create', ['user_id' => $user->id]) }}" class="inline-flex items-center px-2 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Dokument senden
                                        </a>
                                    </div>
                                    
                                    @if($user->id !== auth()->id())
                                        <div class="flex space-x-2 mt-2" @click.stop>
                                            <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                                                @csrf
                                                <button type="submit" class="text-xs font-semibold px-2 py-1 rounded border transition {{ $user->is_admin ? 'bg-red-50 text-red-600 border-red-200 hover:bg-red-100' : 'bg-green-50 text-green-600 border-green-200 hover:bg-green-100' }}">
                                                    {{ $user->is_admin ? 'Admin-Rechte entziehen' : 'Zum Admin befördern' }}
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.users.delete', $user) }}" onsubmit="return confirm('Möchten Sie diesen Benutzer wirklich löschen? Alle zugehörigen Daten (Arbeitszeiten, Berichte) werden unwiderruflich gelöscht.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-semibold px-2 py-1 rounded border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 transition">
                                                    Löschen
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center">
                                    <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                    <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                </div>
                            </div>
                            
                            <div x-show="open" class="mt-4 border-t pt-4">
                                @if($user->months && $user->months->count() > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($user->months as $month => $days)
                                            @php
                                                $dateObj = \Carbon\Carbon::createFromFormat('Y-m', $month);
                                                $totalHours = 0;
                                                foreach($days as $day) {
                                                    $startTime = \Carbon\Carbon::parse($day->start_time);
                                                    $endTime = \Carbon\Carbon::parse($day->end_time);
                                                    $duration = $endTime->diffInMinutes($startTime) - $day->break_duration;
                                                    $totalHours += ($duration / 60);
                                                }
                                            @endphp
                                            <div class="bg-white p-3 rounded shadow-sm border border-gray-200">
                                                <div class="font-semibold text-center mb-2">{{ $dateObj->locale('de')->isoFormat('MMMM YYYY') }}</div>
                                                <div class="text-center text-2xl font-bold text-blue-600">{{ number_format($totalHours, 1) }} h</div>
                                                <div class="text-center mt-2">
                                                    <a href="{{ route('report.download', ['year' => $dateObj->year, 'month' => $dateObj->month, 'user_id' => $user->id, 'include_carryover' => 0]) }}" 
                                                       class="text-xs bg-gray-800 text-white px-2 py-1 rounded hover:bg-gray-700">
                                                        PDF Download
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500 italic">Keine Arbeitszeiten erfasst.</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
            </div>
        </div>
    </div>
</x-app-layout>
