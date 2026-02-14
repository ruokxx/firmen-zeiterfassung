<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Dies ist ein Standard-Admin-Account. Bitte ändern Sie aus Sicherheitsgründen sofort Ihre E-Mail-Adresse und Ihr Passwort.') }}
    </div>

    <form method="POST" action="{{ route('admin.setup.store') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Neue E-Mail Adresse')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Neues Passwort')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Passwort bestätigen')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Account aktualisieren') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
