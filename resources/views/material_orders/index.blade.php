<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('Material Bestellungen') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- New Order Form --}}
            @if(auth()->user()->role !== 'azubi')
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-100 mb-4">Neue Bestellung aufgeben</h3>
                <form method="POST" action="{{ route('material-orders.store') }}" class="flex gap-4">
                    @csrf
                    <div class="flex-grow">
                        <x-text-input id="item_name" list="catalog" class="block w-full bg-gray-900 text-gray-100 border-gray-600 focus:border-orange-500 focus:ring-orange-500" type="text" name="item_name" placeholder="Was wird benötigt?" required />
                        <datalist id="catalog">
                            @foreach($catalogItems as $item)
                                <option value="{{ $item->name }}">
                            @endforeach
                        </datalist>
                    </div>
                    <x-primary-button class="bg-orange-600 hover:bg-orange-500">
                        {{ __('Hinzufügen') }}
                    </x-primary-button>
                </form>
            </div>
            @else
            <div class="bg-blue-900/50 border border-blue-700 text-blue-200 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">Als Auszubildender kannst du hier einsehen, was bestellt wurde. Bestellungen aufgeben können nur Mitarbeiter und Chefs.</span>
            </div>
            @endif

            {{-- Active Orders --}}
            <div>
                <h3 class="text-xl font-bold text-orange-500 mb-4">Aktuelle Bestellungen</h3>
                @if($activeGroups->isEmpty())
                    <p class="text-gray-500 italic">Keine offenen Bestellungen.</p>
                @else
                    <div class="space-y-6">
                        @foreach($activeGroups as $date => $orders)
                            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                                <div class="bg-gray-750 px-4 py-2 border-b border-gray-700 flex justify-between items-center">
                                    <span class="text-gray-300 font-semibold">{{ \Carbon\Carbon::parse($date)->locale('de')->isoFormat('dddd, D. MMMM YYYY') }}</span>
                                </div>
                                <div class="divide-y divide-gray-700">
                                    @foreach($orders as $order)
                                        <div class="p-4 flex justify-between items-center hover:bg-gray-750 transition">
                                            <div class="flex items-center gap-4">
                                                @if(auth()->user()->is_admin)
                                                    <form method="POST" action="{{ route('material-orders.toggle', $order) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="checkbox" onchange="this.form.submit()" {{ $order->is_ordered ? 'checked' : '' }} class="rounded border-gray-600 bg-gray-700 text-orange-500 shadow-sm focus:ring-orange-500 focus:ring-offset-gray-800 cursor-pointer w-5 h-5">
                                                    </form>
                                                @else
                                                    <div class="w-5 h-5 rounded border border-gray-600 flex items-center justify-center {{ $order->is_ordered ? 'bg-orange-500 border-orange-500' : 'bg-gray-700' }}">
                                                        @if($order->is_ordered)
                                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                        @endif
                                                    </div>
                                                @endif
                                                
                                                <div>
                                                    <div class="text-gray-100 font-medium {{ $order->is_ordered ? 'line-through text-gray-500' : '' }}">{{ $order->item_name }}</div>
                                                    <div class="text-xs text-gray-400">Bestellt von: {{ $order->user->name }}</div>
                                                    
                                                    @if($order->admin_comment)
                                                        <div class="text-xs text-orange-400 mt-1 font-semibold">Chef-Kommentar: {{ $order->admin_comment }}</div>
                                                    @endif

                                                    @if(auth()->user()->is_admin)
                                                        <form method="POST" action="{{ route('material-orders.update', $order) }}" class="mt-2 flex gap-2">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="text" name="admin_comment" value="{{ $order->admin_comment }}" placeholder="Kommentar..." class="bg-gray-700 text-gray-200 border-gray-600 rounded px-2 py-1 text-xs w-full focus:border-orange-500 focus:ring-orange-500">
                                                            <button type="submit" class="text-xs bg-gray-600 hover:bg-gray-500 text-white px-2 py-1 rounded transition">Speichern</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>

                                            @if(auth()->id() === $order->user_id || auth()->user()->is_admin)
                                                <form method="POST" action="{{ route('material-orders.destroy', $order) }}" onsubmit="return confirm('Wirklich löschen?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-400 hover:text-red-300 transition">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Archived Orders --}}
            @if($archivedGroups->isNotEmpty())
                <div x-data="{ showArchive: false }" class="border-t border-gray-700 pt-8">
                    <button @click="showArchive = !showArchive" class="flex items-center gap-2 text-gray-400 hover:text-gray-200 transition mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform duration-200" :class="showArchive ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <span class="font-bold">Archiv (Erledigte Bestellungen)</span>
                    </button>

                    <div x-show="showArchive" class="space-y-6">
                        @foreach($archivedGroups as $date => $orders)
                            <div class="bg-gray-800/50 rounded-lg border border-gray-700/50 overflow-hidden opacity-75">
                                <div class="bg-gray-800 px-4 py-2 border-b border-gray-700 flex justify-between items-center">
                                    <span class="text-gray-400 font-semibold">{{ \Carbon\Carbon::parse($date)->locale('de')->isoFormat('dddd, D. MMMM YYYY') }}</span>
                                    <span class="text-xs bg-green-900 text-green-200 px-2 py-1 rounded">Erledigt</span>
                                </div>
                                <div class="divide-y divide-gray-700">
                                    @foreach($orders as $order)
                                        <div class="p-4 flex justify-between items-center">
                                            <div class="flex items-center gap-4">
                                                <div class="w-5 h-5 rounded border border-gray-600 bg-green-600/20 border-green-600/50 flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                </div>
                                                
                                                <div>
                                                    <div class="text-gray-400 line-through">{{ $order->item_name }}</div>
                                                    <div class="text-xs text-gray-500">Bestellt von: {{ $order->user->name }}</div>
                                                    
                                                    @if($order->admin_comment)
                                                        <div class="text-xs text-orange-400 mt-1 font-semibold">Chef-Kommentar: {{ $order->admin_comment }}</div>
                                                    @endif

                                                    @if(auth()->user()->is_admin)
                                                        <form method="POST" action="{{ route('material-orders.update', $order) }}" class="mt-2 flex gap-2">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="text" name="admin_comment" value="{{ $order->admin_comment }}" placeholder="Kommentar..." class="bg-gray-700 text-gray-200 border-gray-600 rounded px-2 py-1 text-xs w-full focus:border-orange-500 focus:ring-orange-500">
                                                            <button type="submit" class="text-xs bg-gray-600 hover:bg-gray-500 text-white px-2 py-1 rounded transition">Speichern</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>

                                            @if(auth()->user()->is_admin)
                                                 <form method="POST" action="{{ route('material-orders.toggle', $order) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-xs text-gray-500 hover:text-orange-400 underline">Zurückholen</button>
                                                </form>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
