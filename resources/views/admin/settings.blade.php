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

                    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6" enctype="multipart/form-data">
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

                        <!-- Material Order Email Settings -->
                        <div class="col-span-1 md:col-span-2 border-t border-gray-700 pt-6 mt-2">
                            <h4 class="text-md font-medium text-gray-200 mb-4">Materialbestellungen Erinnerung</h4>
                            <div class="grid grid-cols-1 gap-6">
                                <div class="flex items-center">
                                    <input type="checkbox" name="material_email_enabled" id="material_email_enabled" value="1" {{ ($settings->get('material_email_enabled', '0') == '1') ? 'checked' : '' }} class="rounded bg-gray-900 border-gray-600 text-blue-600 shadow-sm focus:ring-blue-500">
                                    <label for="material_email_enabled" class="ml-2 block text-sm font-medium text-gray-300">Tägliche E-Mail für offene Materialbestellungen aktivieren</label>
                                </div>

                                <div>
                                    <x-input-label for="material_email_time" :value="__('Uhrzeit für E-Mail Versand')" />
                                    <x-text-input id="material_email_time" class="block mt-1 md:w-1/4 w-full" type="time" name="material_email_time" :value="old('material_email_time', $settings->get('material_email_time', '08:00'))" />
                                    <p class="text-sm text-gray-400 mt-1">Die E-Mail wird täglich zu dieser Uhrzeit an den Berichtsempfänger (Chef) gesendet.</p>
                                </div>
                            </div>

                            <div class="mt-6 flex">
                                <button type="submit" form="test-material-email-form" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-2 px-4 rounded text-sm transition shadow-md">
                                    Test-E-Mail jetzt manuell auslösen
                                </button>
                            </div>
                        </div>

                        <!-- Appearance Settings -->
                        <div class="col-span-1 md:col-span-2 border-t border-gray-700 pt-6 mt-2">
                            <h4 class="text-md font-medium text-gray-200 mb-4">Erscheinungsbild</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-1 md:col-span-2">
                                    <x-input-label for="page_title" :value="__('Seiten-Titel')" />
                                    <x-text-input id="page_title" class="block mt-1 w-full" type="text" name="page_title" :value="old('page_title', $settings->get('page_title', 'Asendorf-Elektrotechnik Zeiterfassung'))" />
                                    <p class="text-sm text-gray-400 mt-1">Dieser Text wird im Browser-Tab und auf der Startseite angezeigt.</p>
                                </div>
                                
                                <div>
                                    <x-input-label for="app_logo" :value="__('App Logo (für Login-Seite)')" />
                                    @if($settings->get('app_logo'))
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $settings->get('app_logo')) }}" alt="App Logo" class="h-16 w-auto object-contain bg-white p-1 rounded">
                                        </div>
                                    @endif
                                    <input type="file" name="app_logo" id="app_logo" class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-white hover:file:bg-gray-600 cursor-pointer border border-gray-700 rounded mt-1 bg-gray-900" accept="image/*">
                                </div>

                                <div>
                                    <x-input-label for="pdf_logo" :value="__('PDF Logo (für Monatsbericht)')" />
                                    @if($settings->get('pdf_logo'))
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $settings->get('pdf_logo')) }}" alt="PDF Logo" class="h-16 w-auto object-contain bg-white p-1 rounded">
                                        </div>
                                    @endif
                                    <input type="file" name="pdf_logo" id="pdf_logo" class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-white hover:file:bg-gray-600 cursor-pointer border border-gray-700 rounded mt-1 bg-gray-900" accept="image/*">
                                </div>
                            </div>
                        </div>

                        <!-- Role Colors Settings -->
                        <div class="col-span-1 md:col-span-2 border-t border-gray-700 pt-6 mt-2">
                            <h4 class="text-md font-medium text-gray-200 mb-4">Farben der Mitarbeiter Rollen (Dashboard Team-Liste)</h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                                <div>
                                    <x-input-label for="role_color_admin" :value="__('Admin')" />
                                    <div class="flex items-center gap-2 mt-1">
                                        <input type="color" id="role_color_admin" name="role_color_admin" value="{{ old('role_color_admin', $settings->get('role_color_admin', '#f97316')) }}" class="p-0 border-0 h-8 w-10 bg-transparent cursor-pointer rounded">
                                        <span class="text-xs text-gray-400 font-mono">{{ old('role_color_admin', $settings->get('role_color_admin', '#f97316')) }}</span>
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="role_color_chef" :value="__('Chef')" />
                                    <div class="flex items-center gap-2 mt-1">
                                        <input type="color" id="role_color_chef" name="role_color_chef" value="{{ old('role_color_chef', $settings->get('role_color_chef', '#eab308')) }}" class="p-0 border-0 h-8 w-10 bg-transparent cursor-pointer rounded">
                                        <span class="text-xs text-gray-400 font-mono">{{ old('role_color_chef', $settings->get('role_color_chef', '#eab308')) }}</span>
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="role_color_azubi" :value="__('Azubi')" />
                                    <div class="flex items-center gap-2 mt-1">
                                        <input type="color" id="role_color_azubi" name="role_color_azubi" value="{{ old('role_color_azubi', $settings->get('role_color_azubi', '#22c55e')) }}" class="p-0 border-0 h-8 w-10 bg-transparent cursor-pointer rounded">
                                        <span class="text-xs text-gray-400 font-mono">{{ old('role_color_azubi', $settings->get('role_color_azubi', '#22c55e')) }}</span>
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="role_color_geselle" :value="__('Geselle')" />
                                    <div class="flex items-center gap-2 mt-1">
                                        <input type="color" id="role_color_geselle" name="role_color_geselle" value="{{ old('role_color_geselle', $settings->get('role_color_geselle', '#22c55e')) }}" class="p-0 border-0 h-8 w-10 bg-transparent cursor-pointer rounded">
                                        <span class="text-xs text-gray-400 font-mono">{{ old('role_color_geselle', $settings->get('role_color_geselle', '#22c55e')) }}</span>
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="role_color_employee" :value="__('Mitarbeiter')" />
                                    <div class="flex items-center gap-2 mt-1">
                                        <input type="color" id="role_color_employee" name="role_color_employee" value="{{ old('role_color_employee', $settings->get('role_color_employee', '#eab308')) }}" class="p-0 border-0 h-8 w-10 bg-transparent cursor-pointer rounded">
                                        <span class="text-xs text-gray-400 font-mono">{{ old('role_color_employee', $settings->get('role_color_employee', '#eab308')) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- General Work Settings -->
                        <div class="col-span-1 md:col-span-2 border-t border-gray-700 pt-6 mt-2">
                            <h4 class="text-md font-medium text-gray-200 mb-4">Standard Arbeitszeiten</h4>
                            <div class="bg-blue-900/30 border border-blue-800 text-blue-200 px-4 py-3 rounded relative mb-4" role="alert">
                                <span class="block sm:inline text-sm">Diese Zeiten werden als Vorgabe für neu angelegte Tage und beim Klicken der Status-Buttons (Krank, Urlaub, Schule, Folgt...) genutzt. Die Tages-Sollstunden werden automatisch daraus berechnet.</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <x-input-label for="default_start_time" :value="__('Standard Startzeit')" />
                                    <x-text-input id="default_start_time" class="block mt-1 w-full" type="time" name="default_start_time" :value="old('default_start_time', $settings->get('default_start_time', '08:00'))" />
                                </div>
                                <div>
                                    <x-input-label for="default_end_time" :value="__('Standard Endzeit')" />
                                    <x-text-input id="default_end_time" class="block mt-1 w-full" type="time" name="default_end_time" :value="old('default_end_time', $settings->get('default_end_time', '16:00'))" />
                                </div>
                                <div>
                                    <x-input-label for="default_break_duration" :value="__('Standard Pause (Minuten)')" />
                                    <x-text-input id="default_break_duration" class="block mt-1 w-full" type="number" name="default_break_duration" :value="old('default_break_duration', $settings->get('default_break_duration', '0'))" min="0" />
                                </div>
                            </div>
                        </div>

                        <!-- Help Page Settings -->
                        <div class="col-span-1 md:col-span-2 border-t border-gray-700 pt-6 mt-2">
                            <h4 class="text-md font-medium text-gray-200 mb-4">Hilfeseite & FAQ</h4>
                             
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <x-input-label for="help_page_title" :value="__('Titel der Hilfeseite')" />
                                    <x-text-input id="help_page_title" class="block mt-1 w-full" type="text" name="help_page_title" :value="old('help_page_title', $settings->get('help_page_title', 'Willkommen im Work Time Tracker'))" />
                                </div>

                                <div>
                                    <x-input-label for="help_page_content" :value="__('Einleitungs-Text (HTML erlaubt)')" />
                                    @php
                                        $defaultHelpText = "Dies ist Ihre zentrale Plattform für Arbeitszeiterfassung, Urlaubsplanung und Materialbestellungen. Hier finden Sie eine Übersicht über Ihre geleisteten Stunden, können Ihren Status pflegen und wichtige Dokumente einsehen.<br><br><strong>Arbeitszeiterfassung:</strong> Erfassen Sie Ihre täglichen Arbeitszeiten über das Dashboard oder die Tagesansicht.<br><br><strong>Kalender & Status:</strong> Nutzen Sie die Monatsübersicht, um Tage als \"Urlaub\" (U) oder \"Krank\" (K) zu markieren.<br><br><strong>Lager & Material:</strong> Im Reiter 'Lager' sehen Sie unsere aktuellen Bestände. Wenn Sie Material entnehmen, buchen Sie es hier aus. Fällt der Bestand unter ein Minimum, generiert das System automatisch eine Materialbestellung.<br><br><strong>Material Bestellungen:</strong> Werfen Sie hier einen Blick auf offene Bestellungen oder tragen Sie benötigte Sonder-Artikel direkt ein.";
                                    @endphp
                                    <textarea id="help_page_content" name="help_page_content" rows="6" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm bg-gray-900 text-gray-300 border-gray-700">{{ old('help_page_content', $settings->get('help_page_content', $defaultHelpText)) }}</textarea>
                                    <p class="text-sm text-gray-400 mt-1">Dieser Text ersetzt die oberen Blöcke der Hilfeseite. Einfaches HTML (z.B. &lt;br&gt;, &lt;strong&gt;) ist erlaubt.</p>
                                </div>

                                <div>
                                    <x-input-label for="help_page_copyright" :value="__('Copyright / Footer Text auf der Hilfeseite')" />
                                    <x-text-input id="help_page_copyright" class="block mt-1 w-full" type="text" name="help_page_copyright" :value="old('help_page_copyright', $settings->get('help_page_copyright', '&copy; ' . date('Y') . ' Work Time Tracker. Bei weiteren Fragen wenden Sie sich bitte an den Support oder Ihren Vorgesetzten.'))" />
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

                    <!-- Hidden Form for Testing Material Email -->
                    <form id="test-material-email-form" method="POST" action="{{ route('admin.settings.test-material-email') }}" class="hidden">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>

    @php
        $emailTemplates = [
            [
                'id' => 1,
                'title' => 'Account freigeschaltet',
                'prefix' => 'email_template_account_approved',
                'placeholders' => '{name}, {app_name}, {login_url}',
                'default_subject' => 'Dein Account wurde freigeschaltet',
                'default_body' => "Hallo {name},\n\nDein Account wurde erfolgreich freigeschaltet.\nDu kannst dich nun einloggen und deine Arbeitszeiten erfassen.\n\nZum Login: {login_url}\n\nMit freundlichen Grüßen,\n{app_name}",
            ],
            [
                'id' => 2,
                'title' => 'Neuer Benutzer registriert (An Admin)',
                'prefix' => 'email_template_new_user',
                'placeholders' => '{name}, {email}, {admin_url}',
                'default_subject' => 'Neuer Benutzer registriert: {name}',
                'default_body' => "Hallo,\n\nEin neuer Benutzer hat sich gerade registriert.\n\nName: {name}\nE-Mail: {email}\n\nBitte prüfe die Daten und schalte den Account frei.\nZum Admin-Bereich: {admin_url}",
            ],
            [
                'id' => 3,
                'title' => 'Monatsbericht (PDF)',
                'prefix' => 'monthly_report',
                'placeholders' => '{month}, {year}, {name}',
                'default_subject' => 'Monatsbericht {month} {year}',
                'default_body' => "Hallo,\n\nanbei erhalten Sie den Monatsbericht von {name} für {month} {year}.\n\nMit freundlichen Grüßen,\n" . config('app.name'),
            ],
            [
                'id' => 4,
                'title' => 'Tägliche Erinnerung (Fehlende Arbeitszeit)',
                'prefix' => 'email_template_daily_reminder',
                'placeholders' => '{name}',
                'default_subject' => 'Erinnerung: Arbeitszeit noch nicht vollständig',
                'default_body' => "Hallo {name},\n\ndu hast für heute noch keine Arbeitszeit eingetragen oder deine 8h noch nicht voll.\nBitte denk daran, diese noch zu erfassen.\n\nMit freundlichen Grüßen,\nDein Team",
            ],
            [
                'id' => 5,
                'title' => 'Tägliche Erinnerung (Fehlende Materialbuchung)',
                'prefix' => 'email_template_material_reminder',
                'placeholders' => '{name}',
                'default_subject' => 'Erinnerung: Materialbuchung',
                'default_body' => "Hallo {name},\n\ndu hast heute Arbeitszeit erfasst, aber keine Materialentnahme am Lager ausgebucht.\nWenn du Material entnommen hast, hole die Buchung bitte noch nach.\n\nMit freundlichen Grüßen,\nDein Team",
            ],
            [
                'id' => 6,
                'title' => 'Lager-Warnung (Mindestbestand erreicht)',
                'prefix' => 'email_template_low_stock',
                'placeholders' => '{material_name}, {stock}, {warning_threshold}',
                'default_subject' => 'Lager-Warnung: {material_name} geht zur Neige',
                'default_body' => "Hallo,\n\ndas Material {material_name} hat die Warnschwelle erreicht oder unterschritten.\nAktueller Bestand: {stock} (Warnschwelle: {warning_threshold})\n\nBitte nachbestellen!",
            ],
            [
                'id' => 7,
                'title' => 'Täglicher Materialbericht',
                'prefix' => 'email_template_daily_material_report',
                'placeholders' => '{date}',
                'default_subject' => 'Täglicher Materialbericht - {date}',
                'default_body' => "Hallo,\n\nanbei die heutige Übersicht der Materialentnahmen vom {date}.",
            ],
            [
                'id' => 8,
                'title' => 'Offene Materialbestellungen',
                'prefix' => 'email_template_open_material_orders',
                'placeholders' => '(Keine Variablen)',
                'default_subject' => 'Offene Materialbestellungen',
                'default_body' => "Hallo,\n\nes gibt aktuell unbestellte Materialbestellungen in der Warteschlange, die zur Prüfung bereitstehen.",
            ],
        ];
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700 p-6 text-gray-100">
                <h3 class="text-lg font-bold mb-4">E-Mail Vorlagen bearbeiten</h3>
                <p class="text-sm text-gray-400 mb-6">Hier kannst du die Betreffzeilen und die Textblöcke aller vom System versendeten E-Mails anpassen. Klicke auf eine Vorlage, um sie zu bearbeiten.</p>

                <div x-data="{ activeAccordion: null }" class="space-y-3">
                    @foreach($emailTemplates as $template)
                        <div class="border border-gray-700 rounded-md bg-gray-900 overflow-hidden">
                            <!-- Accordion Header -->
                            <button 
                                @click="activeAccordion = activeAccordion === {{ $template['id'] }} ? null : {{ $template['id'] }}" 
                                class="w-full flex justify-between items-center px-4 py-3 bg-gray-800 hover:bg-gray-700 transition focus:outline-none"
                            >
                                <span class="font-medium text-gray-200">{{ $template['title'] }}</span>
                                <svg 
                                    class="w-5 h-5 text-gray-400 transform transition-transform duration-200" 
                                    :class="{ 'rotate-180': activeAccordion === {{ $template['id'] }} }" 
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Accordion Body -->
                            <div 
                                x-show="activeAccordion === {{ $template['id'] }}" 
                                x-collapse 
                                x-cloak
                                class="px-4 py-4 border-t border-gray-700"
                            >
                                <form method="POST" action="{{ route('admin.settings.update-email-template') }}">
                                    @csrf
                                    <input type="hidden" name="template_key" value="{{ $template['prefix'] }}">

                                    <div class="mb-4">
                                        <label class="block font-medium text-sm text-gray-300 mb-1">Betreff</label>
                                        <input 
                                            type="text" 
                                            name="subject" 
                                            value="{{ App\Models\Setting::where('key', $template['prefix'] . '_subject')->value('value') ?: $template['default_subject'] }}" 
                                            class="block w-full border-gray-600 bg-gray-800 text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        >
                                    </div>

                                    <div class="mb-4">
                                        <label class="block font-medium text-sm text-gray-300 mb-1">Textinhalt</label>
                                        <textarea 
                                            name="body" 
                                            rows="4" 
                                            class="block w-full border-gray-600 bg-gray-800 text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        >{{ App\Models\Setting::where('key', $template['prefix'] . '_body')->value('value') ?: $template['default_body'] }}</textarea>
                                    </div>

                                    <div class="flex justify-between items-center mt-4">
                                        <div class="text-xs text-gray-400">
                                            <strong>Platzhalter:</strong> <span class="text-indigo-400">{{ $template['placeholders'] }}</span>
                                        </div>
                                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-1.5 px-4 rounded text-sm transition">
                                            Vorlage speichern
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
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
