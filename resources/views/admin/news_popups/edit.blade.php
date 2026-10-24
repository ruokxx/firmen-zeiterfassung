<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                {{ __('News-Popup bearbeiten') }}
            </h2>
            <a href="{{ route('admin.news-popups.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-700 border border-gray-600 rounded-md font-semibold text-xs text-gray-200 uppercase tracking-widest hover:bg-gray-600 transition shadow-sm">
                Zurück zur Übersicht
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="{
        title: @js(old('title', $newsPopup->title)),
        content: @js(old('content', $newsPopup->content)),
        type: @js(old('type', $newsPopup->type)),
        duration: 'keep',
        displayType: @js(old('display_type', $newsPopup->display_type))
    }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-700">
                
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-100">Popup anpassen</h3>
                    @if($newsPopup->expires_at)
                        <span class="text-xs text-gray-400">
                            Aktuelles Ablaufdatum: <strong class="text-gray-200">{{ $newsPopup->expires_at->format('d.m.Y H:i') }} Uhr</strong>
                        </span>
                    @endif
                </div>

                <form method="POST" action="{{ route('admin.news-popups.update', $newsPopup) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Titel -->
                    <div>
                        <x-input-label for="title" :value="__('Titel der Mitteilung')" />
                        <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" x-model="title" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <!-- Popup Typ / Stil -->
                    <div>
                        <x-input-label :value="__('Stil / Kategorie')" />
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-1.5">
                            <label class="flex items-center p-3 rounded-lg border cursor-pointer transition"
                                   :class="type === 'info' ? 'bg-blue-900/40 border-blue-500 text-white shadow' : 'bg-gray-900 border-gray-700 text-gray-300 hover:bg-gray-750'">
                                <input type="radio" name="type" value="info" x-model="type" class="sr-only">
                                <span class="text-lg mr-2">ℹ️</span>
                                <span class="text-sm font-semibold">Information (Blau)</span>
                            </label>

                            <label class="flex items-center p-3 rounded-lg border cursor-pointer transition"
                                   :class="type === 'important' ? 'bg-orange-900/40 border-orange-500 text-white shadow' : 'bg-gray-900 border-gray-700 text-gray-300 hover:bg-gray-750'">
                                <input type="radio" name="type" value="important" x-model="type" class="sr-only">
                                <span class="text-lg mr-2">⚠️</span>
                                <span class="text-sm font-semibold">Wichtig (Orange)</span>
                            </label>

                            <label class="flex items-center p-3 rounded-lg border cursor-pointer transition"
                                   :class="type === 'warning' ? 'bg-red-900/40 border-red-500 text-white shadow' : 'bg-gray-900 border-gray-700 text-gray-300 hover:bg-gray-750'">
                                <input type="radio" name="type" value="warning" x-model="type" class="sr-only">
                                <span class="text-lg mr-2">🚨</span>
                                <span class="text-sm font-semibold">Dringend (Rot)</span>
                            </label>

                            <label class="flex items-center p-3 rounded-lg border cursor-pointer transition"
                                   :class="type === 'success' ? 'bg-green-900/40 border-green-500 text-white shadow' : 'bg-gray-900 border-gray-700 text-gray-300 hover:bg-gray-750'">
                                <input type="radio" name="type" value="success" x-model="type" class="sr-only">
                                <span class="text-lg mr-2">✅</span>
                                <span class="text-sm font-semibold">Erfolg / Erledigt (Grün)</span>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <!-- Nachricht Text -->
                    <div>
                        <x-input-label for="content" :value="__('Nachricht / Inhalt des Popups')" />
                        <textarea id="content" name="content" x-model="content" rows="5" class="block mt-1 w-full border-gray-600 bg-gray-900 text-gray-200 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm" required></textarea>
                        <x-input-error :messages="$errors->get('content')" class="mt-2" />
                    </div>

                    <!-- Zeitliche Begrenzung (Dauer) -->
                    <div class="border-t border-gray-700 pt-6">
                        <x-input-label :value="__('Gültigkeit & Laufzeit ändern')" />
                        <p class="text-xs text-gray-400 mb-3">Wählen Sie „Bisherige Laufzeit beibehalten“ oder verlängern/ändern Sie die Frist.</p>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <label class="flex flex-col p-3 rounded-lg border cursor-pointer transition"
                                   :class="duration === 'keep' ? 'bg-orange-950/40 border-orange-500 text-white' : 'bg-gray-900 border-gray-700 text-gray-300 hover:bg-gray-750'">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-sm">Beibehalten</span>
                                    <input type="radio" name="duration_preset" value="keep" x-model="duration" class="text-orange-600 focus:ring-orange-500">
                                </div>
                                <span class="text-xs text-gray-400 mt-1">Nicht verändern</span>
                            </label>

                            <label class="flex flex-col p-3 rounded-lg border cursor-pointer transition"
                                   :class="duration === '1_day' ? 'bg-orange-950/40 border-orange-500 text-white' : 'bg-gray-900 border-gray-700 text-gray-300 hover:bg-gray-750'">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-sm">+ 1 Tag</span>
                                    <input type="radio" name="duration_preset" value="1_day" x-model="duration" class="text-orange-600 focus:ring-orange-500">
                                </div>
                                <span class="text-xs text-gray-400 mt-1">24h ab jetzt</span>
                            </label>

                            <label class="flex flex-col p-3 rounded-lg border cursor-pointer transition"
                                   :class="duration === '2_days' ? 'bg-orange-950/40 border-orange-500 text-white' : 'bg-gray-900 border-gray-700 text-gray-300 hover:bg-gray-750'">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-sm">+ 2 Tage</span>
                                    <input type="radio" name="duration_preset" value="2_days" x-model="duration" class="text-orange-600 focus:ring-orange-500">
                                </div>
                                <span class="text-xs text-gray-400 mt-1">48h ab jetzt</span>
                            </label>

                            <label class="flex flex-col p-3 rounded-lg border cursor-pointer transition"
                                   :class="duration === '3_days' ? 'bg-orange-950/40 border-orange-500 text-white' : 'bg-gray-900 border-gray-700 text-gray-300 hover:bg-gray-750'">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-sm">+ 3 Tage</span>
                                    <input type="radio" name="duration_preset" value="3_days" x-model="duration" class="text-orange-600 focus:ring-orange-500">
                                </div>
                                <span class="text-xs text-gray-400 mt-1">72h ab jetzt</span>
                            </label>

                            <label class="flex flex-col p-3 rounded-lg border cursor-pointer transition"
                                   :class="duration === '7_days' ? 'bg-orange-950/40 border-orange-500 text-white' : 'bg-gray-900 border-gray-700 text-gray-300 hover:bg-gray-750'">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-sm">+ 1 Woche</span>
                                    <input type="radio" name="duration_preset" value="7_days" x-model="duration" class="text-orange-600 focus:ring-orange-500">
                                </div>
                                <span class="text-xs text-gray-400 mt-1">7 Tage ab jetzt</span>
                            </label>

                            <label class="flex flex-col p-3 rounded-lg border cursor-pointer transition"
                                   :class="duration === 'custom' ? 'bg-orange-950/40 border-orange-500 text-white' : 'bg-gray-900 border-gray-700 text-gray-300 hover:bg-gray-750'">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-sm">Datum wählen</span>
                                    <input type="radio" name="duration_preset" value="custom" x-model="duration" class="text-orange-600 focus:ring-orange-500">
                                </div>
                                <span class="text-xs text-gray-400 mt-1">Exakt einstellen</span>
                            </label>

                            <label class="flex flex-col p-3 rounded-lg border cursor-pointer transition"
                                   :class="duration === 'no_limit' ? 'bg-orange-950/40 border-orange-500 text-white' : 'bg-gray-900 border-gray-700 text-gray-300 hover:bg-gray-750'">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-sm">Unbegrenzt</span>
                                    <input type="radio" name="duration_preset" value="no_limit" x-model="duration" class="text-orange-600 focus:ring-orange-500">
                                </div>
                                <span class="text-xs text-gray-400 mt-1">Ohne Ablaufdatum</span>
                            </label>
                        </div>

                        <!-- Individuelles Enddatum (nur sichtbar wenn 'custom') -->
                        <div x-show="duration === 'custom'" x-transition class="mt-4 p-4 bg-gray-900 rounded-lg border border-gray-700">
                            <x-input-label for="expires_at" :value="__('Ablaufdatum und Uhrzeit')" />
                            <x-text-input id="expires_at" class="block mt-1 w-full sm:w-auto" type="datetime-local" name="expires_at" :value="old('expires_at', $newsPopup->expires_at ? $newsPopup->expires_at->format('Y-m-d\TH:i') : now()->addDays(3)->format('Y-m-d\TH:i'))" />
                        </div>
                    </div>

                    <!-- Anzeige-Verhalten -->
                    <div class="border-t border-gray-700 pt-6">
                        <x-input-label :value="__('Anzeige-Verhalten beim Login')" />
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-1.5">
                            <label class="p-3 rounded-lg border cursor-pointer transition"
                                   :class="displayType === 'once_per_user' ? 'bg-orange-950/40 border-orange-500 text-white' : 'bg-gray-900 border-gray-700 text-gray-300 hover:bg-gray-750'">
                                <div class="flex items-center">
                                    <input type="radio" name="display_type" value="once_per_user" x-model="displayType" class="text-orange-600 focus:ring-orange-500 mr-2">
                                    <span class="text-sm font-bold">Einmalig bis Bestätigung</span>
                                </div>
                                <p class="text-xs text-gray-400 mt-1.5 pl-6">Verschwindet für den Mitarbeiter nach Klick auf „Verstanden / Schließen“.</p>
                            </label>

                            <label class="p-3 rounded-lg border cursor-pointer transition"
                                   :class="displayType === 'once_per_day' ? 'bg-orange-950/40 border-orange-500 text-white' : 'bg-gray-900 border-gray-700 text-gray-300 hover:bg-gray-750'">
                                <div class="flex items-center">
                                    <input type="radio" name="display_type" value="once_per_day" x-model="displayType" class="text-orange-600 focus:ring-orange-500 mr-2">
                                    <span class="text-sm font-bold">1x täglich beim Login</span>
                                </div>
                                <p class="text-xs text-gray-400 mt-1.5 pl-6">Erscheint an jedem Tag einmalig beim ersten Login des Tages.</p>
                            </label>

                            <label class="p-3 rounded-lg border cursor-pointer transition"
                                   :class="displayType === 'always' ? 'bg-orange-950/40 border-orange-500 text-white' : 'bg-gray-900 border-gray-700 text-gray-300 hover:bg-gray-750'">
                                <div class="flex items-center">
                                    <input type="radio" name="display_type" value="always" x-model="displayType" class="text-orange-600 focus:ring-orange-500 mr-2">
                                    <span class="text-sm font-bold">Bei jedem Login</span>
                                </div>
                                <p class="text-xs text-gray-400 mt-1.5 pl-6">Erscheint bei jedem Login während der gesamten Laufzeit.</p>
                            </label>
                        </div>
                    </div>

                    <!-- Aktivieren Checkbox -->
                    <div class="border-t border-gray-700 pt-6">
                        <label for="is_active" class="inline-flex items-center text-gray-200 cursor-pointer">
                            <input id="is_active" type="checkbox" class="rounded border-gray-600 text-orange-600 shadow-sm focus:ring-orange-500 bg-gray-900 h-5 w-5" name="is_active" value="1" {{ old('is_active', $newsPopup->is_active) ? 'checked' : '' }}>
                            <span class="ml-3 text-sm font-semibold">{{ __('Popup ist aktiv (sichtbar)') }}</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-700">
                        <a href="{{ route('admin.news-popups.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-700 border border-gray-600 rounded-md font-semibold text-xs text-gray-300 uppercase tracking-widest hover:bg-gray-600 transition">
                            Abbrechen
                        </a>
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-500 transition shadow-md">
                            Änderungen speichern
                        </button>
                    </div>
                </form>

            </div>

            <!-- Live-Vorschau -->
            <div class="bg-gray-800/80 rounded-lg p-6 border border-gray-700">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Live-Vorschau für den Mitarbeiter:</h4>
                <div class="max-w-lg mx-auto bg-gray-900 border rounded-xl p-5 shadow-2xl transition"
                     :class="{
                         'border-blue-600/70': type === 'info',
                         'border-orange-500/70': type === 'important',
                         'border-red-600/70': type === 'warning',
                         'border-green-600/70': type === 'success'
                     }">
                    <div class="flex items-start gap-3">
                        <div class="h-10 w-10 rounded-full flex items-center justify-center flex-shrink-0 text-xl"
                             :class="{
                                 'bg-blue-900/50 text-blue-400': type === 'info',
                                 'bg-orange-900/50 text-orange-400': type === 'important',
                                 'bg-red-900/50 text-red-400': type === 'warning',
                                 'bg-green-900/50 text-green-400': type === 'success'
                             }">
                            <span x-show="type === 'info'">ℹ️</span>
                            <span x-show="type === 'important'">⚠️</span>
                            <span x-show="type === 'warning'">🚨</span>
                            <span x-show="type === 'success'">✅</span>
                        </div>
                        <div class="flex-grow min-w-0">
                            <h4 class="text-lg font-bold text-white break-words" x-text="title || 'Titel der Mitteilung'"></h4>
                            <div class="text-sm text-gray-300 mt-2 whitespace-pre-line break-words" x-text="content || 'Inhalt des Popups...'"></div>
                        </div>
                    </div>
                    <div class="mt-5 flex justify-end">
                        <button type="button" class="px-4 py-2 rounded-lg font-medium text-sm text-white transition shadow-sm"
                                :class="{
                                    'bg-blue-600': type === 'info',
                                    'bg-orange-600': type === 'important',
                                    'bg-red-600': type === 'warning',
                                    'bg-green-600': type === 'success'
                                }">
                            Verstanden / Schließen
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
