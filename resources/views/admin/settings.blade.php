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
