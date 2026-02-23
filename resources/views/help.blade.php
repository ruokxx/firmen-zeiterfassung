<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('Hilfe & FAQ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @php
                $settings = \App\Models\Setting::all()->pluck('value', 'key');
                $defaultHelpText = "Dies ist Ihre zentrale Plattform für Arbeitszeiterfassung, Urlaubsplanung und Materialbestellungen. Hier finden Sie eine Übersicht über Ihre geleisteten Stunden, können Ihren Status pflegen und wichtige Dokumente einsehen.<br><br><strong>Arbeitszeiterfassung:</strong> Erfassen Sie Ihre täglichen Arbeitszeiten über das Dashboard oder die Tagesansicht.<br><br><strong>Kalender & Status:</strong> Nutzen Sie die Monatsübersicht, um Tage als \"Urlaub\" (U) oder \"Krank\" (K) zu markieren.<br><br><strong>Material Bestellungen:</strong> Bestellen Sie benötigtes Material direkt über das System.";
            @endphp
            
            <!-- Welcome / Intro -->
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700 p-6">
                <h3 class="text-2xl font-bold text-orange-500 mb-4">{{ $settings->get('help_page_title', 'Willkommen im Work Time Tracker') }}</h3>
                <div class="text-gray-300 prose prose-invert max-w-none">
                    {!! nl2br(strip_tags($settings->get('help_page_content', $defaultHelpText), '<br><strong><b><i><em><ul><li><a>')) !!}
                </div>
            </div>

            <!-- FAQ Section -->
            <div x-data="{ active: null }" class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700 p-6">
                <h3 class="text-xl font-bold text-gray-100 mb-6 border-b border-gray-700 pb-2">Häufig gestellte Fragen (FAQ)</h3>
                
                <div class="space-y-2">
                    @forelse($faqs ?? [] as $faq)
                    <div class="border border-gray-700 rounded-lg bg-gray-750">
                        <button @click="active = active === {{ $faq->id }} ? null : {{ $faq->id }}" class="w-full text-left px-4 py-3 flex justify-between items-center focus:outline-none">
                            <span class="font-bold text-gray-200">{{ $faq->question }}</span>
                            <svg class="h-5 w-5 text-orange-500 transform transition-transform duration-200" :class="{'rotate-180': active === {{ $faq->id }}}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="active === {{ $faq->id }}" x-collapse class="px-4 pb-4 text-gray-400 text-sm prose prose-invert max-w-none">
                            {!! nl2br(strip_tags($faq->answer, '<strong><br><ul><li><em><i><b><a>')) !!}
                        </div>
                    </div>
                    @empty
                        <p class="text-gray-400 italic">Es sind noch keine FAQs hinterlegt.</p>
                    @endforelse
                </div>
            </div>

            <!-- Contact / Footer -->
            <div class="text-center text-gray-500 text-sm mt-8">
                <p>{!! $settings->get('help_page_copyright', '&copy; ' . date('Y') . ' Work Time Tracker. Bei weiteren Fragen wenden Sie sich bitte an den Support oder Ihren Vorgesetzten.') !!}</p>
            </div>
        </div>
    </div>
</x-app-layout>
