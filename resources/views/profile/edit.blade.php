<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Search/Reports Section --}}
            <div class="p-4 sm:p-8 bg-gray-800 shadow sm:rounded-lg border border-gray-700">
                <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium text-gray-100">
                            {{ __('Verfügbare Monatsberichte') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-400">
                            {{ __("Lade dir hier deine fertigen Stundenzettel als PDF herunter.") }}
                        </p>
                    </header>

                    <div class="mt-6 space-y-3">
                        @if(session('success'))
                            <div class="bg-green-900/50 border border-green-600 text-green-200 px-4 py-3 rounded relative mb-4" role="alert">
                                <span class="block sm:inline">{{ session('success') }}</span>
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="bg-red-900/50 border border-red-600 text-red-200 px-4 py-3 rounded relative mb-4" role="alert">
                                <span class="block sm:inline">{{ session('error') }}</span>
                            </div>
                        @endif

                        @forelse($reports as $report)
                            <div class="flex items-center justify-between p-3 bg-gray-900 rounded-lg border border-gray-700">
                                <span class="text-gray-200 font-medium">{{ $report['label'] }}</span>
                                <div class="flex space-x-2">
                                    <form action="{{ route('report.send') }}" method="POST" class="inline flex items-center gap-2">
                                        @csrf
                                        <input type="hidden" name="year" value="{{ $report['year'] }}">
                                        <input type="hidden" name="month" value="{{ $report['month'] }}">
                                        <label class="inline-flex items-center cursor-pointer mr-2">
                                            <input type="checkbox" name="append_prev_month" value="1" class="rounded border-gray-600 bg-gray-700 text-orange-500 shadow-sm focus:ring-orange-500 focus:ring-offset-gray-800 h-4 w-4">
                                            <span class="ml-1 text-xs text-gray-400">Vormonat anhängen</span>
                                        </label>
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 transition">
                                            An Chef senden
                                        </button>
                                        <button type="submit" formaction="{{ route('report.download') }}" formmethod="GET" class="inline-flex items-center px-3 py-1.5 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-500 transition ml-2">
                                            Download PDF
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 italic">Noch keine Berichte verfügbar.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-gray-800 shadow sm:rounded-lg border border-gray-700">
                <div class="max-w-xl">
                    @include('profile.partials.user-documents-list')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-gray-800 shadow sm:rounded-lg border border-gray-700">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-gray-800 shadow sm:rounded-lg border border-gray-700">
                <div class="max-w-xl">
                    @include('profile.partials.delete-month-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-gray-800 shadow sm:rounded-lg border border-gray-700">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
