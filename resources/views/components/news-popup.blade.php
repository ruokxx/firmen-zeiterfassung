@php
    $user = auth()->user();
    $activePopup = null;

    if ($user) {
        $activePopups = \App\Models\NewsPopup::active()->latest()->get();
        foreach ($activePopups as $popup) {
            if (!$popup->isDismissedByUser($user)) {
                $activePopup = $popup;
                break;
            }
        }
    }
@endphp

@if($activePopup)
<div x-data="{ 
        show: false,
        popupId: {{ $activePopup->id }},
        userId: {{ $user->id }},
        async dismiss() {
            this.show = false;
            try {
                // Immediate client storage
                const key = 'news_popup_dismissed_' + this.popupId + '_' + this.userId;
                localStorage.setItem(key, new Date().toISOString());

                // Server-side registration
                await fetch('{{ route('news-popups.dismiss', $activePopup->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')
                    }
                });
            } catch (e) {
                console.error('Fehler beim Bestätigen des Popups:', e);
            }
        }
     }" 
     x-init="
        const key = 'news_popup_dismissed_' + popupId + '_' + userId;
        @if($activePopup->display_type === 'once_per_day')
            const lastDismissed = localStorage.getItem(key);
            const isToday = lastDismissed && new Date(lastDismissed).toDateString() === new Date().toDateString();
            if (!isToday) {
                setTimeout(() => show = true, 600);
            }
        @else
            if (!localStorage.getItem(key)) {
                setTimeout(() => show = true, 600);
            }
        @endif
     "
     x-show="show" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
     class="fixed z-50 inset-0 overflow-y-auto" 
     aria-labelledby="news-popup-title" 
     role="dialog" 
     aria-modal="true"
     style="display: none;">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="dismiss()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Dialog Box -->
        <div class="inline-block align-bottom bg-gray-800 rounded-xl px-5 pt-5 pb-5 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-700">
            
            <!-- Top bar with badge and close icon -->
            <div class="flex items-center justify-between pb-3 border-b border-gray-700">
                <div class="flex items-center gap-2">
                    @if($activePopup->type === 'important')
                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-orange-900/60 text-orange-300 border border-orange-700">
                            ⚠️ Wichtige Mitteilung
                        </span>
                    @elseif($activePopup->type === 'warning')
                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-red-900/60 text-red-300 border border-red-700">
                            🚨 Dringende Mitteilung
                        </span>
                    @elseif($activePopup->type === 'success')
                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-green-900/60 text-green-300 border border-green-700">
                            ✅ Mitteilung
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-blue-900/60 text-blue-300 border border-blue-700">
                            ℹ️ Information
                        </span>
                    @endif

                    @if($activePopup->expires_at)
                        <span class="text-[11px] text-gray-400">
                            Gültig bis {{ $activePopup->expires_at->format('d.m.Y') }}
                        </span>
                    @endif
                </div>

                <button type="button" @click="dismiss()" class="text-gray-400 hover:text-white transition p-1 rounded-lg hover:bg-gray-700" title="Schließen">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Content Area -->
            <div class="mt-4">
                <h3 class="text-xl font-bold text-white leading-snug" id="news-popup-title">
                    {{ $activePopup->title }}
                </h3>

                <div class="mt-3 text-sm text-gray-200 leading-relaxed bg-gray-900/60 p-4 rounded-lg border border-gray-750 max-h-80 overflow-y-auto whitespace-pre-line">
                    {!! nl2br(e($activePopup->content)) !!}
                </div>
            </div>

            <!-- Footer Button -->
            <div class="mt-6 flex justify-end">
                <button type="button" 
                        @click="dismiss()" 
                        class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent shadow-md px-5 py-2.5 text-sm font-semibold text-white transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800"
                        style="background-color: {{ $activePopup->type === 'important' ? '#ea580c' : ($activePopup->type === 'warning' ? '#dc2626' : ($activePopup->type === 'success' ? '#16a34a' : '#2563eb')) }};">
                    Verstanden / Schließen
                </button>
            </div>
        </div>
    </div>
</div>
@endif
