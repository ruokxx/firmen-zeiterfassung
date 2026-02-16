<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-100">
            {{ __('Monat leeren') }}
        </h2>

        <p class="mt-1 text-sm text-gray-400">
            {{ __('Löschen Sie alle Einträge eines bestimmten Monats. Diese Aktion kann nicht rückgängig gemacht werden.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.clear-month') }}" class="p-6 bg-red-900/10 border border-red-700 rounded-lg"
          x-data="{ 
              step: 1,
              confirmSubmission(e) {
                  if (this.step === 1) {
                      e.preventDefault();
                      this.step = 2;
                  } else if (this.step === 2) {
                      if (!confirm('SIND SIE SICHER? Alle Daten dieses Monats werden UNWIDERRUFLICH gelöscht!')) {
                          e.preventDefault();
                      }
                  }
              }
          }"
          @submit="confirmSubmission"
    >
        @csrf
        @method('delete')

        <div class="mb-4">
            <x-input-label for="clear_month_select" :value="__('Monat auswählen')" class="text-red-200" />
            <select id="clear_month_select" name="month_year" class="mt-1 block w-full border-gray-600 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm bg-gray-900 text-gray-300" required
                    onchange="
                        const [y, m] = this.value.split('-');
                        document.getElementById('clear_year').value = y;
                        document.getElementById('clear_month').value = m;
                        this.closest('form').querySelector('[x-data]').__x.$data.step = 1; 
                    ">
                <option value="">-- Bitte wählen --</option>
                @foreach($reports as $report)
                    <option value="{{ $report['year'] }}-{{ $report['month'] }}">
                        {{ $report['label'] }}
                    </option>
                @endforeach
            </select>
            <input type="hidden" id="clear_year" name="year">
            <input type="hidden" id="clear_month" name="month">
        </div>

        <div class="flex items-center gap-4">
            <template x-if="step === 1">
                <x-danger-button type="submit">
                    {{ __('Monat leeren') }}
                </x-danger-button>
            </template>

            <template x-if="step === 2">
                <div class="flex items-center gap-4">
                    <span class="text-red-400 font-bold uppercase animate-pulse">Wirklich löschen?</span>
                    <x-danger-button type="submit" class="bg-red-700 hover:bg-red-800 focus:ring-red-900">
                        {{ __('JA, ALLES LÖSCHEN') }}
                    </x-danger-button>
                    <button type="button" @click="step = 1" class="text-gray-400 hover:text-gray-200 underline text-sm">Abbrechen</button>
                </div>
            </template>
        </div>
    </form>
</section>
