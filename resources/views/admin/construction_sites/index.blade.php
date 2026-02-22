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
                <form method="GET" action="{{ route('admin.construction-sites.index') }}" class="flex flex-col lg:flex-row gap-4 items-end">
                    
                    <div class="w-full lg:w-1/4">
                        <x-input-label for="search" :value="__('Baustelle')" />
                        <x-text-input id="search" class="block w-full mt-1 bg-gray-900 border-gray-600 focus:border-orange-500 focus:ring-orange-500 text-gray-100" type="text" name="search" value="{{ $search }}" placeholder="Stuttgart..." autofocus />
                    </div>

                    <div class="w-full lg:w-1/4">
                        <x-input-label for="user_search" :value="__('Mitarbeiter Name')" />
                        <x-text-input id="user_search" class="block w-full mt-1 bg-gray-900 border-gray-600 focus:border-orange-500 focus:ring-orange-500 text-gray-100" type="text" name="user_search" value="{{ $userSearch ?? '' }}" placeholder="Max Mustermann..." />
                    </div>

                    <div class="w-full lg:w-48">
                        <x-input-label for="date_from" :value="__('Datum von')" />
                        <x-text-input id="date_from" class="block w-full mt-1 bg-gray-900 border-gray-600 focus:border-orange-500 focus:ring-orange-500 text-gray-100" type="date" name="date_from" value="{{ $dateFrom ?? '' }}" />
                    </div>

                    <div class="w-full lg:w-48">
                        <x-input-label for="date_to" :value="__('Datum bis')" />
                        <x-text-input id="date_to" class="block w-full mt-1 bg-gray-900 border-gray-600 focus:border-orange-500 focus:ring-orange-500 text-gray-100" type="date" name="date_to" value="{{ $dateTo ?? '' }}" />
                    </div>

                    <div class="flex gap-2 w-full lg:w-auto">
                        <button type="submit" class="w-full lg:w-auto bg-orange-600 hover:bg-orange-500 text-white font-bold py-2 px-4 rounded transition h-10 mb-[1px] mt-4 lg:mt-0 flex items-center justify-center">
                            Filtern
                        </button>
                        @if($search || ($userSearch ?? false) || ($dateFrom ?? false) || ($dateTo ?? false))
                            <a href="{{ route('admin.construction-sites.index') }}" class="w-full lg:w-auto bg-gray-600 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded transition h-10 mb-[1px] mt-4 lg:mt-0 flex items-center justify-center" title="Filter zurücksetzen">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Results List --}}
            @if($search || ($userSearch ?? false) || ($dateFrom ?? false) || ($dateTo ?? false))
                <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-100 mb-4">Suchergebnisse</h3>
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
