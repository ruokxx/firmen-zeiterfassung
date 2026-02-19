<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('Admin Einstellungen') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700">
                <div class="p-6 text-gray-100">
                    
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-white">SMTP E-Mail Konfiguration</h3>
                        <button type="button" onclick="loadAllInklDefaults()" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded text-sm transition">
                            All-Inkl Einstellungen laden
                        </button>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-900/50 border border-green-600 text-green-200 px-4 py-3 rounded relative mb-6" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-900/50 border border-red-600 text-red-200 px-4 py-3 rounded relative mb-6" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Mailer -->
                            <div>
                                <x-input-label for="mail_mailer" :value="__('Mailer')" />
                                <x-text-input id="mail_mailer" class="block mt-1 w-full" type="text" name="mail_mailer" :value="old('mail_mailer', $settings->get('mail_mailer', 'smtp'))" required />
                            </div>

                            <!-- Host -->
                            <div>
                                <x-input-label for="mail_host" :value="__('Mail Host')" />
                                <x-text-input id="mail_host" class="block mt-1 w-full" type="text" name="mail_host" :value="old('mail_host', $settings->get('mail_host'))" required />
                            </div>

                            <!-- Port -->
                            <div>
                                <x-input-label for="mail_port" :value="__('Port')" />
                                <x-text-input id="mail_port" class="block mt-1 w-full" type="number" name="mail_port" :value="old('mail_port', $settings->get('mail_port'))" required />
                            </div>

                            <!-- Encryption -->
                            <div>
                                <x-input-label for="mail_encryption" :value="__('Verschlüsselung')" />
                                <x-text-input id="mail_encryption" class="block mt-1 w-full" type="text" name="mail_encryption" :value="old('mail_encryption', $settings->get('mail_encryption'))" placeholder="z.B. ssl oder tls" />
                            </div>
                            
                            <!-- Username -->
                            <div>
                                <x-input-label for="mail_username" :value="__('Benutzername (E-Mail Adresse)')" />
                                <x-text-input id="mail_username" class="block mt-1 w-full" type="text" name="mail_username" :value="old('mail_username', $settings->get('mail_username'))" />
                            </div>

                            <!-- Password -->
                            <div>
                                <x-input-label for="mail_password" :value="__('Passwort')" />
                                <x-text-input id="mail_password" class="block mt-1 w-full" type="password" name="mail_password" :value="old('mail_password', $settings->get('mail_password'))" />
                            </div>

                            <!-- From Address -->
                            <div>
                                <x-input-label for="mail_from_address" :value="__('Absender Adresse')" />
                                <x-text-input id="mail_from_address" class="block mt-1 w-full" type="email" name="mail_from_address" :value="old('mail_from_address', $settings->get('mail_from_address'))" required />
                            </div>

                             <!-- From Name -->
                             <div>
                                <x-input-label for="mail_from_name" :value="__('Absender Name')" />
                                <x-text-input id="mail_from_name" class="block mt-1 w-full" type="text" name="mail_from_name" :value="old('mail_from_name', $settings->get('mail_from_name', config('app.name')))" required />
                            </div>

                            <!-- Boss Email -->
                            <div class="col-span-1 md:col-span-2 border-t border-gray-700 pt-6 mt-2">
                                <h4 class="text-md font-medium text-gray-200 mb-4">Berichtsempfänger (Chef)</h4>
                                <div>
                                    <x-input-label for="boss_email" :value="__('E-Mail Adresse des Chefs')" />
                                    <x-text-input id="boss_email" class="block mt-1 w-full" type="email" name="boss_email" :value="old('boss_email', $settings->get('boss_email'))" placeholder="chef@example.com" />
                                    <p class="text-sm text-gray-400 mt-1">An diese Adresse werden die Monatsberichte gesendet, wenn Mitarbeiter "An Chef senden" wählen.</p>
                                </div>
                            </div>

                            <!-- Monthly Report Email Settings -->
                            <div class="col-span-1 md:col-span-2 border-t border-gray-700 pt-6 mt-2">
                                <h4 class="text-md font-medium text-gray-200 mb-4">E-Mail Text für Monatsbericht</h4>
                                 
                                <div class="grid grid-cols-1 gap-6">
                                    <div>
                                        <x-input-label for="monthly_report_subject" :value="__('Betreff')" />
                                        <x-text-input id="monthly_report_subject" class="block mt-1 w-full" type="text" name="monthly_report_subject" :value="old('monthly_report_subject', $settings->get('monthly_report_subject', 'Monatsbericht {month} {year}'))" />
                                        <p class="text-sm text-gray-400 mt-1">Platzhalter: {month}, {year}, {name}</p>
                                    </div>

                                    <div>
                                        <x-input-label for="monthly_report_body" :value="__('E-Mail Text')" />
                                        <textarea id="monthly_report_body" name="monthly_report_body" rows="5" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm bg-gray-900 text-gray-300 border-gray-700">{{ old('monthly_report_body', $settings->get('monthly_report_body', "Hallo,\n\nanbei erhalten Sie den Monatsbericht von {name} für {month} {year}.\n\nMit freundlichen Grüßen,\n" . config('app.name'))) }}</textarea>
                                        <p class="text-sm text-gray-400 mt-1">Platzhalter: {month}, {year}, {name}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Account Approved Email Settings -->
                            <div class="col-span-1 md:col-span-2 border-t border-gray-700 pt-6 mt-2">
                                <h4 class="text-md font-medium text-gray-200 mb-4">E-Mail Text für Account Freischaltung</h4>
                                 
                                <div class="grid grid-cols-1 gap-6">
                                    <div>
                                        <x-input-label for="account_approved_subject" :value="__('Betreff')" />
                                        <x-text-input id="account_approved_subject" class="block mt-1 w-full" type="text" name="account_approved_subject" :value="old('account_approved_subject', $settings->get('account_approved_subject', 'Dein Account wurde freigeschaltet'))" />
                                        <p class="text-sm text-gray-400 mt-1">Platzhalter: {name}, {app_name}</p>
                                    </div>

                                    <div>
                                        <x-input-label for="account_approved_body" :value="__('E-Mail Text')" />
                                        <textarea id="account_approved_body" name="account_approved_body" rows="5" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm bg-gray-900 text-gray-300 border-gray-700">{{ old('account_approved_body', $settings->get('account_approved_body', "Hallo {name},\n\nDein Account wurde erfolgreich freigeschaltet.\nDu kannst dich nun einloggen und deine Arbeitszeiten erfassen.\n\nZum Login: {login_url}\n\nMit freundlichen Grüßen,\n{app_name}")) }}</textarea>
                                        <p class="text-sm text-gray-400 mt-1">Platzhalter: {name}, {login_url}, {app_name}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Auto Backup Settings -->
                        <div class="col-span-1 md:col-span-2 border-t border-gray-700 pt-6 mt-2">
                            <h4 class="text-md font-medium text-gray-200 mb-4">Automatische Backups</h4>
                            <div class="grid grid-cols-1 gap-6">
                                <div class="flex items-center">
                                    <input type="checkbox" name="auto_backup_enabled" id="auto_backup_enabled" value="1" {{ ($settings->get('auto_backup_enabled', '0') == '1') ? 'checked' : '' }} class="rounded bg-gray-900 border-gray-600 text-blue-600 shadow-sm focus:ring-blue-500">
                                    <label for="auto_backup_enabled" class="ml-2 block text-sm font-medium text-gray-300">Tägliches Backup aktivieren (um 22:00 Uhr)</label>
                                </div>

                                <div>
                                    <x-input-label for="backup_retention_count" :value="__('Anzahl zu behaltender Backups')" />
                                    <x-text-input id="backup_retention_count" class="block mt-1 w-full" type="number" name="backup_retention_count" :value="old('backup_retention_count', $settings->get('backup_retention_count', 5))" min="1" />
                                    <p class="text-sm text-gray-400 mt-1">Ältere Backups werden automatisch gelöscht, wenn diese Anzahl überschritten wird.</p>
                                </div>
                            </div>
                        </div>

                        </div>

                        <div class="flex justify-end">
                            <x-primary-button>
                                {{ __('Speichern') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700 p-6 text-gray-100">
                <h3 class="text-lg font-bold mb-4">Datenbank Backup & Restore</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    
                    <!-- Manage Backups -->
                    <div class="space-y-6">
                        <!-- Generate -->
                        <div class="bg-gray-700 p-4 rounded border border-gray-600">
                            <h4 class="font-semibold mb-2">Neues Backup erstellen</h4>
                            <p class="text-sm text-gray-300 mb-3">Erstellt eine Sicherungskopie der aktuellen Datenbank und speichert sie auf dem Server.</p>
                            <form method="POST" action="{{ route('admin.backup.generate') }}">
                                @csrf
                                <button type="submit" class="bg-green-600 hover:bg-green-500 text-white font-bold py-2 px-4 rounded text-sm transition">
                                    Backup generieren
                                </button>
                            </form>
                        </div>

                        <!-- List -->
                        <div class="bg-gray-700 p-4 rounded border border-gray-600">
                            <h4 class="font-semibold mb-2">Verfügbare Backups</h4>
                            @if(isset($backups) && count($backups) > 0)
                                <ul class="space-y-2 max-h-60 overflow-y-auto pr-2">
                                    @foreach($backups as $backup)
                                        <li class="flex justify-between items-center bg-gray-800 p-2 rounded border border-gray-600">
                                            <div class="overflow-hidden">
                                                <span class="block text-xs text-gray-400">{{ \Carbon\Carbon::createFromTimestamp($backup['last_modified'])->format('d.m.Y H:i') }}</span>
                                                <span class="block text-sm font-medium truncate" title="{{ $backup['filename'] }}">
                                                    {{ $backup['filename'] }}
                                                    <span class="text-xs text-gray-500">({{ round($backup['size'] / 1024, 2) }} KB)</span>
                                                </span>
                                            </div>
                                            <div class="flex space-x-1 flex-shrink-0 ml-2">
                                                <a href="{{ route('admin.backup.download', $backup['filename']) }}" class="text-blue-400 hover:text-blue-300 p-1" title="Download">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                                </a>
                                                <form method="POST" action="{{ route('admin.backup.delete', $backup['filename']) }}" onsubmit="return confirm('Backup wirklich löschen?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-400 hover:text-red-300 p-1" title="Löschen">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-gray-500 italic">Keine Backups vorhanden.</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Restore -->
                    <div class="bg-gray-700 p-4 rounded border border-gray-600">
                        <h4 class="font-semibold mb-2">Backup wiederherstellen</h4>
                        <p class="text-sm text-gray-300 mb-3">Laden Sie eine Sicherungsdatei hoch, um die Datenbank wiederherzustellen.</p>
                        
                        <div class="bg-red-900/30 border border-red-800 p-3 rounded mb-4">
                            <strong class="text-red-400 block mb-1">WARNUNG:</strong>
                            <p class="text-sm text-red-200">
                                Dies überschreibt die <strong>gesamte Datenbank</strong> unwiderruflich! 
                                Alle seit dem Backup erstellten Daten gehen verloren.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('admin.backup.restore') }}" enctype="multipart/form-data" onsubmit="return confirm('SIND SIE TOTAL SICHER? Die aktuelle Datenbank wird ÜBERSCHRIEBEN!');">
                            @csrf
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Backup-Datei (.sqlite)</label>
                                    <input type="file" name="backup_file" required class="block w-full text-sm text-gray-300
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-full file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-blue-600 file:text-white
                                      file:hover:bg-blue-700
                                      cursor-pointer bg-gray-800 border border-gray-600 rounded">
                                </div>
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-500 text-white font-bold py-2 px-4 rounded text-sm transition">
                                    Datenbank wiederherstellen
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function loadAllInklDefaults() {
            document.getElementById('mail_mailer').value = 'smtp';
            document.getElementById('mail_host').value = 'kasserver.com'; // Standard All-Inkl Host
            document.getElementById('mail_port').value = '465';
            document.getElementById('mail_encryption').value = 'ssl';
            
            // Optional: clear user/pass to avoid confusion? No, let them fill it.
            // But we can set the From Address to something generic or leave as is.
        }
    </script>
</x-app-layout>
