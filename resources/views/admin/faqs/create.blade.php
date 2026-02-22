<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('FAQ erstellen') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-700">
                
                <h3 class="text-lg font-bold mb-4 text-gray-100">Neue FAQ anlegen</h3>

                <form method="POST" action="{{ route('admin.faqs.store') }}">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="question" :value="__('Frage')" />
                        <x-text-input id="question" class="block mt-1 w-full" type="text" name="question" :value="old('question')" required autofocus />
                        <x-input-error :messages="$errors->get('question')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="answer" :value="__('Antwort')" />
                        <textarea id="answer" name="answer" class="block mt-1 w-full border-gray-600 bg-gray-900 text-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm min-h-[150px]" required>{{ old('answer') }}</textarea>
                        <x-input-error :messages="$errors->get('answer')" class="mt-2" />
                        <p class="text-xs text-gray-500 mt-1">Sie können hier ein wenig HTML Layout verwenden (z.B. &lt;strong&gt;, &lt;br&gt;, &lt;ul&gt; &lt;li&gt;).</p>
                    </div>

                    <div class="flex gap-4 mb-6">
                        <div class="flex-1">
                            <x-input-label for="order" :value="__('Reihenfolge (Sortierung)')" />
                            <x-text-input id="order" class="block mt-1 w-full" type="number" name="order" :value="old('order', 0)" required />
                            <x-input-error :messages="$errors->get('order')" class="mt-2" />
                        </div>
                        <div class="flex-1 flex flex-col justify-end">
                            <label for="is_active" class="inline-flex items-center text-gray-300 cursor-pointer h-10">
                                <input id="is_active" type="checkbox" class="rounded border-gray-600 text-orange-600 shadow-sm focus:ring-orange-500 bg-gray-900" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm">{{ __('Aktiv (Sichtbar)') }}</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4 pt-4 border-t border-gray-700">
                        <a href="{{ route('admin.faqs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 transition mr-3">
                            Abbrechen
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-500 transition">
                            Speichern
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
