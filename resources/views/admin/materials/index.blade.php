<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('Material-Katalog verwalten') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Add New Material --}}
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-100 mb-4">Neues Material hinzufügen</h3>
                <form method="POST" action="{{ route('admin.materials.store') }}" class="flex gap-4">
                    @csrf
                    <div class="flex-grow">
                        <x-text-input id="name" class="block w-full bg-gray-900 text-gray-100 border-gray-600 focus:border-orange-500 focus:ring-orange-500" type="text" name="name" placeholder="Materialname (z.B. Hammer, Nägel)" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <x-primary-button class="bg-orange-600 hover:bg-orange-500">
                        {{ __('Hinzufügen') }}
                    </x-primary-button>
                </form>
            </div>

            {{-- Material List --}}
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-100 mb-4">Vorhandene Materialien</h3>
                @if($materials->isEmpty())
                    <p class="text-gray-500 italic">Noch keine Materialien im Katalog.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($materials as $material)
                            <div class="flex justify-between items-center p-3 bg-gray-750 border border-gray-700 rounded-lg group hover:bg-gray-700 transition">
                                <span class="text-gray-200 font-medium">{{ $material->name }}</span>
                                <form method="POST" action="{{ route('admin.materials.destroy', $material) }}" onsubmit="return confirm('Material löschen?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-500 hover:text-red-500 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="flex justify-start">
                <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-orange-400 transition flex items-center gap-1">
                    <span>&larr;</span> Zurück zum Dashboard
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
