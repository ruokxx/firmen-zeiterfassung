<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-700">
                
                @if(isset($pendingUsers) && $pendingUsers->count() > 0)
                    <div class="mb-8 border-b border-gray-700 pb-6">
                        <h3 class="text-lg font-bold mb-4 text-orange-500">Ausstehende Freischaltungen</h3>
                        <div class="space-y-4">
                            @foreach($pendingUsers as $pUser)
                                <div class="border border-orange-900/50 bg-orange-900/20 rounded-lg p-4 flex justify-between items-center">
                                    <div class="text-gray-200">
                                        <span class="font-bold block text-white">{{ $pUser->first_name }} {{ $pUser->last_name }}</span>
                                        <span class="text-sm text-gray-400 block">{{ $pUser->email }}</span>
                                        <span class="text-xs text-gray-500">Registriert am: {{ $pUser->created_at->format('d.m.Y H:i') }}</span>
                                    </div>
                                    <div class="flex space-x-2">
                                        <form method="POST" action="{{ route('admin.users.approve', $pUser) }}">
                                            @csrf
                                            <button type="submit" class="bg-green-600 hover:bg-green-500 text-white font-bold py-2 px-4 rounded text-sm transition">
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

                <div class="mb-6 flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('admin.materials.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 active:bg-gray-800 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 w-full sm:w-auto justify-center text-center">
                        Material-Katalog verwalten
                    </a>
                    <a href="{{ route('admin.construction-sites.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 active:bg-gray-800 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 w-full sm:w-auto justify-center text-center">
                        Baustellen Suche
                    </a>
                    <a href="{{ route('admin.faqs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 active:bg-gray-800 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 w-full sm:w-auto justify-center text-center">
                        FAQs verwalten
                    </a>
                </div>

                <!-- Vacation Settings (Moved from Settings) -->
                <div class="mb-8 border-b border-gray-700 pb-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-100">Urlaubseinstellungen</h3>
                    <form method="POST" action="{{ route('admin.settings.update-vacation') }}" class="bg-gray-900 border border-gray-700 rounded-lg p-4">
                        @csrf
                        <div class="flex flex-wrap items-end gap-4">
                            <div class="flex-grow w-full sm:w-auto max-w-xs">
                                <x-input-label for="vacation_days_per_year" :value="__('Urlaubstage pro Jahr (Global)')" />
                                <x-text-input id="vacation_days_per_year" class="block mt-1 w-full" type="number" name="vacation_days_per_year" :value="old('vacation_days_per_year', \App\Models\Setting::where('key', 'vacation_days_per_year')->value('value') ?: 30)" min="0" />
                            </div>
                            <button type="submit" class="bg-orange-600 hover:bg-orange-500 text-white font-bold py-2 px-4 rounded text-sm transition h-10 w-full sm:w-auto mb-0.5 mt-2 sm:mt-0">
                                Speichern
                            </button>
                        </div>
                        <p class="text-sm text-gray-400 mt-2">Anzahl der Urlaubstage, die jedem Mitarbeiter pro Jahr zustehen. Diese Einstellung gilt für alle Mitarbeiter, es sei denn, beim Mitarbeiter selbst wurden abweichend eigene Urlaubstage festgelegt.</p>
                    </form>
                </div>

                <!-- Material Settings -->
                <div class="mb-8 border-b border-gray-700 pb-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-100">Lagereinstellungen & Benachrichtigungen</h3>
                    <form method="POST" action="{{ route('materials.settings.update') }}" class="bg-gray-900 border border-gray-700 rounded-lg p-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="low_stock_email_address" :value="__('E-Mail für Warnungen und Tagesbericht')" />
                                <x-text-input id="low_stock_email_address" class="block mt-1 w-full" type="email" name="low_stock_email_address" :value="old('low_stock_email_address', \App\Models\Setting::where('key', 'low_stock_email_address')->value('value') ?? '')" placeholder="z.B. chef@firma.de" />
                                <p class="text-sm text-gray-400 mt-1">Lass dieses Feld leer, wenn keine E-Mails verschickt werden sollen.</p>

                                <div class="mt-6">
                                    <label for="material_reminder_time" class="block text-sm font-medium text-gray-300">Uhrzeit für automatische Erinnerung (Mitarbeiter/Gesellen)</label>
                                    <input type="time" name="material_reminder_time" id="material_reminder_time" value="{{ old('material_reminder_time', \App\Models\Setting::where('key', 'material_reminder_time')->value('value') ?? '17:00') }}" class="mt-1 block w-32 bg-gray-800 border border-gray-600 text-gray-200 rounded-md shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-50 text-sm">
                                    <p class="text-xs text-gray-500 mt-1">Sendet täglich an alle, die kein Material entnommen haben. Im Profil von jedem deaktivierbar.</p>
                                </div>
                            </div>

                            <div class="pt-2 md:pt-0">
                                <div class="flex items-center gap-2 mb-2 md:mt-6">
                                    <input type="checkbox" name="material_daily_report_enabled" id="material_daily_report_enabled" value="1" {{ \App\Models\Setting::where('key', 'material_daily_report_enabled')->value('value') === '1' ? 'checked' : '' }} class="rounded border-gray-600 bg-gray-800 text-orange-500 focus:ring-orange-500">
                                    <label for="material_daily_report_enabled" class="text-sm font-medium text-gray-300">Täglichen Bericht an Chef senden</label>
                                </div>
                                <div class="ml-6">
                                    <label for="material_daily_report_time" class="block text-xs font-medium text-gray-400">Uhrzeit für den Versand</label>
                                    <input type="time" name="material_daily_report_time" id="material_daily_report_time" value="{{ old('material_daily_report_time', \App\Models\Setting::where('key', 'material_daily_report_time')->value('value') ?? '18:00') }}" class="mt-1 block w-32 bg-gray-800 border border-gray-600 text-gray-200 rounded-md shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-50 text-sm">
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="bg-orange-600 hover:bg-orange-500 text-white font-bold py-2 px-4 rounded text-sm transition h-10 w-full sm:w-auto">
                                Speichern
                            </button>
                        </div>
                    </form>
                </div>

                <h3 class="text-lg font-bold mb-4 text-gray-100">Mitarbeiter Übersicht</h3>
                
                <div class="space-y-6">
                    @foreach($users as $user)
                        <div x-data="{ open: false }" class="border border-gray-700 rounded-lg p-4 bg-gray-900">
                            <div class="flex justify-between items-start cursor-pointer group" @click="open = !open">
                                <div>
                                    <span class="font-bold text-lg block text-gray-200 group-hover:text-white transition">{{ $user->name }}</span>
                                    <span class="text-sm text-gray-400 block">{{ $user->email }}</span>
                                    @if($user->mobile_number)
                                        <span class="text-sm text-gray-400 block">📞 {{ $user->mobile_number }}</span>
                                    @endif
                                    @if($user->address)
                                        <span class="text-sm text-gray-500 block mt-1 whitespace-pre-line">{{ $user->address }}</span>
                                    @endif

                                    <div class="mt-2 flex flex-wrap gap-2">
                                         <a href="{{ route('admin.documents.create', ['user_id' => $user->id]) }}" class="inline-flex items-center px-2 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Dokument senden
                                        </a>
                                        <a href="{{ route('admin.users.email', $user) }}" class="inline-flex items-center px-2 py-1 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-500 active:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Email senden
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-2 py-1 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Bearbeiten
                                        </a>
                                    </div>
                                    
                                        <div class="mt-4 pt-4 border-t border-gray-700 grid grid-cols-1 sm:grid-cols-2 lg:flex lg:flex-wrap items-center gap-4" @click.stop>
                                            {{-- Role Update --}}
                                            <form method="POST" action="{{ route('admin.users.update-role', $user) }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                                                @csrf
                                                <select name="role" class="bg-gray-800 text-gray-200 border-gray-600 rounded text-xs focus:ring-orange-500 focus:border-orange-500 py-1.5 w-full sm:w-auto">
                                                    <option value="employee" {{ $user->role === 'employee' ? 'selected' : '' }}>Mitarbeiter</option>
                                                    <option value="geselle" {{ $user->role === 'geselle' ? 'selected' : '' }}>Geselle</option>
                                                    <option value="azubi" {{ $user->role === 'azubi' ? 'selected' : '' }}>Azubi</option>
                                                </select>
                                                
                                                <div class="flex items-center ml-2 border border-gray-600 rounded px-2 py-1.5 bg-gray-800 w-full sm:w-auto">
                                                    <input type="checkbox" name="is_materialwart" id="is_materialwart_{{ $user->id }}" value="1" {{ $user->is_materialwart ? 'checked' : '' }} class="rounded border-gray-600 bg-gray-900 text-orange-500 focus:ring-orange-500">
                                                    <label for="is_materialwart_{{ $user->id }}" class="ml-2 text-xs text-gray-300 whitespace-nowrap">Materialwart</label>
                                                </div>

                                                @if(auth()->user()->is_super_admin || $user->id === auth()->id())
                                                <div class="flex items-center ml-2 border border-gray-600 rounded px-2 py-1.5 bg-gray-800 w-full sm:w-auto">
                                                    <input type="checkbox" name="is_admin" id="is_admin_{{ $user->id }}" value="1" {{ $user->is_admin ? 'checked' : '' }} {{ !auth()->user()->is_super_admin ? 'disabled' : '' }} class="rounded border-gray-600 bg-gray-900 text-orange-500 focus:ring-orange-500">
                                                    @if(!auth()->user()->is_super_admin)
                                                        <input type="hidden" name="is_admin" value="{{ $user->is_admin ? '1' : '0' }}">
                                                    @endif
                                                    <label for="is_admin_{{ $user->id }}" class="ml-2 text-xs text-gray-300 whitespace-nowrap">Admin</label>
                                                </div>
                                                @endif

                                                @if(auth()->user()->is_super_admin)
                                                <div class="flex items-center ml-2 border border-gray-600 rounded px-2 py-1.5 bg-gray-800 w-full sm:w-auto">
                                                    <input type="checkbox" name="is_super_admin" id="is_super_admin_{{ $user->id }}" value="1" {{ $user->is_super_admin ? 'checked' : '' }} class="rounded border-gray-600 bg-gray-900 text-orange-500 focus:ring-orange-500">
                                                    <label for="is_super_admin_{{ $user->id }}" class="ml-2 text-xs text-gray-300 whitespace-nowrap">Super-Admin</label>
                                                </div>
                                                @endif

                                                <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded bg-red-600 text-white hover:bg-red-500 transition w-full sm:w-auto">
                                                    Speichern
                                                </button>
                                            </form>

                                            @if($user->id !== auth()->id())
                                                {{-- Vacation Days Update --}}
                                                <form method="POST" action="{{ route('admin.users.update-vacation-days', $user) }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                                                    @csrf
                                                    <input type="number" name="vacation_days_per_year" value="{{ $user->vacation_days_per_year }}" placeholder="Global ({{ \App\Models\Setting::where('key', 'vacation_days_per_year')->value('value') ?: 30 }})" class="bg-gray-800 text-gray-200 border-gray-600 rounded text-xs focus:ring-orange-500 focus:border-orange-500 w-full sm:w-28 py-1.5" min="0">
                                                    <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded bg-red-600 text-white hover:bg-red-500 transition whitespace-nowrap w-full sm:w-auto text-center">
                                                        Urlaub speichern
                                                    </button>
                                                </form>

                                                {{-- Delete User --}}
                                                <form method="POST" action="{{ route('admin.users.delete', $user) }}" onsubmit="return confirm('Möchten Sie diesen Benutzer wirklich löschen? Alle zugehörigen Daten (Arbeitszeiten, Berichte) werden unwiderruflich gelöscht.');" class="col-span-1 sm:col-span-2 lg:col-span-1 border-t border-gray-700 pt-2 lg:border-t-0 lg:pt-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs font-semibold px-3 py-2 rounded border border-red-800 bg-red-900/30 text-red-500 hover:bg-red-900/50 transition w-full text-center">
                                                        Benutzer löschen
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                </div>
                                <div class="flex items-center text-gray-500">
                                    <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                    <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                </div>
                            </div>
                            
                            <div x-show="open" class="mt-4 border-t border-gray-700 pt-4">
                                @if($user->months && $user->months->count() > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($user->months as $month => $days)
                                            @php
                                                $dateObj = \Carbon\Carbon::createFromFormat('Y-m', $month);
                                                $totalHours = 0;
                                                $vacationDays = 0;
                                                foreach($days as $day) {
                                                    $startTime = \Carbon\Carbon::parse($day->start_time);
                                                    $endTime = \Carbon\Carbon::parse($day->end_time);
                                                    $duration = $endTime->diffInMinutes($startTime) - $day->break_duration;
                                                    $totalHours += ($duration / 60);

                                                    // Calculate Vacation Days for this month
                                                    // Check entries for 'Urlaub'
                                                    foreach($day->timeEntries as $entry) {
                                                        if($entry->constructionSite && $entry->constructionSite->name === 'Urlaub') {
                                                            $vacationDays += ($entry->hours / 8); 
                                                        }
                                                    }
                                                }
                                            @endphp
                                            <div class="bg-gray-800 p-3 rounded shadow-sm border border-gray-700">
                                                <div class="font-semibold text-center mb-2 text-gray-300">{{ $dateObj->locale('de')->isoFormat('MMMM YYYY') }}</div>
                                                <div class="text-center text-2xl font-bold text-orange-500">{{ number_format($totalHours, 1) }} h</div>
                                                @if($vacationDays > 0)
                                                    <div class="text-center text-sm font-bold mt-1" style="color: #22c55e;">{{ number_format($vacationDays, 1) }} Urlaubstage</div>
                                                @endif
                                                <div class="text-center mt-2">
                                                    <a href="{{ route('report.download', ['year' => $dateObj->year, 'month' => $dateObj->month, 'user_id' => $user->id, 'include_carryover' => 0]) }}" 
                                                       class="text-xs bg-gray-700 text-white px-2 py-1 rounded hover:bg-gray-600 border border-gray-600">
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
