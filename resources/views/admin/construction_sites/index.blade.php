<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('Baustellen Suche') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Search Form --}}
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700 p-6">
                <form method="GET" action="{{ route('admin.construction-sites.index') }}" class="flex gap-4">
                    <div class="flex-grow">
                        <x-text-input id="search" class="block w-full bg-gray-900 text-gray-100 border-gray-600 focus:border-orange-500 focus:ring-orange-500" type="text" name="search" value="{{ $search }}" placeholder="Baustelle suchen..." required autofocus />
                    </div>
                    <x-primary-button class="bg-orange-600 hover:bg-orange-500">
                        {{ __('Suchen') }}
                    </x-primary-button>
                </form>
            </div>

            {{-- Results List --}}
            @if($search)
                <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-100 mb-4">Suchergebnisse für "{{ $search }}"</h3>
                    @if($siteDetails->isEmpty())
                        <p class="text-gray-500 italic">Keine Einträge gefunden.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-700">
                                <thead class="bg-gray-750">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Datum</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Mitarbeiter</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Baustelle</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Stunden</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-gray-800 divide-y divide-gray-700">
                                    @foreach($siteDetails as $detail)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                {{ \Carbon\Carbon::parse($detail['date'])->locale('de')->isoFormat('dddd, D. MMMM YYYY') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-100 font-semibold">
                                                {{ $detail['user_name'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-orange-400">
                                                {{ $detail['site_name'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                {{ number_format($detail['hours'], 1) }} h
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif

            <div class="flex justify-start">
                <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-orange-400 transition flex items-center gap-1">
                    <span>&larr;</span> Zurück zum Dashboard
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
