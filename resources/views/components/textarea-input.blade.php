@props(['disabled' => false, 'value' => ''])

<textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'bg-gray-700 border-gray-600 text-white focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm']) !!}>{{ $slot->isEmpty() ? $value ?? '' : $slot }}</textarea>
