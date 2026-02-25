<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            {{ __('Nutzer bearbeiten:') }} {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-gray-800 shadow sm:rounded-lg border border-gray-700">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-100">
                                {{ __('Profil-Informationen') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-400">
                                {{ __("Aktualisieren Sie die Profil-Informationen und die Email-Adresse dieses Nutzers.") }}
                            </p>
                        </header>

                        <form method="post" action="{{ route('admin.users.update', $user) }}" class="mt-6 space-y-6">
                            @csrf
                            @method('patch')

                            <div>
                                <x-input-label for="name" :value="__('Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-gray-700 text-gray-200 border-gray-600 focus:border-orange-500 focus:ring-orange-500" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-gray-700 text-gray-200 border-gray-600 focus:border-orange-500 focus:ring-orange-500" :value="old('email', $user->email)" required autocomplete="username" />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            </div>

                            <div>
                                <x-input-label for="mobile_number" :value="__('Telefonnummer')" />
                                <x-text-input id="mobile_number" name="mobile_number" type="text" class="mt-1 block w-full bg-gray-700 text-gray-200 border-gray-600 focus:border-orange-500 focus:ring-orange-500" :value="old('mobile_number', $user->mobile_number)" />
                                <x-input-error class="mt-2" :messages="$errors->get('mobile_number')" />
                            </div>

                            <div>
                                <x-input-label for="address" :value="__('Anschrift')" />
                                <textarea id="address" name="address" rows="3" class="mt-1 block w-full rounded-md shadow-sm bg-gray-700 text-gray-200 border-gray-600 focus:border-orange-500 focus:ring-orange-500">{{ old('address', $user->address) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('address')" />
                            </div>

                            <hr class="border-gray-700">

                            <div>
                                <h3 class="text-lg font-medium text-gray-100 border-b border-gray-700 pb-2 mb-4">Benachrichtigungen</h3>
                                
                                <div class="flex items-center gap-4 mb-4">
                                    <input type="hidden" name="daily_reminder_enabled" value="0">
                                    <input id="daily_reminder_enabled" name="daily_reminder_enabled" type="checkbox" value="1" class="rounded border-gray-600 bg-gray-900 text-orange-500 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-50" {{ old('daily_reminder_enabled', $user->daily_reminder_enabled) ? 'checked' : '' }}>
                                    <x-input-label for="daily_reminder_enabled" :value="__('Tägliche Arbeitszeit-Erinnerung (Mo-Fr, 18 Uhr) bei weniger als 8 Stunden')" class="mb-0 text-gray-300" />
                                </div>
                                <x-input-error class="mt-2" :messages="$errors->get('daily_reminder_enabled')" />

                                <div class="flex items-center gap-4">
                                    <input type="hidden" name="daily_material_reminder_enabled" value="0">
                                    <input id="daily_material_reminder_enabled" name="daily_material_reminder_enabled" type="checkbox" value="1" class="rounded border-gray-600 bg-gray-900 text-orange-500 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-50" {{ old('daily_material_reminder_enabled', $user->daily_material_reminder_enabled) ? 'checked' : '' }}>
                                    <div>
                                        <x-input-label for="daily_material_reminder_enabled" :value="__('Tägliche Material-Erinnerung, falls kein Material gebucht wurde')" class="mb-0 text-gray-300" />
                                        <p class="text-xs text-gray-500 mt-1">Erinnert Sie an Arbeitstagen daran, entnommenes Material einzutragen, falls Sie dies noch nicht getan haben.</p>
                                    </div>
                                </div>
                                <x-input-error class="mt-2" :messages="$errors->get('daily_material_reminder_enabled')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-500 active:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Speichern') }}
                                </button>
                                <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-400 hover:text-gray-200 hover:underline">
                                    Abbrechen
                                </a>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
