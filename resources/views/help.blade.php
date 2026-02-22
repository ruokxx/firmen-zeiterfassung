<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('Hilfe & FAQ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Welcome / Intro -->
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700 p-6">
                <h3 class="text-2xl font-bold text-orange-500 mb-4">Willkommen im Work Time Tracker</h3>
                <p class="text-gray-300 mb-4">
                    Dies ist Ihre zentrale Plattform für Arbeitszeiterfassung, Urlaubsplanung und Materialbestellungen. 
                    Hier finden Sie eine Übersicht über Ihre geleisteten Stunden, können Ihren Status pflegen und wichtige Dokumente einsehen.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div class="bg-gray-750 p-4 rounded-lg border border-gray-600">
                        <h4 class="font-bold text-white mb-2 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Arbeitszeiterfassung
                        </h4>
                        <p class="text-sm text-gray-400">
                            Erfassen Sie Ihre täglichen Arbeitszeiten über das Dashboard oder die Tagesansicht. 
                            Das System berechnet automatisch Ihre Überstunden und zeigt Ihnen Ihren aktuellen monatlichen Fortschritt an.
                        </p>
                    </div>
                    <div class="bg-gray-750 p-4 rounded-lg border border-gray-600">
                        <h4 class="font-bold text-white mb-2 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            Kalender & Status
                        </h4>
                        <p class="text-sm text-gray-400">
                            Nutzen Sie die Monatsübersicht, um Tage als "Urlaub" (U), "Krank" (K) oder "Schule/Sonstiges" (8) zu markieren. 
                            Ihr Urlaubssaldo wird, sofern vom Administrator konfiguriert, automatisch aktualisiert.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Features Description -->
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700 p-6">
                <h3 class="text-xl font-bold text-gray-100 mb-4 border-b border-gray-700 pb-2">Funktionen im Detail</h3>
                
                <div class="space-y-6">
                    <div>
                        <h4 class="text-lg font-bold text-orange-400 mb-2">Dashboard</h4>
                        <ul class="list-disc list-inside text-gray-300 space-y-1 ml-2">
                            <li>Jahresübersicht mit allen Monaten und Stunden-Fortschritt.</li>
                            <li>Monatliche Zusammenfassung von Soll- und Ist-Stunden.</li>
                            <li>Mini-Kalender mit farblicher Markierung (Grün=Urlaub, Rot=Krank, Schwarz=Schule).</li>
                            <li>Tagesaktuelle Statusanzeige.</li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-lg font-bold text-orange-400 mb-2">Monatsansicht</h4>
                        <ul class="list-disc list-inside text-gray-300 space-y-1 ml-2">
                            <li>Detaillierte Auflistung aller Tage eines Monats.</li>
                            <li>Schnell-Aktionen: <strong>K</strong> (Krank), <strong>U</strong> (Urlaub), <strong>8</strong> (8 Stunden / Schule).</li>
                            <li>Bearbeiten einzelner Tage durch Klick auf das Datum.</li>
                            <li>PDF-Export der monatlichen Arbeitszeiten.</li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-lg font-bold text-orange-400 mb-2">Material Bestellungen</h4>
                        <ul class="list-disc list-inside text-gray-300 space-y-1 ml-2">
                            <li>Bestellen Sie benötigtes Material direkt über das System.</li>
                            <li>Verfolgen Sie den Status Ihrer Bestellungen.</li>
                        </ul>
                    </div>
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
                <p>&copy; {{ date('Y') }} Work Time Tracker. Bei weiteren Fragen wenden Sie sich bitte an den Support oder Ihren Vorgesetzten.</p>
            </div>
        </div>
    </div>
</x-app-layout>
