@php
    $appLogo = \App\Models\Setting::where('key', 'app_logo')->value('value');
@endphp
@if($appLogo)
    <img src="{{ asset('storage/' . $appLogo) }}" {{ $attributes }} alt="App Logo" />
@else
    <img src="https://asendorf-elektrotechnik.de/wp-content/uploads/2024/11/Assendorf-Logo-CMYK_PDF-2048x716.jpg" {{ $attributes }} alt="Asendorf-Elektrotechnik Logo" />
@endif
