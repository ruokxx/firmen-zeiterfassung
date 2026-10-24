<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                {{ __('News & Ankündigungen Popups') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-700 border border-gray-600 rounded-md font-semibold text-xs text-gray-200 uppercase tracking-widest hover:bg-gray-600 transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Zurück zum Chef-Bereich
                </a>
                <a href="{{ route('admin.news-popups.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-500 transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Neues Popup erstellen
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-700">
                
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-gray-100">Aktive & Vergangene Popups</h3>
                        <p class="text-sm text-gray-400 mt-1">Popups werden angemeldeten Benutzern nach dem Login eingeblendet und können zeitlich (z. B. auf 1, 2 oder 3 Tage) begrenzt werden.</p>
                    </div>
                </div>

                @if(session('success'))
                    <div class="bg-green-900 border border-green-700 text-green-300 px-4 py-3 rounded mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-750 border-b border-gray-700 text-xs uppercase tracking-wider text-gray-400">
                                <th class="p-3 font-semibold">Titel & Nachricht</th>
                                <th class="p-3 font-semibold">Typ</th>
                                <th class="p-3 font-semibold">Zeitliche Begrenzung</th>
                                <th class="p-3 font-semibold">Anzeige</th>
                                <th class="p-3 font-semibold text-center">Gesehen / Bestätigt</th>
                                <th class="p-3 font-semibold text-center">Status</th>
                                <th class="p-3 font-semibold text-right">Aktionen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @forelse($popups as $popup)
                                @php
                                    $isExpired = $popup->isExpired();
                                    $isActiveNow = $popup->is_active && !$isExpired;
                                @endphp
                                <tr class="hover:bg-gray-750/50 transition {{ !$isActiveNow ? 'opacity-70' : '' }}">
                                    <td class="p-3">
                                        <div class="font-bold text-white text-base">{{ $popup->title }}</div>
                                        <div class="text-xs text-gray-400 mt-1 line-clamp-2 max-w-md">
                                            {{ Str::limit($popup->content, 120) }}
                                        </div>
                                        <div class="text-[10px] text-gray-500 mt-1">
                                            Erstellt: {{ $popup->created_at->format('d.m.Y H:i') }}
                                        </div>
                                    </td>
                                    <td class="p-3 whitespace-nowrap">
                                        @if($popup->type === 'important')
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-orange-900/60 text-orange-300 border border-orange-700">
                                                ⚠️ Wichtig
                                            </span>
                                        @elseif($popup->type === 'warning')
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-900/60 text-red-300 border border-red-700">
                                                🚨 Dringend
                                            </span>
                                        @elseif($popup->type === 'success')
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-900/60 text-green-300 border border-green-700">
                                                ✅ Info / Erfolg
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-900/60 text-blue-300 border border-blue-700">
                                                ℹ️ Information
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3 whitespace-nowrap">
                                        @if($popup->expires_at)
                                            @if($isExpired)
                                                <span class="text-red-400 text-xs block font-semibold">Abgelaufen</span>
                                                <span class="text-gray-500 text-xs">{{ $popup->expires_at->format('d.m.Y H:i') }} Uhr</span>
                                            @else
                                                <span class="text-green-400 text-xs block font-semibold">
                                                    Noch {{ now()->diffForHumans($popup->expires_at, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}
                                                </span>
                                                <span class="text-gray-400 text-xs">Bis {{ $popup->expires_at->format('d.m.Y H:i') }} Uhr</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400 text-xs">Unbegrenzt (dauerhaft)</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-xs text-gray-300 whitespace-nowrap">
                                        @if($popup->display_type === 'once_per_day')
                                            <span title="Wird einmal am Tag beim Login angezeigt">Täglich beim Login</span>
                                        @elseif($popup->display_type === 'always')
                                            <span title="Wird bei jedem Login angezeigt">Bei jedem Login</span>
                                        @else
                                            <span title="Wird dem Nutzer einmalig bis zum Klick auf Schließen angezeigt">Einmalig bis Bestätigung</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-center whitespace-nowrap">
                                        <div class="inline-flex flex-col items-center">
                                            <span class="text-sm font-bold text-gray-200">
                                                {{ $popup->reads_count }} / {{ $totalUsersCount }}
                                            </span>
                                            <div class="w-16 bg-gray-700 rounded-full h-1.5 mt-1 overflow-hidden">
                                                <div class="bg-orange-500 h-1.5 rounded-full" style="width: {{ $totalUsersCount > 0 ? min(100, round(($popup->reads_count / $totalUsersCount) * 100)) : 0 }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-3 text-center whitespace-nowrap">
                                        <form method="POST" action="{{ route('admin.news-popups.toggle', $popup) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-2.5 py-1 text-xs font-semibold rounded border transition {{ $popup->is_active ? 'bg-green-900/40 text-green-300 border-green-700 hover:bg-green-900/60' : 'bg-gray-700 text-gray-400 border-gray-600 hover:bg-gray-600' }}">
                                                {{ $popup->is_active ? 'Aktiviert' : 'Pausiert' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="p-3 text-right whitespace-nowrap">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.news-popups.edit', $popup) }}" class="text-xs font-semibold px-3 py-1.5 rounded bg-blue-700 text-white hover:bg-blue-600 transition">
                                                Bearbeiten
                                            </a>
                                            <form method="POST" action="{{ route('admin.news-popups.destroy', $popup) }}" onsubmit="return confirm('Möchten Sie dieses News-Popup wirklich unwiderruflich löschen?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded border border-red-800 bg-red-900/30 text-red-400 hover:bg-red-900/60 transition">
                                                    Löschen
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-gray-400 italic">
                                        Es sind bisher keine News-Popups vorhanden. Klicken Sie auf „+ Neues Popup erstellen“, um eine Ankündigung für Ihre Mitarbeiter anzulegen.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
