<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('FAQs verwalten') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-700">
                
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-100">Häufig gestellte Fragen (FAQ)</h3>
                    <a href="{{ route('admin.faqs.create') }}" class="bg-green-600 hover:bg-green-500 text-white font-bold py-2 px-4 rounded text-sm transition">
                        + Neue FAQ erstellen
                    </a>
                </div>

                @if(session('success'))
                    <div class="bg-green-900 border border-green-700 text-green-300 px-4 py-3 rounded mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-750 border-b border-gray-700">
                                <th class="p-3 text-gray-300 font-bold">Reihenfolge</th>
                                <th class="p-3 text-gray-300 font-bold">Frage</th>
                                <th class="p-3 text-gray-300 font-bold">Aktiv</th>
                                <th class="p-3 text-gray-300 font-bold text-right">Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($faqs as $faq)
                                <tr class="border-b border-gray-700 hover:bg-gray-750 transition">
                                    <td class="p-3 text-gray-400">{{ $faq->order }}</td>
                                    <td class="p-3 text-gray-200">{{ $faq->question }}</td>
                                    <td class="p-3">
                                        @if($faq->is_active)
                                            <span class="px-2 py-1 bg-green-900/50 text-green-400 text-xs rounded border border-green-800">Ja</span>
                                        @else
                                            <span class="px-2 py-1 bg-red-900/50 text-red-400 text-xs rounded border border-red-800">Nein</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.faqs.edit', $faq) }}" class="text-xs font-semibold px-3 py-1.5 rounded bg-blue-700 text-white hover:bg-blue-600 transition">
                                                Bearbeiten
                                            </a>
                                            <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" onsubmit="return confirm('Möchten Sie diese FAQ wirklich löschen?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded border border-red-800 bg-red-900/30 text-red-500 hover:bg-red-900/50 transition">
                                                    Löschen
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-4 text-center text-gray-500 italic">Noch keine FAQs vorhanden.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
