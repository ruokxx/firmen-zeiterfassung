<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('Lager') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-900 border border-green-700 text-green-100 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-900 border border-red-700 text-red-100 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-900 border border-red-700 text-red-100 px-4 py-3 rounded relative">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-gray-800 border border-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-100">

                    <h3 class="text-xl font-bold text-orange-500 mb-6 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Aktueller Materialbestand
                    </h3>

                    @php
                        // Combine categories and uncategorized into a structured array
                        $renderGroups = [];
                        foreach($categories as $category) {
                            if($category->materials->count() > 0) {
                                $renderGroups[] = [
                                    'title' => $category->name, 
                                    'materials' => $category->materials,
                                    'is_complete' => $category->isComplete()
                                ];
                            }
                        }
                        if($uncategorizedMaterials->count() > 0) {
                            $renderGroups[] = [
                                'title' => 'Unkategorisiert', 
                                'materials' => $uncategorizedMaterials,
                                'is_complete' => null // No symbol for uncategorized
                            ];
                        }
                    @endphp

                    @forelse($renderGroups as $group)
                        <div class="mb-10" x-data="{ 
                            open: sessionStorage.getItem('materialCategoryOpen_{{ Str::slug($group['title']) }}') === 'true',
                            toggle() {
                                this.open = !this.open;
                                sessionStorage.setItem('materialCategoryOpen_{{ Str::slug($group['title']) }}', this.open);
                            }
                        }">
                            <h4 @click="toggle()" class="cursor-pointer text-xl font-bold text-gray-200 mb-4 pb-2 border-b border-gray-700 flex items-center justify-between select-none hover:text-orange-400 transition">
                                <div class="flex items-center gap-3">
                                    {{ $group['title'] }}
                                    
                                    @if($group['is_complete'] === true)
                                        <span title="Bestand vollständig" class="flex items-center justify-center bg-green-900/50 text-green-500 rounded-full p-1 border border-green-700 shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    @elseif($group['is_complete'] === false)
                                        <span title="Material fehlt (unter Warnschwelle)" class="flex items-center justify-center bg-red-900/50 text-red-500 rounded-full p-1 border border-red-700 shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                                <svg :class="{'rotate-180': open}" class="w-6 h-6 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </h4>

                            <div x-show="open" x-transition.opacity style="display: none;" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($group['materials'] as $material)
                                    <div x-data="{ stock: {{ $material->stock_count }} }" class="bg-gray-750 border rounded-lg p-5 flex flex-col justify-between shadow-sm" :class="stock <= {{ $material->low_stock_threshold }} ? 'border-red-600' : 'border-gray-600'">
                                        <div>
                                            <div class="flex justify-between items-start mb-2">
                                                <h4 class="text-lg font-semibold text-gray-200">{{ $material->name }}</h4>
                                                <span class="text-2xl font-bold" :class="stock <= {{ $material->low_stock_threshold }} ? 'text-red-500' : 'text-orange-500'">
                                                    <span x-text="stock">{{ $material->stock_count }}</span> 
                                                    <span class="text-sm font-normal text-gray-400">{{ $material->unit }}</span>
                                                </span>
                                            </div>
                                            <template x-if="stock <= {{ $material->low_stock_threshold }}">
                                                <p class="text-xs text-red-400 mb-4">Geringer Bestand! (Warnschwelle: {{ $material->low_stock_threshold }})</p>
                                            </template>
                                            <template x-if="stock > {{ $material->low_stock_threshold }}">
                                                <p class="text-xs text-gray-400 mb-4">Ausreichend auf Lager.</p>
                                            </template>
                                        </div>

                                        <form action="{{ route('materials.transaction', $material) }}" method="POST" class="mt-auto" x-data="{
                                            submitting: false,
                                            submitForm(event) {
                                                if(this.submitting) return;
                                                this.submitting = true;
                                                
                                                const formData = new FormData(event.target);
                                                
                                                fetch(event.target.action, {
                                                    method: 'POST',
                                                    body: formData,
                                                    headers: {
                                                        'X-Requested-With': 'XMLHttpRequest',
                                                        'Accept': 'application/json'
                                                    }
                                                })
                                                .then(response => response.json())
                                                .then(data => {
                                                    if(data.success) {
                                                        this.stock = data.new_stock;
                                                        // Show a brief success pulse on the card or similar (optional)
                                                    } else {
                                                        alert(data.message || 'Fehler beim Speichern');
                                                    }
                                                })
                                                .catch(error => {
                                                    console.error('Error:', error);
                                                    alert('Netzwerkfehler beim Speichern der Aktion.');
                                                })
                                                .finally(() => {
                                                    this.submitting = false;
                                                });
                                            }
                                        }" @submit.prevent="submitForm($event)">
                                            @csrf
                                            <div class="flex items-center gap-2">
                                                <input type="number" name="quantity" min="0" step="any" value="1" required class="w-20 bg-gray-900 border border-gray-600 text-gray-200 rounded-md shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-50">
                                                <input type="hidden" name="type" id="type_{{ $material->id }}" value="taken">
                                                
                                                @if(auth()->user()->role === 'azubi')
                                                    <button type="button" disabled title="Azubis dürfen keine Materialien entnehmen." class="flex-1 bg-gray-600 text-gray-400 font-bold py-2 px-3 rounded text-sm text-center shadow cursor-not-allowed">
                                                        Entnehmen
                                                    </button>
                                                @else
                                                    <button type="submit" onclick="document.getElementById('type_{{ $material->id }}').value='taken'" :disabled="submitting" :class="submitting ? 'opacity-50 cursor-wait' : ''" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-3 rounded text-sm transition text-center shadow">
                                                        Entnehmen
                                                    </button>
                                                @endif

                                                @if(auth()->user()->is_admin || auth()->user()->is_chef || auth()->user()->is_materialwart)
                                                    <button type="submit" onclick="document.getElementById('type_{{ $material->id }}').value='added'" :disabled="submitting" :class="submitting ? 'opacity-50 cursor-wait' : ''" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-3 rounded text-sm transition text-center shadow">
                                                        Auffüllen
                                                    </button>
                                                @else
                                                    <button type="button" disabled title="Nur Chefs und Materialwarte dürfen auffüllen." class="flex-1 bg-gray-600 text-gray-400 font-bold py-2 px-3 rounded text-sm text-center shadow cursor-not-allowed">
                                                        Auffüllen
                                                    </button>
                                                @endif
                                            </div>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center text-gray-400 py-6">
                            Bisher keine Materialien im Lager angelegt.
                        </div>
                    @endforelse

                </div>
            </div>
            
            @if(auth()->user()->is_admin || auth()->user()->is_chef || auth()->user()->is_materialwart)
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('materials.manage') }}" class="bg-gray-700 hover:bg-gray-600 text-gray-200 font-bold py-2 px-4 rounded transition border border-gray-600">
                    Zur Materialverwaltung
                </a>
            </div>
            @endif

        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Restore scroll position if it exists in sessionStorage
            const scrollPosition = sessionStorage.getItem('materialScrollPosition');
            if (scrollPosition !== null) {
                window.scrollTo(0, parseInt(scrollPosition, 10));
                sessionStorage.removeItem('materialScrollPosition');
            }

            // Save scroll position when any form is submitted
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    sessionStorage.setItem('materialScrollPosition', window.scrollY);
                    
                    // Also pass a parameter to keep the accordion open if possible (requires backend/view support, 
                    // but for now scrolling to the exact pixel is the main goal)
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
