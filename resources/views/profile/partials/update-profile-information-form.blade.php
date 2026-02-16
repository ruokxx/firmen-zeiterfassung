<section>
    <header>
        <h2 class="text-lg font-medium text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="address" :value="__('Adresse')" />
            <x-textarea-input id="address" name="address" class="mt-1 block w-full" :value="old('address', $user->address)" autocomplete="street-address" required />
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        <div>
            <x-input-label for="mobile_number" :value="__('Handynummer')" />
            <x-text-input id="mobile_number" name="mobile_number" type="text" class="mt-1 block w-full" :value="old('mobile_number', $user->mobile_number)" autocomplete="tel" required />
            <x-input-error class="mt-2" :messages="$errors->get('mobile_number')" />
        </div>

        <div>
            <x-input-label for="google_calendar_url" :value="__('Google Kalender URL (Embed)')" />
            <x-text-input id="google_calendar_url" name="google_calendar_url" type="url" class="mt-1 block w-full" :value="old('google_calendar_url', $user->google_calendar_url)" placeholder="https://calendar.google.com/calendar/embed?src=..." />
            <x-input-error class="mt-2" :messages="$errors->get('google_calendar_url')" />
            <p class="mt-1 text-sm text-gray-400">
                {{ __('Fügen Sie hier die "Öffentliche URL zu diesem Kalender" oder die Embed-URL ein. Diese finden Sie in den Google Kalender Einstellungen unter "Integrieren".') }}
            </p>
        </div>

        <div>
            <div class="flex items-center gap-2">
                <x-input-label for="trello_url" :value="__('Trello URL (Optional)')" />
                <span class="px-2 py-0.5 rounded text-xs font-bold bg-red-900 text-red-200 border border-red-700">EXPERIMENTELL</span>
            </div>
            
            <div class="flex gap-2 items-center mb-2">
                @if($user->trello_token)
                    <span class="text-green-500 text-sm font-bold flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Trello verbunden
                    </span>
                    @if(isset($trelloBoards) && count($trelloBoards) > 0)
                        <select onchange="document.getElementById('trello_url').value = this.value" class="text-sm bg-gray-800 text-gray-300 border-gray-700 rounded shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Board auswahlen --</option>
                            @foreach($trelloBoards as $board)
                                <option value="{{ $board['url'] }}">{{ $board['name'] }}</option>
                            @endforeach
                        </select>
                    @endif
                @else
                    <a href="{{ route('auth.trello.redirect') }}" class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Mit Trello verbinden
                    </a>
                @endif
            </div>

            <x-text-input id="trello_url" name="trello_url" type="url" class="mt-1 block w-full" :value="old('trello_url', $user->trello_url)" placeholder="https://trello.com/b/..." />
            <x-input-error class="mt-2" :messages="$errors->get('trello_url')" />
            <p class="mt-1 text-sm text-gray-400">
                {{ __('Fügen Sie hier die URL zu Ihrem Trello Board oder Ihrer Karte ein. Verbinden Sie Trello oben, um Ihre Boards automatisch zu laden.') }}
            </p>
        </div>

        <div>
            <x-input-label for="language" :value="__('Sprache / Language')" />
            <select id="language" name="language" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm bg-gray-900 text-gray-300">
                <option value="de" {{ old('language', $user->language) == 'de' ? 'selected' : '' }}>Deutsch</option>
                <option value="en" {{ old('language', $user->language) == 'en' ? 'selected' : '' }}>English</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('language')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
