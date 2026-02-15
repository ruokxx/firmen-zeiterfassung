<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dokument verteilen') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="title" :value="__('Titel')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Beschreibung (Optional)')" />
                            <textarea id="description" name="description" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1" rows="3">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="file" :value="__('Datei')" />
                            <input type="file" id="file" name="file" class="block mt-1 w-full border border-gray-300 rounded p-2" required>
                            <x-input-error :messages="$errors->get('file')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <span class="block font-medium text-sm text-gray-700 mb-2">Empfänger auswählen</span>
                            
                            <div class="flex items-center mb-2">
                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <label for="select-all" class="ml-2 text-sm text-gray-600 font-semibold">Alle auswählen</label>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 max-h-60 overflow-y-auto border p-4 rounded">
                                @foreach($users as $user)
                                    <div class="flex items-center">
                                        <input type="checkbox" id="user_{{ $user->id }}" name="user_ids[]" value="{{ $user->id }}" 
                                            class="user-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            {{ (isset($selectedUser) && $selectedUser == $user->id) ? 'checked' : '' }}>
                                        <label for="user_{{ $user->id }}" class="ml-2 text-sm text-gray-600">{{ $user->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('user_ids')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.documents.index') }}" class="text-gray-600 underline hover:text-gray-900 mr-4">Abbrechen</a>
                            <x-primary-button>
                                {{ __('Verteilen') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>
</x-app-layout>
