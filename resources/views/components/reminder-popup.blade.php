@php
    $settings = \App\Models\Setting::all()->pluck('value', 'key');
    $isEnabled = $settings->get('hours_reminder_enabled', '1') == '1';
    $showReminder = false;
    $reminderMessage = 'Bitte denke daran, deine Stunden rechtzeitig abzugeben.';

    if ($isEnabled) {
        $type = $settings->get('hours_reminder_type', 'vorletzter_werktag');
        $today = \Carbon\Carbon::today();
        $year = $today->year;
        $month = $today->month;

        if ($type === 'vorletzter_werktag') {
            // Calculate next-to-last working day
            $date = \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth();
            $workingDaysFound = 0;
            $reminderDay = null;
            while ($workingDaysFound < 2) {
                if (!$date->isWeekend()) {
                    $workingDaysFound++;
                    if ($workingDaysFound === 2) {
                        $reminderDay = $date->day;
                        break;
                    }
                }
                $date->subDay();
            }
            if ($today->day === $reminderDay) {
                $showReminder = true;
            }
            $reminderMessage .= ' Dies ist eine Erinnerung für den vorletzten Werktag des Monats.';
        } elseif ($type === 'letzter_werktag') {
            // Calculate last working day
            $date = \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth();
            $reminderDay = null;
            while (true) {
                if (!$date->isWeekend()) {
                    $reminderDay = $date->day;
                    break;
                }
                $date->subDay();
            }
            if ($today->day === $reminderDay) {
                $showReminder = true;
            }
            $reminderMessage .= ' Dies ist eine Erinnerung für den letzten Werktag des Monats.';
        } elseif ($type === 'letzten_3_werktage') {
            // Calculate last 3 working days
            $date = \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth();
            $workingDays = [];
            while (count($workingDays) < 3) {
                if (!$date->isWeekend()) {
                    $workingDays[] = $date->day;
                }
                $date->subDay();
            }
            if (in_array($today->day, $workingDays)) {
                $showReminder = true;
            }
            $reminderMessage .= ' Dies ist eine Erinnerung für die letzten 3 Werktage des Monats.';
        } elseif ($type === 'feste_tage') {
            $daysString = $settings->get('hours_reminder_fixed_days', '15,20');
            $days = array_map('intval', explode(',', $daysString));
            if (in_array($today->day, $days)) {
                $showReminder = true;
            }
            $reminderMessage .= ' Dies ist eine Erinnerung für den ' . implode('. und ', array_map('trim', explode(',', $daysString))) . '. des Monats.';
        }
    }
@endphp

<div x-data="{ show: false }" 
     x-init="
        @if($showReminder)
            const date = new Date();
            const day = date.getDate();
            const key = 'reminder_dismissed_' + date.getFullYear() + '_' + (date.getMonth() + 1) + '_' + day;
            
            if (!localStorage.getItem(key)) {
                setTimeout(() => show = true, 1000);
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
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true"
     style="display: none;">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="show = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Stunden abgeben!
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            {{ $reminderMessage }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <button type="button" 
                        @click="
                            show = false; 
                            const date = new Date();
                            const key = 'reminder_dismissed_' + date.getFullYear() + '_' + (date.getMonth() + 1) + '_' + date.getDate();
                            localStorage.setItem(key, 'true');
                        " 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Verstanden
                </button>
            </div>
        </div>
    </div>
</div>
