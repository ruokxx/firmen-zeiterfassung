<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('Email an User senden') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700 p-6">
                
                <h3 class="text-lg font-bold text-gray-100 mb-4">Empfänger: <span class="text-orange-500">{{ $user->name }}</span> ({{ $user->email }})</h3>

                <form method="POST" action="{{ route('admin.users.email.send', $user) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="subject" :value="__('Betreff')" />
                        <x-text-input id="subject" class="block mt-1 w-full bg-gray-900 text-gray-100 border-gray-600 focus:border-orange-500 focus:ring-orange-500" type="text" name="subject" :value="old('subject')" required autofocus />
                        <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="message" :value="__('Nachricht')" />
                        <textarea id="message" name="message" rows="8" class="block mt-1 w-full bg-gray-900 text-gray-100 border-gray-600 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" required>{{ old('message') }}</textarea>
                        <x-input-error :messages="$errors->get('message')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="attachment" :value="__('Anhang (Optional)')" />
                        <input type="file" id="attachment" name="attachment" class="block mt-1 w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-gray-200 hover:file:bg-gray-600 cursor-pointer" />
                        <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4 gap-4">
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-200 underline text-sm">Abbrechen</a>
                        <x-primary-button class="bg-orange-600 hover:bg-orange-500">
                            {{ __('Email senden') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
